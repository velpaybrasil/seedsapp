<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class User extends Model {
    protected static string $table = 'users';
    protected static array $fillable = [
        'name',
        'email',
        'password',
        'active',
        'reset_token',
        'reset_token_expiry',
        'last_login',
        'created_at',
        'updated_at'
    ];

    // Métodos específicos da classe User que não existem na classe base
    public static function findByEmail(string $email): ?array {
        try {
            $db = self::getDB();
            $sql = "SELECT * FROM " . static::$table . " WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    public static function validateLogin(string $email, string $password): ?array {
        try {
            $db = self::getDB();
            $sql = "SELECT * FROM " . static::$table . " WHERE email = :email AND active = 1";
            $stmt = $db->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                return null;
            }

            if (!password_verify($password, $user['password'])) {
                self::incrementFailedAttempts($user['id']);
                return null;
            }

            self::resetFailedAttempts($user['id']);
            self::updateLastLogin($user['id']);
            
            return $user;
        } catch (\PDOException $e) {
            error_log("[User] Erro ao validar login: " . $e->getMessage());
            return null;
        }
    }

    private static function incrementFailedAttempts(int $userId): void {
        try {
            $db = self::getDB();
            $sql = "UPDATE " . static::$table . " SET 
                    failed_login_attempts = failed_login_attempts + 1,
                    locked_until = CASE 
                        WHEN failed_login_attempts >= 4 THEN DATE_ADD(NOW(), INTERVAL 30 MINUTE)
                        ELSE NULL 
                    END
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $userId]);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao incrementar tentativas falhas: " . $e->getMessage());
        }
    }

    private static function resetFailedAttempts(int $userId): void {
        try {
            $db = self::getDB();
            $sql = "UPDATE " . static::$table . " SET 
                    failed_login_attempts = 0,
                    locked_until = NULL
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $userId]);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao resetar tentativas falhas: " . $e->getMessage());
        }
    }

    public static function updateLastLogin(int $id): bool {
        try {
            $db = self::getDB();
            $sql = "UPDATE " . static::$table . " SET last_login = NOW() WHERE id = :id";
            $stmt = $db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao atualizar último login: " . $e->getMessage());
            return false;
        }
    }

    public static function getNameById($userId): string {
        try {
            if (!$userId) return 'Não definido';
            
            $db = self::getDB();
            $sql = "SELECT name FROM " . static::$table . " WHERE id = :userId";
            $stmt = $db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result['name'] ?? 'Não definido';
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar nome do usuário: " . $e->getMessage());
            return 'Erro ao buscar nome';
        }
    }

    public static function getLeaders(): array {
        try {
            $db = self::getDB();
            
            $sql = "SELECT DISTINCT u.* 
                    FROM " . static::$table . " u 
                    INNER JOIN user_roles ur ON u.id = ur.user_id 
                    INNER JOIN roles r ON ur.role_id = r.id 
                    WHERE r.name IN ('leader', 'admin') 
                    AND u.active = 1 
                    ORDER BY u.name";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar líderes: " . $e->getMessage());
            return [];
        }
    }

    public static function getMembersWithoutGroup(): array {
        try {
            $db = self::getDB();
            
            $sql = "SELECT u.* 
                    FROM " . static::$table . " u 
                    LEFT JOIN group_members gm ON u.id = gm.user_id 
                    WHERE gm.id IS NULL 
                    AND u.active = 1 
                    ORDER BY u.name";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar membros sem grupo: " . $e->getMessage());
            return [];
        }
    }

    public static function updateSettings($userId, $data) {
        try {
            $columns = self::getTableColumns();
            $hasTheme = in_array('theme', $columns);
            $hasNotifications = in_array('notifications_enabled', $columns);
            $hasEmailNotifications = in_array('email_notifications', $columns);
            
            $updates = [];
            $params = ['id' => $userId];
            
            if ($hasTheme) {
                $updates[] = "theme = :theme";
                $params['theme'] = $data['theme'];
            }
            
            if ($hasNotifications) {
                $updates[] = "notifications_enabled = :notifications_enabled";
                $params['notifications_enabled'] = $data['notifications_enabled'];
            }
            
            if ($hasEmailNotifications) {
                $updates[] = "email_notifications = :email_notifications";
                $params['email_notifications'] = $data['email_notifications'];
            }
            
            if (empty($updates)) {
                return true; 
            }
            
            $updates[] = "updated_at = CURRENT_TIMESTAMP";
            
            $sql = "UPDATE " . static::$table . " SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = self::getDB()->prepare($sql);
            
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log('Erro ao atualizar configurações do usuário: ' . $e->getMessage());
            return false;
        }
    }
    
    private static function getTableColumns() {
        try {
            $sql = "SHOW COLUMNS FROM " . static::$table;
            $stmt = self::getDB()->query($sql);
            $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            return array_map('strtolower', $columns);
        } catch (\PDOException $e) {
            error_log('Erro ao obter colunas da tabela ' . static::$table . ': ' . $e->getMessage());
            return [];
        }
    }

    public static function getAllWithRoles(): array {
        try {
            $db = self::getDB();
            
            $sql = "SELECT u.*, GROUP_CONCAT(r.name) as role_names, GROUP_CONCAT(r.id) as role_ids 
                    FROM " . static::$table . " u 
                    LEFT JOIN user_roles ur ON u.id = ur.user_id 
                    LEFT JOIN roles r ON ur.role_id = r.id 
                    GROUP BY u.id 
                    ORDER BY u.name";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
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
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar usuários com papéis: " . $e->getMessage());
            return [];
        }
    }

    public static function getUserRoles(int $userId): array {
        try {
            $db = self::getDB();
            
            $sql = "SELECT r.* 
                    FROM roles r
                    INNER JOIN user_roles ur ON r.id = ur.role_id
                    WHERE ur.user_id = :user_id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar papéis do usuário: " . $e->getMessage());
            return [];
        }
    }

    public static function assignRoles(int $userId, array $roleIds): bool {
        try {
            $db = self::getDB();
            
            $sql = "DELETE FROM user_roles WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            if (!empty($roleIds)) {
                $values = implode(',', array_fill(0, count($roleIds), '(?, ?)'));
                $params = [];
                foreach ($roleIds as $roleId) {
                    $params[] = $userId;
                    $params[] = $roleId;
                }

                $sql = "INSERT INTO user_roles (user_id, role_id) VALUES {$values}";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            }

            return true;
        } catch (\PDOException $e) {
            error_log("[User] Erro ao atribuir papéis ao usuário: " . $e->getMessage());
            return false;
        }
    }

    public static function updateRoles(int $userId, array $roleIds): bool {
        return self::assignRoles($userId, $roleIds);
    }

    public static function getUserPermissions(int $userId): array {
        try {
            $db = self::getDB();
            
            $sql = "SELECT DISTINCT p.id, p.name, p.slug, p.module_id
                    FROM permissions p
                    JOIN role_permissions rp ON p.id = rp.permission_id
                    JOIN user_roles ur ON rp.role_id = ur.role_id
                    WHERE ur.user_id = :user_id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar permissões do usuário: " . $e->getMessage());
            return [];
        }
    }

    public static function requirePasswordChange(int $userId, bool $require = true): bool {
        try {
            $db = self::getDB();
            
            $sql = "UPDATE " . static::$table . " SET must_change_password = :require WHERE id = :id";
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'id' => $userId,
                'require' => $require
            ]);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao requerer troca de senha: " . $e->getMessage());
            return false;
        }
    }

    public static function mustChangePassword(int $userId): bool {
        try {
            $db = self::getDB();
            
            $sql = "SELECT must_change_password FROM " . static::$table . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result ? (bool)$result['must_change_password'] : false;
        } catch (\PDOException $e) {
            error_log("[User] Erro ao verificar se deve trocar senha: " . $e->getMessage());
            return false;
        }
    }

    public static function getAccessibleModules(int $userId): array {
        try {
            $db = self::getDB();
            
            $sql = "SELECT DISTINCT sm.* 
                    FROM system_modules sm
                    JOIN user_permissions up ON sm.id = up.module_id
                    WHERE up.user_id = :user_id 
                    AND up.can_view = 1 
                    AND sm.active = 1
                    ORDER BY sm.order_index ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar módulos acessíveis: " . $e->getMessage());
            return [];
        }
    }

    public static function getPermissions(int $userId): array {
        $permissionModel = new UserPermission();
        return $permissionModel->getUserPermissions($userId);
    }

    public static function hasPermission(int $userId, string $moduleSlug, string $permission = 'view'): bool {
        $permissionModel = new UserPermission();
        return $permissionModel->hasPermission($userId, $moduleSlug, $permission);
    }

    public static function setPermissions(int $userId, array $modulePermissions): bool {
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

    /**
     * Conta o número de usuários ativos no sistema
     * @return int Número de usuários ativos
     */
    public static function countActiveUsers(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE active = 1";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (\PDOException $e) {
            error_log("[User] Erro ao contar usuários ativos: " . $e->getMessage());
            throw new \Exception('Erro ao contar usuários ativos');
        }
    }

    /**
     * Busca usuários recentes com suas funções
     * @param int $limit Limite de registros
     * @return array Lista de usuários recentes
     */
    public static function getRecentUsers(int $limit = 5): array
    {
        try {
            $sql = "
                SELECT u.*, GROUP_CONCAT(r.name) as roles
                FROM " . static::$table . " u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                GROUP BY u.id
                ORDER BY u.created_at DESC
                LIMIT :limit
            ";
            
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar usuários recentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca usuários com suas funções
     * @param string|null $search Termo de busca
     * @param string|null $status Status do usuário
     * @param int $page Página atual
     * @param int $perPage Registros por página
     * @return array Lista de usuários e total de registros
     */
    public static function getUsersWithRoles(?string $search = null, ?string $status = null, int $page = 1, int $perPage = 10): array {
        try {
            $db = self::getDB();
            $offset = ($page - 1) * $perPage;
            
            $where = [];
            $params = [];
            
            if ($search) {
                $where[] = "(u.name LIKE :search OR u.email LIKE :search)";
                $params['search'] = "%{$search}%";
            }
            
            if ($status !== null) {
                $where[] = "u.active = :status";
                $params['status'] = $status === 'active' ? 1 : 0;
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            // Count total records
            $countSql = "SELECT COUNT(DISTINCT u.id) as total 
                        FROM " . static::$table . " u 
                        LEFT JOIN user_roles ur ON u.id = ur.user_id 
                        LEFT JOIN roles r ON ur.role_id = r.id 
                        {$whereClause}";
            
            $stmt = $db->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Get paginated records
            $sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles 
                   FROM " . static::$table . " u 
                   LEFT JOIN user_roles ur ON u.id = ur.user_id 
                   LEFT JOIN roles r ON ur.role_id = r.id 
                   {$whereClause} 
                   GROUP BY u.id 
                   ORDER BY u.created_at DESC 
                   LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return [
                'users' => $users,
                'total' => $total
            ];
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar usuários com papéis: " . $e->getMessage());
            return [
                'users' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Busca um usuário com suas funções
     * @param int $id ID do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public static function getUserWithRoles(int $id): ?array
    {
        try {
            $sql = "
                SELECT u.*, GROUP_CONCAT(r.name) as roles
                FROM " . static::$table . " u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.id = :id
                GROUP BY u.id
            ";
            
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar usuário: " . $e->getMessage());
            return null;
        }
    }

    public static function removeAllRoles(int $userId): bool {
        try {
            $db = self::getDB();
            $sql = "DELETE FROM user_roles WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);
            return $stmt->execute(['user_id' => $userId]);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao remover papéis do usuário: " . $e->getMessage());
            return false;
        }
    }
}
