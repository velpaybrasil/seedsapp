<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class SystemModule {
    private PDO $db;
    protected string $table = 'system_modules';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public function findBySlug(string $slug): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug";
        return Database::fetch($sql, ['slug' => $slug]);
    }

    public function getAll(bool $activeOnly = true): array {
        $sql = "SELECT * FROM {$this->table}";
        if ($activeOnly) {
            $sql .= " WHERE active = 1";
        }
        $sql .= " ORDER BY order_index ASC";
        return Database::fetchAll($sql);
    }

    public function getModuleHierarchy(): array {
        $sql = "SELECT m.*, 
                       (SELECT COUNT(*) FROM {$this->table} WHERE parent_id = m.id) as has_children
                FROM {$this->table} m
                WHERE m.parent_id IS NULL AND m.active = 1
                ORDER BY m.order_index ASC";
        
        $modules = Database::fetchAll($sql);
        
        foreach ($modules as &$module) {
            if ($module['has_children']) {
                $sql = "SELECT * FROM {$this->table} 
                        WHERE parent_id = :parent_id AND active = 1 
                        ORDER BY order_index ASC";
                $module['children'] = Database::fetchAll($sql, ['parent_id' => $module['id']]);
            }
        }
        
        return $modules;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (name, description, slug, icon, parent_id, order_index, active, created_at, updated_at)
                VALUES (:name, :description, :slug, :icon, :parent_id, :order_index, :active, NOW(), NOW())";
        
        Database::execute($sql, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'slug' => $data['slug'],
            'icon' => $data['icon'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'order_index' => $data['order_index'] ?? 0,
            'active' => $data['active'] ?? true
        ]);

        return (int) Database::lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        $fields[] = "updated_at = NOW()";

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        return Database::execute($sql, $params);
    }

    public function delete(int $id): bool {
        // Primeiro verifica se tem módulos filhos
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE parent_id = :id";
        $count = Database::count($sql, ['id' => $id]);

        if ($count > 0) {
            throw new \Exception('Não é possível excluir um módulo que possui submódulos.');
        }

        // Depois verifica se tem permissões associadas
        $sql = "SELECT COUNT(*) FROM user_permissions WHERE module_id = :id";
        $count = Database::count($sql, ['id' => $id]);

        if ($count > 0) {
            throw new \Exception('Não é possível excluir um módulo que possui permissões associadas.');
        }

        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }
}
