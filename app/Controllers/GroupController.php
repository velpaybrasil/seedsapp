<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\GrowthGroup;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Ministry;
use App\Core\Database\Database;
use Exception;
use PDOException;
use App\Core\Session;

class GroupController extends Controller {
    protected GrowthGroup $groupModel;
    protected User $userModel;
    protected Visitor $visitorModel;
    protected Ministry $ministryModel;
    protected Database $db;
    protected Session $session;

    public function __construct() {
        parent::__construct();
        $this->groupModel = new GrowthGroup();
        $this->visitorModel = new Visitor();
        $this->ministryModel = new Ministry();
        $this->userModel = new User();
        $this->session = new Session();
    }

    public function index(): void {
        try {
            // Get ministries for the dropdown
            $ministries = $this->ministryModel->getAll();
            
            // Get active groups ordered by name
            $groups = $this->groupModel->getAllActive();
            
            // Format data for display
            foreach ($groups as &$group) {
                try {
                    // Get and format leaders - handle missing table gracefully
                    try {
                        $leaders = $this->groupModel->getGroupLeaders($group['id']);
                        $group['leader_name'] = 'Sem líder';
                        $leaderNames = [];
                        
                        foreach ($leaders as $leader) {
                            if ($leader['role'] === 'leader') {
                                $group['leader_name'] = $leader['name'];
                            }
                            $role = $leader['role'] === 'leader' ? 'Líder' : 'Co-líder';
                            $leaderNames[] = $leader['name'] . " ($role)";
                        }
                        
                        $group['leaders'] = !empty($leaderNames) ? implode(', ', $leaderNames) : 'Sem líder';
                    } catch (PDOException $e) {
                        if (strpos($e->getMessage(), "Table 'group_leaders' doesn't exist") !== false) {
                            $group['leader_name'] = 'Sem líder';
                            $group['leaders'] = 'Sem líder';
                        } else {
                            throw $e;
                        }
                    }
                    
                    // Get ministry name
                    if (!empty($group['ministry_id'])) {
                        $ministry = $this->ministryModel->find($group['ministry_id']);
                        $group['ministry_name'] = $ministry ? $ministry['name'] : 'Sem ministério';
                    } else {
                        $group['ministry_name'] = 'Sem ministério';
                    }

                    // Format meeting time
                    if (!empty($group['meeting_time'])) {
                        $group['meeting_time'] = date('H:i', strtotime($group['meeting_time']));
                    }
                } catch (Exception $e) {
                    error_log("[GroupController] Erro ao formatar grupo {$group['id']}: " . $e->getMessage());
                    // Continue with next group if there's an error formatting one
                    continue;
                }
            }
            
            View::render('groups/index', [
                'groups' => $groups,
                'ministries' => $ministries
            ]);
        } catch (Exception $e) {
            error_log("[GroupController] Erro ao carregar a lista de grupos: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->setFlash('error', 'Erro ao carregar a lista de grupos');
            $this->redirect('/dashboard');
        }
    }

    public function create(): void {
        try {
            error_log("[GroupController] Iniciando criação de grupo");
            
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

            $leaders = $this->userModel->all();
            $ministries = $this->ministryModel->all(['status' => 'active']);
            
            error_log("[GroupController] Carregando formulário de criação");
            error_log("[GroupController] Total de líderes disponíveis: " . count($leaders));
            error_log("[GroupController] Total de ministérios disponíveis: " . count($ministries));
            
            View::render('groups/create', [
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
            $ministries = $this->ministryModel->findAll([], ['name' => 'ASC']);
            $leaders = $this->groupModel->getGroupLeaders($id);
            $users = $this->userModel->findAll(['active' => 1], ['name' => 'ASC']);
            
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

    public function show(int $id): void
    {
        try {
            $group = $this->groupModel->find($id);
            if (!$group) {
                $this->jsonResponse(['error' => 'Grupo não encontrado'], 404);
                return;
            }

            // Get leaders with full information
            $leaders = $this->groupModel->getGroupLeaders($id);

            // Get ministry information
            $ministry = null;
            if (!empty($group['ministry_id'])) {
                $ministry = $this->ministryModel->find($group['ministry_id']);
            }

            // Format meeting time
            if (!empty($group['meeting_time'])) {
                $group['meeting_time'] = date('H:i', strtotime($group['meeting_time']));
            }

            $this->jsonResponse([
                'group' => $group,
                'leaders' => $leaders,
                'ministry' => $ministry
            ]);
        } catch (Exception $e) {
            error_log("[GroupController] Erro ao buscar detalhes do grupo: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->jsonResponse(['error' => 'Erro ao processar a requisição'], 500);
        }
    }

    public function addParticipant(): void {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $data['user_id'] ?? null;
            $groupId = $data['group_id'] ?? null;

            if (!$userId || !$groupId) {
                $this->jsonResponse(['error' => 'Dados inválidos'], 400);
                return;
            }

            // Verificar se o grupo existe
            $group = $this->groupModel->find($groupId);
            if (!$group) {
                $this->jsonResponse(['error' => 'Grupo não encontrado'], 404);
                return;
            }

            // Verificar se o usuário existe
            $user = $this->userModel->find($userId);
            if (!$user) {
                $this->jsonResponse(['error' => 'Usuário não encontrado'], 404);
                return;
            }

            // Verificar se o grupo tem vagas disponíveis
            $participants = $this->groupModel->getParticipants($groupId);
            if (count($participants) >= $group['max_participants']) {
                $this->jsonResponse(['error' => 'Grupo está cheio'], 400);
                return;
            }

            // Adicionar o usuário ao grupo
            if ($this->groupModel->addParticipant($groupId, $userId)) {
                $this->jsonResponse(['success' => true]);
            } else {
                $this->jsonResponse(['error' => 'Erro ao adicionar usuário ao grupo'], 500);
            }

        } catch (\Exception $e) {
            error_log("[GroupController] Erro ao adicionar usuário ao grupo: " . $e->getMessage());
            error_log("[GroupController] Stack trace: " . $e->getTraceAsString());
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function showGroup($id)
    {
        try {
            $group = $this->groupModel->find($id);
            if (!$group) {
                throw new \Exception('Grupo não encontrado');
            }

            // Buscar líderes do grupo
            $leaders = $this->groupModel->getGroupLeaders($id);

            // Buscar membros pendentes e aprovados
            $pendingMembers = $this->groupModel->getMembers('pending');
            $approvedMembers = $this->groupModel->getMembers('approved');

            return $this->view('groups/show', [
                'group' => $group,
                'leaders' => $leaders,
                'pendingMembers' => $pendingMembers,
                'approvedMembers' => $approvedMembers
            ]);

        } catch (\Exception $e) {
            error_log("Erro ao carregar grupo: " . $e->getMessage());
            $this->session->setFlash('error', 'Erro ao carregar grupo');
            return $this->redirect('/groups');
        }
    }

    public function approveMember($groupId, $userId)
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

            $group = $this->groupModel->find($groupId);
            if (!$group) {
                throw new \Exception('Grupo não encontrado');
            }

            if ($group->updateMemberStatus($userId, 'approved')) {
                $this->session->setFlash('success', 'Membro aprovado com sucesso!');
            } else {
                throw new \Exception('Erro ao aprovar membro');
            }

        } catch (\Exception $e) {
            error_log("Erro ao aprovar membro: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect("/groups/{$groupId}");
    }

    public function rejectMember($groupId, $userId)
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

            $group = $this->groupModel->find($groupId);
            if (!$group) {
                throw new \Exception('Grupo não encontrado');
            }

            if ($group->updateMemberStatus($userId, 'rejected')) {
                $this->session->setFlash('success', 'Inscrição rejeitada com sucesso');
            } else {
                throw new \Exception('Erro ao rejeitar inscrição');
            }

        } catch (\Exception $e) {
            error_log("Erro ao rejeitar membro: " . $e->getMessage());
            // $this->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect("/groups/{$groupId}");
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

    public function viewGroup($id): void
    {
        try {
            $group = $this->groupModel->find($id);
            if (!$group) {
                $this->setFlash('error', 'Grupo não encontrado');
                redirect('/groups');
                return;
            }

            // Carregar participantes ativos
            $participants = $this->groupModel->getParticipants($id);
            
            // Carregar reuniões com estatísticas
            $meetings = $this->groupModel->getMeetings($id);

            // Carregar líderes
            $leaders = $this->groupModel->getGroupLeaders($id);

            parent::view('groups/view', [
                'group' => $group,
                'participants' => $participants,
                'meetings' => $meetings,
                'leaders' => $leaders
            ]);

        } catch (\Exception $e) {
            error_log("Error viewing group: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar dados do grupo');
            redirect('/groups');
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
}
