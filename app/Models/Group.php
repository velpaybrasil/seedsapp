<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class Group extends Model {
    protected static string $table = 'growth_groups';
    protected static array $fillable = [
        'name',
        'description',
        'ministry_id',
        'leader_id',
        'co_leader_id',
        'meeting_day',
        'meeting_time',
        'meeting_address',
        'neighborhood',
        'max_participants',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * Conta o número de grupos ativos
     * @return int Número de grupos ativos
     */
    public static function countActiveGroups(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE status = 'active'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (\PDOException $e) {
            error_log("[Group] Erro ao contar grupos ativos: " . $e->getMessage());
            throw new \Exception('Erro ao contar grupos ativos');
        }
    }

    /**
     * Busca grupos recentes com informações do líder
     * @param int $limit Limite de registros
     * @return array Lista de grupos recentes
     */
    public static function getRecentGroups(int $limit = 5): array {
        try {
            $sql = "
                SELECT g.*, 
                       COALESCE(l.name, 'Sem líder') as leader_name,
                       COALESCE(cl.name, 'Sem co-líder') as co_leader_name,
                       m.name as ministry_name
                FROM " . static::$table . " g
                LEFT JOIN users l ON g.leader_id = l.id
                LEFT JOIN users cl ON g.co_leader_id = cl.id
                LEFT JOIN ministries m ON g.ministry_id = m.id
                ORDER BY g.created_at DESC
                LIMIT :limit
            ";
            
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[Group] Erro ao buscar grupos recentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca grupos ativos com informações do líder
     * @return array Lista de grupos ativos
     */
    public static function getActiveGroups(): array {
        try {
            $sql = "
                SELECT g.*, 
                       COALESCE(l.name, 'Sem líder') as leader_name,
                       COALESCE(cl.name, 'Sem co-líder') as co_leader_name,
                       m.name as ministry_name
                FROM " . static::$table . " g
                LEFT JOIN users l ON g.leader_id = l.id
                LEFT JOIN users cl ON g.co_leader_id = cl.id
                LEFT JOIN ministries m ON g.ministry_id = m.id
                WHERE g.status = 'active'
                ORDER BY g.name ASC
            ";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[Group] Erro ao buscar grupos ativos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca grupos de um líder específico
     * @param int $leaderId ID do líder
     * @return array Lista de grupos do líder
     */
    public static function getGroupsByLeader(int $leaderId): array {
        try {
            $sql = "
                SELECT g.*, 
                       COALESCE(l.name, 'Sem líder') as leader_name,
                       COALESCE(cl.name, 'Sem co-líder') as co_leader_name,
                       m.name as ministry_name
                FROM " . static::$table . " g
                LEFT JOIN users l ON g.leader_id = l.id
                LEFT JOIN users cl ON g.co_leader_id = cl.id
                LEFT JOIN ministries m ON g.ministry_id = m.id
                WHERE g.leader_id = :leader_id
                ORDER BY g.created_at DESC
            ";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':leader_id', $leaderId, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[Group] Erro ao buscar grupos do líder: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca um grupo com informações do líder
     * @param int $id ID do grupo
     * @return array|null Dados do grupo ou null se não encontrado
     */
    public static function getGroupWithLeader(int $id): ?array {
        try {
            $sql = "
                SELECT g.*, 
                       COALESCE(l.name, 'Sem líder') as leader_name,
                       COALESCE(cl.name, 'Sem co-líder') as co_leader_name,
                       m.name as ministry_name
                FROM " . static::$table . " g
                LEFT JOIN users l ON g.leader_id = l.id
                LEFT JOIN users cl ON g.co_leader_id = cl.id
                LEFT JOIN ministries m ON g.ministry_id = m.id
                WHERE g.id = :id
            ";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("[Group] Erro ao buscar grupo com líder: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca grupos com paginação e filtros
     * @param string|null $search Termo de busca
     * @param string|null $status Status do grupo
     * @param int $page Página atual
     * @param int $perPage Registros por página
     * @return array Lista de grupos e total de registros
     */
    public static function getGroups(?string $search = null, ?string $status = null, int $page = 1, int $perPage = 10): array
    {
        try {
            $conditions = [];
            $params = [];
            
            if ($search) {
                $conditions[] = "(g.name LIKE :search OR g.neighborhood LIKE :search)";
                $params[':search'] = "%{$search}%";
            }
            
            if ($status) {
                $conditions[] = "g.status = :status";
                $params[':status'] = $status;
            }
            
            $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            // Conta total de registros
            $countSql = "
                SELECT COUNT(*) as total 
                FROM " . static::$table . " g
                {$where}
            ";
            
            $stmt = self::getDB()->prepare($countSql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $total = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Calcula offset
            $offset = ($page - 1) * $perPage;
            
            // Busca grupos
            $sql = "
                SELECT g.*, 
                       COALESCE(l.name, 'Sem líder') as leader_name,
                       COALESCE(cl.name, 'Sem co-líder') as co_leader_name,
                       m.name as ministry_name
                FROM " . static::$table . " g
                LEFT JOIN users l ON g.leader_id = l.id
                LEFT JOIN users cl ON g.co_leader_id = cl.id
                LEFT JOIN ministries m ON g.ministry_id = m.id
                {$where}
                ORDER BY g.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = self::getDB()->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'groups' => $groups,
                'total' => $total,
                'pages' => ceil($total / $perPage)
            ];
        } catch (\PDOException $e) {
            error_log("[Group] Erro ao buscar grupos: " . $e->getMessage());
            return [
                'groups' => [],
                'total' => 0,
                'pages' => 0
            ];
        }
    }
}
