<?php

namespace App\Models;

use App\Core\Model;

class FinancialTransaction extends Model {
    protected $table;
    
    public function __construct() {
        parent::__construct();
        $this->table = 'financial_transactions';
    }
    
    public function getMonthlyTotal(int $month, int $year): float {
        $sql = "SELECT COALESCE(SUM(amount), 0) FROM {$this->table} 
                WHERE MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$month, $year]);
        return (float)$stmt->fetchColumn();
    }
    
    public function getMonthlyTithes(int $month, int $year): float {
        $sql = "SELECT COALESCE(SUM(amount), 0) FROM {$this->table} 
                WHERE type = 'tithe' 
                AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$month, $year]);
        return (float)$stmt->fetchColumn();
    }
    
    public function getMonthlyOfferings(int $month, int $year): float {
        $sql = "SELECT COALESCE(SUM(amount), 0) FROM {$this->table} 
                WHERE type = 'offering' 
                AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$month, $year]);
        return (float)$stmt->fetchColumn();
    }
    
    public function getRecent(int $limit = 5): array {
        $sql = "SELECT t.*, u.name as created_by_name 
                FROM {$this->table} t
                INNER JOIN users u ON t.created_by = u.id
                ORDER BY t.transaction_date DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getMonthlyStats(): array {
        $sql = "SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') as month,
                    SUM(CASE WHEN type = 'tithe' THEN amount ELSE 0 END) as tithes,
                    SUM(CASE WHEN type = 'offering' THEN amount ELSE 0 END) as offerings,
                    COUNT(*) as total_transactions
                FROM {$this->table}
                WHERE transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
                ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getStatsByPeriod(string $period): array {
        $interval = match($period) {
            'week' => 'INTERVAL 7 DAY',
            'month' => 'INTERVAL 30 DAY',
            'year' => 'INTERVAL 12 MONTH',
            default => 'INTERVAL 30 DAY'
        };
        
        $sql = "SELECT 
                    DATE(transaction_date) as date,
                    SUM(CASE WHEN type = 'tithe' THEN amount ELSE 0 END) as tithes,
                    SUM(CASE WHEN type = 'offering' THEN amount ELSE 0 END) as offerings,
                    COUNT(*) as total_transactions
                FROM {$this->table}
                WHERE transaction_date >= DATE_SUB(CURRENT_DATE, {$interval})
                GROUP BY DATE(transaction_date)
                ORDER BY date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getContributorStats(string $contributorName): array {
        $sql = "SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') as month,
                    SUM(amount) as total_amount,
                    COUNT(*) as total_transactions
                FROM {$this->table}
                WHERE contributor_name LIKE ?
                GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
                ORDER BY month DESC
                LIMIT 12";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["%{$contributorName}%"]);
        return $stmt->fetchAll();
    }
    
    public function searchTransactions(array $filters): array {
        $sql = "SELECT t.*, u.name as created_by_name 
                FROM {$this->table} t
                INNER JOIN users u ON t.created_by = u.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(t.transaction_date) >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(t.transaction_date) <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND t.type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['contributor'])) {
            $sql .= " AND t.contributor_name LIKE ?";
            $params[] = "%{$filters['contributor']}%";
        }
        
        if (!empty($filters['min_amount'])) {
            $sql .= " AND t.amount >= ?";
            $params[] = $filters['min_amount'];
        }
        
        if (!empty($filters['max_amount'])) {
            $sql .= " AND t.amount <= ?";
            $params[] = $filters['max_amount'];
        }
        
        $sql .= " ORDER BY t.transaction_date DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function generateReport(string $startDate, string $endDate): array {
        $sql = "SELECT 
                    DATE(transaction_date) as date,
                    type,
                    COUNT(*) as transaction_count,
                    SUM(amount) as total_amount,
                    MIN(amount) as min_amount,
                    MAX(amount) as max_amount,
                    AVG(amount) as avg_amount
                FROM {$this->table}
                WHERE transaction_date BETWEEN ? AND ?
                GROUP BY DATE(transaction_date), type
                ORDER BY date ASC, type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function getTopContributors(int $limit = 10, string $startDate = null, string $endDate = null): array {
        $sql = "SELECT 
                    contributor_name,
                    COUNT(*) as transaction_count,
                    SUM(amount) as total_amount,
                    MAX(amount) as max_contribution
                FROM {$this->table}
                WHERE 1=1";
        
        $params = [];
        if ($startDate) {
            $sql .= " AND transaction_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND transaction_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY contributor_name
                  ORDER BY total_amount DESC
                  LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
