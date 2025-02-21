<?php

namespace App\Models;

use App\Database;
use PDO;

class Report {
    private $db;
    private $table = 'reports';

    public function __construct() {
        $this->db = Database::getInstance();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            type VARCHAR(50) NOT NULL,
            fields JSON NOT NULL,
            filters JSON,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id)
        )";
        
        $this->db->exec($sql);
    }

    public function getMembersReport($filters = []) {
        $sql = "SELECT m.*, 
                       gg.name as group_name,
                       r.name as role_name,
                       COUNT(a.id) as total_attendances
                FROM members m
                LEFT JOIN growth_groups gg ON m.group_id = gg.id
                LEFT JOIN roles r ON m.role_id = r.id
                LEFT JOIN attendances a ON m.id = a.member_id";

        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "m.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['group_id'])) {
            $where[] = "m.group_id = :group_id";
            $params['group_id'] = $filters['group_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY m.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupsReport($filters = []) {
        $sql = "SELECT g.*, 
                       COUNT(DISTINCT m.id) as total_members,
                       COUNT(DISTINCT v.id) as total_visitors,
                       COUNT(DISTINCT a.id) as total_attendances
                FROM growth_groups g
                LEFT JOIN members m ON g.id = m.group_id
                LEFT JOIN visitors v ON g.id = v.group_id
                LEFT JOIN attendances a ON g.id = a.group_id";

        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "g.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY g.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVisitorsReport($filters = []) {
        $sql = "SELECT v.*,
                       COUNT(vv.id) as total_visits,
                       MIN(vv.visit_date) as first_visit,
                       MAX(vv.visit_date) as last_visit
                FROM visitors v
                LEFT JOIN visitor_visits vv ON v.id = vv.visitor_id";

        $where = [];
        $params = [];

        if (!empty($filters['period'])) {
            $where[] = "vv.visit_date >= :start_date";
            switch ($filters['period']) {
                case 'week':
                    $params['start_date'] = date('Y-m-d', strtotime('-1 week'));
                    break;
                case 'month':
                    $params['start_date'] = date('Y-m-d', strtotime('-1 month'));
                    break;
                case 'quarter':
                    $params['start_date'] = date('Y-m-d', strtotime('-3 months'));
                    break;
                case 'year':
                    $params['start_date'] = date('Y-m-d', strtotime('-1 year'));
                    break;
            }
        }

        if (!empty($filters['status'])) {
            $where[] = "v.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY v.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMinistriesReport($filters = []) {
        $sql = "SELECT m.*,
                       u.name as leader_name,
                       COUNT(DISTINCT mm.member_id) as total_members,
                       COUNT(DISTINCT e.id) as total_events
                FROM ministries m
                LEFT JOIN users u ON m.leader_id = u.id
                LEFT JOIN ministry_members mm ON m.id = mm.ministry_id
                LEFT JOIN events e ON m.id = e.ministry_id";

        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "m.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY m.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, description, type, fields, filters, created_by) 
                VALUES (:name, :description, :type, :fields, :filters, :created_by)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => $data['type'],
            'fields' => json_encode($data['fields']),
            'filters' => json_encode($data['filters'] ?? []),
            'created_by' => $data['created_by']
        ]);
        
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = :name,
                    description = :description,
                    type = :type,
                    fields = :fields,
                    filters = :filters
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => $data['type'],
            'fields' => json_encode($data['fields']),
            'filters' => json_encode($data['filters'] ?? [])
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function findAll() {
        $sql = "SELECT r.*, u.name as created_by_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.created_by = u.id
                ORDER BY r.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT r.*, u.name as created_by_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $sql = "SELECT r.*, u.name as creator_name 
                FROM {$this->table} r
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($report) {
            $report['fields'] = json_decode($report['fields'], true);
            $report['filters'] = json_decode($report['filters'], true);
        }
        
        return $report;
    }

    public function generateReport($id, $filters = []) {
        $report = $this->find($id);
        if (!$report) return null;

        $query = $this->buildReportQuery($report, $filters);
        $stmt = $this->db->prepare($query);
        $stmt->execute($filters);
        
        return [
            'report' => $report,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    private function buildReportQuery($report, &$filters) {
        $table = $this->getTableFromType($report['type']);
        $fields = $this->buildFieldsList($report['fields']);
        $where = $this->buildWhereClause($report['filters'], $filters);
        
        return "SELECT {$fields} FROM {$table} WHERE 1=1 {$where}";
    }

    private function getTableFromType($type) {
        return match($type) {
            'visitors' => 'visitors',
            'groups' => 'growth_groups',
            'volunteers' => 'volunteers',
            default => throw new \Exception('Tipo de relatório inválido')
        };
    }

    private function buildFieldsList($fields) {
        $selectFields = [];
        foreach ($fields as $field) {
            $selectFields[] = $field['field'] . ' as ' . ($field['alias'] ?? $field['field']);
        }
        return implode(', ', $selectFields);
    }

    private function buildWhereClause($reportFilters, &$params) {
        if (empty($reportFilters)) return '';
        
        $where = [];
        foreach ($reportFilters as $filter) {
            $paramName = ':' . str_replace('.', '_', $filter['field']);
            if (isset($params[$filter['field']])) {
                $where[] = "{$filter['field']} {$filter['operator']} {$paramName}";
                $params[$paramName] = $params[$filter['field']];
            }
        }
        
        return $where ? ' AND ' . implode(' AND ', $where) : '';
    }
}
