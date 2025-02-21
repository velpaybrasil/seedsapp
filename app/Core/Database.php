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
                    $dbConfig['driver'],
                    $dbConfig['host'],
                    $dbConfig['database'],
                    $dbConfig['charset']
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

                error_log("[Database] Connection established successfully");

            } catch (PDOException $e) {
                error_log("[Database] Failed to connect to database: " . $e->getMessage());
                error_log("[Database] Stack trace: " . $e->getTraceAsString());
                throw $e;
            }
        }

        return self::$connection;
    }

    public static function fetch(string $sql, array $params = []): ?array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log('Database fetch error: ' . $e->getMessage());
            throw new PDOException('Database fetch failed: ' . $e->getMessage());
        }
    }

    public static function fetchAll(string $sql, array $params = []): array {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Database fetchAll error: ' . $e->getMessage());
            throw new PDOException('Database fetchAll failed: ' . $e->getMessage());
        }
    }

    public static function execute(string $sql, array $params = []): bool {
        try {
            $stmt = self::getConnection()->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Database execute error: ' . $e->getMessage());
            throw new PDOException('Database execute failed: ' . $e->getMessage());
        }
    }

    public static function count(string $sql, array $params = []): int {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Database count error: ' . $e->getMessage());
            throw new PDOException('Database count failed: ' . $e->getMessage());
        }
    }

    public static function lastInsertId(): string {
        return self::getConnection()->lastInsertId();
    }

    public static function beginTransaction(): bool {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool {
        return self::getConnection()->rollBack();
    }

    public static function quote(string $string): string {
        return self::getConnection()->quote($string);
    }
}
