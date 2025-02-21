<?php

namespace App\Models;

use PDO;
use PDOException;

class Message {
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
    
    public function create(array $data): ?int {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO messages (
                subject, content, type, status,
                scheduled_date, sender_id,
                created_at, updated_at
            ) VALUES (
                :subject, :content, :type, :status,
                :scheduled_date, :sender_id,
                NOW(), NOW()
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'subject' => $data['subject'],
                'content' => $data['content'],
                'type' => $data['type'],
                'status' => $data['status'] ?? 'draft',
                'scheduled_date' => $data['scheduled_date'] ?? null,
                'sender_id' => $data['sender_id']
            ]);
            
            $messageId = $this->db->lastInsertId();
            
            // Adiciona os destinatários
            if (!empty($data['recipients'])) {
                $sql = "INSERT INTO message_recipients (
                    message_id, recipient_type, recipient_id,
                    created_at, updated_at
                ) VALUES (
                    :message_id, :recipient_type, :recipient_id,
                    NOW(), NOW()
                )";
                
                $stmt = $this->db->prepare($sql);
                
                foreach ($data['recipients'] as $recipient) {
                    $stmt->execute([
                        'message_id' => $messageId,
                        'recipient_type' => $recipient['type'],
                        'recipient_id' => $recipient['id'] ?? null
                    ]);
                }
            }
            
            $this->db->commit();
            return $messageId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erro ao criar mensagem: " . $e->getMessage());
            return null;
        }
    }
    
    public function update(int $id, array $data): bool {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                if ($key !== 'id' && $key !== 'recipients') {
                    $fields[] = "$key = :$key";
                    $values[$key] = $value;
                }
            }
            
            $fields[] = "updated_at = NOW()";
            $values['id'] = $id;
            
            $this->db->beginTransaction();
            
            // Atualiza a mensagem
            $sql = "UPDATE messages SET " . implode(", ", $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            // Atualiza os destinatários se fornecidos
            if (isset($data['recipients'])) {
                // Remove destinatários antigos
                $sql = "DELETE FROM message_recipients WHERE message_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);
                
                // Adiciona novos destinatários
                $sql = "INSERT INTO message_recipients (
                    message_id, recipient_type, recipient_id,
                    created_at, updated_at
                ) VALUES (
                    :message_id, :recipient_type, :recipient_id,
                    NOW(), NOW()
                )";
                
                $stmt = $this->db->prepare($sql);
                
                foreach ($data['recipients'] as $recipient) {
                    $stmt->execute([
                        'message_id' => $id,
                        'recipient_type' => $recipient['type'],
                        'recipient_id' => $recipient['id'] ?? null
                    ]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erro ao atualizar mensagem: " . $e->getMessage());
            return false;
        }
    }
    
    public function find(int $id): ?array {
        try {
            $sql = "SELECT m.*, u.name as sender_name,
                    GROUP_CONCAT(
                        CONCAT(mr.recipient_type, ':', COALESCE(mr.recipient_id, 'all'))
                        ORDER BY mr.id
                        SEPARATOR ';'
                    ) as recipients
                    FROM messages m
                    INNER JOIN users u ON m.sender_id = u.id
                    LEFT JOIN message_recipients mr ON m.id = mr.message_id
                    WHERE m.id = ?
                    GROUP BY m.id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar mensagem: " . $e->getMessage());
            return null;
        }
    }
    
    public function delete(int $id): bool {
        try {
            $this->db->beginTransaction();
            
            // Remove destinatários
            $sql = "DELETE FROM message_recipients WHERE message_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            // Remove a mensagem
            $sql = "DELETE FROM messages WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erro ao deletar mensagem: " . $e->getMessage());
            return false;
        }
    }
    
    public function findAll(array $filters = []): array {
        try {
            $where = ["1=1"];
            $values = [];
            
            if (!empty($filters['type'])) {
                $where[] = "m.type = ?";
                $values[] = $filters['type'];
            }
            
            if (!empty($filters['status'])) {
                $where[] = "m.status = ?";
                $values[] = $filters['status'];
            }
            
            if (!empty($filters['sender_id'])) {
                $where[] = "m.sender_id = ?";
                $values[] = $filters['sender_id'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(m.subject LIKE ? OR m.content LIKE ?)";
                $search = "%{$filters['search']}%";
                $values = array_merge($values, [$search, $search]);
            }
            
            $sql = "SELECT m.*, u.name as sender_name,
                    GROUP_CONCAT(
                        CONCAT(mr.recipient_type, ':', COALESCE(mr.recipient_id, 'all'))
                        ORDER BY mr.id
                        SEPARATOR ';'
                    ) as recipients
                    FROM messages m
                    INNER JOIN users u ON m.sender_id = u.id
                    LEFT JOIN message_recipients mr ON m.id = mr.message_id
                    WHERE " . implode(" AND ", $where) . "
                    GROUP BY m.id
                    ORDER BY m.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar mensagens: " . $e->getMessage());
            return [];
        }
    }
    
    public function getRecipients(int $messageId): array {
        try {
            $sql = "SELECT mr.*, 
                    CASE mr.recipient_type
                        WHEN 'user' THEN u.name
                        WHEN 'group' THEN g.name
                        WHEN 'ministry' THEN m.name
                        ELSE 'Todos'
                    END as recipient_name
                    FROM message_recipients mr
                    LEFT JOIN users u ON mr.recipient_type = 'user' AND mr.recipient_id = u.id
                    LEFT JOIN growth_groups g ON mr.recipient_type = 'group' AND mr.recipient_id = g.id
                    LEFT JOIN ministries m ON mr.recipient_type = 'ministry' AND mr.recipient_id = m.id
                    WHERE mr.message_id = ?
                    ORDER BY mr.recipient_type, recipient_name";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$messageId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar destinatários: " . $e->getMessage());
            return [];
        }
    }
    
    public function markAsRead(int $messageId, int $recipientId): bool {
        try {
            $sql = "UPDATE message_recipients 
                    SET read_at = NOW(), updated_at = NOW()
                    WHERE message_id = ? AND recipient_id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$messageId, $recipientId]);
        } catch (PDOException $e) {
            error_log("Erro ao marcar mensagem como lida: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUnreadCount(int $userId): int {
        try {
            $sql = "SELECT COUNT(*) FROM message_recipients mr
                    INNER JOIN messages m ON mr.message_id = m.id
                    WHERE (
                        (mr.recipient_type = 'user' AND mr.recipient_id = ?)
                        OR (mr.recipient_type = 'group' AND mr.recipient_id IN (
                            SELECT group_id FROM group_participants WHERE user_id = ? AND active = 1
                        ))
                        OR (mr.recipient_type = 'ministry' AND mr.recipient_id IN (
                            SELECT ministry_id FROM volunteers WHERE user_id = ? AND active = 1
                        ))
                        OR mr.recipient_type = 'all'
                    )
                    AND mr.read_at IS NULL
                    AND m.status = 'sent'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $userId, $userId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar mensagens não lidas: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getScheduledMessages(): array {
        try {
            $sql = "SELECT m.*, u.name as sender_name
                    FROM messages m
                    INNER JOIN users u ON m.sender_id = u.id
                    WHERE m.status = 'scheduled'
                    AND m.scheduled_date <= NOW()
                    ORDER BY m.scheduled_date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar mensagens agendadas: " . $e->getMessage());
            return [];
        }
    }
}
