<?php

namespace App\Models;

use App\Models\Model;

class VisitorFormSubmission extends Model
{
    protected static $table = 'visitor_form_submissions';
    
    public static function create($data)
    {
        $db = static::getDB();
        
        $sql = "INSERT INTO visitor_form_submissions (
                    form_id, 
                    visitor_id, 
                    data, 
                    ip_address, 
                    user_agent, 
                    created_at
                ) VALUES (
                    :form_id, 
                    :visitor_id, 
                    :data, 
                    :ip_address, 
                    :user_agent, 
                    NOW()
                )";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':form_id' => $data['form_id'],
            ':visitor_id' => $data['visitor_id'],
            ':data' => $data['data'],
            ':ip_address' => $data['ip_address'],
            ':user_agent' => $data['user_agent']
        ]);
        
        return $result ? $db->lastInsertId() : false;
    }
    
    public static function getByFormId($formId, $page = 1, $perPage = 20)
    {
        $db = static::getDB();
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT s.*, v.name as visitor_name, v.email as visitor_email 
                FROM visitor_form_submissions s 
                LEFT JOIN visitors v ON s.visitor_id = v.id 
                WHERE s.form_id = :form_id 
                ORDER BY s.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':form_id', $formId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function countByFormId($formId)
    {
        $db = static::getDB();
        
        $sql = "SELECT COUNT(*) FROM visitor_form_submissions WHERE form_id = :form_id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':form_id' => $formId]);
        
        return $stmt->fetchColumn();
    }
    
    public static function find($id)
    {
        $db = static::getDB();
        
        $sql = "SELECT s.*, v.name as visitor_name, v.email as visitor_email 
                FROM visitor_form_submissions s 
                LEFT JOIN visitors v ON s.visitor_id = v.id 
                WHERE s.id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function delete($id)
    {
        $db = static::getDB();
        
        $sql = "DELETE FROM visitor_form_submissions WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
