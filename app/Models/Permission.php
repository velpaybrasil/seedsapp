<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Permission {
    private PDO $db;
    protected string $table = 'permissions';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT p.*, m.name as module_name
                FROM {$this->table} p
                INNER JOIN modules m ON p.module_id = m.id
                ORDER BY m.order_index, p.name";
        return Database::fetchAll($sql);
    }

    public function getByModule(int $moduleId): array {
        $sql = "SELECT p.*, m.name as module_name
                FROM {$this->table} p
                INNER JOIN modules m ON p.module_id = m.id
                WHERE p.module_id = :module_id
                ORDER BY p.name";
        return Database::fetchAll($sql, ['module_id' => $moduleId]);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT p.*, m.name as module_name
                FROM {$this->table} p
                INNER JOIN modules m ON p.module_id = m.id
                WHERE p.id = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (module_id, name, slug, description)
                VALUES (:module_id, :name, :slug, :description)";
        
        Database::execute($sql, [
            'module_id' => $data['module_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null
        ]);

        return (int) Database::lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table}
                SET module_id = :module_id,
                    name = :name,
                    slug = :slug,
                    description = :description
                WHERE id = :id";

        return Database::execute($sql, [
            'module_id' => $data['module_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'id' => $id
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    public function getModules(): array {
        $sql = "SELECT *
                FROM modules
                WHERE is_active = TRUE
                ORDER BY order_index";
        return Database::fetchAll($sql);
    }

    public function getPermissionsByRole(int $roleId): array {
        $sql = "SELECT p.id
                FROM {$this->table} p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = :role_id";
        return Database::fetchAll($sql, ['role_id' => $roleId]);
    }
}
