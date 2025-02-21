<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\GrowthGroup;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Ministry;
use App\Models\GroupLeader;
use App\Models\GroupMember;
use App\Core\Database\Database;
use Exception;
use PDOException;
use App\Core\Session;

class GroupController extends Controller {
    protected GrowthGroup $groupModel;
    protected User $userModel;
    protected Visitor $visitorModel;
    protected Ministry $ministryModel;
    protected GroupLeader $groupLeaderModel;
    protected GroupMember $groupMemberModel;
    protected Database $db;
    protected Session $session;

    public function __construct() {
        parent::__construct();
        $this->groupModel = new GrowthGroup();
        $this->visitorModel = new Visitor();
        $this->ministryModel = new Ministry();
        $this->userModel = new User();
        $this->groupLeaderModel = new GroupLeader();
        $this->groupMemberModel = new GroupMember();
        $this->session = new Session();
    }

    public function index(): void {
        try {
            $groups = $this->groupModel->getAllGroups();
            $ministries = $this->ministryModel->getAll();
            
            $this->view('groups/index', [
                'title' => 'Grupos de Crescimento',
                'groups' => $groups,
                'ministries' => $ministries
            ]);
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/dashboard');
        }
    }

    public function create(): void {
        try {
            error_log("[GroupController] Iniciando criação de grupo");
            
            // Buscar lista de ministérios
            $ministries = $this->ministryModel->getAll();
            error_log("[GroupController] Ministérios encontrados: " . count($ministries));

            // Buscar líderes disponíveis
            $leaders = $this->userModel->getLeaders();
            error_log("[GroupController] Líderes encontrados: " . count($leaders));
            
            if ($this->isPost()) {
                $data = $this->getPostData();
                error_log("[GroupController] Dados recebidos: " . json_encode($data));
                
                $errors = $this->validateInput(
                    $data,
                    [
                        'name' => 'required|min:3',
                        'description' => 'required',
                        'meeting_day' => 'required',
                        'meeting_time' => 'required',
                        'meeting_address' => 'required',
                        'neighborhood' => 'required',
                        'max_participants' => 'required|numeric',
                        'ministry_id' => 'required|numeric',
                        'leaders' => 'required|array|min:1',
                        'latitude' => 'numeric|between:-90,90',
                        'longitude' => 'numeric|between:-180,180'
                    ]
                );

                if (!empty($errors)) {
                    error_log("[GroupController] Erros de validação: " . json_encode($errors));
                    $this->setFlash('error', 'Por favor, corrija os erros no formulário.');
                    $_SESSION['form_errors'] = $errors;
                    $this->redirect('/groups/create');
                    return;
                }

                try {
                    $groupId = $this->groupModel->create($data);
                    if ($groupId) {
                        error_log("[GroupController] Grupo criado com sucesso. ID: " . $groupId);
                        $this->setFlash('success', 'Grupo criado com sucesso!');
                        $this->redirect('/groups');
                    } else {
                        throw new \Exception('Erro ao criar o grupo.');
                    }
                } catch (\Exception $e) {
                    error_log("[GroupController] Erro ao criar grupo: " . $e->getMessage());
                    error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
                    $this->setFlash('error', 'Erro ao criar o grupo.');
                    $this->redirect('/groups/create');
                }
                return;
            }
            
            error_log("[GroupController] Carregando formulário de criação");
            error_log("[GroupController] Total de líderes disponíveis: " . count($leaders));
            error_log("[GroupController] Total de ministérios disponíveis: " . count($ministries));
            
            $this->view('groups/create', [
                'title' => 'Criar Grupo',
                'leaders' => $leaders,
                'ministries' => $ministries,
                'user' => $this->getCurrentUser()
            ]);

        } catch (\Exception $e) {
            error_log("[GroupController] Erro ao carregar formulário de criação: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao carregar o formulário.');
            $this->redirect('/groups');
        }
    }

    public function viewGroup($id): void {
        try {
            $group = $this->groupModel->find($id);
            if (!$group) {
                $this->setFlash('error', 'Grupo não encontrado');
                redirect('/groups');
                return;
            }

            // Buscar membros ativos
            $members = $this->groupModel->getGroupMembers($id);
            
            // Buscar visitantes que não estão em nenhum grupo
            $visitors = $this->getVisitorsWithoutGroup();
            
            // Buscar membros que não estão em nenhum grupo
            $availableMembers = $this->userModel->getMembersWithoutGroup();
            
            // Buscar reuniões do grupo
            $meetings = $this->groupModel->getGroupMeetings($id);

            $this->view('groups/view', [
                'group' => $group,
                'members' => $members,
                'visitors' => $visitors,
                'availableMembers' => $availableMembers,
                'meetings' => $meetings
            ]);
        } catch (Exception $e) {
            error_log("[GroupController] Erro ao visualizar grupo: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao carregar dados do grupo');
            redirect('/groups');
        }
    }

    private function getVisitorsWithoutGroup(): array {
        try {
            $sql = "SELECT v.* FROM visitors v 
                    LEFT JOIN group_members gm ON v.id = gm.visitor_id 
                    WHERE gm.id IS NULL AND v.status = 'active'";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[GroupController] Erro ao buscar visitantes sem grupo: " . $e->getMessage());
            return [];
        }
    }

    public function addParticipant($groupId): void {
        try {
            // Validar CSRF
            if (!$this->validateCSRF()) {
                $this->setFlash('error', 'Token CSRF inválido');
                redirect("/groups/view/{$groupId}");
                return;
            }

            $participantType = $_POST['participant_type'] ?? '';
            $visitorId = $_POST['visitor_id'] ?? null;
            $memberId = $_POST['member_id'] ?? null;

            error_log("[GroupController] Adicionando participante ao grupo {$groupId}");
            error_log("[GroupController] Tipo: {$participantType}");
            error_log("[GroupController] Visitor ID: {$visitorId}");
            error_log("[GroupController] Member ID: {$memberId}");

            // Validar dados
            if (!$participantType || ($participantType === 'visitor' && !$visitorId) || ($participantType === 'member' && !$memberId)) {
                $this->setFlash('error', 'Dados inválidos');
                redirect("/groups/view/{$groupId}");
                return;
            }

            // Verificar se o grupo existe
            $group = $this->groupModel->find($groupId);
            if (!$group) {
                $this->setFlash('error', 'Grupo não encontrado');
                redirect('/groups');
                return;
            }

            // Verificar limite de participantes
            $currentMembers = $this->groupModel->getGroupMembers($groupId);
            if (!empty($group['max_participants']) && count($currentMembers) >= $group['max_participants']) {
                $this->setFlash('error', 'O grupo já atingiu o limite máximo de participantes');
                redirect("/groups/view/{$groupId}");
                return;
            }

            if ($participantType === 'visitor') {
                // Verificar se o visitante existe
                $visitor = $this->visitorModel->find($visitorId);
                if (!$visitor) {
                    $this->setFlash('error', 'Visitante não encontrado');
                    redirect("/groups/view/{$groupId}");
                    return;
                }

                // Verificar se o visitante já está em algum grupo
                if ($this->groupModel->isVisitorInAnyGroup($visitorId)) {
                    $this->setFlash('error', 'Este visitante já está em outro grupo');
                    redirect("/groups/view/{$groupId}");
                    return;
                }

                // Adicionar visitante ao grupo
                if ($this->groupModel->addVisitorToGroup($groupId, $visitorId)) {
                    $this->setFlash('success', 'Visitante adicionado ao grupo com sucesso!');
                } else {
                    $this->setFlash('error', 'Erro ao adicionar visitante ao grupo');
                }
            } else {
                // Verificar se o membro existe
                $member = $this->userModel->find($memberId);
                if (!$member) {
                    $this->setFlash('error', 'Membro não encontrado');
                    redirect("/groups/view/{$groupId}");
                    return;
                }

                // Verificar se o membro já está em algum grupo
                if ($this->groupModel->isMemberInAnyGroup($memberId)) {
                    $this->setFlash('error', 'Este membro já está em outro grupo');
                    redirect("/groups/view/{$groupId}");
                    return;
                }

                // Adicionar membro ao grupo
                if ($this->groupModel->addMemberToGroup($groupId, $memberId)) {
                    $this->setFlash('success', 'Membro adicionado ao grupo com sucesso!');
                } else {
                    $this->setFlash('error', 'Erro ao adicionar membro ao grupo');
                }
            }

            redirect("/groups/view/{$groupId}");
        } catch (Exception $e) {
            error_log("[GroupController] Erro ao adicionar participante: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao adicionar participante ao grupo');
            redirect("/groups/view/{$groupId}");
        }
    }

    public function edit($id): void
    {
        try {
            // Buscar grupo
            $group = $this->groupModel->find($id);
            if (!$group) {
                $this->setFlash('error', 'Grupo não encontrado.');
                $this->redirect('/groups');
                return;
            }

            // Se for POST, processar atualização
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->update($id);
                return;
            }

            // Buscar dados para o formulário
            $ministries = $this->ministryModel->getAll();
            $leaders = $this->groupLeaderModel->getGroupLeaders($id);
            $users = $this->userModel->getAll();
            
            // Renderizar view
            View::render('groups/edit', [
                'group' => $group,
                'ministries' => $ministries,
                'leaders' => $leaders,
                'users' => $users
            ]);
        } catch (Exception $e) {
            error_log("[GroupController] Erro ao editar grupo: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar dados do grupo.');
            $this->redirect('/groups');
        }
    }

    public function update($id): void {
        try {
            // Validar CSRF
            if (!$this->validateCSRF()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Token CSRF inválido'
                ], 403);
                return;
            }

            error_log("[GroupController] Iniciando atualização do grupo ID: " . $id);

            // Verificar se o grupo existe
            $group = $this->groupModel->find($id);
            if (!$group) {
                error_log("[GroupController] Grupo não encontrado: " . $id);
                $this->setFlash('error', 'Grupo não encontrado.');
                $this->redirect('/groups');
                return;
            }

            // Obter e tratar dados do formulário
            $data = $_POST;
            error_log("[GroupController] POST data bruto: " . print_r($_POST, true));
            error_log("[GroupController] Files data: " . print_r($_FILES, true));

            // Tratar campos numéricos
            $data['max_participants'] = !empty($data['max_participants']) ? intval($data['max_participants']) : null;
            $data['ministry_id'] = !empty($data['ministry_id']) ? intval($data['ministry_id']) : null;
            
            // Tratar coordenadas
            $data['latitude'] = !empty($data['latitude']) ? floatval($data['latitude']) : null;
            $data['longitude'] = !empty($data['longitude']) ? floatval($data['longitude']) : null;

            // Tratar campos de data e hora
            if (!empty($data['meeting_time'])) {
                $data['meeting_time'] = date('H:i:s', strtotime($data['meeting_time']));
            }

            error_log("[GroupController] Dados processados antes de enviar ao modelo: " . print_r($data, true));

            // Validação mínima
            if (empty($data['name'])) {
                $this->setFlash('error', 'O nome do grupo é obrigatório.');
                $this->redirect("/groups/{$id}/edit");
                return;
            }

            // Atualizar grupo
            if ($this->groupModel->update($id, $data)) {
                error_log("[GroupController] Atualização concluída com sucesso");
                $this->setFlash('success', 'Grupo atualizado com sucesso!');
            } else {
                throw new \Exception("Falha ao atualizar o grupo");
            }

            // Atualizar líderes
            $leaders = array_map(function($leaderId) {
                return [
                    'user_id' => (int) $leaderId,
                    'role' => 'leader'
                ];
            }, $_POST['leaders'] ?? []);

            if (!$this->groupLeaderModel->updateGroupLeaders($id, $leaders)) {
                throw new \Exception('Erro ao atualizar os líderes do grupo');
            }

        } catch (\Exception $e) {
            error_log("[GroupController] Erro ao atualizar grupo: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao atualizar o grupo. Por favor, tente novamente.');
        }

        $this->redirect('/groups');
    }

    public function delete(int $id): void
    {
        try {
            $group = $this->groupModel->find($id);
            if (!$group) {
                $this->jsonResponse(['error' => 'Grupo não encontrado'], 404);
            }

            $this->groupModel->delete($id);
            $this->jsonResponse(['success' => true]);
        } catch (Exception $e) {
            error_log("[GroupController] Erro ao excluir grupo: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->jsonResponse(['error' => 'Erro ao excluir grupo'], 500);
        }
    }

    public function show(int $id): void {
        try {
            $group = $this->groupModel->find($id);
            if (!$group) {
                throw new \Exception('Grupo não encontrado');
            }

            $this->view('groups/show', [
                'title' => 'Detalhes do Grupo',
                'group' => $group
            ]);
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/groups');
        }
    }

    public function addMeeting($groupId): void
    {
        try {
            // Validar CSRF
            if (!$this->validateCSRF()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Token CSRF inválido'
                ], 403);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método não permitido']);
                return;
            }

            $data = [
                'meeting_date' => $_POST['meeting_date'] ?? null,
                'topic' => $_POST['topic'] ?? null,
                'notes' => $_POST['notes'] ?? null
            ];

            if (!$data['meeting_date']) {
                $this->setFlash('error', 'Data da reunião é obrigatória');
                redirect("/groups/{$groupId}");
                return;
            }

            if ($this->groupModel->addMeeting($groupId, $data)) {
                $this->setFlash('success', 'Reunião registrada com sucesso');
            } else {
                $this->setFlash('error', 'Erro ao registrar reunião');
            }

            redirect("/groups/{$groupId}");

        } catch (\Exception $e) {
            error_log("Error adding meeting: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao registrar reunião');
            redirect("/groups/{$groupId}");
        }
    }

    public function getMeetingAttendance($meetingId): void
    {
        try {
            if (!$this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
                return;
            }

            $attendance = $this->groupModel->getAttendance($meetingId);
            
            echo json_encode([
                'success' => true,
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            error_log("Error getting attendance: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar dados de presença'
            ]);
        }
    }

    public function updateMeetingAttendance($meetingId): void
    {
        try {
            // Validar CSRF
            if (!$this->validateCSRF()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Token CSRF inválido'
                ], 403);
                return;
            }

            if (!$this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['attendance']) || !is_array($data['attendance'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
                return;
            }

            if ($this->groupModel->updateAttendance($meetingId, $data['attendance'])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Presenças atualizadas com sucesso'
                ]);
            } else {
                throw new \Exception('Erro ao atualizar presenças');
            }

        } catch (\Exception $e) {
            error_log("Error updating attendance: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao atualizar presenças'
            ]);
        }
    }

    protected function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function getPostData(): array {
        return [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'meeting_day' => $_POST['meeting_day'] ?? '',
            'meeting_time' => $_POST['meeting_time'] ?? '',
            'meeting_address' => $_POST['meeting_address'] ?? '',
            'neighborhood' => $_POST['neighborhood'] ?? '',
            'max_participants' => $_POST['max_participants'] ?? '',
            'ministry_id' => $_POST['ministry_id'] ?? null,
            'leaders' => $_POST['leaders'] ?? [],
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null,
            'active' => isset($_POST['active']) ? 1 : 0
        ];
    }

    /**
     * Adiciona um membro ao grupo
     */
    public function addMember(int $groupId, int $userId): void {
        try {
            // Valida se o grupo existe
            $group = $this->groupModel->find($groupId);
            if (!$group) {
                throw new \Exception('Grupo não encontrado');
            }

            // Valida se ainda há vagas
            $currentMembers = $this->groupMemberModel->countActiveMembers($groupId);
            if ($currentMembers >= $group['max_participants']) {
                throw new \Exception('Grupo está cheio');
            }

            // Adiciona o membro
            if (!$this->groupMemberModel->addMember($groupId, $userId)) {
                throw new \Exception('Erro ao adicionar membro');
            }

            $this->setFlash('success', 'Membro adicionado com sucesso!');
            $this->redirect("/groups/{$groupId}");
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect("/groups/{$groupId}");
        }
    }

    /**
     * Remove um membro do grupo
     */
    public function removeMember(int $groupId, int $userId): void {
        try {
            // Remove o membro
            if (!$this->groupMemberModel->removeMember($groupId, $userId)) {
                throw new \Exception('Erro ao remover membro');
            }

            $this->setFlash('success', 'Membro removido com sucesso!');
            $this->redirect("/groups/{$groupId}");
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect("/groups/{$groupId}");
        }
    }

    /**
     * Atualiza o status de um membro
     */
    public function updateMemberStatus(int $groupId, int $userId, string $status): void {
        try {
            // Atualiza o status
            if (!in_array($status, ['active', 'inactive'])) {
                throw new \Exception('Status inválido');
            }

            if (!$this->groupMemberModel->updateStatus($groupId, $userId, $status)) {
                throw new \Exception('Erro ao atualizar status');
            }

            $this->setFlash('success', 'Status atualizado com sucesso!');
            $this->redirect("/groups/{$groupId}");
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect("/groups/{$groupId}");
        }
    }

    /**
     * Lista os membros de um grupo
     */
    public function members(int $groupId): void {
        try {
            // Busca o grupo
            $group = $this->groupModel->find($groupId);
            if (!$group) {
                throw new \Exception('Grupo não encontrado');
            }

            // Busca os membros
            $members = $this->groupMemberModel->getGroupMembers($groupId);

            // Busca usuários para adicionar
            $users = $this->userModel->getAll();

            $this->view('groups/members', [
                'title' => 'Membros do Grupo',
                'group' => $group,
                'members' => $members,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/groups');
        }
    }
}
