<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Visitor;
use App\Models\VisitorContactLog;
use App\Models\GrowthGroup;
use App\Core\Database\Database;
use App\Core\View;
use PDO;
use PDOException;
use Exception;

class VisitorController extends Controller {
    private VisitorContactLog $contactLogModel;
    
    public function __construct() {
        parent::__construct();
        error_log("Initializing VisitorController...");

        // Require authentication for all visitor operations
        $this->requireAuth();

        try {
            $this->contactLogModel = new VisitorContactLog();
            error_log("VisitorController initialized successfully");
        } catch (\Exception $e) {
            error_log("Error initializing VisitorController: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function index() {
        try {
            // Validar e sanitizar parâmetros da URL
            $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
            $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
            $gc_id = filter_input(INPUT_GET, 'gc_id', FILTER_VALIDATE_INT) ?: null;
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $perPage = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT) ?: 10;
            $orderBy = filter_input(INPUT_GET, 'order_by', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'created_at';
            $direction = strtoupper(filter_input(INPUT_GET, 'direction', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'DESC');

            // Garantir valores mínimos
            $page = max(1, $page);
            $perPage = max(1, min(100, $perPage)); // Limita a 100 registros por página

            // Validar parâmetros de ordenação
            $allowedFields = ['name', 'email', 'phone', 'created_at', 'first_visit_date', 'status'];
            if (!in_array($orderBy, $allowedFields)) {
                $orderBy = 'created_at';
            }
            if (!in_array($direction, ['ASC', 'DESC'])) {
                $direction = 'DESC';
            }

            // Preparar filtros
            $filters = [
                'search' => $search,
                'status' => $status,
                'gc_id' => $gc_id
            ];
            
            // Carregar dados em etapas para identificar possíveis falhas
            try {
                $visitors = Visitor::findWithFilters($filters, $page, $perPage, $orderBy, $direction);
                error_log("Successfully loaded visitors: " . count($visitors));
            } catch (\Exception $e) {
                error_log("Error loading visitors: " . $e->getMessage());
                $visitors = [];
            }

            try {
                $total = Visitor::countWithFilters($filters);
                error_log("Total visitors count: {$total}");
            } catch (\Exception $e) {
                error_log("Error counting visitors: " . $e->getMessage());
                $total = 0;
            }

            // Calcular paginação
            $totalPages = ceil($total / $perPage);
            $paginationData = [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalItems' => $total,
                'totalPages' => $totalPages
            ];

            // Carregar estatísticas
            try {
                $stats = [
                    'current_week' => Visitor::countThisWeek(),
                    'current_month' => Visitor::countByPeriod(date('Y'), date('m')),
                    'last_month' => Visitor::countByPeriod(date('Y', strtotime('-1 month')), date('m', strtotime('-1 month'))),
                    'forwarded_to_group' => Visitor::countByStatus('forwarded_to_group'),
                    'current_year' => Visitor::countByPeriod(date('Y'))
                ];
                error_log("Successfully loaded stats");
            } catch (\Exception $e) {
                error_log("Error loading stats: " . $e->getMessage());
                $stats = [];
            }

            // Carregar grupos de crescimento
            try {
                $groups = GrowthGroup::getAllActive();
                error_log("Successfully loaded growth groups");
            } catch (\Exception $e) {
                error_log("Error loading growth groups: " . $e->getMessage());
                $groups = [];
            }

            // Preparar dados para a view
            $data = [
                'pageTitle' => 'Visitantes',
                'visitors' => $visitors,
                'pagination' => $paginationData,
                'filters' => $filters,
                'stats' => $stats,
                'groups' => $groups,
                'orderBy' => $orderBy,
                'direction' => $direction
            ];

            View::render('visitors/index', $data);
        } catch (\Exception $e) {
            error_log("Error in VisitorController::index: " . $e->getMessage());
            $this->setFlash('error', 'Ocorreu um erro ao carregar os visitantes.');
            redirect('/dashboard');
        }
    }

    private function getChartData($period) {
        $labels = [];
        $data = [];
        
        switch ($period) {
            case 'week':
                // Últimos 7 dias
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('d/m', strtotime($date));
                    $visitorModel = new Visitor();
                    $data[] = $visitorModel->countByDate($date);
                }
                break;
                
            case 'month':
                // Últimos 30 dias
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('d/m', strtotime($date));
                    $visitorModel = new Visitor();
                    $data[] = $visitorModel->countByDate($date);
                }
                break;
                
            case 'year':
                // Últimos 12 meses
                for ($i = 11; $i >= 0; $i--) {
                    $month = date('m', strtotime("-$i months"));
                    $year = date('Y', strtotime("-$i months"));
                    $labels[] = date('M/y', strtotime("$year-$month-01"));
                    $visitorModel = new Visitor();
                    $data[] = $visitorModel->countByPeriod($month, $year);
                }
                break;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function create() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validar dados
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'whatsapp' => $_POST['whatsapp'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'neighborhood' => $_POST['neighborhood'] ?? '',
                    'city' => $_POST['city'] ?? '',
                    'state' => $_POST['state'] ?? '',
                    'zipcode' => $_POST['zipcode'] ?? '',
                    'birth_date' => $_POST['birth_date'] ?? null,
                    'marital_status' => $_POST['marital_status'] ?? '',
                    'profession' => $_POST['profession'] ?? '',
                    'first_visit_date' => $_POST['first_visit_date'] ?? date('Y-m-d'),
                    'group_id' => $_POST['group_id'] ?? null,
                    'wants_group' => $_POST['wants_group'] ?? 'no',
                    'notes' => $_POST['notes'] ?? '',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Sanitizar dados
                $data = array_map(function($value) {
                    return $value !== null ? trim($value) : null;
                }, $data);
                
                $data = array_map(function($value) {
                    return $value !== null ? strip_tags($value) : null;
                }, $data);

                // Remover campos vazios
                $data = array_filter($data, function($value) {
                    return $value !== '' && $value !== null;
                });

                // Formatar telefone e CEP
                if (!empty($data['phone'])) {
                    $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
                }
                if (!empty($data['whatsapp'])) {
                    $data['whatsapp'] = preg_replace('/[^0-9]/', '', $data['whatsapp']);
                }
                if (!empty($data['zipcode'])) {
                    $data['zipcode'] = preg_replace('/[^0-9]/', '', $data['zipcode']);
                }

                $visitor = Visitor::create($data);

                if ($visitor) {
                    $this->setFlash('success', 'Visitante cadastrado com sucesso!');
                    redirect('/visitors');
                } else {
                    $this->setFlash('error', 'Erro ao cadastrar visitante.');
                    redirect('/visitors/create');
                }
            }

            // Carregar dados necessários para o formulário
            $groups = GrowthGroup::getAllActive();

            View::render('visitors/create', [
                'pageTitle' => 'Novo Visitante',
                'groups' => $groups
            ]);

        } catch (\Exception $e) {
            error_log("Error in VisitorController::create: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao cadastrar visitante: ' . $e->getMessage());
            redirect('/visitors/create');
        }
    }
    
    public function store() {
        try {
            // Validar CSRF
            if (!$this->validateCSRF()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Token CSRF inválido'
                ], 403);
                return;
            }

            // Validar dados
            if (empty($_POST['name'])) {
                throw new \Exception('O nome é obrigatório');
            }

            // Preparar dados
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'whatsapp' => $_POST['whatsapp'] ?? null,
                'address' => $_POST['address'] ?? null,
                'neighborhood' => $_POST['neighborhood'] ?? null,
                'city' => $_POST['city'] ?? null,
                'state' => $_POST['state'] ?? null,
                'zipcode' => $_POST['zipcode'] ?? null,
                'birth_date' => $_POST['birth_date'] ?? null,
                'marital_status' => $_POST['marital_status'] ?? null,
                'profession' => $_POST['profession'] ?? null,
                'first_visit_date' => $_POST['first_visit_date'] ?? date('Y-m-d'),
                'group_id' => $_POST['group_id'] ?? null,
                'wants_group' => $_POST['wants_group'] ?? 'no',
                'notes' => $_POST['notes'] ?? null,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Sanitizar dados
            $data = array_map(function($value) {
                return $value !== null ? trim($value) : null;
            }, $data);
            
            $data = array_map(function($value) {
                return $value !== null ? strip_tags($value) : null;
            }, $data);

            // Remover campos vazios
            $data = array_filter($data, function($value) {
                return $value !== '' && $value !== null;
            });

            // Formatar telefone e CEP
            if (!empty($data['phone'])) {
                $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
            }
            if (!empty($data['whatsapp'])) {
                $data['whatsapp'] = preg_replace('/[^0-9]/', '', $data['whatsapp']);
            }
            if (!empty($data['zipcode'])) {
                $data['zipcode'] = preg_replace('/[^0-9]/', '', $data['zipcode']);
            }

            $visitor = Visitor::create($data);

            if ($visitor) {
                $this->setFlash('success', 'Visitante cadastrado com sucesso!');
                redirect('/visitors');
            } else {
                throw new \Exception('Erro ao cadastrar visitante');
            }

        } catch (\Exception $e) {
            error_log("Error in VisitorController::store: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao cadastrar visitante: ' . $e->getMessage());
            redirect('/visitors/create');
        }
    }

    public function edit($id) {
        try {
            $visitor = Visitor::find($id);
            
            if (!$visitor) {
                $this->setFlash('error', 'Visitante não encontrado.');
                redirect('/visitors');
            }

            // Carregar grupos de crescimento ativos
            $groups = GrowthGroup::getAllActive();

            // Carregar histórico de grupos
            $groupHistory = Visitor::getGroupHistory($id);

            View::render('visitors/edit', [
                'pageTitle' => 'Editar Visitante',
                'visitor' => $visitor,
                'groups' => $groups,
                'groupHistory' => $groupHistory
            ]);

        } catch (\Exception $e) {
            error_log("Error in VisitorController::edit: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar dados do visitante.');
            redirect('/visitors');
        }
    }
    
    public function update($id) {
        try {
            error_log("[VisitorController] Starting update for visitor ID: " . $id);
            
            // Validar CSRF
            if (!$this->validateCSRF()) {
                error_log("[VisitorController] Invalid CSRF token");
                $this->setFlash('error', 'Token CSRF inválido');
                redirect("/visitors/edit/{$id}");
                return;
            }

            // Validar dados
            if (empty($_POST['name'])) {
                error_log("[VisitorController] Name is required");
                $this->setFlash('error', 'O nome é obrigatório');
                redirect("/visitors/edit/{$id}");
                return;
            }

            // Buscar visitante existente
            $visitor = Visitor::find($id);
            if (!$visitor) {
                error_log("[VisitorController] Visitor not found: " . $id);
                $this->setFlash('error', 'Visitante não encontrado');
                redirect('/visitors');
                return;
            }

            error_log("[VisitorController] POST data: " . print_r($_POST, true));

            // Preparar dados
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'whatsapp' => $_POST['whatsapp'] ?? null,
                'address' => $_POST['address'] ?? null,
                'neighborhood' => $_POST['neighborhood'] ?? null,
                'city' => $_POST['city'] ?? null,
                'state' => $_POST['state'] ?? null,
                'zipcode' => $_POST['zipcode'] ?? null,
                'birth_date' => $_POST['birth_date'] ?? null,
                'first_visit_date' => $_POST['first_visit_date'] ?? null,
                'marital_status' => $_POST['marital_status'] ?? null,
                'profession' => $_POST['profession'] ?? null,
                'group_id' => $_POST['group_id'] ?? null,
                'wants_group' => $_POST['wants_group'] ?? 'no',
                'notes' => $_POST['notes'] ?? null,
                'how_knew_church' => $_POST['how_knew_church'] ?? null,
                'prayer_requests' => $_POST['prayer_requests'] ?? null,
                'observations' => $_POST['observations'] ?? null,
                'status' => $_POST['status'] ?? $visitor['status'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            error_log("[VisitorController] Initial data: " . print_r($data, true));

            // Sanitizar dados
            $data = array_map(function($value) {
                return $value !== null ? trim($value) : null;
            }, $data);
            
            $data = array_map(function($value) {
                return $value !== null ? strip_tags($value) : null;
            }, $data);

            error_log("[VisitorController] Data after sanitization: " . print_r($data, true));

            // Remover campos vazios, exceto os que podem ser vazios
            $data = array_filter($data, function($value, $key) {
                // Campos que podem ser vazios
                $allowEmpty = [
                    'email', 'phone', 'whatsapp', 'address', 'neighborhood', 
                    'city', 'state', 'zipcode', 'birth_date', 'first_visit_date',
                    'marital_status', 'profession', 'group_id', 'notes',
                    'how_knew_church', 'prayer_requests', 'observations',
                    'status', 'updated_at'
                ];
                
                return in_array($key, $allowEmpty) || ($value !== '' && $value !== null);
            }, ARRAY_FILTER_USE_BOTH);

            error_log("[VisitorController] Data after filtering: " . print_r($data, true));

            // Formatar telefone e CEP
            if (!empty($data['phone'])) {
                $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
            }
            if (!empty($data['whatsapp'])) {
                $data['whatsapp'] = preg_replace('/[^0-9]/', '', $data['whatsapp']);
            }
            if (!empty($data['zipcode'])) {
                $data['zipcode'] = preg_replace('/[^0-9]/', '', $data['zipcode']);
            }

            error_log("[VisitorController] Data after formatting: " . print_r($data, true));

            // Verificar se houve mudança de grupo
            $groupChanged = isset($data['group_id']) && $data['group_id'] != $visitor['group_id'];

            // Atualizar visitante
            try {
                $result = Visitor::update($id, $data);
                error_log("[VisitorController] Update result: " . ($result ? "success" : "failed"));
                
                if ($result) {
                    // Se houve mudança de grupo, registrar no histórico
                    if ($groupChanged) {
                        $this->addToGroupHistory($id, $visitor['group_id'], $data['group_id']);
                    }

                    $this->setFlash('success', 'Visitante atualizado com sucesso!');
                    redirect('/visitors');
                } else {
                    throw new \Exception('Erro ao atualizar visitante. Verifique o log para mais detalhes.');
                }
            } catch (\PDOException $e) {
                error_log("[VisitorController] PDO error: " . $e->getMessage());
                error_log("[VisitorController] SQL state: " . $e->getCode());
                error_log("[VisitorController] Error info: " . print_r($e->errorInfo, true));
                throw new \Exception('Erro no banco de dados: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            error_log("[VisitorController] Error updating visitor: " . $e->getMessage());
            error_log("[VisitorController] Data: " . print_r($data ?? [], true));
            error_log("[VisitorController] Stack trace: " . $e->getTraceAsString());
            
            // Em ambiente de desenvolvimento, mostrar o erro completo
            if (defined('APP_ENV') && APP_ENV === 'development') {
                $errorMessage = $e->getMessage() . "\n" . $e->getTraceAsString();
            } else {
                $errorMessage = 'Erro ao atualizar visitante. Por favor, tente novamente.';
            }
            
            $this->setFlash('error', $errorMessage);
            redirect("/visitors/edit/{$id}");
        }
    }

    public function delete($id) {
        try {
            // Verificar permissão para excluir visitantes
            if (!$this->hasPermission('visitors', 'delete')) {
                $this->jsonResponse(['success' => false, 'message' => 'Você não tem permissão para excluir visitantes'], 403);
                return;
            }

            // Verificar se o visitante existe
            $visitor = Visitor::find($id);
            if (!$visitor) {
                $this->jsonResponse(['success' => false, 'message' => 'Visitante não encontrado'], 404);
                return;
            }

            if (Visitor::delete($id)) {
                // Registrar a ação no log
                error_log("Visitor {$id} deleted by user {$_SESSION['user_id']}");
                $this->jsonResponse(['success' => true]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir visitante'], 500);
            }

        } catch (\Exception $e) {
            error_log("Error in VisitorController::delete: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir visitante: ' . $e->getMessage()], 500);
        }
    }

    public function visits($id) {
        $visitor = Visitor::find($id);
        
        if (!$visitor) {
            $this->setFlash('error', 'Visitante não encontrado.');
            redirect('/visitors');
        }
        
        $visits = Visitor::getVisits($id);
        
        View::render('visitors/visits', [
            'visitor' => $visitor,
            'visits' => $visits ?? []
        ]);
    }
    
    public function addVisit($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("/visitors/{$id}");
        }
        
        // Validar campos obrigatórios
        if (empty($_POST['date'])) {
            $this->setFlash('error', 'Data da visita é obrigatória.');
            redirect("/visitors/{$id}");
            return;
        }
        
        $data = [
            'visit_date' => date('Y-m-d', strtotime($_POST['date'])),
            'notes' => !empty($_POST['notes']) ? $_POST['notes'] : null
        ];
        
        if (Visitor::addVisitRecord($id, $data)) {
            $this->setFlash('success', 'Visita registrada com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao registrar visita.');
        }
        
        redirect("/visitors/{$id}");
    }
    
    public function followUps($id) {
        $visitor = Visitor::find($id);
        
        if (!$visitor) {
            $this->setFlash('error', 'Visitante não encontrado.');
            redirect('/visitors');
        }
        
        $followUps = Visitor::getFollowUps($id);
        
        View::render('visitors/follow-ups', [
            'visitor' => $visitor,
            'followUps' => $followUps ?? []
        ]);
    }
    
    public function addFollowUp($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("/visitors/{$id}");
        }
        
        try {
            // Validar campos obrigatórios
            if (empty($_POST['contact_date']) || empty($_POST['type'])) {
                $this->setFlash('error', 'Data e tipo do contato são obrigatórios.');
                redirect("/visitors/{$id}");
                return;
            }
            
            $data = [
                'contact_date' => $_POST['contact_date'],
                'contact_type' => $_POST['type'],
                'notes' => !empty($_POST['notes']) ? $_POST['notes'] : null,
                'next_contact' => !empty($_POST['next_contact']) ? $_POST['next_contact'] : null
            ];
            
            $this->addFollowUpRecord($id, $data);
            $this->setFlash('success', 'Follow-up registrado com sucesso!');
            
        } catch (\Exception $e) {
            error_log("[VisitorController] Erro ao adicionar follow-up: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao registrar follow-up.');
        }
        
        redirect("/visitors/{$id}");
    }

    public function show($id) {
        try {
            // Buscar visitante com detalhes
            $visitor = Visitor::findWithDetails($id);
            if (!$visitor) {
                error_log("[VisitorController] Visitante não encontrado: " . $id);
                $this->setFlash('error', 'Visitante não encontrado.');
                redirect('/visitors');
            }

            // Buscar histórico de grupos
            $groupHistory = Visitor::getGroupHistory($id);
            $visitor['group_history'] = $groupHistory;

            // Buscar formulários associados
            $visitorForms = Visitor::getVisitorForms($id);
            $visitor['forms'] = $visitorForms;

            // Buscar grupos do visitante
            $visitorGroups = Visitor::getVisitorGroups($id);
            $visitor['groups'] = $visitorGroups;

            // Buscar grupos ativos para possível atribuição
            $groups = GrowthGroup::getAllActive();

            View::render('visitors/show', [
                'pageTitle' => 'Detalhes do Visitante',
                'visitor' => $visitor,
                'groups' => $groups
            ]);

        } catch (\Exception $e) {
            error_log("Error in VisitorController::show: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar detalhes do visitante.');
            redirect('/visitors');
        }
    }

    private function addFollowUpRecord(int $visitorId, array $data): void {
        try {
            $sql = "INSERT INTO visitor_follow_ups (visitor_id, contact_date, contact_type, notes, status, next_contact) 
                    VALUES (?, ?, ?, ?, 'pending', ?)";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([
                $visitorId,
                $data['contact_date'],
                $data['contact_type'],
                $data['notes'],
                $data['next_contact']
            ]);
            
            error_log("[VisitorController] Follow-up adicionado para visitante {$visitorId}");
        } catch (\PDOException $e) {
            error_log("[VisitorController] Erro ao adicionar follow-up: " . $e->getMessage());
            throw $e;
        }
    }

    public function dashboard() {
        $period = $_GET['period'] ?? 'month';
        
        $data = [
            'stats' => $this->getStats($period),
            'period' => $period,
            'pendingFollowUps' => $this->getPendingFollowUps()
        ];
        
        View::render('visitors/dashboard', $data);
    }
    
    public function search() {
        try {
            $term = $_GET['term'] ?? '';
            if (strlen($term) < 2) {
                $this->jsonResponse(['error' => 'Termo de busca muito curto'], 400);
                return;
            }

            $visitorModel = new Visitor();
            $visitors = $visitorModel->search($term);
            
            $this->jsonResponse($visitors);
        } catch (\Exception $e) {
            error_log("Erro na busca de visitantes: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erro ao buscar visitantes'], 500);
        }
    }

    public function apiSearch(): void {
        try {
            $searchTerm = $_GET['q'] ?? '';
            
            if (strlen($searchTerm) < 2) {
                $this->jsonResponse(['error' => 'O termo de busca deve ter pelo menos 2 caracteres'], 400);
                return;
            }

            $visitorModel = new Visitor();
            $visitors = $visitorModel->search($searchTerm);
            
            // Formatar os dados para a resposta
            $formattedVisitors = array_map(function($visitor) {
                return [
                    'id' => $visitor['id'],
                    'name' => $visitor['name'],
                    'email' => $visitor['email'] ?? '',
                    'phone' => $visitor['phone'] ?? ''
                ];
            }, $visitors);

            $this->jsonResponse($formattedVisitors);
        } catch (\Exception $e) {
            error_log("[VisitorController] Erro na busca de visitantes: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erro ao buscar visitantes'], 500);
        }
    }

    public function addToGroup() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $visitorId = $data['visitor_id'] ?? null;
            $groupId = $data['group_id'] ?? null;

            if (!$visitorId || !$groupId) {
                $this->jsonResponse(['error' => 'Dados inválidos'], 400);
                return;
            }

            $visitorModel = new Visitor();
            $visitor = $visitorModel->find($visitorId);
            
            if (!$visitor) {
                $this->jsonResponse(['error' => 'Visitante não encontrado'], 404);
                return;
            }

            // Atualizar o grupo do visitante
            $success = $visitorModel->update($visitorId, [
                'group_id' => $groupId,
                'wants_group' => 'yes'
            ]);

            if ($success) {
                $this->jsonResponse(['success' => true]);
            } else {
                $this->jsonResponse(['error' => 'Erro ao adicionar visitante ao grupo'], 500);
            }
        } catch (\Exception $e) {
            error_log("Erro ao adicionar visitante ao grupo: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erro ao adicionar visitante ao grupo'], 500);
        }
    }

    public function addContactLog($visitorId) {
        try {
            if (!$this->isPost()) {
                return $this->jsonResponse(['error' => 'Method not allowed'], 405);
            }

            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            if (empty($content)) {
                return $this->jsonResponse(['error' => 'O conteúdo do contato é obrigatório'], 400);
            }

            $visitorModel = new Visitor();
            $visitor = $visitorModel->find($visitorId);
            if (!$visitor) {
                return $this->jsonResponse(['error' => 'Visitante não encontrado'], 404);
            }

            $logData = [
                'visitor_id' => $visitorId,
                'user_id' => $_SESSION['user_id'],
                'content' => $content,
                'follow_up_date' => filter_input(INPUT_POST, 'follow_up_date'),
                'follow_up_notes' => filter_input(INPUT_POST, 'follow_up_notes', FILTER_SANITIZE_SPECIAL_CHARS),
                'follow_up_status' => 'pending'
            ];

            // Remove follow_up_date se estiver vazio
            if (empty($logData['follow_up_date'])) {
                unset($logData['follow_up_date']);
                unset($logData['follow_up_notes']);
                unset($logData['follow_up_status']);
            }

            $logId = $this->contactLogModel->create($logData);
            
            if ($logId) {
                $log = $this->contactLogModel->findByVisitorId($visitorId)[0];
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Log de contato adicionado com sucesso',
                    'log' => [
                        'id' => $log['id'],
                        'content' => $log['content'],
                        'user_name' => $log['user_name'],
                        'created_at' => $log['created_at'],
                        'follow_up_date' => $log['follow_up_date'] ?? null,
                        'follow_up_notes' => $log['follow_up_notes'] ?? null,
                        'follow_up_status' => $log['follow_up_status'] ?? null
                    ]
                ]);
            }

            return $this->jsonResponse(['error' => 'Erro ao adicionar log de contato'], 500);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function updateFollowUpStatus($visitorId, $logId) {
        try {
            if (!$this->isPost()) {
                return $this->jsonResponse(['error' => 'Method not allowed'], 405);
            }

            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
            if (!in_array($status, ['pending', 'completed', 'cancelled'])) {
                return $this->jsonResponse(['error' => 'Status inválido'], 400);
            }

            // Verificar se o log pertence ao visitante
            $logs = $this->contactLogModel->findByVisitorId($visitorId);
            $logExists = false;
            foreach ($logs as $log) {
                if ($log['id'] == $logId) {
                    $logExists = true;
                    break;
                }
            }

            if (!$logExists) {
                return $this->jsonResponse(['error' => 'Log não encontrado'], 404);
            }

            if ($this->contactLogModel->updateFollowUpStatus($logId, $status)) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Status do follow-up atualizado com sucesso'
                ]);
            }

            return $this->jsonResponse(['error' => 'Erro ao atualizar status do follow-up'], 500);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getContactLogs($visitorId) {
        try {
            $visitorModel = new Visitor();
            $visitor = $visitorModel->find($visitorId);
            if (!$visitor) {
                return $this->jsonResponse(['error' => 'Visitante não encontrado'], 404);
            }

            $logs = $this->contactLogModel->findByVisitorId($visitorId);
            return $this->jsonResponse([
                'success' => true,
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function addToGroupHistory(int $visitorId, int $groupId): void {
        try {
            $sql = "INSERT INTO visitor_group_history (visitor_id, group_id, join_date) 
                    VALUES (?, ?, CURDATE())";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$visitorId, $groupId]);
            
            error_log("[VisitorController] Histórico de grupo adicionado: Visitante {$visitorId} -> Grupo {$groupId}");
        } catch (\PDOException $e) {
            error_log("[VisitorController] Erro ao adicionar histórico de grupo: " . $e->getMessage());
            throw $e;
        }
    }

    private function addGroupChangeLog(int $visitorId, ?int $oldGroupId, int $newGroupId): void {
        try {
            $sql = "INSERT INTO visitor_group_changes (visitor_id, old_group_id, new_group_id, change_date) 
                    VALUES (?, ?, ?, CURDATE())";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$visitorId, $oldGroupId, $newGroupId]);
            
            error_log("[VisitorController] Mudança de grupo registrada: Visitante {$visitorId} -> Grupo {$oldGroupId} para {$newGroupId}");
        } catch (\PDOException $e) {
            error_log("[VisitorController] Erro ao registrar mudança de grupo: " . $e->getMessage());
            throw $e;
        }
    }

    private function getStats($period): array {
        try {
            $visitorModel = new Visitor();
            $stats = [
                'current_week' => $visitorModel->countThisWeek(),
                'current_month' => $visitorModel->countByPeriod(date('Y'), date('m')),
                'last_month' => $visitorModel->countByPeriod(date('Y', strtotime('-1 month')), date('m', strtotime('-1 month'))),
                'forwarded_to_group' => $visitorModel->countByStatus('forwarded_to_group'),
                'current_year' => $visitorModel->countByPeriod(date('Y'))
            ];
            error_log("[VisitorController] Estatísticas obtidas: " . json_encode($stats));
            return $stats;
        } catch (\PDOException $e) {
            error_log("[VisitorController] Erro ao buscar estatísticas: " . $e->getMessage());
            return [
                'total_visitors' => 0,
                'new_visitors' => 0,
                'waiting_group' => 0
            ];
        }
    }

    private function getPendingFollowUps(): array {
        try {
            $sql = "SELECT f.*, v.name as visitor_name, v.phone as visitor_phone
                    FROM visitor_follow_ups f
                    JOIN visitors v ON f.visitor_id = v.id
                    WHERE f.status = 'pending'
                    ORDER BY f.contact_date ASC
                    LIMIT 10";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("[VisitorController] Erro ao buscar follow-ups pendentes: " . $e->getMessage());
            return [];
        }
    }
}
