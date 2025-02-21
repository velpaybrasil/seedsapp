<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\User;
use App\Models\Group;
use App\Models\Visitor;

class DashboardController extends Controller {
    private User $userModel;
    private Group $groupModel;
    private Visitor $visitorModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        
        $this->userModel = new User();
        $this->groupModel = new Group();
        $this->visitorModel = new Visitor();
    }

    public function index(): void {
        try {
            // Obtém estatísticas
            $stats = [
                'users' => [
                    'total' => $this->userModel->count(),
                    'active' => $this->userModel->countActiveUsers()
                ],
                'groups' => [
                    'total' => $this->groupModel->count(),
                    'active' => $this->groupModel->countActiveGroups()
                ],
                'visitors' => [
                    'total' => $this->visitorModel->count()
                ]
            ];

            // Obtém os últimos grupos com seus líderes
            $latestGroups = $this->groupModel->getRecentGroups(5);
            foreach ($latestGroups as &$group) {
                $group['created_at'] = date('d/m/Y', strtotime($group['created_at']));
                $group['status'] = isset($group['active']) && $group['active'] ? 'Ativo' : 'Inativo';
                $group['leader'] = isset($group['leader_id']) ? $this->userModel->getNameById($group['leader_id']) : 'Não definido';
            }

            // Obtém os últimos visitantes
            $latestVisitors = $this->visitorModel->getRecentVisitors(5);
            foreach ($latestVisitors as &$visitor) {
                $visitor['created_at'] = date('d/m/Y', strtotime($visitor['created_at']));
                $visitor['group'] = isset($visitor['group_id']) ? $this->groupModel->getGroupName($visitor['group_id']) : 'Não definido';
            }

            // Obtém dados do usuário logado
            $user = $this->getCurrentUser();

            // Renderiza a view com os dados
            View::render('dashboard/index', [
                'user' => $user,
                'stats' => $stats,
                'latestGroups' => $latestGroups,
                'latestVisitors' => $latestVisitors
            ]);

        } catch (\Exception $e) {
            error_log('Erro no dashboard: ' . $e->getMessage());
            $this->setFlashMessage('error', 'Erro ao carregar o dashboard.');
            $this->redirect('/');
        }
    }
}
