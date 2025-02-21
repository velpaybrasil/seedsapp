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

    public function getActive(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        return Database::fetchAll($sql);
    }

    public function all(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        return Database::fetchAll($sql);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (name, description, icon, created_at, updated_at) 
                VALUES (:name, :description, :icon, NOW(), NOW())";
        
        Database::execute($sql, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null
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
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }
}
