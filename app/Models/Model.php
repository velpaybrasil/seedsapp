<?php

namespace App\Models;

abstract class Model
{
    protected static $table;
    protected static $db;

    protected static function getDB()
    {
        if (!static::$db) {
            static::$db = \App\Core\Database::getInstance()->getConnection();
        }
        return static::$db;
    }

    public static function find($id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM " . static::$table . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getAll()
    {
        $db = static::getDB();
        $sql = "SELECT * FROM " . static::$table . " ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = static::getDB();
        
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
        $sql = "DELETE FROM " . static::$table . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public static function count()
    {
        $db = static::getDB();
        $sql = "SELECT COUNT(*) FROM " . static::$table;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function paginate($page = 1, $perPage = 20)
    {
        $db = static::getDB();
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM " . static::$table . " 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
