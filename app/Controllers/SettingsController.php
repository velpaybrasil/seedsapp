<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\UserProfile;
use App\Models\User;

class SettingsController extends Controller {
    private UserProfile $profileModel;
    private User $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->profileModel = new UserProfile();
        $this->userModel = new User();
    }
    
    public function index(): void {
        try {
            $user = auth();
            if (!$user) {
                $this->setFlash('error', 'Usuário não encontrado.');
                $this->redirect('/dashboard');
                return;
            }

            View::render('settings/index', [
                'title' => 'Configurações',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            error_log('Erro ao carregar configurações: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao carregar configurações.');
            $this->redirect('/dashboard');
        }
    }
    
    public function profile(): void {
        $this->requireAuth();
        
        $userId = $this->getCurrentUserId();
        $profile = $this->profileModel->getProfile($userId);
        $stats = $this->profileModel->getProfileStats($userId);
        $recentActivity = $this->profileModel->getRecentActivity($userId);
        
        $this->view('settings/profile', [
            'profile' => $profile,
            'stats' => $stats,
            'recentActivity' => $recentActivity
        ]);
    }
    
    public function updateProfile(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $userId = $this->getCurrentUserId();
            $errors = $this->validateProfileData($data);
            
            if (empty($errors)) {
                try {
                    // Handle avatar upload if present
                    if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                        $avatarPath = $this->handleAvatarUpload($_FILES['avatar'], $userId);
                        if ($avatarPath) {
                            $data['avatar'] = $avatarPath;
                        }
                    }
                    
                    $this->profileModel->updateProfile($userId, $data);
                    
                    // Update user table fields if present
                    if (!empty($data['name'])) {
                        $this->userModel->update($userId, ['name' => $data['name']]);
                    }
                    
                    $this->setFlash('success', 'Perfil atualizado com sucesso!');
                    $this->redirect('/settings/profile');
                    return;
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao atualizar perfil: ' . $e->getMessage();
                }
            }
            
            $this->setFlash('errors', $errors);
        }
        
        $this->redirect('/settings/profile');
    }
    
    public function security(): void {
        $this->requireAuth();
        
        $userId = $this->getCurrentUserId();
        $profile = $this->profileModel->getProfile($userId);
        
        $this->view('settings/security', [
            'profile' => $profile
        ]);
    }
    
    public function updatePassword(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $userId = $this->getCurrentUserId();
            $errors = $this->validatePasswordUpdate($data, $userId);
            
            if (empty($errors)) {
                try {
                    $this->profileModel->updatePassword($userId, $data['new_password']);
                    $this->setFlash('success', 'Senha atualizada com sucesso!');
                    $this->redirect('/settings/security');
                    return;
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao atualizar senha: ' . $e->getMessage();
                }
            }
            
            $this->setFlash('errors', $errors);
        }
        
        $this->redirect('/settings/security');
    }
    
    public function updateEmail(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $userId = $this->getCurrentUserId();
            $errors = $this->validateEmailUpdate($data, $userId);
            
            if (empty($errors)) {
                try {
                    $this->profileModel->updateEmail($userId, $data['new_email']);
                    $this->setFlash('success', 'E-mail atualizado com sucesso!');
                    $this->redirect('/settings/security');
                    return;
                } catch (\Exception $e) {
                    $errors[] = 'Erro ao atualizar e-mail: ' . $e->getMessage();
                }
            }
            
            $this->setFlash('errors', $errors);
        }
        
        $this->redirect('/settings/security');
    }
    
    public function preferences(): void {
        $this->requireAuth();
        
        $userId = $this->getCurrentUserId();
        $profile = $this->profileModel->getProfile($userId);
        $defaultPreferences = $this->profileModel->getDefaultPreferences();
        
        $this->view('settings/preferences', [
            'profile' => $profile,
            'defaultPreferences' => $defaultPreferences
        ]);
    }
    
    public function updatePreferences(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $preferences = $this->getPostData();
            $userId = $this->getCurrentUserId();
            
            try {
                $this->profileModel->updatePreferences($userId, $preferences);
                $this->setFlash('success', 'Preferências atualizadas com sucesso!');
            } catch (\Exception $e) {
                $this->setFlash('error', 'Erro ao atualizar preferências: ' . $e->getMessage());
            }
        }
        
        $this->redirect('/settings/preferences');
    }
    
    private function validateProfileData(array $data): array {
        $errors = [];
        
        if (!empty($data['phone']) && !preg_match('/^\(\d{2}\) \d{5}-\d{4}$/', $data['phone'])) {
            $errors[] = 'Telefone inválido. Use o formato (99) 99999-9999';
        }
        
        if (!empty($data['postal_code']) && !preg_match('/^\d{5}-\d{3}$/', $data['postal_code'])) {
            $errors[] = 'CEP inválido. Use o formato 99999-999';
        }
        
        if (!empty($data['birth_date'])) {
            $birthDate = \DateTime::createFromFormat('Y-m-d', $data['birth_date']);
            if (!$birthDate || $birthDate > new \DateTime()) {
                $errors[] = 'Data de nascimento inválida';
            }
        }
        
        return $errors;
    }
    
    private function validatePasswordUpdate(array $data, int $userId): array {
        $errors = [];
        
        if (empty($data['current_password'])) {
            $errors[] = 'A senha atual é obrigatória';
        } elseif (!$this->profileModel->validatePassword($userId, $data['current_password'])) {
            $errors[] = 'Senha atual incorreta';
        }
        
        if (empty($data['new_password'])) {
            $errors[] = 'A nova senha é obrigatória';
        } elseif (strlen($data['new_password']) < 8) {
            $errors[] = 'A nova senha deve ter pelo menos 8 caracteres';
        }
        
        if ($data['new_password'] !== ($data['confirm_password'] ?? '')) {
            $errors[] = 'As senhas não conferem';
        }
        
        return $errors;
    }
    
    private function validateEmailUpdate(array $data, int $userId): array {
        $errors = [];
        
        if (empty($data['new_email'])) {
            $errors[] = 'O novo e-mail é obrigatório';
        } elseif (!filter_var($data['new_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail inválido';
        } elseif ($this->userModel->findByEmail($data['new_email'])) {
            $errors[] = 'Este e-mail já está em uso';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'A senha é obrigatória';
        } elseif (!$this->profileModel->validatePassword($userId, $data['password'])) {
            $errors[] = 'Senha incorreta';
        }
        
        return $errors;
    }
    
    private function handleAvatarUpload(array $file, int $userId): ?string {
        try {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            // Validate file type
            if (!in_array($file['type'], $allowedTypes)) {
                $this->setFlash('error', 'Tipo de arquivo não permitido. Use JPG, PNG ou GIF.');
                return null;
            }
            
            // Validate file size
            if ($file['size'] > $maxSize) {
                $this->setFlash('error', 'O arquivo é muito grande. Tamanho máximo: 5MB');
                return null;
            }
            
            // Validate if file was actually uploaded
            if (!is_uploaded_file($file['tmp_name'])) {
                error_log('Tentativa de upload inválido detectada');
                $this->setFlash('error', 'Upload inválido detectado');
                return null;
            }
            
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = "avatar_{$userId}_" . time() . "." . $extension;
            $uploadDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    error_log("Erro ao criar diretório de upload: $uploadDir");
                    $this->setFlash('error', 'Erro ao criar diretório de upload');
                    return null;
                }
                // Ensure directory has correct permissions
                chmod($uploadDir, 0777);
            }
            
            $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
            
            // Remove old avatar if exists
            $oldAvatar = $this->profileModel->getProfile($userId)['avatar'] ?? null;
            if ($oldAvatar) {
                $oldPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'public' . $oldAvatar;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                error_log("Erro ao mover arquivo para: $targetPath");
                $this->setFlash('error', 'Erro ao salvar o avatar. Por favor, tente novamente.');
                return null;
            }
            
            // Set correct permissions for the uploaded file
            chmod($targetPath, 0644);
            
            return '/uploads/avatars/' . $filename;
            
        } catch (\Exception $e) {
            error_log('Erro ao fazer upload do avatar: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao processar o upload do avatar');
            return null;
        }
    }
}
