<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\GroupMember;
use App\Models\GrowthGroup;
use App\Models\Visitor;

class GroupMemberController extends Controller
{
    private $groupMemberModel;
    private $groupModel;
    private $visitorModel;

    public function __construct()
    {
        parent::__construct();
        $this->groupMemberModel = new GroupMember();
        $this->groupModel = new GrowthGroup();
        $this->visitorModel = new Visitor();
    }

    /**
     * Exibe formulário de pré-inscrição
     */
    public function preRegister($groupId)
    {
        try {
            $group = $this->groupModel->find($groupId);
            if (!$group) {
                $this->setFlash('error', 'Grupo não encontrado');
                redirect('/groups');
                return;
            }

            // Carregar visitantes para o select
            $visitors = $this->visitorModel->getAll();

            parent::view('groups/pre_register', [
                'group' => $group,
                'visitors' => $visitors
            ]);

        } catch (\Exception $e) {
            error_log("Error showing pre-registration form: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar formulário de pré-inscrição');
            redirect('/groups');
        }
    }

    /**
     * Processa a pré-inscrição
     */
    public function submitPreRegistration($groupId)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método não permitido']);
                return;
            }

            $visitorId = $_POST['visitor_id'] ?? null;
            $notes = $_POST['notes'] ?? null;

            if (!$visitorId) {
                $this->setFlash('error', 'Visitante não selecionado');
                redirect("/groups/{$groupId}/pre-register");
                return;
            }

            $memberId = $this->groupMemberModel->createPreRegistration($groupId, $visitorId, $notes);
            
            $this->setFlash('success', 'Pré-inscrição realizada com sucesso! Aguarde a aprovação do líder.');
            redirect("/groups/{$groupId}");

        } catch (\Exception $e) {
            error_log("Error submitting pre-registration: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao realizar pré-inscrição: ' . $e->getMessage());
            redirect("/groups/{$groupId}/pre-register");
        }
    }

    /**
     * Lista membros pendentes de aprovação
     */
    public function pendingMembers($groupId)
    {
        try {
            $group = $this->groupModel->find($groupId);
            if (!$group) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Grupo não encontrado']);
                return;
            }

            $pendingMembers = $this->groupMemberModel->getPendingMembers($groupId);
            
            echo json_encode([
                'success' => true,
                'data' => $pendingMembers
            ]);

        } catch (\Exception $e) {
            error_log("Error getting pending members: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar membros pendentes'
            ]);
        }
    }

    /**
     * Atualiza status de um membro
     */
    public function updateMemberStatus($memberId)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método não permitido']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $newStatus = $data['status'] ?? null;
            $notes = $data['notes'] ?? null;

            if (!in_array($newStatus, ['approved', 'rejected'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Status inválido']);
                return;
            }

            $currentUser = $this->getCurrentUser();
            $this->groupMemberModel->updateStatus($memberId, $newStatus, $currentUser['id'], $notes);
            
            echo json_encode([
                'success' => true,
                'message' => 'Status atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            error_log("Error updating member status: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar status'
            ]);
        }
    }

    /**
     * Busca histórico de um membro
     */
    public function memberHistory($memberId)
    {
        try {
            $history = $this->groupMemberModel->getMemberHistory($memberId);
            
            echo json_encode([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            error_log("Error getting member history: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar histórico'
            ]);
        }
    }
}
