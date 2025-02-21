<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class VisitorFormField extends Model
{
    protected static string $table = 'visitor_form_fields';
    
    protected static array $fillable = [
        'form_id',
        'field_name',
        'field_label',
        'field_type',
        'is_required',
        'display_order',
        'placeholder',
        'help_text'
    ];

    public static function create($data)
    {
        $db = static::getDB();
        
        $sql = "INSERT INTO " . static::$table . " (
                    form_id, 
                    field_name, 
                    field_label, 
                    field_type, 
                    is_required, 
                    display_order, 
                    placeholder,
                    help_text,
                    created_at, 
                    updated_at
                ) VALUES (
                    :form_id, 
                    :field_name, 
                    :field_label, 
                    :field_type, 
                    :is_required, 
                    :display_order, 
                    :placeholder,
                    :help_text,
                    NOW(), 
                    NOW()
                )";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':form_id' => $data['form_id'],
            ':field_name' => $data['field_name'],
            ':field_label' => $data['field_label'],
            ':field_type' => $data['field_type'],
            ':is_required' => $data['is_required'],
            ':display_order' => $data['display_order'],
            ':placeholder' => $data['placeholder'] ?? '',
            ':help_text' => $data['help_text'] ?? ''
        ]);
        
        return $result ? $db->lastInsertId() : false;
    }
    
    public static function getByFormId($formId)
    {
        $db = static::getDB();
        
        $sql = "SELECT * FROM " . static::$table . " 
                WHERE form_id = :form_id 
                ORDER BY display_order ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':form_id' => $formId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function deleteByFormId($formId)
    {
        $db = static::getDB();
        
        $sql = "DELETE FROM " . static::$table . " WHERE form_id = :form_id";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([':form_id' => $formId]);
    }
    
    public static function find($id)
    {
        $db = static::getDB();
        
        $sql = "SELECT * FROM " . static::$table . " WHERE id = :id LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function update($id, $data)
    {
        $db = static::getDB();
        
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }
        
        $fields[] = "updated_at = NOW()";
        
        $sql = "UPDATE " . static::$table . " SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
}
