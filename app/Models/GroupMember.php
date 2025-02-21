<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class GroupMember extends Model
{
    protected static string $table = 'group_members';

    protected static array $fillable = [
        'group_id',
        'visitor_id',
        'status',
        'role',
        'joined_at',
        'notes'
    ];

    /**
     * Cria uma nova pré-inscrição para um grupo
     */
    public static function createPreRegistration($groupId, $visitorId, $notes = null)
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // Verifica se já existe uma inscrição
            $sql = "SELECT id, status FROM " . static::$table . "
                    WHERE group_id = ? AND visitor_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId, $visitorId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Se já existe e foi rejeitada, permite nova inscrição
                if ($existing['status'] === 'rejected') {
                    $updateSql = "UPDATE " . static::$table . "
                                SET status = 'pending', notes = ?, updated_at = CURRENT_TIMESTAMP
                                WHERE id = ?";
                    $stmt = $db->prepare($updateSql);
                    $stmt->execute([$notes, $existing['id']]);
                    
                    $memberId = $existing['id'];
                } else {
                    throw new \Exception('Já existe uma inscrição para este visitante neste grupo');
                }
            } else {
                // Cria nova inscrição
                $sql = "INSERT INTO " . static::$table . "
                        (group_id, visitor_id, status, role, notes)
                        VALUES (?, ?, 'pending', 'member', ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$groupId, $visitorId, $notes]);
                
                $memberId = $db->lastInsertId();
            }

            $db->commit();
            return $memberId;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[GroupMember] Error creating pre-registration: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Atualiza o status de uma pré-inscrição
     */
    public static function updateStatus($memberId, $newStatus, $changedBy, $notes = null)
    {
        try {
            $db = static::getDB();
            $db->beginTransaction();

            // Busca status atual
            $sql = "SELECT status FROM " . static::$table . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$memberId]);
            $currentStatus = $stmt->fetchColumn();

            if (!$currentStatus) {
                throw new \Exception('Membro não encontrado');
            }

            // Atualiza status
            $updateSql = "UPDATE " . static::$table . "
                        SET status = ?, 
                            joined_at = " . ($newStatus === 'approved' ? 'CURRENT_TIMESTAMP' : 'NULL') . ",
                            notes = ?
                        WHERE id = ?";
            $stmt = $db->prepare($updateSql);
            $stmt->execute([$newStatus, $notes, $memberId]);

            // Registra histórico
            $historySql = "INSERT INTO group_member_history
                          (member_id, old_status, new_status, changed_by, notes)
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($historySql);
            $stmt->execute([$memberId, $currentStatus, $newStatus, $changedBy, $notes]);

            $db->commit();
            return true;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[GroupMember] Error updating status: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca pré-inscrições pendentes para um grupo
     */
    public static function getPendingMembers($groupId)
    {
        try {
            $db = static::getDB();
            $sql = "SELECT gm.*, v.name as visitor_name, v.email, v.phone
                    FROM " . static::$table . " gm
                    INNER JOIN visitors v ON v.id = gm.visitor_id
                    WHERE gm.group_id = ? AND gm.status = 'pending'
                    ORDER BY gm.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GroupMember] Error getting pending members: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca membros aprovados de um grupo
     */
    public static function getApprovedMembers($groupId)
    {
        try {
            $db = static::getDB();
            $sql = "SELECT gm.*, v.name as visitor_name, v.email, v.phone
                    FROM " . static::$table . " gm
                    INNER JOIN visitors v ON v.id = gm.visitor_id
                    WHERE gm.group_id = ? AND gm.status = 'approved'
                    ORDER BY gm.joined_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GroupMember] Error getting approved members: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca histórico de status de um membro
     */
    public static function getMemberHistory($memberId)
    {
        try {
            $db = static::getDB();
            $sql = "SELECT gmh.*, u.name as changed_by_name
                    FROM group_member_history gmh
                    INNER JOIN users u ON u.id = gmh.changed_by
                    WHERE gmh.member_id = ?
                    ORDER BY gmh.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$memberId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[GroupMember] Error getting member history: " . $e->getMessage());
            throw $e;
        }
    }
}
