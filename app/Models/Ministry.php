<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Core\Model;

class Ministry extends Model {
    protected static string $table = 'ministries';
    protected static array $fillable = [
        'name',
        'description',
        'active'
    ];

    public static function create($data)
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            $sql = "INSERT INTO ministries (
                name, description, active,
                created_at, updated_at
            ) VALUES (
                :name, :description, :active,
                NOW(), NOW()
            )";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'active' => $data['active'] ?? true
            ]);
            
            $ministryId = $db->lastInsertId();

            // Add leaders if provided
            if ($ministryId && !empty($data['leaders'])) {
                $sql = "INSERT INTO ministry_leaders (ministry_id, user_id, role) VALUES (:ministry_id, :user_id, :role)";
                $stmt = $db->prepare($sql);

                foreach ($data['leaders'] as $leader) {
                    $stmt->execute([
                        'ministry_id' => $ministryId,
                        'user_id' => $leader['user_id'],
                        'role' => $leader['role'] ?? 'leader'
                    ]);
                }
            }

            $db->commit();
            return $ministryId;

        } catch (PDOException $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Erro ao criar ministério: " . $e->getMessage());
            return false;
        }
    }

    public static function update($id, $data)
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            $updateFields = [];
            $params = ['id' => $id];

            foreach (['name', 'description', 'active'] as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $updateFields[] = "updated_at = NOW()";
            
            $sql = "UPDATE ministries SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($params);

            // Update leaders if provided
            if ($result && !empty($data['leaders'])) {
                // Remove existing leaders
                $sql = "DELETE FROM ministry_leaders WHERE ministry_id = :ministry_id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['ministry_id' => $id]);

                // Add new leaders
                $sql = "INSERT INTO ministry_leaders (ministry_id, user_id, role) VALUES (:ministry_id, :user_id, :role)";
                $stmt = $db->prepare($sql);

                foreach ($data['leaders'] as $leader) {
                    $stmt->execute([
                        'ministry_id' => $id,
                        'user_id' => $leader['user_id'],
                        'role' => $leader['role'] ?? 'leader'
                    ]);
                }
            }

            $db->commit();
            return true;

        } catch (PDOException $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Erro ao atualizar ministério: " . $e->getMessage());
            return false;
        }
    }

    public static function delete($id)
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // First delete related records from ministry_leaders
            $sql = "DELETE FROM ministry_leaders WHERE ministry_id = :id";
            $stmt = $db->prepare($sql);
            $result1 = $stmt->execute(['id' => $id]);

            // Then delete the ministry
            $sql = "DELETE FROM ministries WHERE id = :id";
            $stmt = $db->prepare($sql);
            $result2 = $stmt->execute(['id' => $id]);

            if ($result1 && $result2) {
                $db->commit();
                return true;
            }

            $db->rollBack();
            return false;

        } catch (PDOException $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Erro ao excluir ministério: " . $e->getMessage());
            return false;
        }
    }
    
    public static function find($id)
    {
        try {
            $sql = "SELECT m.*, u.name as leader_name,
                    COUNT(v.id) as volunteer_count
                    FROM ministries m
                    LEFT JOIN users u ON m.leader_id = u.id
                    LEFT JOIN volunteers v ON m.id = v.ministry_id AND v.active = 1
                    WHERE m.id = ?
                    GROUP BY m.id";
            
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar ministério: " . $e->getMessage());
            return null;
        }
    }
    
    public static function getAllWithFilters($filters = [])
    {
        try {
            error_log("[Ministry] Iniciando getAllWithFilters");
            $db = static::getDB();
            
            $where = ['1=1'];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = "m.active = :status";
                $params[':status'] = $filters['status'] === 'active' ? 1 : 0;
            }

            if (!empty($filters['search'])) {
                $where[] = "m.name LIKE :search";
                $params[':search'] = "%{$filters['search']}%";
            }

            // Primeiro buscar os ministérios
            $sql = "SELECT m.*
                    FROM ministries m
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY m.name ASC";

            error_log("[Ministry] Query ministérios: " . $sql);
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $ministries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Se encontrou ministérios, buscar os líderes
            if (!empty($ministries)) {
                foreach ($ministries as &$ministry) {
                    $sql = "SELECT u.name 
                           FROM ministry_leaders ml
                           JOIN users u ON ml.user_id = u.id
                           WHERE ml.ministry_id = :ministry_id";
                    
                    $stmt = $db->prepare($sql);
                    $stmt->execute([':ministry_id' => $ministry['id']]);
                    $leaders = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                    
                    $ministry['leaders'] = !empty($leaders) ? implode(', ', $leaders) : 'Sem líderes';
                    $ministry['total_groups'] = 0; // Por enquanto, sem contagem de grupos
                    $ministry['total_participants'] = 0; // Por enquanto, sem contagem de participantes
                }
            }
            
            error_log("[Ministry] Número de ministérios encontrados: " . count($ministries));
            
            return $ministries;
        } catch (\PDOException $e) {
            error_log("[Ministry] Erro ao buscar ministérios: " . $e->getMessage());
            error_log("[Ministry] Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }
    
    public static function all($conditions = [])
    {
        try {
            $db = static::getDB();
            $sql = "SELECT * FROM ministries WHERE active = 1";
            $params = [];

            if (!empty($conditions)) {
                foreach ($conditions as $field => $value) {
                    $sql .= " AND {$field} = :{$field}";
                    $params[$field] = $value;
                }
            }

            $sql .= " ORDER BY name ASC";

            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[Ministry] Erro ao buscar ministérios: " . $e->getMessage());
            return [];
        }
    }
    
    public static function addVolunteer($ministryId, $userId, $data)
    {
        try {
            $db = static::getDB();
            $sql = "INSERT INTO volunteers (
                ministry_id, user_id, role, availability,
                start_date, active, created_at, updated_at
            ) VALUES (
                :ministry_id, :user_id, :role, :availability,
                :start_date, :active, NOW(), NOW()
            )";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'ministry_id' => $ministryId,
                'user_id' => $userId,
                'role' => $data['role'],
                'availability' => $data['availability'] ?? null,
                'start_date' => $data['start_date'] ?? date('Y-m-d'),
                'active' => $data['active'] ?? true
            ]);
            
            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao adicionar voluntário: " . $e->getMessage());
            return null;
        }
    }
    
    public static function removeVolunteer($ministryId, $userId)
    {
        try {
            $db = static::getDB();
            $sql = "UPDATE volunteers 
                    SET active = 0, updated_at = NOW()
                    WHERE ministry_id = ? AND user_id = ?";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([$ministryId, $userId]);
        } catch (PDOException $e) {
            error_log("Erro ao remover voluntário: " . $e->getMessage());
            return false;
        }
    }
    
    public static function getVolunteers($ministryId)
    {
        try {
            $db = static::getDB();
            $sql = "SELECT v.*, u.name, u.email, u.phone
                    FROM volunteers v
                    INNER JOIN users u ON v.user_id = u.id
                    WHERE v.ministry_id = ? AND v.active = 1
                    ORDER BY u.name ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$ministryId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar voluntários: " . $e->getMessage());
            return [];
        }
    }
    
    public static function createSchedule($ministryId, $data)
    {
        try {
            $db = static::getDB();
            $sql = "INSERT INTO schedules (
                ministry_id, date, time, description,
                created_at, updated_at
            ) VALUES (
                :ministry_id, :date, :time, :description,
                NOW(), NOW()
            )";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'ministry_id' => $ministryId,
                'date' => $data['date'],
                'time' => $data['time'],
                'description' => $data['description'] ?? null
            ]);
            
            $scheduleId = $db->lastInsertId();

            if (!empty($data['volunteers'])) {
                $sql = "INSERT INTO schedule_volunteers (
                    schedule_id, volunteer_id, notes,
                    created_at, updated_at
                ) VALUES (
                    :schedule_id, :volunteer_id,
                    :notes, NOW(), NOW()
                )";
                
                $stmt = $db->prepare($sql);
                
                foreach ($data['volunteers'] as $volunteer) {
                    $stmt->execute([
                        'schedule_id' => $scheduleId,
                        'volunteer_id' => $volunteer['id'],
                        'notes' => $volunteer['notes'] ?? null
                    ]);
                }
            }

            return $scheduleId;
        } catch (PDOException $e) {
            error_log("Erro ao criar agenda: " . $e->getMessage());
            return null;
        }
    }
    
    public static function getSchedules($ministryId, $startDate, $endDate)
    {
        try {
            $sql = "SELECT s.*, 
                    GROUP_CONCAT(
                        CONCAT(v.id, ':', u.name, ':', sv.confirmed)
                        SEPARATOR '|'
                    ) as volunteers
                    FROM schedules s
                    LEFT JOIN schedule_volunteers sv ON s.id = sv.schedule_id
                    LEFT JOIN volunteers v ON sv.volunteer_id = v.id
                    LEFT JOIN users u ON v.user_id = u.id
                    WHERE s.ministry_id = ?
                    AND s.date BETWEEN ? AND ?
                    GROUP BY s.id
                    ORDER BY s.date ASC, s.time ASC";
            
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute([$ministryId, $startDate, $endDate]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar agendas: " . $e->getMessage());
            return [];
        }
    }
    
    public static function confirmVolunteer($scheduleId, $volunteerId, $confirmed = true)
    {
        try {
            $sql = "UPDATE schedule_volunteers 
                    SET confirmed = ?, confirmation_date = NOW(), updated_at = NOW()
                    WHERE schedule_id = ? AND volunteer_id = ?";
            
            $stmt = static::getDB()->prepare($sql);
            return $stmt->execute([$confirmed, $scheduleId, $volunteerId]);
        } catch (PDOException $e) {
            error_log("Erro ao confirmar voluntário: " . $e->getMessage());
            return false;
        }
    }

    public static function getLeaders($ministryId)
    {
        try {
            $sql = "SELECT ml.*, u.name, u.email 
                    FROM ministry_leaders ml 
                    INNER JOIN users u ON ml.user_id = u.id
                    WHERE ml.ministry_id = :ministry_id 
                    ORDER BY ml.role, u.name";
            
            $stmt = static::getDB()->prepare($sql);
            $stmt->execute(['ministry_id' => $ministryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar líderes: " . $e->getMessage());
            return [];
        }
    }

    public static function getWithDetails($id)
    {
        try {
            $db = static::getDB();
            $sql = "SELECT m.*, 
                    GROUP_CONCAT(DISTINCT CONCAT(l.user_id, ':', l.role, ':', u.name) SEPARATOR '|') as leaders,
                    COUNT(DISTINCT v.id) as volunteer_count
                    FROM ministries m
                    LEFT JOIN ministry_leaders l ON m.id = l.ministry_id
                    LEFT JOIN users u ON l.user_id = u.id
                    LEFT JOIN volunteers v ON m.id = v.ministry_id AND v.active = 1
                    WHERE m.id = :id
                    GROUP BY m.id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $ministry = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ministry) {
                return null;
            }

            // Process leaders data
            if ($ministry['leaders']) {
                $leadersData = [];
                foreach (explode('|', $ministry['leaders']) as $leaderInfo) {
                    list($userId, $role, $name) = explode(':', $leaderInfo);
                    $leadersData[] = [
                        'user_id' => $userId,
                        'role' => $role,
                        'name' => $name
                    ];
                }
                $ministry['leaders'] = $leadersData;
            } else {
                $ministry['leaders'] = [];
            }

            return $ministry;
        } catch (PDOException $e) {
            error_log("Erro ao buscar detalhes do ministério: " . $e->getMessage());
            return null;
        }
    }

    public static function addLeader($ministryId, $userId, $role = 'leader')
    {
        try {
            $db = static::getDB();
            $sql = "INSERT INTO ministry_leaders (ministry_id, user_id, role, created_at, updated_at)
                    VALUES (:ministry_id, :user_id, :role, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE role = :role, updated_at = NOW()";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'ministry_id' => $ministryId,
                'user_id' => $userId,
                'role' => $role
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao adicionar líder: " . $e->getMessage());
            return false;
        }
    }

    public static function removeLeader($ministryId, $userId)
    {
        try {
            $db = static::getDB();
            $sql = "DELETE FROM ministry_leaders 
                    WHERE ministry_id = :ministry_id AND user_id = :user_id";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'ministry_id' => $ministryId,
                'user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao remover líder: " . $e->getMessage());
            return false;
        }
    }

    public static function findAll(array $conditions = [], array $orderBy = []): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT * FROM " . static::$table;
            
            if (!empty($conditions)) {
                $where = [];
                foreach ($conditions as $field => $value) {
                    $where[] = "$field = :$field";
                }
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            if (!empty($orderBy)) {
                $order = [];
                foreach ($orderBy as $field => $direction) {
                    $order[] = "$field $direction";
                }
                $sql .= " ORDER BY " . implode(', ', $order);
            }
            
            $stmt = $db->prepare($sql);
            
            if (!empty($conditions)) {
                foreach ($conditions as $field => $value) {
                    $stmt->bindValue(":$field", $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in findAll: " . $e->getMessage());
            return [];
        }
    }

    public static function addSchedule(int $ministryId, array $data): bool
    {
        try {
            $db = static::getDB();
            $sql = "INSERT INTO ministry_schedules (
                ministry_id, event_date, event_time, description,
                volunteers_needed, notes, created_at, updated_at
            ) VALUES (
                :ministry_id, :event_date, :event_time, :description,
                :volunteers_needed, :notes, NOW(), NOW()
            )";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'ministry_id' => $ministryId,
                'event_date' => $data['event_date'],
                'event_time' => $data['event_time'],
                'description' => $data['description'] ?? null,
                'volunteers_needed' => $data['volunteers_needed'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error in addSchedule: " . $e->getMessage());
            return false;
        }
    }

    public static function removeSchedule(int $ministryId, int $scheduleId): bool
    {
        try {
            $db = static::getDB();
            $sql = "DELETE FROM ministry_schedules 
                   WHERE ministry_id = :ministry_id 
                   AND id = :schedule_id";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'ministry_id' => $ministryId,
                'schedule_id' => $scheduleId
            ]);
        } catch (PDOException $e) {
            error_log("Error in removeSchedule: " . $e->getMessage());
            return false;
        }
    }

    public static function assignVolunteer(int $ministryId, int $scheduleId, array $data): bool
    {
        try {
            $db = static::getDB();
            $sql = "INSERT INTO ministry_schedule_volunteers (
                ministry_id, schedule_id, volunteer_id,
                role, notes, created_at, updated_at
            ) VALUES (
                :ministry_id, :schedule_id, :volunteer_id,
                :role, :notes, NOW(), NOW()
            )";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'ministry_id' => $ministryId,
                'schedule_id' => $scheduleId,
                'volunteer_id' => $data['volunteer_id'],
                'role' => $data['role'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error in assignVolunteer: " . $e->getMessage());
            return false;
        }
    }

    public static function getStats(string $period = 'month'): array
    {
        try {
            $db = static::getDB();
            $dateFilter = match($period) {
                'week' => 'DATE_SUB(CURDATE(), INTERVAL 1 WEEK)',
                'year' => 'DATE_SUB(CURDATE(), INTERVAL 1 YEAR)',
                default => 'DATE_SUB(CURDATE(), INTERVAL 1 MONTH)'
            };

            $sql = "SELECT 
                COUNT(DISTINCT m.id) as total_ministries,
                COUNT(DISTINCT mv.volunteer_id) as total_volunteers,
                COUNT(DISTINCT ms.id) as total_schedules,
                (SELECT COUNT(*) FROM ministry_schedule_volunteers 
                 WHERE created_at >= $dateFilter) as new_assignments
                FROM ministries m
                LEFT JOIN ministry_volunteers mv ON m.id = mv.ministry_id
                LEFT JOIN ministry_schedules ms ON m.id = ms.ministry_id
                WHERE m.active = 1";

            $stmt = $db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getStats: " . $e->getMessage());
            return [];
        }
    }

    public static function getUpcomingSchedules(int $limit = 10): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT ms.*, m.name as ministry_name,
                   COUNT(msv.volunteer_id) as volunteers_assigned
                   FROM ministry_schedules ms
                   JOIN ministries m ON ms.ministry_id = m.id
                   LEFT JOIN ministry_schedule_volunteers msv 
                        ON ms.id = msv.schedule_id
                   WHERE ms.event_date >= CURDATE()
                   GROUP BY ms.id
                   ORDER BY ms.event_date ASC
                   LIMIT :limit";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUpcomingSchedules: " . $e->getMessage());
            return [];
        }
    }

    public static function getAll(): array {
        try {
            $db = static::getDB();
            $sql = "SELECT * FROM " . static::$table . " ORDER BY name ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[Ministry] Error getting all ministries: " . $e->getMessage());
            return [];
        }
    }
}
