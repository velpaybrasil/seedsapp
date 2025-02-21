<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class GroupLeader extends Model {
    protected static string $table = 'growth_group_leaders';
    protected static array $fillable = [
        'group_id',
        'user_id',
        'role',
        'created_at'
    ];

    /**
     * Adiciona um líder ao grupo
     * @param int $groupId ID do grupo
     * @param int $userId ID do usuário
     * @param string $role Papel do líder (leader/co-leader)
     * @return bool
     */
    public static function addLeader(int $groupId, int $userId, string $role = 'leader'): bool
    {
        try {
            $sql = "INSERT INTO " . static::$table . " (group_id, user_id, role) VALUES (:group_id, :user_id, :role)";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("[GroupLeader] Erro ao adicionar líder: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove um líder do grupo
     * @param int $groupId ID do grupo
     * @param int $userId ID do usuário
     * @return bool
     */
    public static function removeLeader(int $groupId, int $userId): bool
    {
        try {
            $sql = "DELETE FROM " . static::$table . " WHERE group_id = :group_id AND user_id = :user_id";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("[GroupLeader] Erro ao remover líder: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca os líderes de um grupo
     * @param int $groupId ID do grupo
     * @return array Lista de líderes
     */
    public static function getGroupLeaders(int $groupId): array
    {
        try {
            $sql = "
                SELECT u.*, gl.role
                FROM " . static::$table . " gl
                JOIN users u ON gl.user_id = u.id
                WHERE gl.group_id = :group_id
                ORDER BY gl.role ASC, u.name ASC
            ";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GroupLeader] Erro ao buscar líderes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Atualiza os líderes de um grupo
     * @param int $groupId ID do grupo
     * @param array $leaders Lista de IDs dos líderes
     * @return bool
     */
    public static function updateGroupLeaders(int $groupId, array $leaders): bool
    {
        try {
            self::getDB()->beginTransaction();

            // Remove todos os líderes atuais
            $sql = "DELETE FROM " . static::$table . " WHERE group_id = :group_id";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();

            // Adiciona os novos líderes
            foreach ($leaders as $leader) {
                $sql = "INSERT INTO " . static::$table . " (group_id, user_id, role) VALUES (:group_id, :user_id, :role)";
                $stmt = self::getDB()->prepare($sql);
                $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $leader['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(':role', $leader['role'], PDO::PARAM_STR);
                $stmt->execute();
            }

            self::getDB()->commit();
            return true;
        } catch (\PDOException $e) {
            self::getDB()->rollBack();
            error_log("[GroupLeader] Erro ao atualizar líderes: " . $e->getMessage());
            return false;
        }
    }
}
