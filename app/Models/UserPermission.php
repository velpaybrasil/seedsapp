<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UserPermission {
    private PDO $db;
    protected string $table = 'user_permissions';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getUserPermissions(int $userId): array {
        $sql = "SELECT up.*, sm.name as module_name, sm.slug as module_slug 
                FROM {$this->table} up
                JOIN system_modules sm ON up.module_id = sm.id
                WHERE up.user_id = :user_id";
        return Database::fetchAll($sql, ['user_id' => $userId]);
    }

    public function getModulePermissions(int $moduleId): array {
        $sql = "SELECT up.*, u.name as user_name, u.email as user_email 
                FROM {$this->table} up
                JOIN users u ON up.user_id = u.id
                WHERE up.module_id = :module_id";
        return Database::fetchAll($sql, ['module_id' => $moduleId]);
    }

    public function hasPermission(int $userId, string $moduleSlug, string $permission = 'view'): bool {
        $sql = "SELECT up.* 
                FROM {$this->table} up
                JOIN system_modules sm ON up.module_id = sm.id
                WHERE up.user_id = :user_id AND sm.slug = :module_slug";
        
        $userPerm = Database::fetch($sql, [
            'user_id' => $userId,
            'module_slug' => $moduleSlug
        ]);

        if (!$userPerm) {
            return false;
        }

        switch ($permission) {
            case 'view':
                return (bool) $userPerm['can_view'];
            case 'create':
                return (bool) $userPerm['can_create'];
            case 'edit':
                return (bool) $userPerm['can_edit'];
            case 'delete':
                return (bool) $userPerm['can_delete'];
            default:
                return false;
        }
    }

    public function setPermission(int $userId, int $moduleId, array $permissions): bool {
        // Primeiro verifica se já existe
        $sql = "SELECT id FROM {$this->table} WHERE user_id = :user_id AND module_id = :module_id";
        $existing = Database::fetch($sql, [
            'user_id' => $userId,
            'module_id' => $moduleId
        ]);

        if ($existing) {
            // Atualiza permissões existentes
            $sql = "UPDATE {$this->table} SET
                    can_view = :can_view,
                    can_create = :can_create,
                    can_edit = :can_edit,
                    can_delete = :can_delete,
                    updated_at = NOW()
                    WHERE id = :id";

            return Database::execute($sql, [
                'id' => $existing['id'],
                'can_view' => $permissions['can_view'] ?? false,
                'can_create' => $permissions['can_create'] ?? false,
                'can_edit' => $permissions['can_edit'] ?? false,
                'can_delete' => $permissions['can_delete'] ?? false
            ]);
        } else {
            // Cria novas permissões
            $sql = "INSERT INTO {$this->table} 
                    (user_id, module_id, can_view, can_create, can_edit, can_delete, created_at, updated_at)
                    VALUES 
                    (:user_id, :module_id, :can_view, :can_create, :can_edit, :can_delete, NOW(), NOW())";

            return Database::execute($sql, [
                'user_id' => $userId,
                'module_id' => $moduleId,
                'can_view' => $permissions['can_view'] ?? false,
                'can_create' => $permissions['can_create'] ?? false,
                'can_edit' => $permissions['can_edit'] ?? false,
                'can_delete' => $permissions['can_delete'] ?? false
            ]);
        }
    }

    public function deleteUserPermissions(int $userId): bool {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        return Database::execute($sql, ['user_id' => $userId]);
    }

    public function deleteModulePermissions(int $moduleId): bool {
        $sql = "DELETE FROM {$this->table} WHERE module_id = :module_id";
        return Database::execute($sql, ['module_id' => $moduleId]);
    }
}
