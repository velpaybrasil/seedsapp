<?php

namespace App\Models;

use PDO;
use PDOException;

class Financial {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            error_log("Erro de conexão: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function createCategory(array $data): ?int {
        try {
            $sql = "INSERT INTO financial_categories (
                name, type, description,
                active, created_at, updated_at
            ) VALUES (
                :name, :type, :description,
                :active, NOW(), NOW()
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'name' => $data['name'],
                'type' => $data['type'],
                'description' => $data['description'] ?? null,
                'active' => $data['active'] ?? true
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar categoria: " . $e->getMessage());
            return null;
        }
    }
    
    public function getCategories(array $filters = []): array {
        try {
            $where = ["1=1"];
            $values = [];
            
            if (isset($filters['active'])) {
                $where[] = "active = ?";
                $values[] = $filters['active'];
            }
            
            if (!empty($filters['type'])) {
                $where[] = "type = ?";
                $values[] = $filters['type'];
            }
            
            $sql = "SELECT * FROM financial_categories 
                    WHERE " . implode(" AND ", $where) . "
                    ORDER BY name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar categorias: " . $e->getMessage());
            return [];
        }
    }
    
    public function createTransaction(array $data): ?int {
        try {
            $sql = "INSERT INTO financial_transactions (
                category_id, type, amount, description,
                date, payment_method, status, document_number,
                user_id, created_at, updated_at
            ) VALUES (
                :category_id, :type, :amount, :description,
                :date, :payment_method, :status, :document_number,
                :user_id, NOW(), NOW()
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'category_id' => $data['category_id'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
                'date' => $data['date'],
                'payment_method' => $data['payment_method'],
                'status' => $data['status'] ?? 'pending',
                'document_number' => $data['document_number'] ?? null,
                'user_id' => $data['user_id']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar transação: " . $e->getMessage());
            return null;
        }
    }
    
    public function updateTransaction(int $id, array $data): bool {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $fields[] = "$key = :$key";
                    $values[$key] = $value;
                }
            }
            
            $fields[] = "updated_at = NOW()";
            $values['id'] = $id;
            
            $sql = "UPDATE financial_transactions 
                    SET " . implode(", ", $fields) . " 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar transação: " . $e->getMessage());
            return false;
        }
    }
    
    public function getTransactions(array $filters = []): array {
        try {
            $where = ["1=1"];
            $values = [];
            
            if (!empty($filters['start_date'])) {
                $where[] = "t.date >= ?";
                $values[] = $filters['start_date'];
            }
            
            if (!empty($filters['end_date'])) {
                $where[] = "t.date <= ?";
                $values[] = $filters['end_date'];
            }
            
            if (!empty($filters['type'])) {
                $where[] = "t.type = ?";
                $values[] = $filters['type'];
            }
            
            if (!empty($filters['status'])) {
                $where[] = "t.status = ?";
                $values[] = $filters['status'];
            }
            
            if (!empty($filters['category_id'])) {
                $where[] = "t.category_id = ?";
                $values[] = $filters['category_id'];
            }
            
            $sql = "SELECT t.*, c.name as category_name, u.name as user_name
                    FROM financial_transactions t
                    INNER JOIN financial_categories c ON t.category_id = c.id
                    INNER JOIN users u ON t.user_id = u.id
                    WHERE " . implode(" AND ", $where) . "
                    ORDER BY t.date DESC, t.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar transações: " . $e->getMessage());
            return [];
        }
    }
    
    public function createTitheOffering(array $data): ?int {
        try {
            $sql = "INSERT INTO tithes_offerings (
                user_id, type, amount, date,
                payment_method, anonymous, notes,
                created_at, updated_at
            ) VALUES (
                :user_id, :type, :amount, :date,
                :payment_method, :anonymous, :notes,
                NOW(), NOW()
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $data['user_id'] ?? null,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'payment_method' => $data['payment_method'],
                'anonymous' => $data['anonymous'] ?? false,
                'notes' => $data['notes'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar dízimo/oferta: " . $e->getMessage());
            return null;
        }
    }
    
    public function getTithesOfferings(array $filters = []): array {
        try {
            $where = ["1=1"];
            $values = [];
            
            if (!empty($filters['start_date'])) {
                $where[] = "t.date >= ?";
                $values[] = $filters['start_date'];
            }
            
            if (!empty($filters['end_date'])) {
                $where[] = "t.date <= ?";
                $values[] = $filters['end_date'];
            }
            
            if (!empty($filters['type'])) {
                $where[] = "t.type = ?";
                $values[] = $filters['type'];
            }
            
            if (!empty($filters['user_id'])) {
                $where[] = "t.user_id = ?";
                $values[] = $filters['user_id'];
            }
            
            $sql = "SELECT t.*, u.name as user_name
                    FROM tithes_offerings t
                    LEFT JOIN users u ON t.user_id = u.id
                    WHERE " . implode(" AND ", $where) . "
                    ORDER BY t.date DESC, t.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar dízimos/ofertas: " . $e->getMessage());
            return [];
        }
    }
    
    public function getFinancialStats(string $period = 'month'): array {
        try {
            $interval = match($period) {
                'week' => 'INTERVAL 7 DAY',
                'month' => 'INTERVAL 30 DAY',
                'year' => 'INTERVAL 12 MONTH',
                default => 'INTERVAL 30 DAY'
            };
            
            $sql = "SELECT 
                        t.date,
                        SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END) as total_income,
                        SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END) as total_expense,
                        SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE -t.amount END) as balance
                    FROM financial_transactions t
                    WHERE t.date >= DATE_SUB(CURRENT_DATE, $interval)
                    AND t.status = 'completed'
                    GROUP BY t.date
                    ORDER BY t.date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar estatísticas financeiras: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTithesOfferingsStats(string $period = 'month'): array {
        try {
            $interval = match($period) {
                'week' => 'INTERVAL 7 DAY',
                'month' => 'INTERVAL 30 DAY',
                'year' => 'INTERVAL 12 MONTH',
                default => 'INTERVAL 30 DAY'
            };
            
            $sql = "SELECT 
                        t.date,
                        SUM(CASE WHEN t.type = 'tithe' THEN t.amount ELSE 0 END) as total_tithes,
                        SUM(CASE WHEN t.type = 'offering' THEN t.amount ELSE 0 END) as total_offerings,
                        COUNT(DISTINCT CASE WHEN t.type = 'tithe' THEN t.user_id END) as tithe_contributors,
                        COUNT(DISTINCT CASE WHEN t.type = 'offering' THEN t.user_id END) as offering_contributors
                    FROM tithes_offerings t
                    WHERE t.date >= DATE_SUB(CURRENT_DATE, $interval)
                    GROUP BY t.date
                    ORDER BY t.date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar estatísticas de dízimos/ofertas: " . $e->getMessage());
            return [];
        }
    }
}
