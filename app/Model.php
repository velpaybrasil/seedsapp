<?php

namespace App;

abstract class Model {
    protected \PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    public function findAll(array $conditions = [], array $orderBy = []): array {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        if (!empty($orderBy)) {
            $orderClauses = [];
            foreach ($orderBy as $column => $direction) {
                $orderClauses[] = "$column $direction";
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function create(array $data): int {
        error_log("=== Model::create ===");
        error_log("Tabela: " . $this->table);
        error_log("Dados: " . print_r($data, true));
        
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        error_log("SQL: " . $sql);
        error_log("Valores: " . print_r(array_values($data), true));
        
        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array_values($data));
            error_log("Resultado do execute: " . ($result ? "true" : "false"));
            if (!$result) {
                error_log("Erro PDO: " . print_r($stmt->errorInfo(), true));
                throw new \Exception("Erro ao inserir registro: " . $stmt->errorInfo()[2]);
            }
            $id = (int)$this->db->lastInsertId();
            error_log("ID inserido: " . $id);
            return $id;
        } catch (\PDOException $e) {
            error_log("PDOException: " . $e->getMessage());
            throw new \Exception("Erro ao inserir registro: " . $e->getMessage());
        }
    }
    
    public function update(int $id, array $data): bool {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([...array_values($data), $id]);
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function count(array $conditions = []): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    
    protected function beginTransaction(): void {
        error_log("=== Model::beginTransaction ===");
        try {
            if (!$this->db->inTransaction()) {
                $result = $this->db->beginTransaction();
                error_log("Resultado beginTransaction: " . ($result ? "true" : "false"));
            } else {
                error_log("Transação já está em andamento");
            }
        } catch (\Exception $e) {
            error_log("Erro ao iniciar transação: " . $e->getMessage());
            throw $e;
        }
    }
    
    protected function commit(): void {
        error_log("=== Model::commit ===");
        try {
            if ($this->db->inTransaction()) {
                $result = $this->db->commit();
                error_log("Resultado commit: " . ($result ? "true" : "false"));
            } else {
                error_log("Nenhuma transação em andamento para fazer commit");
            }
        } catch (\Exception $e) {
            error_log("Erro ao fazer commit: " . $e->getMessage());
            throw $e;
        }
    }
    
    protected function rollBack(): void {
        error_log("=== Model::rollBack ===");
        try {
            if ($this->db->inTransaction()) {
                $result = $this->db->rollBack();
                error_log("Resultado rollBack: " . ($result ? "true" : "false"));
            } else {
                error_log("Nenhuma transação em andamento para fazer rollback");
            }
        } catch (\Exception $e) {
            error_log("Erro ao fazer rollback: " . $e->getMessage());
            throw $e;
        }
    }
}
