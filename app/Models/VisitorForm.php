<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class VisitorForm extends Model {
    protected static string $table = 'visitor_forms';
    protected static array $fillable = [
        'title',
        'slug',
        'description',
        'logo_url',
        'header_text',
        'footer_text',
        'theme_color',
        'active'
    ];

    protected static array $searchableFields = ['title', 'slug', 'description'];

    public static function findBySlug($slug)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM " . static::$table . " WHERE slug = :slug";
        $stmt = $db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = static::getDB();
        
        // Adicionar timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":{$field}";
        }, $fields);
        
        $sql = "INSERT INTO " . static::$table . " 
                (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $db->prepare($sql);
        
        $params = array_combine(
            array_map(function($field) {
                return ":{$field}";
            }, $fields),
            array_values($data)
        );
        
        $result = $stmt->execute($params);
        return $result ? $db->lastInsertId() : false;
    }

    public static function update($id, $data)
    {
        $db = static::getDB();
        
        // Adicionar timestamp de atualização
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($data);
        $set = array_map(function($field) {
            return "{$field} = :{$field}";
        }, $fields);
        
        $sql = "UPDATE " . static::$table . " 
                SET " . implode(', ', $set) . " 
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        
        $params = array_combine(
            array_map(function($field) {
                return ":{$field}";
            }, $fields),
            array_values($data)
        );
        $params[':id'] = $id;
        
        return $stmt->execute($params);
    }

    public static function delete($id)
    {
        $db = static::getDB();
        
        // Primeiro excluir os campos relacionados
        $sql = "DELETE FROM visitor_form_fields WHERE form_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Depois excluir o formulário
        $sql = "DELETE FROM " . static::$table . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public static function search($query)
    {
        $db = static::getDB();
        
        $conditions = [];
        $params = [];
        
        foreach (static::$searchableFields as $field) {
            $conditions[] = "{$field} LIKE :search_{$field}";
            $params[":search_{$field}"] = "%{$query}%";
        }
        
        $sql = "SELECT * FROM " . static::$table . " 
                WHERE " . implode(' OR ', $conditions) . " 
                ORDER BY created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFields(int $formId): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT * FROM visitor_form_fields 
                    WHERE form_id = ? 
                    ORDER BY `order` ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$formId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error getting form fields: " . $e->getMessage());
            return [];
        }
    }

    public static function countSubmissions(int $formId): int
    {
        try {
            $db = static::getDB();
            $sql = "SELECT COUNT(*) FROM visitor_form_submissions WHERE form_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$formId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error counting submissions: " . $e->getMessage());
            return 0;
        }
    }

    public static function getSubmissions(int $formId, int $limit, int $offset): array
    {
        try {
            $db = static::getDB();
            $sql = "SELECT * FROM visitor_form_submissions 
                    WHERE form_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$formId, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error getting submissions: " . $e->getMessage());
            return [];
        }
    }

    public static function addField(int $formId, array $data): bool
    {
        try {
            $db = static::getDB();
            
            // Get current max order
            $sql = "SELECT MAX(`order`) FROM visitor_form_fields WHERE form_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$formId]);
            $maxOrder = (int) $stmt->fetchColumn();
            
            // Set order for new field
            $data['order'] = $maxOrder + 1;
            $data['form_id'] = $formId;
            
            $fields = array_keys($data);
            $placeholders = array_map(function($field) {
                return ":{$field}";
            }, $fields);
            
            $sql = "INSERT INTO visitor_form_fields 
                    (" . implode(', ', $fields) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $db->prepare($sql);
            
            $params = array_combine(
                array_map(function($field) {
                    return ":{$field}";
                }, $fields),
                array_values($data)
            );
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error adding field: " . $e->getMessage());
            return false;
        }
    }

    public static function updateField(int $fieldId, array $data): bool
    {
        try {
            $db = static::getDB();
            
            $fields = array_keys($data);
            $set = array_map(function($field) {
                return "{$field} = :{$field}";
            }, $fields);
            
            $sql = "UPDATE visitor_form_fields 
                    SET " . implode(', ', $set) . " 
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            
            $params = array_combine(
                array_map(function($field) {
                    return ":{$field}";
                }, $fields),
                array_values($data)
            );
            $params[':id'] = $fieldId;
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error updating field: " . $e->getMessage());
            return false;
        }
    }

    public static function deleteField(int $fieldId): bool
    {
        try {
            $db = static::getDB();
            $sql = "DELETE FROM visitor_form_fields WHERE id = ?";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$fieldId]);
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error deleting field: " . $e->getMessage());
            return false;
        }
    }

    public static function paginate($page = 1, $perPage = 20)
    {
        try {
            $db = static::getDB();
            
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT * FROM " . static::$table . " 
                    ORDER BY created_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error paginating: " . $e->getMessage());
            return [];
        }
    }

    public static function count(): int
    {
        try {
            $db = static::getDB();
            $sql = "SELECT COUNT(*) FROM " . static::$table;
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("[VisitorForm] Error counting: " . $e->getMessage());
            return 0;
        }
    }
}
