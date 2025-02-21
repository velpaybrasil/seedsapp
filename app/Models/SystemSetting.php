<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class SystemSetting extends Model {
    protected static string $table = 'system_settings';
    
    /**
     * Get a setting value by its category and key
     */
    public static function get(string $category, string $key, $default = null) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT value, value_type FROM " . self::$table . " WHERE category = ? AND key_name = ?");
        $stmt->execute([$category, $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return $default;
        }
        
        return self::castValue($result['value'], $result['value_type']);
    }
    
    /**
     * Set a setting value
     */
    public static function set(string $category, string $key, $value, string $description = '', bool $isPublic = false): bool {
        $db = self::getDB();
        $valueType = self::determineValueType($value);
        $serializedValue = self::serializeValue($value);
        $userId = $_SESSION['user_id'] ?? null;
        
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("INSERT INTO " . self::$table . " 
                (category, key_name, value, value_type, description, is_public, created_by, updated_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                value = VALUES(value),
                value_type = VALUES(value_type),
                description = VALUES(description),
                is_public = VALUES(is_public),
                updated_by = VALUES(updated_by)");
                
            $success = $stmt->execute([
                $category,
                $key,
                $serializedValue,
                $valueType,
                $description,
                $isPublic,
                $userId,
                $userId
            ]);
            
            $db->commit();
            return $success;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Get all settings by category
     */
    public static function getByCategory(string $category, bool $includePrivate = false): array {
        $db = self::getDB();
        $sql = "SELECT * FROM " . self::$table . " WHERE category = ?";
        if (!$includePrivate) {
            $sql .= " AND is_public = 1";
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$category]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function($row) {
            $row['value'] = self::castValue($row['value'], $row['value_type']);
            return $row;
        }, $results);
    }
    
    /**
     * Get all categories
     */
    public static function getAllCategories(): array {
        $db = self::getDB();
        $stmt = $db->query("SELECT DISTINCT category FROM " . self::$table . " ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Delete a setting
     */
    public static function deleteSetting(string $category, string $key): bool {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM " . self::$table . " WHERE category = ? AND key_name = ?");
        return $stmt->execute([$category, $key]);
    }
    
    /**
     * Determine the type of value being stored
     */
    private static function determineValueType($value): string {
        if (is_bool($value)) return 'boolean';
        if (is_int($value)) return 'integer';
        if (is_array($value)) return 'array';
        if (is_object($value)) return 'json';
        return 'string';
    }
    
    /**
     * Serialize value for storage
     */
    private static function serializeValue($value): string {
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_array($value) || is_object($value)) return json_encode($value);
        return (string)$value;
    }
    
    /**
     * Cast stored value to its proper type
     */
    private static function castValue(string $value, string $type) {
        switch ($type) {
            case 'boolean':
                return (bool)$value;
            case 'integer':
                return (int)$value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
