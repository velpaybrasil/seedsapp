<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Volunteer extends Model {
    protected $table;

    public function __construct() {
        parent::__construct();
        $this->table = 'volunteers';
    }

    public function getActiveCount(): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} v
                INNER JOIN users u ON v.user_id = u.id
                WHERE u.active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    public function getUpcomingSchedules(int $limit = 5): array {
        $sql = "SELECT s.*, v.ministry, u.name as volunteer_name
                FROM schedules s
                INNER JOIN {$this->table} v ON s.volunteer_id = v.id
                INNER JOIN users u ON v.user_id = u.id
                WHERE s.event_date >= CURRENT_DATE
                AND s.status IN ('scheduled', 'confirmed')
                ORDER BY s.event_date ASC, s.event_time ASC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getRecentSchedules(int $limit = 5): array {
        $sql = "SELECT s.*, v.ministry, u.name as volunteer_name
                FROM schedules s
                INNER JOIN {$this->table} v ON s.volunteer_id = v.id
                INNER JOIN users u ON v.user_id = u.id
                ORDER BY s.event_date DESC, s.event_time DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getStatsByPeriod(string $period): array {
        $interval = match($period) {
            'week' => 'INTERVAL 7 DAY',
            'month' => 'INTERVAL 30 DAY',
            'year' => 'INTERVAL 12 MONTH',
            default => 'INTERVAL 30 DAY'
        };
        
        $sql = "SELECT 
                    DATE(s.event_date) as date,
                    COUNT(DISTINCT v.id) as total_volunteers,
                    COUNT(DISTINCT CASE WHEN s.status = 'completed' THEN v.id END) as active_volunteers
                FROM {$this->table} v
                LEFT JOIN schedules s ON v.id = s.volunteer_id
                WHERE s.event_date >= DATE_SUB(CURRENT_DATE, {$interval})
                GROUP BY DATE(s.event_date)
                ORDER BY date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByMinistry(string $ministry): array {
        $sql = "SELECT v.*, u.name, u.email
                FROM {$this->table} v
                INNER JOIN users u ON v.user_id = u.id
                WHERE v.ministry = ?
                ORDER BY u.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ministry]);
        return $stmt->fetchAll();
    }
    
    public function getSchedulesByDateRange(string $startDate, string $endDate): array {
        $sql = "SELECT s.*, v.ministry, u.name as volunteer_name
                FROM schedules s
                INNER JOIN {$this->table} v ON s.volunteer_id = v.id
                INNER JOIN users u ON v.user_id = u.id
                WHERE s.event_date BETWEEN ? AND ?
                ORDER BY s.event_date ASC, s.event_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function createSchedule(array $data): int {
        $this->beginTransaction();
        
        try {
            // Check for conflicts
            $sql = "SELECT COUNT(*) FROM schedules 
                    WHERE volunteer_id = ? 
                    AND event_date = ? 
                    AND event_time = ?
                    AND status IN ('scheduled', 'confirmed')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['volunteer_id'],
                $data['event_date'],
                $data['event_time']
            ]);
            
            if ((int)$stmt->fetchColumn() > 0) {
                throw new \Exception('Já existe uma escala para este voluntário nesta data e horário.');
            }
            
            $scheduleId = parent::create($data);
            $this->commit();
            return $scheduleId;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
    
    public function updateScheduleStatus(int $scheduleId, string $status): bool {
        return $this->update($scheduleId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function getVolunteerSchedules(int $volunteerId, ?string $status = null): array {
        $sql = "SELECT s.*, v.ministry
                FROM schedules s
                INNER JOIN {$this->table} v ON s.volunteer_id = v.id
                WHERE s.volunteer_id = ?";
        
        $params = [$volunteerId];
        
        if ($status) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY s.event_date ASC, s.event_time ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getRecentVolunteers(int $limit = 5): array {
        $sql = "SELECT v.*, m.name as ministry_name 
                FROM {$this->table} v 
                LEFT JOIN ministries m ON v.ministry_id = m.id 
                ORDER BY v.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT v.*, m.name as ministry_name 
                FROM {$this->table} v 
                LEFT JOIN ministries m ON v.ministry_id = m.id 
                WHERE v.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countActive() {
        return $this->count(['status' => 'active']);
    }

    public function countInactive() {
        return $this->count(['status' => 'inactive']);
    }

    public function findByMinistryId($ministryId) {
        $sql = "SELECT v.*, m.name as ministry_name 
                FROM {$this->table} v 
                LEFT JOIN ministries m ON v.ministry_id = m.id 
                WHERE v.ministry_id = :ministry_id 
                ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ministry_id' => $ministryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVolunteersByPeriod($startDate, $endDate) {
        $sql = "SELECT v.*, m.name as ministry_name 
                FROM {$this->table} v 
                LEFT JOIN ministries m ON v.ministry_id = m.id 
                WHERE v.created_at BETWEEN :start_date AND :end_date 
                ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchedules($volunteerId, $limit = null) {
        $sql = "SELECT * FROM volunteer_schedules 
                WHERE volunteer_id = ? 
                ORDER BY schedule_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params = [$volunteerId, $limit];
        } else {
            $params = [$volunteerId];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSchedule($volunteerId, $data) {
        $sql = "INSERT INTO volunteer_schedules (
                    volunteer_id, schedule_date, ministry_id, 
                    start_time, end_time, notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $volunteerId,
            $data['schedule_date'],
            $data['ministry_id'],
            $data['start_time'],
            $data['end_time'],
            $data['notes'] ?? null
        ]);
    }

    public function updateSchedule($scheduleId, $data) {
        $updates = [];
        $params = [];
        
        if (isset($data['schedule_date'])) {
            $updates[] = "schedule_date = ?";
            $params[] = $data['schedule_date'];
        }
        
        if (isset($data['ministry_id'])) {
            $updates[] = "ministry_id = ?";
            $params[] = $data['ministry_id'];
        }
        
        if (isset($data['start_time'])) {
            $updates[] = "start_time = ?";
            $params[] = $data['start_time'];
        }
        
        if (isset($data['end_time'])) {
            $updates[] = "end_time = ?";
            $params[] = $data['end_time'];
        }
        
        if (isset($data['notes'])) {
            $updates[] = "notes = ?";
            $params[] = $data['notes'];
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $params[] = $scheduleId;
        
        $sql = "UPDATE volunteer_schedules 
                SET " . implode(", ", $updates) . ", updated_at = NOW() 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteSchedule($scheduleId) {
        $sql = "DELETE FROM volunteer_schedules WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$scheduleId]);
    }
}
