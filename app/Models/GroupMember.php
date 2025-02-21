<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class GroupMember extends Model {
    protected static string $table = 'group_members';
    protected static array $fillable = [
        'group_id',
        'user_id',
        'status',
        'joined_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Adiciona um membro ao grupo
     * @param int $groupId ID do grupo
     * @param int $userId ID do usuário
     * @return bool
     */
    public static function addMember(int $groupId, int $userId): bool
    {
        try {
            $sql = "INSERT INTO " . static::$table . " (group_id, user_id) VALUES (:group_id, :user_id)";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("[GroupMember] Erro ao adicionar membro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove um membro do grupo
     * @param int $groupId ID do grupo
     * @param int $userId ID do usuário
     * @return bool
     */
    public static function removeMember(int $groupId, int $userId): bool
    {
        try {
            $sql = "DELETE FROM " . static::$table . " WHERE group_id = :group_id AND user_id = :user_id";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("[GroupMember] Erro ao remover membro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza o status de um membro
     * @param int $groupId ID do grupo
     * @param int $userId ID do usuário
     * @param string $status Novo status
     * @return bool
     */
    public static function updateStatus(int $groupId, int $userId, string $status): bool
    {
        try {
            $sql = "UPDATE " . static::$table . " SET status = :status WHERE group_id = :group_id AND user_id = :user_id";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("[GroupMember] Erro ao atualizar status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca os membros de um grupo
     * @param int $groupId ID do grupo
     * @return array Lista de membros
     */
    public static function getGroupMembers(int $groupId): array
    {
        try {
            $sql = "
                SELECT m.*, u.name, u.email
                FROM " . static::$table . " m
                JOIN users u ON m.user_id = u.id
                WHERE m.group_id = :group_id
                ORDER BY u.name ASC
            ";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GroupMember] Erro ao buscar membros: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica se um usuário é membro de um grupo
     * @param int $groupId ID do grupo
     * @param int $userId ID do usuário
     * @return bool
     */
    public static function isMember(int $groupId, int $userId): bool
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE group_id = :group_id AND user_id = :user_id AND status = 'active'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'] > 0;
        } catch (\PDOException $e) {
            error_log("[GroupMember] Erro ao verificar membro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Conta o número de membros ativos de um grupo
     * @param int $groupId ID do grupo
     * @return int Número de membros
     */
    public static function countActiveMembers(int $groupId): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE group_id = :group_id AND status = 'active'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (\PDOException $e) {
            error_log("[GroupMember] Erro ao contar membros: " . $e->getMessage());
            return 0;
        }
    }
}
