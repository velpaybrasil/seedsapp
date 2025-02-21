<?php

namespace App\Models;

use App\Models\Model;

class VisitorForm extends Model
{
    protected static $table = 'visitor_forms';
    
    public static function getAll()
    {
        $db = static::getDB();
        $sql = "SELECT * FROM visitor_forms ORDER BY created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function find($id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM visitor_forms WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public static function create($data)
    {
        $db = static::getDB();
        
        $sql = "INSERT INTO visitor_forms (title, slug, description, theme_color, active, created_at, updated_at) 
                VALUES (:title, :slug, :description, :theme_color, :active, NOW(), NOW())";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':description' => $data['description'],
            ':theme_color' => $data['theme_color'],
            ':active' => $data['active']
        ]);
        
        return $result ? $db->lastInsertId() : false;
    }
    
    public static function update($id, $data)
    {
        $db = static::getDB();
        
        $fields = [];
        $params = [':id' => $id];
        
        // Construir a query dinamicamente baseada nos campos fornecidos
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }
        
        $fields[] = "updated_at = NOW()";
        
        $sql = "UPDATE visitor_forms SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public static function delete($id)
    {
        $db = static::getDB();
        $sql = "DELETE FROM visitor_forms WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public static function findBySlug($slug)
    {
        $db = static::getDB();
        
        $sql = "SELECT * FROM visitor_forms WHERE slug = :slug";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public static function getActiveForm($id)
    {
        $db = static::getDB();
        
        $sql = "SELECT * FROM visitor_forms WHERE id = :id AND active = 1 LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
