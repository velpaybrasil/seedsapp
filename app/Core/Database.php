<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                error_log("[Database] Initializing database connection...");

                // Load database configuration
                $configPath = __DIR__ . '/../../config/database.php';
                if (!file_exists($configPath)) {
                    error_log("[Database] Database configuration file not found: {$configPath}");
                    throw new PDOException('Database configuration file not found');
                }

                $config = require $configPath;
                if (!isset($config['default']) || !is_array($config['default'])) {
                    error_log("[Database] Invalid database configuration");
                    throw new PDOException('Invalid database configuration');
                }

                $dbConfig = $config['default'];

                // Validate required configuration
                $required = ['host', 'database', 'username', 'password'];
                foreach ($required as $field) {
                    if (empty($dbConfig[$field])) {
                        error_log("[Database] Missing required database configuration: {$field}");
                        throw new PDOException("Missing required database configuration: {$field}");
                    }
                }

                // Build DSN
                $dsn = sprintf(
                    '%s:host=%s;dbname=%s;charset=%s',
                    $dbConfig['driver'] ?? 'mysql',
                    $dbConfig['host'],
                    $dbConfig['database'],
                    $dbConfig['charset'] ?? 'utf8mb4'
                );

                error_log("[Database] Connecting to database: {$dsn}");

                // Create connection with error mode set
                self::$connection = new PDO(
                    $dsn,
                    $dbConfig['username'],
                    $dbConfig['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]
                );

                error_log("[Database] Database connection established successfully");
            } catch (PDOException $e) {
                error_log("[Database] Connection error: " . $e->getMessage());
                throw $e;
            }
        }

        return self::$connection;
    }

    public static function beginTransaction(): bool {
        try {
            return self::getConnection()->beginTransaction();
        } catch (PDOException $e) {
            error_log("[Database] Error starting transaction: " . $e->getMessage());
            return false;
        }
    }

    public static function commit(): bool {
        try {
            return self::getConnection()->commit();
        } catch (PDOException $e) {
            error_log("[Database] Error committing transaction: " . $e->getMessage());
            return false;
        }
    }

    public static function rollBack(): bool {
        try {
            return self::getConnection()->rollBack();
        } catch (PDOException $e) {
            error_log("[Database] Error rolling back transaction: " . $e->getMessage());
            return false;
        }
    }

    public static function execute(string $sql, array $params = []): bool {
        try {
            $stmt = self::getConnection()->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("[Database] Error executing query: " . $e->getMessage());
            throw $e;
        }
    }

    public static function fetch(string $sql, array $params = []): ?array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("[Database] Error fetching row: " . $e->getMessage());
            throw $e;
        }
    }

    public static function fetchAll(string $sql, array $params = []): array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("[Database] Error fetching all rows: " . $e->getMessage());
            throw $e;
        }
    }

    public static function lastInsertId(): string {
        return self::getConnection()->lastInsertId();
    }
}
