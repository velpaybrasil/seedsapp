<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;
use App\Core\Model;

class GrowthGroup extends Model
{
    protected static string $table = 'growth_groups';
    protected static array $fillable = [
        'name',
        'description',
        'meeting_day',
        'meeting_time',
        'meeting_address',
        'neighborhood',
        'extra_neighborhoods',
        'max_participants',
        'ministry_id',
        'status'
    ];

    protected static array $validationRules = [
        'name' => 'required|min:3|max:255',
        'meeting_day' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
        'meeting_time' => 'required|date_format:H:i',
        'meeting_address' => 'required|min:5|max:255',
        'neighborhood' => 'required|min:2|max:100',
        'extra_neighborhoods' => 'nullable|max:1000',
        'max_participants' => 'nullable|integer|min:1',
        'ministry_id' => 'nullable|exists:ministries,id',
        'status' => 'required|in:active,inactive'
    ];

    public static function getParticipants($groupId): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT 
                        v.*,
                        ggp.join_date,
                        ggp.status as participant_status,
                        COUNT(DISTINCT gga.id) as total_meetings,
                        SUM(CASE WHEN gga.present = 1 THEN 1 ELSE 0 END) as meetings_attended
                    FROM visitors v
                    INNER JOIN growth_group_participants ggp ON v.id = ggp.visitor_id
                    LEFT JOIN growth_group_meetings ggm ON ggm.group_id = ggp.group_id
                    LEFT JOIN growth_group_attendance gga ON gga.meeting_id = ggm.id AND gga.participant_id = ggp.id
                    WHERE ggp.group_id = ? AND ggp.status = 'active'
                    GROUP BY v.id, ggp.id
                    ORDER BY v.name ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting participants: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public static function getMeetings($groupId): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT 
                        m.*,
                        COUNT(DISTINCT a.id) as total_attendees,
                        COUNT(DISTINCT p.id) as total_participants
                    FROM growth_group_meetings m
                    LEFT JOIN growth_group_attendance a ON a.meeting_id = m.id AND a.present = 1
                    LEFT JOIN growth_group_participants p ON p.group_id = m.group_id AND p.status = 'active'
                    WHERE m.group_id = ?
                    GROUP BY m.id
                    ORDER BY m.meeting_date DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting meetings: " . $e->getMessage());
            return [];
        }
    }

    public static function getAttendance($meetingId): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT 
                        v.id as visitor_id,
                        v.name as visitor_name,
                        gga.present,
                        gga.notes
                    FROM growth_group_participants ggp
                    INNER JOIN visitors v ON v.id = ggp.visitor_id
                    LEFT JOIN growth_group_attendance gga ON gga.participant_id = ggp.id AND gga.meeting_id = ?
                    WHERE ggp.group_id = (
                        SELECT group_id FROM growth_group_meetings WHERE id = ?
                    )
                    AND ggp.status = 'active'
                    ORDER BY v.name ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute([$meetingId, $meetingId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting attendance: " . $e->getMessage());
            return [];
        }
    }

    public static function addMeeting($groupId, $data): bool
    {
        try {
            $db = static::getDB();
            $sql = "INSERT INTO growth_group_meetings (group_id, meeting_date, topic, notes) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            return $stmt->execute([
                $groupId,
                $data['meeting_date'],
                $data['topic'] ?? null,
                $data['notes'] ?? null
            ]);

        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error adding meeting: " . $e->getMessage());
            return false;
        }
    }

    public static function updateAttendance($meetingId, $attendance): bool
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // Primeiro remove todos os registros existentes
            $sql = "DELETE FROM growth_group_attendance WHERE meeting_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$meetingId]);

            // Depois insere os novos registros
            $sql = "INSERT INTO growth_group_attendance (meeting_id, participant_id, present, notes) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            foreach ($attendance as $record) {
                $stmt->execute([
                    $meetingId,
                    $record['participant_id'],
                    $record['present'] ? 1 : 0,
                    $record['notes'] ?? null
                ]);
            }

            $db->commit();
            return true;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[GrowthGroup] Error updating attendance: " . $e->getMessage());
            return false;
        }
    }

    public static function hasParticipant($groupId, $visitorId): bool {
        try {
            $db = static::getDB();
            $sql = "SELECT COUNT(*) FROM growth_group_participants 
                    WHERE group_id = ? AND visitor_id = ? AND status = 'active'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId, $visitorId]);
            
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Erro ao verificar participante: " . $e->getMessage());
            return false;
        }
    }

    public static function addParticipant($groupId, $visitorId): bool {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // Verificar se o grupo existe
            $group = self::find($groupId);
            if (!$group) {
                throw new \Exception("Grupo não encontrado");
            }

            // Verificar se já atingiu o limite de participantes
            $currentParticipants = self::getParticipants($groupId);
            if (count($currentParticipants) >= $group['max_participants']) {
                throw new \Exception("Grupo atingiu o limite de participantes");
            }

            // Verificar se o visitante já está no grupo
            if (self::hasParticipant($groupId, $visitorId)) {
                throw new \Exception("Visitante já está no grupo");
            }

            // Adicionar o participante
            $sql = "INSERT INTO growth_group_participants (group_id, visitor_id, join_date, status) 
                    VALUES (?, ?, CURDATE(), 'active')";
            $stmt = $db->prepare($sql);
            
            $stmt->execute([$groupId, $visitorId]);

            $db->commit();
            return true;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[GrowthGroup] Erro ao adicionar participante: " . $e->getMessage());
            return false;
        }
    }

    public static function removeParticipant($groupId, $visitorId): bool {
        try {
            $db = static::getDB();
            $sql = "UPDATE growth_group_participants SET status = 'inactive' 
                    WHERE group_id = ? AND visitor_id = ?";
            $stmt = $db->prepare($sql);
            
            $stmt->execute([$groupId, $visitorId]);
            return true;
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Erro ao remover participante: " . $e->getMessage());
            return false;
        }
    }

    public static function create($data)
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // First create the group
            $sql = "INSERT INTO " . static::$table . " (
                name, description, meeting_day, meeting_time, 
                meeting_address, neighborhood, extra_neighborhoods, 
                max_participants, status, ministry_id
            ) VALUES (
                :name, :description, :meeting_day, :meeting_time,
                :meeting_address, :neighborhood, :extra_neighborhoods,
                :max_participants, :status, :ministry_id
            )";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? null,
                ':meeting_day' => $data['meeting_day'],
                ':meeting_time' => $data['meeting_time'],
                ':meeting_address' => $data['meeting_address'],
                ':neighborhood' => $data['neighborhood'],
                ':extra_neighborhoods' => $data['extra_neighborhoods'] ?? null,
                ':max_participants' => $data['max_participants'] ?? 12,
                ':status' => $data['status'] ?? 'active',
                ':ministry_id' => $data['ministry_id'] ?? null
            ]);

            $groupId = $db->lastInsertId();

            // Add leaders if provided
            if (!empty($data['leaders'])) {
                $leaderSql = "INSERT INTO growth_group_leaders (group_id, user_id, role) VALUES (?, ?, ?)";
                $leaderStmt = $db->prepare($leaderSql);

                foreach ($data['leaders'] as $leader) {
                    $leaderStmt->execute([$groupId, $leader['user_id'], $leader['role']]);
                }
            }

            $db->commit();
            return $groupId;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[GrowthGroup] Error creating group: " . $e->getMessage());
            throw $e;
        }
    }

    public static function update($id, $data)
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // Update group
            $sql = "UPDATE " . static::$table . " SET
                name = :name,
                description = :description,
                meeting_day = :meeting_day,
                meeting_time = :meeting_time,
                meeting_address = :meeting_address,
                neighborhood = :neighborhood,
                extra_neighborhoods = :extra_neighborhoods,
                max_participants = :max_participants,
                status = :status,
                ministry_id = :ministry_id
                WHERE id = :id";

            $stmt = $db->prepare($sql);
            $success = $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':description' => $data['description'] ?? null,
                ':meeting_day' => $data['meeting_day'],
                ':meeting_time' => $data['meeting_time'],
                ':meeting_address' => $data['meeting_address'],
                ':neighborhood' => $data['neighborhood'],
                ':extra_neighborhoods' => $data['extra_neighborhoods'] ?? null,
                ':max_participants' => $data['max_participants'] ?? null,
                ':status' => $data['status'] ?? 'active',
                ':ministry_id' => $data['ministry_id'] ?? null
            ]);

            if (!$success) {
                throw new PDOException("Failed to update group");
            }

            // Update leaders if provided
            if (isset($data['leaders'])) {
                // First remove all current leaders
                $deleteSql = "DELETE FROM growth_group_leaders WHERE group_id = ?";
                $deleteStmt = $db->prepare($deleteSql);
                $deleteStmt->execute([$id]);

                // Then add new leaders
                if (!empty($data['leaders'])) {
                    $leaderSql = "INSERT INTO growth_group_leaders (group_id, user_id, role) VALUES (?, ?, ?)";
                    $leaderStmt = $db->prepare($leaderSql);

                    foreach ($data['leaders'] as $leader) {
                        $leaderStmt->execute([$id, $leader['user_id'], $leader['role']]);
                    }
                }
            }

            $db->commit();
            return true;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[GrowthGroup] Error updating group: " . $e->getMessage());
            throw $e;
        }
    }

    public static function recordAttendance($groupId, $meetingDate, $data): bool
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // First delete any existing attendance for this date
            $sql = "DELETE FROM growth_group_attendance WHERE group_id = ? AND meeting_date = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId, $meetingDate]);

            // Then insert new attendance records
            $sql = "INSERT INTO growth_group_attendance (group_id, visitor_id, meeting_date, status, notes) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            foreach ($data as $attendance) {
                $stmt->execute([
                    $groupId,
                    $attendance['visitor_id'],
                    $meetingDate,
                    $attendance['status'],
                    $attendance['notes'] ?? null
                ]);
            }

            $db->commit();
            return true;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[GrowthGroup] Error recording attendance: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getAttendanceStats($groupId, $period = 'month'): array
    {
        try {
            $db = static::getDB();
            
            $dateFilter = match($period) {
                'week' => 'DATE_SUB(CURDATE(), INTERVAL 7 DAY)',
                'month' => 'DATE_SUB(CURDATE(), INTERVAL 30 DAY)',
                'year' => 'DATE_SUB(CURDATE(), INTERVAL 1 YEAR)',
                default => 'DATE_SUB(CURDATE(), INTERVAL 30 DAY)'
            };

            $sql = "SELECT 
                    COUNT(DISTINCT meeting_date) as total_meetings,
                    COUNT(DISTINCT visitor_id) as total_visitors,
                    AVG(CASE WHEN status = 'present' THEN 1 ELSE 0 END) * 100 as attendance_rate
                    FROM growth_group_attendance
                    WHERE group_id = ?
                    AND meeting_date >= {$dateFilter}";

            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting attendance stats: " . $e->getMessage());
            return [
                'total_meetings' => 0,
                'total_visitors' => 0,
                'attendance_rate' => 0
            ];
        }
    }

    public static function delete($id): bool
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // Delete related records first
            $tables = ['growth_group_participants', 'growth_group_leaders', 'growth_group_attendance'];
            foreach ($tables as $table) {
                $sql = "DELETE FROM {$table} WHERE group_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$id]);
            }

            // Then delete the group
            $sql = "DELETE FROM " . static::$table . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);

            $db->commit();
            return true;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log("[GrowthGroup] Error deleting group: " . $e->getMessage());
            error_log("[GrowthGroup] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function getNeighborhoodStats(): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT 
                        g.neighborhood,
                        COUNT(DISTINCT g.id) as total_groups,
                        COUNT(DISTINCT p.visitor_id) as total_participants,
                        ROUND(AVG(
                            CASE 
                                WHEN g.max_participants > 0 
                                THEN (COUNT(DISTINCT p.visitor_id) * 100.0 / g.max_participants)
                                ELSE 0 
                            END
                        ), 2) as avg_capacity
                    FROM " . static::$table . " g
                    LEFT JOIN growth_group_participants p ON g.id = p.group_id AND p.status = 'active'
                    WHERE g.status = 'active'
                    GROUP BY g.neighborhood
                    ORDER BY total_groups DESC, total_participants DESC";

            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting neighborhood stats: " . $e->getMessage());
            return [];
        }
    }

    public static function getActiveCount(): int
    {
        try {
            $db = static::getDB();
            $sql = "SELECT COUNT(*) FROM " . static::$table . " WHERE status = 'active'";
            return (int) $db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting active count: " . $e->getMessage());
            return 0;
        }
    }

    public static function getAverageAttendance($month, $year): float
    {
        try {
            $db = static::getDB();
            $sql = "SELECT 
                        AVG(attendance_rate) as avg_attendance
                    FROM (
                        SELECT 
                            m.id,
                            COUNT(DISTINCT CASE WHEN a.present = 1 THEN p.visitor_id END) * 100.0 / 
                            NULLIF(COUNT(DISTINCT p.visitor_id), 0) as attendance_rate
                        FROM growth_group_meetings m
                        LEFT JOIN growth_group_attendance a ON m.id = a.meeting_id
                        LEFT JOIN growth_group_participants p ON m.group_id = p.group_id 
                            AND p.status = 'active'
                            AND p.join_date <= m.meeting_date
                        WHERE MONTH(m.meeting_date) = ? AND YEAR(m.meeting_date) = ?
                        GROUP BY m.id
                    ) attendance_data";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$month, $year]);
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting average attendance: " . $e->getMessage());
            return 0.0;
        }
    }

    public static function getGroupLeaders($groupId): array
    {
        try {
            $db = static::getDB();
            
            // Primeiro verifica se a tabela existe
            $sql = "SHOW TABLES LIKE 'growth_group_leaders'";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                error_log("[GrowthGroup] Tabela growth_group_leaders não existe");
                return [];
            }

            // Se a tabela existe, busca os líderes
            $sql = "SELECT u.*, ggl.role 
                   FROM users u
                   INNER JOIN growth_group_leaders ggl ON u.id = ggl.user_id
                   WHERE ggl.group_id = ? AND ggl.status = 'active'
                   ORDER BY ggl.role DESC, u.name ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting group leaders: " . $e->getMessage());
            return [];
        }
    }

    public static function getMembers($groupId, $status = null): array
    {
        try {
            $db = static::getDB();
            
            $sql = "SELECT gm.*, u.name as user_name, u.email, u.phone 
                    FROM growth_group_members gm 
                    JOIN users u ON gm.user_id = u.id 
                    WHERE gm.group_id = ?";
            
            $params = [$groupId];
            
            if ($status) {
                $sql .= " AND gm.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY gm.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error getting group members: " . $e->getMessage());
            return [];
        }
    }

    public static function addMember($groupId, $userId, $role = 'member', $status = 'pending'): bool
    {
        try {
            $db = static::getDB();
            
            $sql = "INSERT INTO growth_group_members (group_id, user_id, role, status) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE role = VALUES(role), status = VALUES(status)";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([$groupId, $userId, $role, $status]);
            
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error adding group member: " . $e->getMessage());
            return false;
        }
    }

    public static function updateMemberStatus($groupId, $userId, $status): bool
    {
        try {
            $db = static::getDB();
            
            $sql = "UPDATE growth_group_members 
                    SET status = ?, 
                        joined_at = ? 
                    WHERE group_id = ? AND user_id = ?";
            
            $joinedAt = $status === 'approved' ? date('Y-m-d H:i:s') : null;
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([$status, $joinedAt, $groupId, $userId]);
            
        } catch (PDOException $e) {
            error_log("[GrowthGroup] Error updating member status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca todos os grupos com informações dos líderes
     * @return array Lista de grupos
     */
    public static function getAllGroups(): array {
        try {
            $sql = "
                SELECT g.*, 
                       m.name as ministry_name,
                       GROUP_CONCAT(DISTINCT CONCAT(u.name, ' (', CASE WHEN gl.role = 'leader' THEN 'Líder' ELSE 'Co-líder' END, ')') SEPARATOR ', ') as leaders
                FROM " . static::$table . " g
                LEFT JOIN ministries m ON g.ministry_id = m.id
                LEFT JOIN growth_group_leaders gl ON g.id = gl.group_id
                LEFT JOIN users u ON gl.user_id = u.id
                GROUP BY g.id
                ORDER BY g.name ASC
            ";
            
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao buscar todos os grupos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca grupos recentes com informações dos líderes
     * @param int $limit Limite de registros
     * @return array Lista de grupos recentes
     */
    public static function getRecentGroups(int $limit = 5): array {
        try {
            $sql = "
                SELECT g.*, 
                       m.name as ministry_name,
                       GROUP_CONCAT(DISTINCT CONCAT(u.name, ' (', CASE WHEN gl.role = 'leader' THEN 'Líder' ELSE 'Co-líder' END, ')') SEPARATOR ', ') as leaders
                FROM " . static::$table . " g
                LEFT JOIN ministries m ON g.ministry_id = m.id
                LEFT JOIN growth_group_leaders gl ON g.id = gl.group_id
                LEFT JOIN users u ON gl.user_id = u.id
                GROUP BY g.id
                ORDER BY g.created_at DESC
                LIMIT :limit
            ";
            
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao buscar grupos recentes: " . $e->getMessage());
            return [];
        }
    }

    public static function getAllActive(): array
    {
        try {
            $sql = "
                SELECT g.*, 
                       m.name as ministry_name,
                       GROUP_CONCAT(DISTINCT CONCAT(u.name, ' (', CASE WHEN gl.role = 'leader' THEN 'Líder' ELSE 'Co-líder' END, ')') SEPARATOR ', ') as leaders
                FROM " . static::$table . " g
                LEFT JOIN ministries m ON g.ministry_id = m.id
                LEFT JOIN growth_group_leaders gl ON g.id = gl.group_id
                LEFT JOIN users u ON gl.user_id = u.id
                WHERE g.status = 'active'
                GROUP BY g.id
                ORDER BY g.name ASC
            ";
            
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao buscar grupos ativos: " . $e->getMessage());
            return [];
        }
    }

    public static function getGroupMembers($groupId): array {
        try {
            $db = self::getDB();
            $sql = "SELECT gm.*, u.name, u.email, u.phone, u.role as user_role
                    FROM group_members gm
                    INNER JOIN users u ON gm.user_id = u.id
                    WHERE gm.group_id = ? AND gm.status = 'approved'
                    ORDER BY u.name";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao buscar membros do grupo: " . $e->getMessage());
            return [];
        }
    }

    public static function isVisitorInAnyGroup($visitorId): bool {
        try {
            $db = self::getDB();
            $sql = "SELECT COUNT(*) FROM group_members WHERE user_id = ? AND status = 'approved'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$visitorId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao verificar visitante em grupos: " . $e->getMessage());
            return false;
        }
    }

    public static function isMemberInAnyGroup($memberId): bool {
        try {
            $db = self::getDB();
            $sql = "SELECT COUNT(*) FROM group_members WHERE user_id = ? AND status = 'approved'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$memberId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao verificar membro em grupos: " . $e->getMessage());
            return false;
        }
    }

    public static function addVisitorToGroup($groupId, $visitorId): bool {
        try {
            $db = self::getDB();
            $db->beginTransaction();

            $sql = "INSERT INTO group_members (
                        group_id, user_id, status, role, 
                        joined_at, created_at, updated_at
                    ) VALUES (
                        ?, ?, 'approved', 'member',
                        NOW(), NOW(), NOW()
                    )";
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$groupId, $visitorId]);

            if ($result) {
                // Atualizar o status do visitante
                $sql = "UPDATE visitors SET 
                        group_id = ?, 
                        status = 'active',
                        updated_at = NOW() 
                        WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$groupId, $visitorId]);
                
                $db->commit();
                return true;
            }

            $db->rollBack();
            return false;
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao adicionar visitante ao grupo: " . $e->getMessage());
            if (isset($db)) {
                $db->rollBack();
            }
            return false;
        }
    }

    public static function addMemberToGroup($groupId, $memberId): bool {
        try {
            $db = self::getDB();
            $sql = "INSERT INTO group_members (
                        group_id, user_id, status, role, 
                        joined_at, created_at, updated_at
                    ) VALUES (
                        ?, ?, 'approved', 'member',
                        NOW(), NOW(), NOW()
                    )";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([$groupId, $memberId]);
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao adicionar membro ao grupo: " . $e->getMessage());
            return false;
        }
    }

    public static function getGroupMeetings($groupId): array {
        try {
            $db = self::getDB();
            $sql = "SELECT m.*, 
                    (SELECT COUNT(*) FROM meeting_attendance ma WHERE ma.meeting_id = m.id) as total_attendees,
                    (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id = m.group_id AND gm.status = 'approved') as total_participants
                    FROM group_meetings m
                    WHERE m.group_id = ?
                    ORDER BY m.meeting_date DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GrowthGroup] Erro ao buscar reuniões do grupo: " . $e->getMessage());
            return [];
        }
    }
}
