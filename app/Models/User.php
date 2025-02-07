<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private PDO $db;
    protected string $table = 'users';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return Database::fetch($sql, ['id' => $id]);
    }

    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        return Database::fetch($sql, ['email' => $email]);
    }

    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (name, email, password, role, is_owner, active, created_at, updated_at) 
                VALUES (:name, :email, :password, :role, :is_owner, :active, NOW(), NOW())";
        
        Database::execute($sql, [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'user',
            'is_owner' => $data['is_owner'] ?? 0,
            'active' => $data['active'] ?? 1
        ]);

        return (int) Database::lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'password') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        // Adiciona o updated_at
        $fields[] = "updated_at = NOW()";

        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        return Database::execute($sql, $params);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    public function all(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        return Database::fetchAll($sql);
    }

    public function count(): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return Database::count($sql);
    }

    public function countActiveUsers(): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE active = 1";
        return Database::count($sql);
    }

    public function getRecentUsers(int $limit = 5): array {
        $sql = "SELECT id, name, email, role, created_at 
                FROM {$this->table} 
                WHERE active = 1 
                ORDER BY created_at DESC 
                LIMIT :limit";
        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    public function validateLogin(string $email, string $password): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND active = 1";
        $user = Database::fetch($sql, ['email' => $email]);
        
        if (!$user) {
            return null;
        }

        // Verifica se o usuário está bloqueado
        if ($user['locked_until'] && new \DateTime($user['locked_until']) > new \DateTime()) {
            throw new \Exception('Conta bloqueada temporariamente. Tente novamente mais tarde.');
        }

        if (!password_verify($password, $user['password'])) {
            // Incrementa tentativas falhas
            $this->incrementFailedAttempts($user['id']);
            return null;
        }

        // Reseta tentativas falhas e atualiza último login
        $this->resetFailedAttempts($user['id']);
        $this->updateLastLogin($user['id']);
        
        return $user;
    }

    private function incrementFailedAttempts(int $userId): void {
        $sql = "UPDATE {$this->table} SET 
                failed_login_attempts = failed_login_attempts + 1,
                locked_until = CASE 
                    WHEN failed_login_attempts >= 4 THEN DATE_ADD(NOW(), INTERVAL 30 MINUTE)
                    ELSE NULL 
                END
                WHERE id = :id";
        Database::execute($sql, ['id' => $userId]);
    }

    private function resetFailedAttempts(int $userId): void {
        $sql = "UPDATE {$this->table} SET 
                failed_login_attempts = 0,
                locked_until = NULL
                WHERE id = :id";
        Database::execute($sql, ['id' => $userId]);
    }

    public function updateLastLogin(int $id): bool {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        return Database::execute($sql, ['id' => $id]);
    }

    public function updatePassword(int $id, string $password): bool {
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        return Database::execute($sql, [
            'id' => $id,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function getNameById($userId): string {
        try {
            if (!$userId) return 'Não definido';
            
            $sql = "SELECT name FROM users WHERE id = :userId";
            $result = Database::fetch($sql, ['userId' => $userId]);
            return $result['name'] ?? 'Não definido';
        } catch (\PDOException $e) {
            error_log('Erro ao buscar nome do usuário: ' . $e->getMessage());
            return 'Não definido';
        }
    }

    public function getPermissions(int $userId): array {
        $permissionModel = new UserPermission();
        return $permissionModel->getUserPermissions($userId);
    }

    public function hasPermission(int $userId, string $moduleSlug, string $permission = 'view'): bool {
        $permissionModel = new UserPermission();
        return $permissionModel->hasPermission($userId, $moduleSlug, $permission);
    }

    public function setPermissions(int $userId, array $modulePermissions): bool {
        $permissionModel = new UserPermission();
        $success = true;

        foreach ($modulePermissions as $moduleId => $permissions) {
            $result = $permissionModel->setPermission($userId, $moduleId, $permissions);
            if (!$result) {
                $success = false;
            }
        }

        return $success;
    }

    public function getAccessibleModules(int $userId): array {
        $sql = "SELECT DISTINCT sm.* 
                FROM system_modules sm
                JOIN user_permissions up ON sm.id = up.module_id
                WHERE up.user_id = :user_id 
                AND up.can_view = 1 
                AND sm.active = 1
                ORDER BY sm.order_index ASC";
        
        return Database::fetchAll($sql, ['user_id' => $userId]);
    }

    public function requirePasswordChange(int $userId, bool $require = true): bool {
        $sql = "UPDATE {$this->table} SET must_change_password = :require WHERE id = :id";
        return Database::execute($sql, [
            'id' => $userId,
            'require' => $require
        ]);
    }

    public function mustChangePassword(int $userId): bool {
        $sql = "SELECT must_change_password FROM {$this->table} WHERE id = :id";
        $result = Database::fetch($sql, ['id' => $userId]);
        return $result ? (bool)$result['must_change_password'] : false;
    }

    public function getAllWithRoles(): array {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as role_names, GROUP_CONCAT(r.id) as role_ids 
                FROM {$this->table} u 
                LEFT JOIN user_roles ur ON u.id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.id 
                GROUP BY u.id 
                ORDER BY u.name";
        
        $users = Database::fetchAll($sql);
        
        // Format roles for each user
        foreach ($users as &$user) {
            $roleNames = $user['role_names'] ? explode(',', $user['role_names']) : [];
            $roleIds = $user['role_ids'] ? explode(',', $user['role_ids']) : [];
            
            $user['roles'] = [];
            for ($i = 0; $i < count($roleNames); $i++) {
                if ($roleNames[$i]) {
                    $user['roles'][] = [
                        'id' => $roleIds[$i],
                        'name' => $roleNames[$i]
                    ];
                }
            }
            
            unset($user['role_names']);
            unset($user['role_ids']);
        }
        
        return $users;
    }
}
