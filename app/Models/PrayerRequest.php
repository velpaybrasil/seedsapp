<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class PrayerRequest extends Model
{
    protected static string $table = 'prayer_requests';
    protected static array $fillable = ['visitor_name', 'request', 'status'];

    public static function initialize()
    {
        try {
            error_log("[PrayerRequest] Starting initialization...");
            
            // Verificar e criar a tabela se necessário
            static::checkTable();
            
            // Criar um pedido de teste se a tabela estiver vazia
            static::createTestRequest();
            
            error_log("[PrayerRequest] Initialization completed successfully");
            return true;
        } catch (\Exception $e) {
            error_log("[PrayerRequest] Initialization failed: " . $e->getMessage());
            error_log("[PrayerRequest] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private static function checkTable(): void
    {
        try {
            error_log("[PrayerRequest] Checking if table exists...");
            $db = static::getDB();
            
            // Verificar se a conexão está funcionando
            try {
                $db->query("SELECT 1");
                error_log("[PrayerRequest] Database connection successful");
            } catch (\PDOException $e) {
                error_log("[PrayerRequest] Database connection failed: " . $e->getMessage());
                throw $e;
            }
            
            $stmt = $db->query("SHOW TABLES LIKE '" . static::$table . "'");
            $exists = $stmt->fetch(PDO::FETCH_NUM);
            error_log("[PrayerRequest] Table exists: " . ($exists ? 'yes' : 'no'));

            if (!$exists) {
                error_log("[PrayerRequest] Creating table...");
                $sql = "CREATE TABLE IF NOT EXISTS " . static::$table . " (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    visitor_name VARCHAR(255) NOT NULL,
                    request TEXT NOT NULL,
                    status ENUM('pending', 'praying', 'completed') NOT NULL DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                error_log("[PrayerRequest] SQL: " . $sql);
                $db->exec($sql);
                error_log("[PrayerRequest] Table created successfully");
            }
        } catch (\PDOException $e) {
            error_log("[PrayerRequest] Error checking/creating table: " . $e->getMessage());
            error_log("[PrayerRequest] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private static function createTestRequest(): void
    {
        try {
            error_log("[PrayerRequest] Checking if test request exists...");
            $db = static::getDB();
            $stmt = $db->query("SELECT COUNT(*) FROM " . static::$table);
            $count = $stmt->fetchColumn();
            error_log("[PrayerRequest] Found {$count} requests");

            if ($count == 0) {
                error_log("[PrayerRequest] Creating test request...");
                $sql = "INSERT INTO " . static::$table . " (visitor_name, request, status) 
                        VALUES (:name, :request, :status)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':name' => 'Visitante Teste',
                    ':request' => 'Este é um pedido de oração de teste.',
                    ':status' => 'pending'
                ]);
                error_log("[PrayerRequest] Test request created successfully");
            }
        } catch (\PDOException $e) {
            error_log("[PrayerRequest] Error creating test request: " . $e->getMessage());
            error_log("[PrayerRequest] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pendente',
            'praying' => 'Em Oração',
            'completed' => 'Concluído'
        ];
    }

    public static function getAllWithStatus($status = null)
    {
        try {
            error_log("[PrayerRequest] Getting requests with status: " . ($status ?? 'all'));
            $db = static::getDB();
            $sql = "SELECT * FROM " . static::$table;
            $params = [];

            if ($status) {
                $sql .= " WHERE status = :status";
                $params[':status'] = $status;
            }

            $sql .= " ORDER BY created_at DESC";
            error_log("[PrayerRequest] SQL: " . $sql);
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("[PrayerRequest] Found " . count($results) . " requests");
            return $results;
            
        } catch (\PDOException $e) {
            error_log("[PrayerRequest] Error getting prayer requests: " . $e->getMessage());
            error_log("[PrayerRequest] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function updateStatus($id, $status)
    {
        try {
            if (!in_array($status, ['pending', 'praying', 'completed'])) {
                throw new \Exception("Status inválido");
            }

            $db = static::getDB();
            $sql = "UPDATE " . static::$table . " SET status = :status WHERE id = :id";
            error_log("[PrayerRequest] SQL: " . $sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([':id' => $id, ':status' => $status]);
            error_log("[PrayerRequest] Update result: " . ($result ? 'success' : 'failure'));
            return $result;
        } catch (\PDOException $e) {
            error_log("[PrayerRequest] Error updating prayer request status: " . $e->getMessage());
            error_log("[PrayerRequest] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}
