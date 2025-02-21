<?php

namespace App;

class Database {
    private static ?\PDO $instance = null;
    
    private function __construct() {}
    
    public static function getInstance(): \PDO {
        if (self::$instance === null) {
            try {
                if (!defined('DB_HOST')) {
                    throw new \Exception("Database configuration not loaded");
                }
                
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                self::$instance = new \PDO($dsn, DB_USER, DB_PASS, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]);
            } catch (\PDOException $e) {
                throw new \Exception(sprintf(
                    "Connection failed: %s (Host: %s, Database: %s, User: %s)",
                    $e->getMessage(),
                    DB_HOST,
                    DB_NAME,
                    DB_USER
                ));
            } catch (\Exception $e) {
                throw new \Exception("Database error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
    
    public static function beginTransaction(): void {
        self::getInstance()->beginTransaction();
    }
    
    public static function commit(): void {
        self::getInstance()->commit();
    }
    
    public static function rollback(): void {
        self::getInstance()->rollback();
    }
}
