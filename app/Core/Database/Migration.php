<?php

namespace App\Core\Database;

abstract class Migration {
    protected $db;

    public function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (\PDOException $e) {
            error_log("[Migration] Erro de conexão com o banco: " . $e->getMessage());
            throw $e;
        }
    }

    abstract public function up(): void;
    abstract public function down(): void;

    protected function execute(string $sql): void {
        try {
            $this->db->exec($sql);
        } catch (\PDOException $e) {
            error_log("[Migration] Erro ao executar SQL: " . $e->getMessage());
            error_log("[Migration] SQL: " . $sql);
            throw $e;
        }
    }

    protected function query(string $sql): array {
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("[Migration] Erro ao executar query: " . $e->getMessage());
            error_log("[Migration] SQL: " . $sql);
            throw $e;
        }
    }
}
