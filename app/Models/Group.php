<?php

namespace App\Models;

use App\Core\Database;

class Group {
    protected string $table = 'growth_groups';

    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (
                    name, leader_id, meeting_day, meeting_time, 
                    address, neighborhood, city, state, 
                    capacity, description, status, created_at
                ) VALUES (
                    :name, :leader_id, :meeting_day, :meeting_time,
                    :address, :neighborhood, :city, :state,
                    :capacity, :description, :status, NOW()
                )";
        
        Database::execute($sql, [
            'name' => $data['name'],
            'leader_id' => $data['leader_id'],
            'meeting_day' => $data['meeting_day'],
            'meeting_time' => $data['meeting_time'],
            'address' => $data['address'],
            'neighborhood' => $data['neighborhood'],
            'city' => $data['city'],
            'state' => $data['state'],
            'capacity' => $data['capacity'] ?? 12,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active'
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

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        return Database::execute($sql, $params);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    public function count(): int {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = Database::fetch($sql);
        return (int) ($result['total'] ?? 0);
    }

    public function countActiveGroups(): int {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'active'";
        $result = Database::fetch($sql);
        return (int) ($result['total'] ?? 0);
    }

    public function getRecentGroups(int $limit = 5): array {
        $sql = "
            SELECT g.*, u.name as leader_name
            FROM {$this->table} g
            LEFT JOIN users u ON g.leader_id = u.id
            ORDER BY g.created_at DESC
            LIMIT :limit
        ";
        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    public function getActiveGroups(): array {
        $sql = "
            SELECT g.*, u.name as leader_name
            FROM {$this->table} g
            LEFT JOIN users u ON g.leader_id = u.id
            WHERE g.status = 'active'
            ORDER BY g.name ASC
        ";
        return Database::fetchAll($sql);
    }

    public function getGroupsByLeader(int $leaderId): array {
        $sql = "
            SELECT * FROM {$this->table}
            WHERE leader_id = :leader_id
            ORDER BY created_at DESC
        ";
        return Database::fetchAll($sql, ['leader_id' => $leaderId]);
    }

    public function getGroupWithLeader(int $id): ?array {
        $sql = "
            SELECT g.*, u.name as leader_name
            FROM {$this->table} g
            LEFT JOIN users u ON g.leader_id = u.id
            WHERE g.id = :id
        ";
        return Database::fetch($sql, ['id' => $id]);
    }
}
