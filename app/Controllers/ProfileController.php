<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\User;
use App\Models\Role;

class ProfileController extends Controller {
    private User $userModel;
    private Role $roleModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
    }
    
    public function showProfile() {
        try {
            // Verificar se o usuário está logado
            if (!isset($_SESSION['user_id'])) {
                $this->setFlash('error', 'Você precisa estar logado para acessar esta página.');
                $this->redirect('/login');
                return;
            }

            // Pegar ID do usuário da sessão
            $userId = $_SESSION['user_id'];
            
            // Buscar dados completos do usuário
            $userDetails = $this->userModel->getUserWithRoles($userId);
            if (!$userDetails) {
                $this->setFlash('error', 'Usuário não encontrado.');
                $this->redirect('/login');
                return;
            }
            
            // Preparar dados do perfil
            $profile = [
                'id' => $userDetails['id'] ?? null,
                'name' => $userDetails['name'] ?? null,
                'email' => $userDetails['email'] ?? null,
                'avatar' => $userDetails['avatar'] ?? null,
                'phone' => $userDetails['phone'] ?? null,
                'birth_date' => $userDetails['birth_date'] ?? null,
                'address' => $userDetails['address'] ?? null,
                'city' => $userDetails['city'] ?? null,
                'state' => $userDetails['state'] ?? null,
                'zip_code' => $userDetails['zip_code'] ?? null,
                'bio' => $userDetails['bio'] ?? null,
                'facebook' => $userDetails['facebook'] ?? null,
                'instagram' => $userDetails['instagram'] ?? null,
                'twitter' => $userDetails['twitter'] ?? null,
                'linkedin' => $userDetails['linkedin'] ?? null,
                'role' => !empty($userDetails['roles']) ? $userDetails['roles'][0]['name'] : 'Membro',
                'joined_date' => $userDetails['created_at'] ?? date('Y-m-d H:i:s')
            ];
            
            // Preparar estatísticas
            $stats = [
                'total_messages' => $this->countUserMessages($userId),
                'total_notifications' => $this->countUserNotifications($userId),
                'total_schedules' => $this->countUserSchedules($userId),
                'total_transactions' => $this->countUserTransactions($userId)
            ];
            
            // Preparar atividades recentes
            $recentActivity = [
                [
                    'type' => 'message',
                    'title' => 'Bem-vindo ao Sistema',
                    'description' => 'Esta é sua primeira atividade no sistema.',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            View::render('settings/profile', [
                'profile' => $profile,
                'stats' => $stats,
                'recentActivity' => $recentActivity,
                'title' => 'Meu Perfil'
            ]);

        } catch (\Exception $e) {
            error_log("[ProfileController] Erro ao carregar perfil: " . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar perfil.');
            $this->redirect('/dashboard');
        }
    }
    
    private function countUserMessages(int $userId): int {
        // TODO: Implementar contagem de mensagens
        return 0;
    }
    
    private function countUserNotifications(int $userId): int {
        // TODO: Implementar contagem de notificações
        return 0;
    }
    
    private function countUserSchedules(int $userId): int {
        // TODO: Implementar contagem de escalas
        return 0;
    }
    
    private function countUserTransactions(int $userId): int {
        // TODO: Implementar contagem de transações
        return 0;
    }
    
    public function updateProfile() {
        try {
            // Verificar se o usuário está logado
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Você precisa estar logado para atualizar seu perfil.');
            }

            // Pegar ID do usuário da sessão
            $userId = $_SESSION['user_id'];
            
            // Validar campos obrigatórios
            if (empty($_POST['name'])) {
                throw new \Exception('O nome é obrigatório.');
            }
            
            // Preparar dados para atualização
            $data = [
                'name' => $_POST['name'],
                'phone' => $_POST['phone'] ?? null,
                'birth_date' => $_POST['birth_date'] ?? null,
                'address' => $_POST['address'] ?? null,
                'city' => $_POST['city'] ?? null,
                'state' => $_POST['state'] ?? null,
                'zip_code' => $_POST['zip_code'] ?? null,
                'bio' => $_POST['bio'] ?? null,
                'facebook' => $_POST['facebook'] ?? null,
                'instagram' => $_POST['instagram'] ?? null,
                'twitter' => $_POST['twitter'] ?? null,
                'linkedin' => $_POST['linkedin'] ?? null,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Atualizar avatar se fornecido
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatar = $this->handleAvatarUpload($_FILES['avatar']);
                if ($avatar) {
                    $data['avatar'] = $avatar;
                }
            }
            
            // Atualizar usuário
            if (!$this->userModel->update($userId, $data)) {
                throw new \Exception('Erro ao atualizar perfil.');
            }
            
            $this->setFlash('success', 'Perfil atualizado com sucesso.');
            
        } catch (\Exception $e) {
            error_log("[ProfileController] Erro ao atualizar perfil: " . $e->getMessage());
            $this->setFlash('error', $e->getMessage());
        }
        
        $this->redirect('/profile');
    }
    
    private function handleAvatarUpload(array $file): ?string {
        try {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowedTypes)) {
                throw new \Exception('Tipo de arquivo não permitido.');
            }
            
            if ($file['size'] > $maxSize) {
                throw new \Exception('Arquivo muito grande. Máximo permitido: 5MB');
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('avatar_') . '.' . $extension;
            $uploadPath = __DIR__ . '/../../public/uploads/avatars/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
                throw new \Exception('Erro ao fazer upload do arquivo.');
            }
            
            return '/uploads/avatars/' . $filename;
            
        } catch (\Exception $e) {
            error_log("[ProfileController] Erro ao fazer upload do avatar: " . $e->getMessage());
            return null;
        }
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
