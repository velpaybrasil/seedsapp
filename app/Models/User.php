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

    public static function findByResetToken(string $token): ?array {
        try {
            $db = self::getDB();
            $sql = "SELECT * FROM " . static::$table . " 
                    WHERE reset_token = :token 
                    AND reset_token_expires > NOW() 
                    AND active = 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['token' => $token]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar usuário por token de reset: " . $e->getMessage());
            return null;
        }
    }

    public static function validateLogin(string $email, string $password): ?array {
        try {
            error_log("[User] Iniciando validação de login para: " . $email);
            
            $db = self::getDB();
            if (!$db) {
                error_log("[User] Erro: Conexão com banco de dados não disponível");
                return null;
            }
            
            // Busca o usuário pelo email
            $sql = "SELECT * FROM " . static::$table . " WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                error_log("[User] Usuário não encontrado: " . $email);
                return null;
            }

            error_log("[User] Usuário encontrado, verificando senha");
            error_log("[User] Hash armazenado: " . $user['password']);

            // Verifica a senha
            if (!password_verify($password, $user['password'])) {
                error_log("[User] Senha inválida para usuário: " . $email);
                self::incrementFailedAttempts($user['id']);
                return null;
            }

            error_log("[User] Senha válida, verificando status da conta");

            // Verifica se o usuário está ativo
            if (!$user['active']) {
                error_log("[User] Conta inativa: " . $email);
                return null;
            }

            // Verifica se a conta está bloqueada
            if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
                error_log("[User] Conta bloqueada até: " . $user['locked_until']);
                return null;
            }

            error_log("[User] Conta ativa e desbloqueada");

            // Verifica se a senha precisa ser atualizada
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                error_log("[User] Atualizando hash da senha");
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                self::update($user['id'], ['password' => $newHash]);
            }

            // Reseta as tentativas falhas
            self::resetFailedAttempts($user['id']);
            
            // Busca os papéis do usuário
            $user['roles'] = self::getUserRoles($user['id']) ?? [];
            
            error_log("[User] Login validado com sucesso para: " . $email);
            return $user;

        } catch (\PDOException $e) {
            error_log("[User] Erro ao validar login: " . $e->getMessage());
            return null;
        }
    }

    public static function updateLastLogin(int $id): bool {
        try {
            $db = self::getDB();
            $sql = "UPDATE " . static::$table . " 
                    SET last_login = NOW(), 
                        failed_login_attempts = 0, 
                        locked_until = NULL 
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("[User] Erro ao atualizar último login: " . $e->getMessage());
            return false;
        }
    }

    private static function incrementFailedAttempts(int $userId): void {
        try {
            $db = self::getDB();
            
            // Busca tentativas atuais
            $sql = "SELECT failed_login_attempts FROM " . static::$table . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $attempts = ($result['failed_login_attempts'] ?? 0) + 1;
            $lockUntil = null;

            // Define o tempo de bloqueio baseado no número de tentativas
            if ($attempts >= 5) {
                $lockUntil = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            } elseif ($attempts >= 3) {
                $lockUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            }

            // Atualiza o contador e o bloqueio
            $sql = "UPDATE " . static::$table . " SET 
                    failed_login_attempts = :attempts,
                    locked_until = :locked_until
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => $userId,
                'attempts' => $attempts,
                'locked_until' => $lockUntil
            ]);
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
            $params = [];
            
            // Base query
            $sql = "SELECT u.* FROM " . static::$table . " u";
            
            // Where conditions
            $where = [];
            if ($search) {
                $where[] = "(u.name LIKE :search OR u.email LIKE :search)";
                $params['search'] = "%{$search}%";
            }
            if ($status !== null) {
                $where[] = "u.active = :status";
                $params['status'] = $status;
            }
            
            // Add where clause if conditions exist
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            // Count total records
            $countSql = str_replace("u.*", "COUNT(*) as total", $sql);
            $stmt = $db->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Get paginated records
            $sql .= " ORDER BY u.created_at DESC LIMIT :offset, :limit";
            $stmt = $db->prepare($sql);
            
            // Bind all parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get roles for each user
            foreach ($users as &$user) {
                $user['roles'] = self::getUserRoles($user['id']) ?? [];
            }
            
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

    public static function getUserRole(int $userId): string {
        try {
            $db = self::getDB();
            $sql = "SELECT r.name as role_name 
                    FROM users u 
                    JOIN user_roles ur ON u.id = ur.user_id 
                    JOIN roles r ON ur.role_id = r.id 
                    WHERE u.id = :user_id 
                    ORDER BY r.priority DESC 
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result ? $result['role_name'] : 'user';
        } catch (\PDOException $e) {
            error_log("[User] Erro ao buscar papel do usuário: " . $e->getMessage());
            return 'user';
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

    public static function update($id, $data) {
        try {
            $db = self::getDB();

            // Se estiver atualizando o email, verifica se já existe
            if (isset($data['email'])) {
                $sql = "SELECT id FROM " . static::$table . " WHERE email = :email AND id != :id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['email' => $data['email'], 'id' => $id]);
                if ($stmt->fetch()) {
                    throw new \Exception("Email já cadastrado");
                }
            }

            // Se estiver atualizando a senha, faz o hash
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']);
            }

            // Remove campos não permitidos
            $allowedFields = array_intersect_key($data, array_flip(self::$fillable));
            
            if (empty($allowedFields)) {
                return true;
            }

            // Sanitiza os dados
            foreach ($allowedFields as $field => &$value) {
                if ($value === '') {
                    $value = null;
                } elseif (!is_null($value)) {
                    $value = trim(strip_tags($value));
                }
            }

            // Sempre atualiza o updated_at
            $allowedFields['updated_at'] = date('Y-m-d H:i:s');

            // Prepara a query
            $updates = array_map(function($field) {
                return "{$field} = :{$field}";
            }, array_keys($allowedFields));

            $sql = "UPDATE " . static::$table . " 
                    SET " . implode(', ', $updates) . " 
                    WHERE id = :id";

            $stmt = $db->prepare($sql);
            return $stmt->execute($allowedFields + ['id' => $id]);

        } catch (\PDOException $e) {
            error_log("[User] Erro ao atualizar usuário: " . $e->getMessage());
            throw $e;
        }
    }

    public static function create($data) {
        try {
            // Verifica se o email já existe
            if (self::findByEmail($data['email'])) {
                throw new \Exception("Email já cadastrado");
            }

            // Hash da senha
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            // Garante que campos obrigatórios estejam presentes
            $data['active'] = $data['active'] ?? 1;
            $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
            $data['updated_at'] = $data['updated_at'] ?? date('Y-m-d H:i:s');

            // Filtra apenas os campos permitidos
            $allowedFields = array_intersect_key($data, array_flip(self::$fillable));
            
            // Prepara a query
            $fields = array_keys($allowedFields);
            $placeholders = array_map(function($field) {
                return ":{$field}";
            }, $fields);

            $sql = "INSERT INTO " . static::$table . " 
                    (" . implode(', ', $fields) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";

            $stmt = self::getDB()->prepare($sql);
            $stmt->execute($allowedFields);

            $userId = (int)self::getDB()->lastInsertId();

            // Se houver roles para atribuir
            if (!empty($data['roles'])) {
                self::assignRoles($userId, (array)$data['roles']);
            }

            return $userId;
        } catch (\PDOException $e) {
            error_log("[User] Erro ao criar usuário: " . $e->getMessage());
            throw $e;
        }
    }
}
