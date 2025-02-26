<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Role extends Model {
    protected static string $table = 'roles';
    protected static array $fillable = ['name', 'description', 'is_system', 'active'];

    public static function getAll() {
        try {
            $sql = "
                SELECT 
                    r.*,
                    COUNT(DISTINCT ur.user_id) as user_count
                FROM " . static::$table . " r
                LEFT JOIN user_roles ur ON r.id = ur.role_id
                GROUP BY r.id
                ORDER BY r.name";

            $roles = static::getDB()->query($sql)->fetchAll();

            // Fetch permissions for each role
            foreach ($roles as &$role) {
                $role['permissions'] = static::getPermissions($role['id']);
                $role['users'] = static::getUsers($role['id']);
            }

            return $roles;
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao buscar papéis: " . $e->getMessage());
            return [];
        }
    }

    public static function getPermissions($roleId) {
        try {
            $sql = "
                SELECT 
                    p.*,
                    m.name as module_name
                FROM permissions p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                INNER JOIN modules m ON p.module_id = m.id
                WHERE rp.role_id = :role_id
                ORDER BY m.name, p.name";

            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['role_id' => $roleId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao buscar permissões do papel: " . $e->getMessage());
            return [];
        }
    }

    public static function getUsers($roleId) {
        try {
            $sql = "
                SELECT u.*
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                WHERE ur.role_id = :role_id
                ORDER BY u.name";

            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['role_id' => $roleId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao buscar usuários do papel: " . $e->getMessage());
            return [];
        }
    }

    public static function create($data) {
        try {
            $sql = "INSERT INTO " . static::$table . " (name, description, is_system, active) VALUES (:name, :description, :is_system, :active)";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_system' => $data['is_system'] ?? false,
                'active' => $data['active'] ?? true
            ]);
            return static::getDB()->lastInsertId();
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao criar papel: " . $e->getMessage());
            throw $e;
        }
    }

    public static function update($id, $data) {
        try {
            $sql = "UPDATE " . static::$table . " SET name = :name, description = :description, is_system = :is_system, active = :active WHERE id = :id";
            $stmt = static::getDB()->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_system' => $data['is_system'] ?? false,
                'active' => $data['active'] ?? true
            ]);
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao atualizar papel: " . $e->getMessage());
            throw $e;
        }
    }

    public static function delete($id) {
        try {
            $sql = "DELETE FROM " . static::$table . " WHERE id = :id";
            $stmt = static::getDB()->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao excluir papel: " . $e->getMessage());
            throw $e;
        }
    }

    public static function assignPermissions($roleId, $moduleId, $permissions) {
        try {
            $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
            $stmt = static::getDB()->prepare($sql);

            foreach ($permissions as $permissionId) {
                $stmt->execute([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId
                ]);
            }

            return true;
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao atribuir permissões ao papel: " . $e->getMessage());
            return false;
        }
    }

    public static function clearPermissions($roleId) {
        try {
            $sql = "DELETE FROM role_permissions WHERE role_id = :role_id";
            $stmt = static::getDB()->prepare($sql);
            return $stmt->execute(['role_id' => $roleId]);
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao remover permissões do papel: " . $e->getMessage());
            return false;
        }
    }

    public static function isSystemRole($roleId) {
        try {
            $sql = "SELECT is_system FROM " . static::$table . " WHERE id = :id";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['id' => $roleId]);
            $result = $stmt->fetch();
            return $result && (bool)$result['is_system'];
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao verificar se papel é do sistema: " . $e->getMessage());
            return false;
        }
    }

    public static function findByName(string $name): ?array {
        try {
            $sql = "SELECT * FROM " . static::$table . " WHERE name = :name LIMIT 1";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['name' => $name]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[Role] Erro ao buscar papel por nome: " . $e->getMessage());
            return null;
        }
    }
}
