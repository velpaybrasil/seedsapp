<?php

namespace App\Controllers;

use App\Models\Ministry;
use App\Core\Controller;
use App\Core\View;
use App\Models\User;
use Exception;

class MinistryController extends Controller {
    private $ministryModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }
    
    public function index() {
        error_log("[MinistryController] Iniciando index");
        
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null
        ];
        
        error_log("[MinistryController] Filtros: " . print_r($filters, true));
        
        $ministries = Ministry::getAllWithFilters($filters);
        error_log("[MinistryController] Ministérios retornados: " . print_r($ministries, true));
        
        $data = [
            'pageTitle' => 'Ministérios',
            'ministries' => $ministries,
            'filters' => $filters
        ];
        
        error_log("[MinistryController] Dados para view: " . print_r($data, true));
        
        View::render('ministries/index', $data);
    }
    
    public function create() {
        $userModel = new User();
        $users = $userModel->all();
        
        View::render('ministries/create', [
            'pageTitle' => 'Novo Ministério',
            'users' => $users
        ]);
    }
    
    public function store() {
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? null,
            'active' => isset($_POST['status']) ? (bool)$_POST['status'] : true,
            'leaders' => []
        ];
        
        if (empty($data['name'])) {
            $this->setFlash('error', 'O nome do ministério é obrigatório.');
            return $this->create();
        }
        
        // Process leaders if provided
        if (isset($_POST['leaders']) && is_array($_POST['leaders'])) {
            foreach ($_POST['leaders'] as $leader) {
                if (!empty($leader['user_id'])) {
                    $data['leaders'][] = [
                        'user_id' => $leader['user_id'],
                        'role' => $leader['role'] ?? 'leader'
                    ];
                }
            }
        }
        
        $id = Ministry::create($data);
        
        if ($id) {
            $this->setFlash('success', 'Ministério criado com sucesso!');
            redirect('/ministries');
        } else {
            $this->setFlash('error', 'Erro ao criar ministério.');
            $userModel = new User();
            View::render('ministries/create', [
                'pageTitle' => 'Novo Ministério',
                'data' => $data,
                'users' => $userModel->all()
            ]);
        }
    }
    
    public function edit($id) {
        $ministry = Ministry::find($id);
        
        if (!$ministry) {
            $this->setFlash('error', 'Ministério não encontrado!');
            redirect('/ministries');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? null,
                'leader_id' => $_POST['leader_id'],
                'co_leader_id' => $_POST['co_leader_id'] ?? null,
                'meeting_info' => $_POST['meeting_info'] ?? null,
                'requirements' => $_POST['requirements'] ?? null,
                'status' => $_POST['status']
            ];
            
            if (Ministry::update($id, $data)) {
                $this->setFlash('success', 'Ministério atualizado com sucesso!');
                redirect('/ministries');
            } else {
                View::render('ministries/edit', [
                    'pageTitle' => 'Editar Ministério',
                    'ministry' => array_merge($ministry, $data)
                ]);
            }
        } else {
            View::render('ministries/edit', [
                'pageTitle' => 'Editar Ministério',
                'ministry' => $ministry
            ]);
        }
    }
    
    public function delete($id) {
        try {
            if (Ministry::delete($id)) {
                $this->setFlash('success', 'Ministério excluído com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao excluir ministério.');
            }
        } catch (Exception $e) {
            error_log("Erro ao excluir ministério: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao excluir ministério.');
        }
        
        redirect('/ministries');
    }
    
    public function viewDetails($id) {
        $ministry = Ministry::find($id);
        
        if (!$ministry) {
            $this->setFlash('error', 'Ministério não encontrado!');
            redirect('/ministries');
        }
        
        $data = [
            'pageTitle' => 'Detalhes do Ministério',
            'ministry' => $ministry,
            'volunteers' => Ministry::getVolunteers($id, ['active' => 1], ['name' => 'ASC']),
            'schedules' => Ministry::getSchedules($id, ['future' => true], ['event_date' => 'ASC'])
        ];
        
        $this->view('ministries/view', $data);
    }
    
    public function addVolunteer($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_POST['user_id'],
                'skills' => $_POST['skills'] ?? null,
                'joined_at' => $_POST['joined_at'] ?? date('Y-m-d'),
                'role' => $_POST['role'] ?? 'volunteer'
            ];
            
            if (Ministry::addVolunteer($id, $data, $_POST['role'] ?? 'volunteer')) {
                $this->setFlash('success', 'Voluntário adicionado com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao adicionar voluntário.');
            }
            
            redirect("/ministries/view/{$id}");
        }
    }
    
    public function removeVolunteer($ministryId, $userId) {
        try {
            if (Ministry::removeVolunteer($ministryId, $userId)) {
                $this->setFlash('success', 'Voluntário removido com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao remover voluntário.');
            }
        } catch (Exception $e) {
            error_log("Erro ao remover voluntário: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao remover voluntário.');
        }
        
        redirect("/ministries/view/{$ministryId}");
    }
    
    public function addSchedule($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'event_date' => $_POST['event_date'],
                'event_time' => $_POST['event_time'],
                'description' => $_POST['description'] ?? null,
                'volunteers_needed' => $_POST['volunteers_needed'] ?? null,
                'notes' => $_POST['notes'] ?? null
            ];
            
            if (Ministry::addSchedule($id, $data)) {
                $this->setFlash('success', 'Escala adicionada com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao adicionar escala.');
            }
            
            redirect("/ministries/view/{$id}");
        }
    }
    
    public function removeSchedule($ministryId, $scheduleId) {
        try {
            if (Ministry::removeSchedule($ministryId, $scheduleId)) {
                $this->setFlash('success', 'Escala removida com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao remover escala.');
            }
        } catch (Exception $e) {
            error_log("Erro ao remover escala: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao remover escala.');
        }
        
        redirect("/ministries/view/{$ministryId}");
    }
    
    public function assignVolunteer($ministryId, $scheduleId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'volunteer_id' => $_POST['volunteer_id'],
                'role' => $_POST['role'] ?? null,
                'notes' => $_POST['notes'] ?? null
            ];
            
            if (Ministry::assignVolunteer($ministryId, $scheduleId, $data)) {
                $this->setFlash('success', 'Voluntário designado com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao designar voluntário.');
            }
            
            redirect("/ministries/view/{$ministryId}");
        }
    }
    
    public function dashboard() {
        $period = $_GET['period'] ?? 'month';
        
        $data = [
            'pageTitle' => 'Dashboard de Ministérios',
            'stats' => Ministry::getStats($period),
            'period' => $period,
            'activeMinistries' => Ministry::findAll(['status' => 'active']),
            'upcomingSchedules' => Ministry::getUpcomingSchedules()
        ];
        
        View::render('ministries/dashboard', $data);
    }
}
