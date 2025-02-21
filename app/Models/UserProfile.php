<?php

namespace App\Models;

use App\Core\Model;

class UserProfile extends Model {
    protected static string $table = 'user_profiles';
    protected $db;
    
    public function __construct() {
        parent::__construct();
        $this->db = \App\Core\Database\Database::getInstance()->getConnection();
    }
    
    public function getProfile(int $userId): array {
        $sql = "SELECT p.*, u.name, u.email, u.role, u.created_at as joined_date
                FROM {$this->table} p
                RIGHT JOIN users u ON p.user_id = u.id
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: [];
    }
    
    public function updateProfile(int $userId, array $data): bool {
        // First, check if profile exists
        $sql = "SELECT id FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
        
        if ($profile) {
            // Update existing profile
            $sql = "UPDATE {$this->table} SET 
                    phone = ?,
                    address = ?,
                    city = ?,
                    state = ?,
                    postal_code = ?,
                    birth_date = ?,
                    bio = ?,
                    avatar = ?,
                    social_media = ?,
                    preferences = ?,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = ?";
        } else {
            // Create new profile
            $sql = "INSERT INTO {$this->table} (
                    user_id, phone, address, city, state, postal_code, 
                    birth_date, bio, avatar, social_media, preferences, 
                    created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                )";
        }
        
        $params = [
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['postal_code'] ?? null,
            $data['birth_date'] ?? null,
            $data['bio'] ?? null,
            $data['avatar'] ?? null,
            $data['social_media'] ? json_encode($data['social_media']) : null,
            $data['preferences'] ? json_encode($data['preferences']) : null
        ];
        
        if ($profile) {
            $params[] = $userId;
        } else {
            array_unshift($params, $userId);
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function updateAvatar(int $userId, string $avatarPath): bool {
        $sql = "UPDATE {$this->table} SET 
                avatar = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$avatarPath, $userId]);
    }
    
    public function updatePreferences(int $userId, array $preferences): bool {
        $sql = "UPDATE {$this->table} SET 
                preferences = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([json_encode($preferences), $userId]);
    }
    
    public function getDefaultPreferences(): array {
        return [
            'theme' => 'light',
            'sidebar_collapsed' => false,
            'notifications' => [
                'email' => true,
                'browser' => true
            ],
            'language' => 'pt_BR',
            'timezone' => 'America/Sao_Paulo',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i'
        ];
    }
    
    public function getProfileStats(int $userId): array {
        // Get various statistics about the user's activity
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM messages WHERE sender_id = ? OR recipient_id = ?) as total_messages,
                    (SELECT COUNT(*) FROM notifications WHERE user_id = ?) as total_notifications,
                    (SELECT COUNT(*) FROM volunteer_schedules WHERE volunteer_id = ?) as total_schedules,
                    (SELECT COUNT(*) FROM financial_transactions WHERE created_by = ?) as total_transactions
                FROM dual";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetch();
    }
    
    public function getRecentActivity(int $userId, int $limit = 10): array {
        $sql = "SELECT 'message' as type, 
                       id, 
                       subject as title, 
                       created_at,
                       NULL as related_id
                FROM messages 
                WHERE sender_id = ? OR recipient_id = ?
                
                UNION ALL
                
                SELECT 'notification' as type,
                       id,
                       message as title,
                       created_at,
                       NULL as related_id
                FROM notifications
                WHERE user_id = ?
                
                UNION ALL
                
                SELECT 'schedule' as type,
                       id,
                       activity as title,
                       created_at,
                       volunteer_id as related_id
                FROM volunteer_schedules
                WHERE volunteer_id = ?
                
                UNION ALL
                
                SELECT 'transaction' as type,
                       id,
                       CONCAT('R$ ', FORMAT(amount, 2)) as title,
                       created_at,
                       NULL as related_id
                FROM financial_transactions
                WHERE created_by = ?
                
                ORDER BY created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function validatePassword(int $userId, string $currentPassword): bool {
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();
        
        return password_verify($currentPassword, $hash);
    }
    
    public function updatePassword(int $userId, string $newPassword): bool {
        $sql = "UPDATE users SET 
                password = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
    }
    
    public function updateEmail(int $userId, string $newEmail): bool {
        $sql = "UPDATE users SET 
                email = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newEmail, $userId]);
    }
}
