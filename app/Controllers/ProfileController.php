<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\User;

class ProfileController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function showProfile() {
        try {
            $user = auth();
            
            if (!$user) {
                $this->setFlash('error', 'Usuário não encontrado.');
                $this->redirect('/dashboard');
                return;
            }
            
            // Get user details from database to ensure we have fresh data
            $userDetails = $this->userModel->find($user['id']);
            
            // Prepare profile data
            $profile = [
                'id' => $userDetails['id'],
                'name' => $userDetails['name'],
                'email' => $userDetails['email'],
                'avatar' => $userDetails['avatar'] ?? null,
                'role' => $userDetails['role'] ?? 'Membro',
                'joined_date' => $userDetails['created_at']
            ];
            
            // Prepare stats data
            $stats = [
                'total_messages' => 0, // TODO: Implement message counting
                'total_notifications' => 0, // TODO: Implement notification counting
                'total_schedules' => 0, // TODO: Implement schedule counting
                'total_transactions' => 0 // TODO: Implement transaction counting
            ];
            
            // Prepare recent activity data (placeholder)
            $recentActivity = [
                [
                    'type' => 'message',
                    'title' => 'Bem-vindo ao Sistema',
                    'description' => 'Esta é sua primeira atividade no sistema.',
                    'created_at' => date('Y-m-d H:i:s'),
                    'icon' => 'fas fa-envelope'
                ]
            ];
            
            View::render('settings/profile', [
                'profile' => $profile,
                'stats' => $stats,
                'recentActivity' => $recentActivity,
                'title' => 'Meu Perfil'
            ]);
        } catch (\Exception $e) {
            error_log('Erro ao carregar perfil: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar perfil.');
            $this->redirect('/dashboard');
        }
    }
    
    public function updateProfile() {
        try {
            $user = auth();
            if (!$user) {
                throw new \Exception('Usuário não encontrado.');
            }
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? null
            ];
            
            // Validar campos obrigatórios
            if (empty($data['name']) || empty($data['email'])) {
                throw new \Exception('Nome e e-mail são obrigatórios.');
            }
            
            // Atualizar usuário
            $success = $this->userModel->update($user['id'], $data);
            
            if ($success) {
                $this->setFlash('success', 'Perfil atualizado com sucesso.');
            } else {
                $this->setFlash('error', 'Erro ao atualizar perfil.');
            }
            
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }
        
        $this->redirect('/profile');
    }
    
    public function showSettings() {
        try {
            $userId = $this->getCurrentUserId();
            $user = $this->userModel->find($userId);
            
            if (!$user) {
                $this->setFlash('error', 'Usuário não encontrado.');
                $this->redirect('/dashboard');
                return;
            }
            
            // Definir valores padrão para campos de configuração
            $user['theme'] = $user['theme'] ?? 'light';
            $user['notifications_enabled'] = $user['notifications_enabled'] ?? 1;
            $user['email_notifications'] = $user['email_notifications'] ?? 1;
            
            View::render('profile/settings', [
                'user' => $user,
                'title' => 'Configurações'
            ]);
        } catch (\Exception $e) {
            error_log('Erro ao carregar configurações: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar configurações.');
            $this->redirect('/dashboard');
        }
    }
    
    public function updateSettings() {
        try {
            $userId = $this->getCurrentUserId();
            
            $data = [
                'theme' => $_POST['theme'] ?? 'light',
                'notifications_enabled' => isset($_POST['notifications_enabled']) ? 1 : 0,
                'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0
            ];
            
            if ($this->userModel->updateSettings($userId, $data)) {
                $this->setFlash('success', 'Configurações atualizadas com sucesso!');
            } else {
                throw new \Exception('Erro ao atualizar as configurações.');
            }
            
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }
        
        $this->redirect('/settings');
    }
}
