<?php

namespace App\Core\Database;

use App\Core\Env;

class Database {
    private static $instance = null;
    private static $connection;

    private function __construct() {
        try {
            error_log("[Database] Iniciando conexão...");
            
            $envFile = __DIR__ . '/../../../.env';
            error_log("[Database] Procurando arquivo .env em: " . $envFile);
            
            if (!file_exists($envFile)) {
                throw new \Exception("Arquivo .env não encontrado em: " . $envFile);
            }
            
            error_log("[Database] Arquivo .env encontrado");
            
            $host = Env::get('DB_HOST');
            $dbname = Env::get('DB_NAME');
            $user = Env::get('DB_USER');
            $pass = Env::get('DB_PASS');

            error_log("[Database] Configurações carregadas:");
            error_log("[Database] Host: " . ($host ?? 'não definido'));
            error_log("[Database] Database: " . ($dbname ?? 'não definido'));
            error_log("[Database] User: " . ($user ?? 'não definido'));

            if (!$host || !$dbname || !$user || !$pass) {
                throw new \Exception("Configurações do banco de dados não encontradas no arquivo .env");
            }

            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            error_log("[Database] Tentando conectar ao banco de dados...");
            error_log("[Database] DSN: {$dsn}");

            self::$connection = new \PDO($dsn, $user, $pass, $options);
            error_log("[Database] Conexão estabelecida com sucesso");
        } catch (\Exception $e) {
            error_log("[Database] Erro de conexão: " . $e->getMessage());
            error_log("[Database] Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = self::$connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            error_log("[Database] Erro na query: " . $e->getMessage());
            error_log("[Database] SQL: " . $sql);
            error_log("[Database] Params: " . json_encode($params));
            throw $e;
        }
    }

    public static function getConnection(): \PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$connection;
    }

    // Prevenir clonagem do objeto
    private function __clone() {}

    // Prevenir unserialize
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
