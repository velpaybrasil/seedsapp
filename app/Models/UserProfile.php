<?php

namespace App\Models;

use App\Core\Model;

class UserProfile extends Model {
    protected static string $table = 'user_profiles';
    
    protected static array $fillable = [
        'user_id',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'birth_date',
        'bio',
        'avatar',
        'social_media',
        'preferences',
        'updated_at'
    ];
    
    public static function getProfile(int $userId): array {
        $db = self::getDB();
        $sql = "SELECT p.*, u.name, u.email, u.role, u.created_at as joined_date
                FROM " . self::$table . " p
                RIGHT JOIN users u ON p.user_id = u.id
                WHERE u.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
    
    public static function updateProfile(int $userId, array $data): bool {
        try {
            $db = self::getDB();
            
            // First, check if profile exists
            $sql = "SELECT id FROM " . self::$table . " WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId]);
            $profile = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Ensure updated_at is set
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            if ($profile) {
                // Update existing profile
                return self::update($profile['id'], $data);
            } else {
                // Create new profile
                $data['user_id'] = $userId;
                return (bool)self::create($data);
            }
        } catch (\Exception $e) {
            error_log("[UserProfile] Error updating profile: " . $e->getMessage());
            error_log("[UserProfile] Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    public static function updateAvatar(int $userId, string $avatarPath): bool {
        $sql = "UPDATE " . self::$table . " SET 
                avatar = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        return $stmt->execute([$avatarPath, $userId]);
    }
    
    public static function updatePreferences(int $userId, array $preferences): bool {
        $sql = "UPDATE " . self::$table . " SET 
                preferences = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        return $stmt->execute([json_encode($preferences), $userId]);
    }
    
    public static function getDefaultPreferences(): array {
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
    
    public static function getProfileStats(int $userId): array {
        // Get various statistics about the user's activity
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM messages WHERE sender_id = ? OR recipient_id = ?) as total_messages,
                    (SELECT COUNT(*) FROM notifications WHERE user_id = ?) as total_notifications,
                    (SELECT COUNT(*) FROM volunteer_schedules WHERE volunteer_id = ?) as total_schedules,
                    (SELECT COUNT(*) FROM financial_transactions WHERE created_by = ?) as total_transactions
                FROM dual";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public static function getRecentActivity(int $userId, int $limit = 10): array {
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
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function validatePassword(int $userId, string $currentPassword): bool {
        $sql = "SELECT password FROM users WHERE id = ?";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();
        
        return password_verify($currentPassword, $hash);
    }
    
    public static function updatePassword(int $userId, string $newPassword): bool {
        $sql = "UPDATE users SET 
                password = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
    }
    
    public static function updateEmail(int $userId, string $newEmail): bool {
        $sql = "UPDATE users SET 
                email = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        return $stmt->execute([$newEmail, $userId]);
    }
}
