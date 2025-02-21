<?php

namespace App\Core;

use App\Core\Database\Database;
use PDO;
use PDOException;
use Exception;

abstract class Model {
    protected static string $table;
    protected static array $fillable = [];

    protected static function getDB(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function find($id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM " . static::$table . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll()
    {
        $db = static::getDB();
        $sql = "SELECT * FROM " . static::$table . " ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        try {
            error_log("[Model] Creating new record in table " . static::$table);
            error_log("[Model] Data: " . print_r($data, true));
            
            $db = static::getDB();
            
            $fields = array_keys($data);
            $placeholders = array_map(function($field) {
                return ":{$field}";
            }, $fields);
            
            $sql = "INSERT INTO " . static::$table . " 
                    (" . implode(', ', $fields) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            error_log("[Model] SQL: " . $sql);
            $stmt = $db->prepare($sql);
            
            $params = array_combine(
                array_map(function($field) {
                    return ":{$field}";
                }, $fields),
                array_values($data)
            );
            
            error_log("[Model] Params: " . print_r($params, true));
            $result = $stmt->execute($params);
            
            if ($result) {
                $lastId = $db->lastInsertId();
                error_log("[Model] Record created successfully with ID: " . $lastId);
                return $lastId;
            } else {
                error_log("[Model] Failed to create record");
                return false;
            }
        } catch (\PDOException $e) {
            error_log("[Model] Database error creating record: " . $e->getMessage());
            error_log("[Model] Stack trace: " . $e->getTraceAsString());
            throw $e;
        } catch (\Exception $e) {
            error_log("[Model] Error creating record: " . $e->getMessage());
            error_log("[Model] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function update($id, $data)
    {
        try {
            error_log("[Model] Updating record in table " . static::$table);
            error_log("[Model] Data: " . print_r($data, true));
            
            $db = static::getDB();
            
            $fields = array_keys($data);
            $set = array_map(function($field) {
                return "{$field} = :{$field}";
            }, $fields);
            
            $sql = "UPDATE " . static::$table . " 
                    SET " . implode(', ', $set) . " 
                    WHERE id = :id";
            
            error_log("[Model] SQL: " . $sql);
            $stmt = $db->prepare($sql);
            
            // Criar array de parâmetros com os nomes corretos
            $params = array_combine(
                array_map(function($field) {
                    return ":{$field}";
                }, $fields),
                array_values($data)
            );
            $params[':id'] = $id;
            
            error_log("[Model] Params: " . print_r($params, true));
            $result = $stmt->execute($params);
            
            if ($result) {
                error_log("[Model] Record updated successfully");
                return true;
            } else {
                error_log("[Model] Failed to update record");
                return false;
            }
        } catch (\PDOException $e) {
            error_log("[Model] Database error updating record: " . $e->getMessage());
            error_log("[Model] Stack trace: " . $e->getTraceAsString());
            throw $e;
        } catch (\Exception $e) {
            error_log("[Model] Error updating record: " . $e->getMessage());
            error_log("[Model] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function delete($id)
    {
        try {
            error_log("[Model] Deleting record with ID {$id} from table " . static::$table);
            
            $db = static::getDB();
            $db->beginTransaction();
            
            $sql = "DELETE FROM " . static::$table . " WHERE id = :id";
            error_log("[Model] SQL: " . $sql);
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            if ($result) {
                $db->commit();
                error_log("[Model] Record deleted successfully");
                return true;
            } else {
                $db->rollBack();
                error_log("[Model] Failed to delete record");
                return false;
            }
        } catch (\PDOException $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("[Model] Database error deleting record: " . $e->getMessage());
            error_log("[Model] Stack trace: " . $e->getTraceAsString());
            throw $e;
        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("[Model] Error deleting record: " . $e->getMessage());
            error_log("[Model] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
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
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
