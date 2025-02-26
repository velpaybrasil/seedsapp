<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Module {
    private PDO $db;
    protected string $table = 'modules';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public function findBySlug(string $slug): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug";
        return Database::fetch($sql, ['slug' => $slug]);
    }

    public function getActive(): array {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY order_index, name";
        return Database::fetchAll($sql);
    }

    public function all(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY order_index, name";
        return Database::fetchAll($sql);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (name, description, slug, icon, order_index, is_active, created_at, updated_at) 
                VALUES (:name, :description, :slug, :icon, :order_index, :is_active, NOW(), NOW())";
        
        Database::execute($sql, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'slug' => $this->generateSlug($data['name']),
            'icon' => $data['icon'] ?? null,
            'order_index' => $data['order_index'] ?? 0,
            'is_active' => isset($data['is_active']) ? 1 : 0
        ]);

        return (int) Database::lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params['name'] = $data['name'];
            
            // Only update slug if name is changed and slug is not explicitly provided
            if (!isset($data['slug'])) {
                $fields[] = "slug = :slug";
                $params['slug'] = $this->generateSlug($data['name']);
            }
        }

        if (isset($data['slug'])) {
            $fields[] = "slug = :slug";
            $params['slug'] = $data['slug'];
        }

        if (isset($data['description'])) {
            $fields[] = "description = :description";
            $params['description'] = $data['description'];
        }

        if (isset($data['icon'])) {
            $fields[] = "icon = :icon";
            $params['icon'] = $data['icon'];
        }

        if (isset($data['order_index'])) {
            $fields[] = "order_index = :order_index";
            $params['order_index'] = (int) $data['order_index'];
        }

        if (isset($data['is_active'])) {
            $fields[] = "is_active = :is_active";
            $params['is_active'] = $data['is_active'] ? 1 : 0;
        }

        $fields[] = "updated_at = NOW()";

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        return Database::execute($sql, $params);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    protected function generateSlug(string $name): string {
        // Convert to lowercase
        $slug = mb_strtolower($name, 'UTF-8');
        
        // Replace accented characters
        $slug = preg_replace('/[áàãâä]/u', 'a', $slug);
        $slug = preg_replace('/[éèêë]/u', 'e', $slug);
        $slug = preg_replace('/[íìîï]/u', 'i', $slug);
        $slug = preg_replace('/[óòõôö]/u', 'o', $slug);
        $slug = preg_replace('/[úùûü]/u', 'u', $slug);
        $slug = preg_replace('/[ýÿ]/u', 'y', $slug);
        $slug = str_replace('ç', 'c', $slug);
        $slug = str_replace('ñ', 'n', $slug);
        
        // Replace spaces and special characters with hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        
        // Replace multiple hyphens with single hyphen
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
