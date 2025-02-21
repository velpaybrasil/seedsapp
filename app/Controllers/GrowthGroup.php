<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;
use App\Core\Model;

class GrowthGroup extends Model
{
    protected array $fillable = [
        'name',
        'description',
        'meeting_day',
        'meeting_time',
        'meeting_address',
        'neighborhood',
        'max_participants',
        'ministry_id',
        'latitude',
        'longitude'
    ];

    public function __construct()
    {
        $this->table = 'growth_groups';
        parent::__construct();
    }

    public function getParticipants(int $groupId): array
    {
        try {
            $sql = "SELECT 
                        u.*,
                        gp.join_date,
                        gp.status as participant_status,
                        COUNT(DISTINCT ga.id) as total_meetings,
                        SUM(CASE WHEN ga.present = 1 THEN 1 ELSE 0 END) as meetings_attended
                    FROM users u
                    INNER JOIN group_participants gp ON u.id = gp.user_id
                    LEFT JOIN group_meetings gm ON gm.group_id = gp.group_id
                    LEFT JOIN group_attendance ga ON ga.meeting_id = gm.id AND ga.participant_id = gp.id
                    WHERE gp.group_id = ? AND gp.status = 'active'
                    GROUP BY u.id, gp.id
                    ORDER BY u.name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$groupId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting participants: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public function hasParticipant(int $groupId, int $visitorId): bool {
        try {
            $sql = "SELECT COUNT(*) FROM group_participants 
                    WHERE group_id = ? AND user_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$groupId, $visitorId]);
            
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Erro ao verificar participante: " . $e->getMessage());
            return false;
        }
    }

    public function addParticipant(int $groupId, int $visitorId): bool {
        try {
            // Verificar se o grupo existe
            $group = $this->find($groupId);
            if (!$group) {
                throw new \Exception("Grupo não encontrado");
            }

            // Verificar se já atingiu o limite de participantes
            $currentParticipants = $this->getParticipants($groupId);
            if (count($currentParticipants) >= $group['max_participants']) {
                throw new \Exception("Grupo atingiu o limite de participantes");
            }

            // Verificar se o visitante já está no grupo
            if ($this->hasParticipant($groupId, $visitorId)) {
                throw new \Exception("Visitante já está no grupo");
            }

            // Adicionar o participante
            $sql = "INSERT INTO group_participants (group_id, user_id, join_date, status) 
                    VALUES (?, ?, CURDATE(), 'active')";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$groupId, $visitorId]);
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Erro ao adicionar participante: " . $e->getMessage());
            return false;
        }
    }

    public function removeParticipant(int $groupId, int $visitorId): bool {
        try {
            $sql = "UPDATE group_participants SET status = 'inactive' 
                    WHERE group_id = ? AND user_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$groupId, $visitorId]);
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Erro ao remover participante: " . $e->getMessage());
            return false;
        }
    }

    public function create(array $data): int 
    {
        try {
            $this->db->beginTransaction();

            // First create the group
            $sql = "INSERT INTO {$this->table} (
                name, description, meeting_day, meeting_time, 
                meeting_address, neighborhood, max_participants,
                status, ministry_id, latitude, longitude
            ) VALUES (
                :name, :description, :meeting_day, :meeting_time,
                :meeting_address, :neighborhood, :max_participants,
                :status, :ministry_id, :latitude, :longitude
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? null,
                ':meeting_day' => $data['meeting_day'],
                ':meeting_time' => $data['meeting_time'],
                ':meeting_address' => $data['meeting_address'],
                ':neighborhood' => $data['neighborhood'],
                ':max_participants' => $data['max_participants'] ?? 12,
                ':status' => $data['status'] ?? 'active',
                ':ministry_id' => $data['ministry_id'] ?? null,
                ':latitude' => $data['latitude'] ?? null,
                ':longitude' => $data['longitude'] ?? null
            ]);

            $groupId = $this->db->lastInsertId();

            // Then add leaders if provided
            if (!empty($data['leaders'])) {
                $leaderSql = "INSERT INTO group_leaders (group_id, user_id, role) VALUES (?, ?, ?)";
                $leaderStmt = $this->db->prepare($leaderSql);

                foreach ($data['leaders'] as $leader) {
                    $leaderStmt->execute([
                        $groupId,
                        $leader['user_id'],
                        $leader['role'] ?? 'leader'
                    ]);
                }
            }

            // Add participants if provided
            if (!empty($data['participants'])) {
                $participantSql = "INSERT INTO group_participants (group_id, user_id, join_date) VALUES (?, ?, CURDATE())";
                $participantStmt = $this->db->prepare($participantSql);

                foreach ($data['participants'] as $userId) {
                    $participantStmt->execute([$groupId, $userId]);
                }
            }

            $this->db->commit();
            return $groupId;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("[GrowthGroup] Error creating group: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        try {
            error_log("[GrowthGroup] Iniciando atualização do grupo ID: " . $id);
            error_log("[GrowthGroup] Dados recebidos: " . json_encode($data));

            $this->db->beginTransaction();

            // Separar os dados dos líderes
            $leaders = isset($data['leaders']) ? $data['leaders'] : [];
            unset($data['leaders']);

            // Atualizar dados básicos do grupo usando o método pai
            $updateData = array_intersect_key($data, array_flip($this->fillable));
            parent::update($id, $updateData);

            // Atualizar líderes
            $stmt = $this->db->prepare("DELETE FROM group_leaders WHERE group_id = ?");
            $stmt->execute([$id]);

            if (!empty($leaders)) {
                $stmt = $this->db->prepare("INSERT INTO group_leaders (group_id, user_id, role) VALUES (?, ?, ?)");
                foreach ($leaders as $leader) {
                    if (!empty($leader['user_id']) && !empty($leader['role'])) {
                        $stmt->execute([
                            $id,
                            $leader['user_id'],
                            $leader['role']
                        ]);
                    }
                }
            }

            $this->db->commit();
            error_log("[GrowthGroup] Atualização concluída com sucesso");
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("[GrowthGroup] Erro ao atualizar grupo: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function recordAttendance(int $groupId, string $meetingDate, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // Prepare meeting data
            $meetingData = [
                'group_id' => $groupId,
                'meeting_date' => $meetingDate,
                'topic' => isset($data['topic']) ? (string)$data['topic'] : null,
                'notes' => isset($data['meeting_notes']) ? (string)$data['meeting_notes'] : null
            ];

            // Create or get meeting record
            $sql = "INSERT INTO group_meetings (group_id, meeting_date, topic, notes)
                    VALUES (:group_id, :meeting_date, :topic, :notes)
                    ON DUPLICATE KEY UPDATE topic = VALUES(topic), notes = VALUES(notes)";
            
            $stmt = $this->db->prepare($sql);
            foreach ($meetingData as $key => $value) {
                $type = is_null($value) ? PDO::PARAM_NULL : (
                    $key === 'group_id' ? PDO::PARAM_INT : PDO::PARAM_STR
                );
                $stmt->bindValue(":$key", $value, $type);
            }
            $stmt->execute();

            // Get meeting ID
            if (!($meetingId = $this->db->lastInsertId())) {
                $stmt = $this->db->prepare(
                    "SELECT id FROM group_meetings WHERE group_id = :group_id AND meeting_date = :meeting_date"
                );
                $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
                $stmt->bindValue(':meeting_date', $meetingDate, PDO::PARAM_STR);
                $stmt->execute();
                $meetingId = (int)$stmt->fetchColumn();
            }

            // Record attendance
            if (!empty($data['attendance'])) {
                $sql = "INSERT INTO group_attendance (meeting_id, participant_id, present, notes)
                        VALUES (:meeting_id, :participant_id, :present, :notes)
                        ON DUPLICATE KEY UPDATE present = VALUES(present), notes = VALUES(notes)";
                
                $stmt = $this->db->prepare($sql);
                
                foreach ($data['attendance'] as $participantId => $attendance) {
                    $attendanceData = [
                        'meeting_id' => (int)$meetingId,
                        'participant_id' => (int)$participantId,
                        'present' => !empty($attendance['present']),
                        'notes' => isset($attendance['notes']) ? (string)$attendance['notes'] : null
                    ];

                    foreach ($attendanceData as $key => $value) {
                        $type = is_null($value) ? PDO::PARAM_NULL : (
                            in_array($key, ['meeting_id', 'participant_id']) ? PDO::PARAM_INT : (
                                $key === 'present' ? PDO::PARAM_BOOL : PDO::PARAM_STR
                            )
                        );
                        $stmt->bindValue(":$key", $value, $type);
                    }
                    $stmt->execute();
                }
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("[GrowthGroup] Error recording attendance: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getAttendanceStats(int $groupId, string $period = 'month'): array
    {
        try {
            $interval = match($period) {
                'week' => 'INTERVAL 7 DAY',
                'month' => 'INTERVAL 30 DAY',
                'year' => 'INTERVAL 12 MONTH',
                default => 'INTERVAL 30 DAY'
            };
            
            $sql = "SELECT 
                        m.meeting_date,
                        COUNT(DISTINCT p.id) as total_participants,
                        COUNT(DISTINCT CASE WHEN a.present = 1 THEN p.id END) as present_count,
                        ROUND(COUNT(DISTINCT CASE WHEN a.present = 1 THEN p.id END) * 100.0 / 
                              NULLIF(COUNT(DISTINCT p.id), 0), 2) as attendance_rate
                    FROM group_meetings m
                    LEFT JOIN group_attendance a ON m.id = a.meeting_id
                    LEFT JOIN group_participants p ON m.group_id = p.group_id 
                        AND p.status = 'active'
                        AND p.join_date <= m.meeting_date
                    WHERE m.group_id = ?
                    AND m.meeting_date >= DATE_SUB(CURRENT_DATE, {$interval})
                    GROUP BY m.meeting_date
                    ORDER BY m.meeting_date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$groupId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting attendance stats: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function findAll(array $conditions = [], array $orderBy = [], ?int $limit = null): array
    {
        try {
            $sql = "SELECT g.* FROM {$this->table} g";
            $params = [];
            
            if (!empty($conditions)) {
                $whereConditions = [];
                foreach ($conditions as $field => $value) {
                    if ($value === null) {
                        $whereConditions[] = "g.$field IS NULL";
                    } else {
                        $whereConditions[] = "g.$field = ?";
                        $params[] = $value;
                    }
                }
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            // Add ORDER BY
            if (!empty($orderBy)) {
                $orderClauses = [];
                foreach ($orderBy as $field => $direction) {
                    $orderClauses[] = "g.$field $direction";
                }
                $sql .= " ORDER BY " . implode(", ", $orderClauses);
            }

            // Add LIMIT
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error finding groups: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function delete($id): bool
    {
        try {
            $this->db->beginTransaction();

            // Primeiro, desativa todos os participantes
            $sql = "UPDATE group_participants SET status = 'inactive' WHERE group_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            // Depois, remove os líderes
            $sql = "DELETE FROM group_leaders WHERE group_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            // Por fim, marca o grupo como inativo
            $sql = "UPDATE {$this->table} SET status = 'inactive' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("[GrowthGroup] Error deleting group: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getNeighborhoodStats(): array
    {
        try {
            $sql = "SELECT 
                        g.neighborhood,
                        COUNT(DISTINCT g.id) as total_groups,
                        COUNT(DISTINCT p.user_id) as total_participants,
                        ROUND(AVG(
                            CASE 
                                WHEN g.max_participants > 0 
                                THEN (COUNT(DISTINCT p.user_id) * 100.0 / g.max_participants)
                                ELSE 0 
                            END
                        ), 2) as avg_capacity
                    FROM {$this->table} g
                    LEFT JOIN group_participants p ON g.id = p.group_id AND p.status = 'active'
                    WHERE g.status = 'active'
                    GROUP BY g.neighborhood
                    ORDER BY total_groups DESC, total_participants DESC";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting neighborhood stats: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getActiveCount(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'active'";
            return (int) $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting active count: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            return 0;
        }
    }

    public function getAverageAttendance($month, $year): float
    {
        try {
            $sql = "SELECT 
                        AVG(attendance_rate) as avg_attendance
                    FROM (
                        SELECT 
                            m.id,
                            COUNT(DISTINCT CASE WHEN a.present = 1 THEN p.id END) * 100.0 / 
                            NULLIF(COUNT(DISTINCT p.id), 0) as attendance_rate
                        FROM group_meetings m
                        LEFT JOIN group_attendance a ON m.id = a.meeting_id
                        LEFT JOIN group_participants p ON m.group_id = p.group_id 
                            AND p.status = 'active'
                            AND p.join_date <= m.meeting_date
                        WHERE MONTH(m.meeting_date) = ? AND YEAR(m.meeting_date) = ?
                        GROUP BY m.id
                    ) attendance_data";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$month, $year]);
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting average attendance: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            return 0.0;
        }
    }

    public function getGroupLeaders(int $groupId): array
    {
        try {
            // Temporariamente retorna array vazio até a tabela ser criada
            return [];

            /*
            $sql = "SELECT u.*, gl.role 
                   FROM users u
                   INNER JOIN group_leaders gl ON u.id = gl.user_id
                   WHERE gl.group_id = ?
                   ORDER BY gl.role DESC, u.name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$groupId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            */
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting group leaders: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }
}
