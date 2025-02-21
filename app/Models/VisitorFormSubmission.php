<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class VisitorFormSubmission extends Model {
    protected static string $table = 'visitor_form_submissions';
    protected static array $fillable = [
        'form_id',
        'visitor_id',
        'data',
        'ip_address',
        'user_agent'
    ];

    public static function getByFormId($formId, $page = 1, $perPage = 20)
    {
        $db = static::getDB();
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT s.*, v.name as visitor_name, v.email as visitor_email 
                FROM " . static::$table . " s 
                LEFT JOIN visitors v ON s.visitor_id = v.id 
                WHERE s.form_id = :form_id 
                ORDER BY s.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':form_id', $formId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function countByFormId($formId)
    {
        $db = static::getDB();
        
        $sql = "SELECT COUNT(*) FROM " . static::$table . " WHERE form_id = :form_id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':form_id' => $formId]);
        
        return $stmt->fetchColumn();
    }
    
    public static function find($id)
    {
        $db = static::getDB();
        
        $sql = "SELECT s.*, v.name as visitor_name, v.email as visitor_email 
                FROM " . static::$table . " s 
                LEFT JOIN visitors v ON s.visitor_id = v.id 
                WHERE s.id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
