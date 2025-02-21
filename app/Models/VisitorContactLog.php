<?php

namespace App\Models;

use App\Model;

class VisitorContactLog extends Model {
    protected string $table = 'visitor_contact_logs';
    protected array $fillable = [
        'visitor_id',
        'user_id',
        'content',
        'follow_up_date',
        'follow_up_notes',
        'follow_up_status'
    ];

    public function __construct() {
        parent::__construct();
    }

    public function create(array $data): int {
        error_log("=== CRIANDO NOVO CONTACT LOG ===");
        error_log("Dados recebidos: " . print_r($data, true));
        
        // Validar dados obrigatórios
        if (empty($data['visitor_id'])) {
            throw new \Exception('ID do visitante é obrigatório');
        }

        // Garantir que temos um user_id
        if (empty($data['user_id'])) {
            $data['user_id'] = $_SESSION['user_id'] ?? null;
            if (empty($data['user_id'])) {
                throw new \Exception('ID do usuário é obrigatório');
            }
        }

        // Garantir que temos um conteúdo
        if (empty($data['content'])) {
            $data['content'] = 'Follow-up adicionado via edição do visitante';
        }

        // Validar data de follow-up
        if (!empty($data['follow_up_date'])) {
            $followUpDate = date('Y-m-d', strtotime($data['follow_up_date']));
            if ($followUpDate === false) {
                throw new \Exception('Data de follow-up inválida');
            }
            $data['follow_up_date'] = $followUpDate;
        }

        // Garantir status padrão se não fornecido
        if (empty($data['follow_up_status'])) {
            $data['follow_up_status'] = 'pending';
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        
        error_log("Dados finais para inserção: " . print_r($data, true));
        return parent::create($data);
    }

    public function findByVisitorId(int $visitorId, string $orderBy = 'created_at DESC'): array {
        // Validar e sanitizar o orderBy para prevenir SQL injection
        $allowedColumns = ['created_at', 'follow_up_date'];
        $allowedDirections = ['ASC', 'DESC'];
        
        $parts = explode(' ', trim($orderBy));
        if (count($parts) !== 2 || 
            !in_array($parts[0], $allowedColumns) || 
            !in_array(strtoupper($parts[1]), $allowedDirections)) {
            $orderBy = 'created_at DESC';
        }

        $sql = "SELECT vcl.*, u.name as user_name 
                FROM {$this->table} vcl 
                INNER JOIN users u ON u.id = vcl.user_id 
                WHERE vcl.visitor_id = ?
                ORDER BY {$orderBy}";
        
        return $this->query($sql, [$visitorId]);
    }

    public function getPendingFollowUps(): array {
        $sql = "SELECT vcl.*, v.name as visitor_name, u.name as user_name 
                FROM {$this->table} vcl 
                INNER JOIN visitors v ON v.id = vcl.visitor_id
                INNER JOIN users u ON u.id = vcl.user_id 
                WHERE vcl.follow_up_status = 'pending' 
                AND vcl.follow_up_date IS NOT NULL 
                AND vcl.follow_up_date <= CURDATE()
                ORDER BY vcl.follow_up_date ASC";
        
        return $this->query($sql);
    }

    public function updateFollowUpStatus(int $id, string $status): bool {
        // Validar status
        if (!in_array($status, ['pending', 'completed', 'cancelled'])) {
            throw new \Exception('Status inválido');
        }

        $sql = "UPDATE {$this->table} 
                SET follow_up_status = ?, 
                    updated_at = NOW() 
                WHERE id = ? 
                AND follow_up_date IS NOT NULL";
                
        return $this->execute($sql, [$status, $id]);
    }

    public function getLogsForVisitor(int $visitorId): array {
        try {
            $sql = "SELECT vcl.*, u.name as user_name 
                    FROM {$this->table} vcl
                    LEFT JOIN users u ON vcl.user_id = u.id
                    WHERE vcl.visitor_id = ?
                    ORDER BY vcl.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$visitorId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[VisitorContactLog] Erro ao buscar logs do visitante: " . $e->getMessage());
            return [];
        }
    }
}
