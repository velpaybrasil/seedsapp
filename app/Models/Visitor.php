<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;
use Exception;
use App\Core\Database\Database;

class Visitor extends Model {
    protected static string $table = 'visitors';
    protected static array $fillable = [
        'name',
        'birth_date',
        'marital_status',
        'phone',
        'whatsapp',
        'address',
        'number',
        'complement',
        'email',
        'neighborhood',
        'city',
        'zipcode',
        'gender',
        'first_visit_date',
        'how_knew_church',
        'prayer_requests',
        'observations',
        'photo',
        'status',
        'consent_date',
        'wants_group',
        'available_days',
        'group_id',
        'follow_up_date',
        'follow_up_notes',
        'follow_up_status'
    ];

    protected static array $validationRules = [
        'name' => 'min:3|max:255',
        'email' => 'email|unique:visitors,email,id',
        'phone' => 'unique:visitors,phone,id|regex:/^[1-9]{2}[0-9]{8,9}$/',
        'whatsapp' => 'regex:/^[1-9]{2}[0-9]{8,9}$/',
        'birth_date' => 'date',
        'first_visit_date' => 'date',
        'status' => 'in:not_contacted,contacted,forwarded_to_group,group_member,not_interested,wants_online_group,already_in_group',
        'wants_group' => 'in:yes,no',
        'zipcode' => 'regex:/^[0-9]{8}$/',
        'gender' => 'in:M,F,O',
        'follow_up_date' => 'date',
        'follow_up_status' => 'in:pending,completed,cancelled'
    ];

    protected static array $searchableFields = [
        'name', 'email', 'phone', 'whatsapp', 'address', 'neighborhood', 'city'
    ];

    public static function validate(array $data): array {
        $errors = [];
        $db = Database::getInstance()->getConnection();
        
        foreach (static::$validationRules as $field => $rules) {
            if (!isset($data[$field]) && strpos($rules, 'required') === false) {
                continue;
            }
            
            $rules = explode('|', $rules);
            foreach ($rules as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $ruleValue] = explode(':', $rule, 2);
                } else {
                    $ruleName = $rule;
                    $ruleValue = null;
                }
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($data[$field])) {
                            $errors[$field][] = "O campo {$field} é obrigatório.";
                        }
                        break;
                        
                    case 'email':
                        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "Email inválido.";
                        }
                        break;
                        
                    case 'min':
                        if (!empty($data[$field]) && strlen($data[$field]) < (int)$ruleValue) {
                            $errors[$field][] = "O campo deve ter no mínimo {$ruleValue} caracteres.";
                        }
                        break;
                        
                    case 'max':
                        if (!empty($data[$field]) && strlen($data[$field]) > (int)$ruleValue) {
                            $errors[$field][] = "O campo deve ter no máximo {$ruleValue} caracteres.";
                        }
                        break;
                        
                    case 'date':
                        if (!empty($data[$field])) {
                            $date = date_parse($data[$field]);
                            if ($date['error_count'] > 0) {
                                $errors[$field][] = "Data inválida.";
                            }
                        }
                        break;
                        
                    case 'in':
                        if (!empty($data[$field])) {
                            $allowedValues = explode(',', $ruleValue);
                            if (!in_array($data[$field], $allowedValues)) {
                                $errors[$field][] = "Valor inválido.";
                            }
                        }
                        break;
                        
                    case 'unique':
                        if (!empty($data[$field])) {
                            [$table, $column, $except] = explode(',', $ruleValue);
                            $query = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
                            $params = [$data[$field]];
                            
                            if (isset($data[$except])) {
                                $query .= " AND id != ?";
                                $params[] = $data[$except];
                            }
                            
                            try {
                                $stmt = $db->prepare($query);
                                $stmt->execute($params);
                                
                                if ($stmt->fetchColumn() > 0) {
                                    $errors[$field][] = "Este {$field} já está em uso.";
                                }
                            } catch (PDOException $e) {
                                error_log("Error in unique validation: " . $e->getMessage());
                                $errors[$field][] = "Erro ao validar unicidade do campo {$field}.";
                            }
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }

    public static function create($data)
    {
        $errors = static::validate($data);
        
        if (!empty($errors)) {
            throw new Exception(json_encode($errors));
        }
        
        $result = parent::create($data);
        return $result;
    }

    public static function update($id, $data)
    {
        $data['id'] = $id;
        $errors = static::validate($data);
        
        if (!empty($errors)) {
            throw new Exception(json_encode($errors));
        }
        
        return parent::update($id, $data);
    }

    public static function findByEmail(string $email)
    {
        try {
            $db = static::getDB();
            $sql = "SELECT * FROM " . static::$table . " WHERE email = :email LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding visitor by email: " . $e->getMessage());
            throw $e;
        }
    }

    public static function findByPhone(string $phone) {
        try {
            $db = static::getDB();
            $sql = "SELECT * FROM " . static::$table . " WHERE phone = :phone LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':phone' => $phone]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding visitor by phone: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getVisitsByGroup(int $groupId): array {
        try {
            $db = static::getDB();
            $sql = "SELECT v.*, gg.name as group_name 
                    FROM " . static::$table . " v
                    INNER JOIN growth_groups gg ON v.group_id = gg.id
                    WHERE v.group_id = :group_id
                    ORDER BY v.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([':group_id' => $groupId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting visits by group: " . $e->getMessage());
            throw $e;
        }
    }

    public static function addVisit(array $data): int {
        try {
            error_log("Adding new visit with data: " . print_r($data, true));
            
            // Verifica se o visitante já existe
            $visitor = null;
            if (!empty($data['email'])) {
                $visitor = self::findByEmail($data['email']);
            } elseif (!empty($data['phone'])) {
                $visitor = self::findByPhone($data['phone']);
            }

            if ($visitor) {
                // Atualiza o visitante existente
                error_log("Updating existing visitor ID: {$visitor['id']}");
                self::update($visitor['id'], $data);
                return $visitor['id'];
            } else {
                // Cria um novo visitante
                error_log("Creating new visitor");
                return self::create($data);
            }

        } catch (PDOException $e) {
            error_log("Error adding visit: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getRecentVisitors(int $limit = 5): array {
        try {
            $sql = "SELECT v.id, v.name, v.email, v.phone, v.created_at
                    FROM " . static::$table . " v
                    ORDER BY v.created_at DESC 
                    LIMIT :limit";
            
            $stmt = static::getDB()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting recent visitors: " . $e->getMessage());
            return [];
        }
    }

    public static function findWithFilters(array $filters, int $page = 1, int $perPage = 10, string $orderBy = 'created_at', string $direction = 'DESC'): array {
        try {
            $allowedFields = ['name', 'email', 'phone', 'created_at', 'first_visit_date', 'status'];
            $orderBy = in_array($orderBy, $allowedFields) ? $orderBy : 'created_at';
            $direction = in_array(strtoupper($direction), ['ASC', 'DESC']) ? strtoupper($direction) : 'DESC';

            $offset = ($page - 1) * $perPage;
            $conditions = [];
            $params = [];

            // Consulta base
            $query = "SELECT v.*, g.name as group_name 
                     FROM " . static::$table . " v 
                     LEFT JOIN growth_groups g ON v.group_id = g.id 
                     WHERE 1=1";

            if (!empty($filters['status'])) {
                $conditions[] = "v.status = :status";
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $searchConditions = [];
                foreach (static::$searchableFields as $field) {
                    $searchConditions[] = "v.{$field} LIKE :search";
                }
                $conditions[] = "(" . implode(" OR ", $searchConditions) . ")";
                $params[':search'] = "%{$filters['search']}%";
            }

            if (!empty($filters['date_start'])) {
                $conditions[] = "v.created_at >= :date_start";
                $params[':date_start'] = $filters['date_start'];
            }

            if (!empty($filters['date_end'])) {
                $conditions[] = "v.created_at <= :date_end";
                $params[':date_end'] = $filters['date_end'];
            }

            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY {$orderBy} {$direction} LIMIT :limit OFFSET :offset";
            $params[':limit'] = $perPage;
            $params[':offset'] = $offset;

            error_log("Query: " . $query);
            error_log("Params: " . json_encode($params));

            $stmt = static::getDB()->prepare($query);
            
            foreach ($params as $key => &$value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindParam($key, $value, $type);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Found " . count($results) . " visitors");
            return $results;

        } catch (PDOException $e) {
            error_log("Error in findWithFilters: " . $e->getMessage());
            error_log("Query: " . $query);
            error_log("Params: " . json_encode($params));
            throw new \Exception("Erro ao buscar visitantes: " . $e->getMessage());
        }
    }

    public static function countWithFilters(array $filters): int {
        try {
            $conditions = [];
            $params = [];
            $query = "SELECT COUNT(*) FROM " . static::$table . " WHERE 1=1";

            if (!empty($filters['status'])) {
                $conditions[] = "status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $searchTerm = "%{$filters['search']}%";
                $conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR whatsapp LIKE ?)";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }

            error_log("Count Query: $query");
            error_log("Count Params: " . print_r($params, true));

            $stmt = static::getDB()->prepare($query);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();

        } catch (PDOException $e) {
            error_log("Error in countWithFilters: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function countTodayVisitors(): int {
        try {
            $sql = "SELECT COUNT(*) as count FROM " . static::$table . " WHERE DATE(created_at) = CURDATE()";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            error_log('Erro ao contar visitantes do dia: ' . $e->getMessage());
            return 0;
        }
    }

    public static function countWeekVisitors(): int {
        try {
            $sql = "SELECT COUNT(*) as count FROM " . static::$table . " WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            error_log('Erro ao contar visitantes da semana: ' . $e->getMessage());
            return 0;
        }
    }

    public static function countThisWeek(): int {
        try {
            error_log("Counting visitors for this week");
            $sql = "SELECT COUNT(*) FROM " . static::$table . " WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            error_log("This week visitor count: {$count}");
            return (int) $count;
        } catch (PDOException $e) {
            error_log("Error counting this week visitors: " . $e->getMessage());
            throw $e;
        }
    }

    public static function countByPeriod(string $year, ?string $month = null): int {
        try {
            $conditions = [];
            $params = [];
            
            $sql = "SELECT COUNT(*) FROM " . static::$table . " WHERE YEAR(created_at) = :year";
            $params[':year'] = $year;
            
            if ($month !== null) {
                $sql .= " AND MONTH(created_at) = :month";
                $params[':month'] = $month;
            }
            
            $stmt = static::getDB()->prepare($sql);
            foreach ($params as $key => &$value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting visitors by period: " . $e->getMessage());
            return 0;
        }
    }

    public static function countByStatus($status): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE status = ?";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$status]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Error counting visitors by status: " . $e->getMessage());
            throw $e;
        }
    }

    public static function findByGroupId($groupId): array {
        try {
            error_log("Buscando visitantes do grupo ID: " . $groupId);
            
            $sql = "SELECT * FROM " . static::$table . " WHERE group_id = ? ORDER BY name ASC";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$groupId]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Encontrados " . count($results) . " visitantes no grupo");
            
            return $results;
        } catch (PDOException $e) {
            error_log("Erro ao buscar visitantes do grupo: " . $e->getMessage());
            return [];
        }
    }

    public static function findByGroup($groupId) {
        try {
            error_log("[Visitor] Buscando visitantes do grupo ID: " . $groupId);
            
            $sql = "SELECT v.* 
                    FROM " . static::$table . " v
                    INNER JOIN group_participants gp ON v.id = gp.visitor_id
                    WHERE gp.group_id = ? AND gp.status = 'active'
                    ORDER BY v.name ASC";
            
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("[Visitor] Encontrados " . count($result) . " visitantes");
            error_log("[Visitor] SQL: " . $sql);
            error_log("[Visitor] GroupId: " . $groupId);
            
            return $result;
        } catch (PDOException $e) {
            error_log("[Visitor] Erro ao buscar visitantes do grupo: " . $e->getMessage());
            error_log("[Visitor] Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public static function getGroupHistory(int $visitorId): array {
        try {
            $sql = "SELECT gh.*, g.name as group_name 
                    FROM group_history gh
                    LEFT JOIN groups g ON gh.group_id = g.id
                    WHERE gh.visitor_id = ?
                    ORDER BY gh.joined_at DESC";

            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$visitorId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[Visitor] Erro ao buscar histórico de grupos: " . $e->getMessage());
            return [];
        }
    }

    public static function findWithDetails($id) {
        $visitor = static::find($id);
        
        if (!$visitor) {
            return null;
        }
        
        // Buscar grupos do visitante
        $groups = static::getVisitorGroups($id);
        $visitor['groups'] = $groups;
        
        // Buscar formulários do visitante
        $forms = static::getVisitorForms($id);
        $visitor['forms'] = $forms;
        
        return $visitor;
    }
    
    /**
     * Busca os formulários associados ao visitante
     */
    public static function getVisitorForms($visitorId) {
        $sql = "SELECT f.* 
                FROM visitor_forms f
                INNER JOIN visitor_form_submissions s ON s.form_id = f.id
                WHERE s.visitor_id = :visitor_id
                GROUP BY f.id
                ORDER BY f.title ASC";
                
        $stmt = static::getDB()->prepare($sql);
        $stmt->execute(['visitor_id' => $visitorId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getVisitorGroups($visitorId) {
        try {
            $sql = "SELECT g.*, 
                           gh.joined_at,
                           gh.status,
                           gh.role
                    FROM growth_groups g
                    INNER JOIN group_history gh ON g.id = gh.group_id
                    WHERE gh.visitor_id = :visitor_id
                    ORDER BY gh.joined_at DESC";
                    
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['visitor_id' => $visitorId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[Visitor] Erro ao buscar grupos do visitante: " . $e->getMessage());
            return [];
        }
    }

    public static function findAll(array $conditions = [], array $orderBy = [], ?int $limit = null): array {
        $sql = "SELECT * FROM " . static::$table;

        $where = [];
        $values = [];

        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $values[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        if (!empty($orderBy)) {
            $orderClauses = [];
            foreach ($orderBy as $field => $direction) {
                $orderClauses[] = "{$field} {$direction}";
            }
            $sql .= " ORDER BY " . implode(", ", $orderClauses);
        }

        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $values[] = $limit;
        }

        try {
            $stmt = static::getDB()->prepare($sql);
            if (!$stmt->execute($values)) {
                error_log("Erro ao buscar visitantes: " . print_r($stmt->errorInfo(), true));
                return [];
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Exception ao buscar visitantes: " . $e->getMessage());
            return [];
        }
    }

    public static function countTotal(): int {
        try {
            $sql = "SELECT COUNT(*) FROM " . static::$table;
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting total visitors: " . $e->getMessage());
            throw $e;
        }
    }

    public static function countPendingFollowups(): int {
        // Temporariamente retornando 0 até a tabela follow_ups ser criada
        return 0;
    }

    public static function countActiveFollowups(): int {
        // Temporariamente retornando 0 até a tabela follow_ups ser criada
        return 0;
    }

    public static function countByDate(string $date): int {
        try {
            $sql = "SELECT COUNT(*) FROM " . static::$table . " WHERE DATE(created_at) = :date";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['date' => $date]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting visitors by date: " . $e->getMessage());
            throw $e;
        }
    }

    public static function findLatest(int $limit): array {
        try {
            $sql = "SELECT * FROM " . static::$table . " ORDER BY created_at DESC LIMIT :limit";
            $stmt = static::getDB()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding latest visitors: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getVisits(int $visitorId): array {
        try {
            $sql = "SELECT * FROM visitor_visits WHERE visitor_id = ? ORDER BY visit_date DESC";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$visitorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting visitor visits: " . $e->getMessage());
            return [];
        }
    }

    public static function addVisitRecord(int $visitorId, array $data): bool {
        try {
            $sql = "INSERT INTO visitor_visits (visitor_id, visit_date, notes) VALUES (?, ?, ?)";
            $stmt = static::getDB()->prepare($sql);
            return $stmt->execute([
                $visitorId,
                $data['visit_date'],
                $data['notes'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error adding visitor visit: " . $e->getMessage());
            return false;
        }
    }

    public static function getFollowUps(int $visitorId): array {
        try {
            $sql = "SELECT vcl.*, u.name as user_name 
                    FROM visitor_contact_logs vcl 
                    INNER JOIN users u ON u.id = vcl.user_id 
                    WHERE vcl.visitor_id = ? 
                    AND vcl.follow_up_date IS NOT NULL 
                    ORDER BY vcl.created_at DESC";
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$visitorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting visitor follow-ups: " . $e->getMessage());
            return [];
        }
    }

    public static function getStats(string $period = 'month'): array {
        try {
            $dateFilter = match($period) {
                'week' => 'DATE_SUB(CURDATE(), INTERVAL 7 DAY)',
                'month' => 'DATE_SUB(CURDATE(), INTERVAL 30 DAY)',
                'year' => 'DATE_SUB(CURDATE(), INTERVAL 1 YEAR)',
                default => 'DATE_SUB(CURDATE(), INTERVAL 30 DAY)'
            };

            $sql = "SELECT 
                    COUNT(*) as total_visitors,
                    SUM(CASE WHEN first_visit_date >= {$dateFilter} THEN 1 ELSE 0 END) as new_visitors,
                    SUM(CASE WHEN wants_group = 'yes' AND group_id IS NULL THEN 1 ELSE 0 END) as waiting_group,
                    SUM(CASE WHEN status = 'group_member' THEN 1 ELSE 0 END) as in_groups,
                    SUM(CASE WHEN status = 'not_contacted' THEN 1 ELSE 0 END) as not_contacted
                    FROM " . static::$table;

            $stmt = static::getDB()->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            error_log("[Visitor] Estatísticas obtidas: " . json_encode($stats));
            return $stats;
        } catch (PDOException $e) {
            error_log("[Visitor] Erro ao buscar estatísticas: " . $e->getMessage());
            return [
                'total_visitors' => 0,
                'new_visitors' => 0,
                'waiting_group' => 0,
                'in_groups' => 0,
                'not_contacted' => 0
            ];
        }
    }

    public static function getPendingFollowUps(): array {
        try {
            $sql = "SELECT v.id as visitor_id, v.name as visitor_name, v.phone, v.whatsapp,
                    f.id as followup_id, f.contact_date, f.contact_type, f.notes, f.next_contact
                    FROM visitor_follow_ups f
                    INNER JOIN " . static::$table . " v ON v.id = f.visitor_id
                    WHERE f.status = 'pending'
                    ORDER BY f.next_contact ASC, f.contact_date ASC
                    LIMIT 10";

            $stmt = static::getDB()->prepare($sql);
            $stmt->execute();
            $followUps = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            error_log("[Visitor] Follow-ups pendentes obtidos: " . count($followUps));
            return $followUps;
        } catch (PDOException $e) {
            error_log("[Visitor] Erro ao buscar follow-ups pendentes: " . $e->getMessage());
            return [];
        }
    }

    public static function getByGroup(int $groupId): array {
        $db = static::getDB();
        $sql = "SELECT v.* FROM " . static::$table . " v
                INNER JOIN group_members gm ON v.id = gm.visitor_id
                WHERE gm.group_id = :group_id
                ORDER BY v.name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':group_id' => $groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function search(string $term): array
    {
        $term = '%' . $term . '%';
        $sql = "SELECT * FROM " . static::$table . " WHERE name LIKE :term OR email LIKE :term OR phone LIKE :term";
        
        try {
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['term' => $term]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching visitors: " . $e->getMessage());
            return [];
        }
    }
}
