<?php

namespace App\Models;

use App\Core\Model;

class Notification extends Model {
    protected string $table = 'notifications';
    
    public function getUnreadCount(int $userId): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE user_id = ? AND read_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
    
    public function getUnread(int $userId, int $limit = 10): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND read_at IS NULL
                ORDER BY created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function markAsRead(int $notificationId, int $userId): bool {
        $sql = "UPDATE {$this->table} 
                SET read_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND user_id = ? AND read_at IS NULL";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$notificationId, $userId]);
    }
    
    public function markAllAsRead(int $userId): bool {
        $sql = "UPDATE {$this->table} 
                SET read_at = CURRENT_TIMESTAMP 
                WHERE user_id = ? AND read_at IS NULL";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    public function createSystemNotification(array $userIds, string $type, string $message, ?array $data = null): void {
        $sql = "INSERT INTO {$this->table} 
                (user_id, type, message, data) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($userIds as $userId) {
            $stmt->execute([
                $userId,
                $type,
                $message,
                $data ? json_encode($data) : null
            ]);
        }
    }
    
    public function getNotificationPreferences(int $userId): array {
        $sql = "SELECT notification_preferences 
                FROM users 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $prefs = $stmt->fetchColumn();
        return $prefs ? json_decode($prefs, true) : $this->getDefaultPreferences();
    }
    
    public function updateNotificationPreferences(int $userId, array $preferences): bool {
        $sql = "UPDATE users 
                SET notification_preferences = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([json_encode($preferences), $userId]);
    }
    
    private function getDefaultPreferences(): array {
        return [
            'email' => [
                'new_message' => true,
                'volunteer_schedule' => true,
                'financial_report' => true,
                'system_updates' => true
            ],
            'browser' => [
                'new_message' => true,
                'volunteer_schedule' => true,
                'financial_report' => true,
                'system_updates' => true
            ]
        ];
    }
    
    public function cleanOldNotifications(int $daysToKeep = 30): int {
        $sql = "DELETE FROM {$this->table} 
                WHERE created_at < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL ? DAY)
                AND read_at IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$daysToKeep]);
        return $stmt->rowCount();
    }
}
