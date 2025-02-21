<?php

namespace App\Models;

class Role {
    private $db;
    private $table = 'roles';

    public function __construct() {
        $this->db = new \PDO(
            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        );
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY name");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, description)
            VALUES (?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET name = ?, description = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? AND is_system = FALSE");
        return $stmt->execute([$id]);
    }

    public function getPermissions($roleId) {
        $stmt = $this->db->prepare("
            SELECT p.*, m.name as module_name
            FROM permissions p
            INNER JOIN role_permissions rp ON p.id = rp.permission_id
            INNER JOIN modules m ON p.module_id = m.id
            WHERE rp.role_id = ?
            ORDER BY m.order_index, p.name
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll();
    }

    public function updatePermissions($roleId, $permissionIds) {
        // Remover permissões existentes
        $stmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$roleId]);

        // Adicionar novas permissões
        if (!empty($permissionIds)) {
            $values = implode(',', array_fill(0, count($permissionIds), '(?, ?)'));
            $params = [];
            foreach ($permissionIds as $permissionId) {
                $params[] = $roleId;
                $params[] = $permissionId;
            }

            $stmt = $this->db->prepare("
                INSERT INTO role_permissions (role_id, permission_id)
                VALUES {$values}
            ");
            $stmt->execute($params);
        }
        return true;
    }

    public function getRoleUsers($roleId) {
        $stmt = $this->db->prepare("
            SELECT u.*
            FROM users u
            INNER JOIN user_roles ur ON u.id = ur.user_id
            WHERE ur.role_id = ?
            ORDER BY u.name
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll();
    }
}
