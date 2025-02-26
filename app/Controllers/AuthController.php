<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\User;

class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function loginForm(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        View::render('auth/login', [
            'title' => 'Login - ' . APP_NAME
        ], 'auth');
    }

    public function login(): void {
        error_log('[AuthController] Iniciando processo de login');
        
        if (!$this->isPost()) {
            $this->redirect('/login');
            return;
        }

        try {
            // Limpa a sessão anterior
            session_regenerate_id(true);
            $_SESSION = array();
            
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            error_log('[AuthController] Dados recebidos - Email: ' . $email);

            // Validação básica
            if (empty($email) || empty($password)) {
                $this->setFlash('error', 'Por favor, preencha todos os campos.');
                $this->redirect('/login');
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('error', 'Por favor, forneça um email válido.');
                $this->redirect('/login');
                return;
            }

            // Tenta autenticar o usuário
            $user = User::validateLogin($email, $password);
            error_log('[AuthController] Resultado da autenticação: ' . ($user ? 'Sucesso' : 'Falha'));

            if (!$user) {
                $this->setFlash('error', 'Email ou senha inválidos.');
                $this->redirect('/login');
                return;
            }

            // Verifica se a conta está ativa
            if (!$user['active']) {
                $this->setFlash('error', 'Esta conta está inativa. Entre em contato com o administrador.');
                $this->redirect('/login');
                return;
            }

            // Verifica se a conta está bloqueada
            if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
                $lockTime = strtotime($user['locked_until']) - time();
                $minutes = ceil($lockTime / 60);
                $this->setFlash('error', "Conta temporariamente bloqueada. Tente novamente em {$minutes} minutos.");
                $this->redirect('/login');
                return;
            }

            error_log('[AuthController] Configurando sessão para o usuário: ' . $user['id']);

            // Busca as permissões do usuário
            $permissions = User::getUserPermissions($user['id']);
            
            // Atualiza o último login
            User::updateLastLogin($user['id']);

            // Define as variáveis de sessão
            $_SESSION = [
                'logged_in' => true,
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_email' => $user['email'],
                'user_permissions' => $permissions,
                'user_theme' => $user['theme'] ?? 'light',
                'CREATED' => time(),
                'LAST_ACTIVITY' => time()
            ];

            error_log('[AuthController] Sessão configurada com sucesso');

            // Se marcou "lembrar-me", gera um token
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                
                User::update($user['id'], ['remember_token' => $hashedToken]);
                
                // Cookie seguro com httponly
                setcookie(
                    'remember_token',
                    $token,
                    [
                        'expires' => time() + (86400 * 30),
                        'path' => '/',
                        'domain' => '',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
                error_log('[AuthController] Token de "lembrar-me" configurado');
            }

            $this->setFlash('success', 'Login realizado com sucesso!');
            $this->redirect('/dashboard');

        } catch (\Exception $e) {
            error_log('[AuthController] Erro no login: ' . $e->getMessage());
            $this->setFlash('error', 'Erro ao realizar login. Por favor, tente novamente.');
            $this->redirect('/login');
        }
    }

    public function logout(): void {
        // Remove o token de "lembrar-me" se existir
        if (isset($_COOKIE['remember_token'])) {
            $this->userModel->update($this->getCurrentUser()['id'], ['remember_token' => null]);
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Limpa a sessão
        session_unset();
        session_destroy();
        session_start();

        $this->setFlash('success', 'Logout realizado com sucesso!');
        $this->redirect('/login');
    }

    public function forgotPasswordForm(): void {
        View::render('auth/forgot-password', [
            'title' => 'Recuperar Senha - ' . APP_NAME
        ], 'auth');
    }

    public function forgotPassword(): void {
        if (!$this->isPost()) {
            $this->redirect('/forgot-password');
        }

        $email = $_POST['email'] ?? '';

        try {
            // Valida o email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Por favor, forneça um email válido.');
                $this->redirect('/forgot-password');
                return;
            }

            // Verifica se o usuário existe
            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                $this->setFlash('danger', 'Não encontramos um usuário com este email.');
                $this->redirect('/forgot-password');
                return;
            }

            // Gera um token de recuperação
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Salva o token no banco
            $this->userModel->update($user['id'], [
                'reset_token' => $token,
                'reset_token_expires' => $expires
            ]);

            // Envia o email
            $resetLink = APP_URL . "/reset-password?token=" . $token;
            $to = $user['email'];
            $subject = "Recuperação de Senha - " . APP_NAME;
            $message = "Olá {$user['name']},\n\n";
            $message .= "Você solicitou a recuperação de sua senha. Clique no link abaixo para criar uma nova senha:\n\n";
            $message .= $resetLink . "\n\n";
            $message .= "Este link é válido por 1 hora.\n\n";
            $message .= "Se você não solicitou esta recuperação, ignore este email.\n\n";
            $message .= "Atenciosamente,\n" . APP_NAME;

            mail($to, $subject, $message);

            $this->setFlash('success', 'Enviamos um email com instruções para recuperar sua senha.');
            $this->redirect('/login');

        } catch (\Exception $e) {
            error_log('Erro na recuperação de senha: ' . $e->getMessage());
            $this->setFlash('danger', 'Erro ao processar sua solicitação. Tente novamente.');
            $this->redirect('/forgot-password');
        }
    }

    public function resetPasswordForm(): void {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->setFlash('danger', 'Token inválido.');
            $this->redirect('/login');
            return;
        }

        View::render('auth/reset-password', [
            'token' => $token,
            'title' => 'Nova Senha - ' . APP_NAME
        ], 'auth');
    }

    public function resetPassword(): void {
        if (!$this->isPost()) {
            $this->redirect('/login');
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        try {
            // Validações básicas
            if (empty($token) || empty($password) || empty($passwordConfirm)) {
                $this->setFlash('danger', 'Todos os campos são obrigatórios.');
                $this->redirect('/reset-password?token=' . $token);
                return;
            }

            if ($password !== $passwordConfirm) {
                $this->setFlash('danger', 'As senhas não coincidem.');
                $this->redirect('/reset-password?token=' . $token);
                return;
            }

            if (strlen($password) < 6) {
                $this->setFlash('danger', 'A senha deve ter pelo menos 6 caracteres.');
                $this->redirect('/reset-password?token=' . $token);
                return;
            }

            // Busca o usuário pelo token
            $user = $this->userModel->findByResetToken($token);
            if (!$user) {
                $this->setFlash('danger', 'Token inválido ou expirado.');
                $this->redirect('/login');
                return;
            }

            // Verifica se o token expirou
            if (strtotime($user['reset_token_expires']) < time()) {
                $this->setFlash('danger', 'Token expirado. Solicite uma nova recuperação de senha.');
                $this->redirect('/forgot-password');
                return;
            }

            // Atualiza a senha
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $this->userModel->update($user['id'], [
                'password' => $hashedPassword,
                'reset_token' => null,
                'reset_token_expires' => null
            ]);

            $this->setFlash('success', 'Senha alterada com sucesso! Faça login com sua nova senha.');
            $this->redirect('/login');

        } catch (\Exception $e) {
            error_log('Erro na redefinição de senha: ' . $e->getMessage());
            $this->setFlash('danger', 'Erro ao redefinir sua senha. Tente novamente.');
            $this->redirect('/reset-password?token=' . $token);
        }
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getCurrentUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return $this->userModel->find($_SESSION['user_id']);
    }

    protected function validateInput(array $data, array $rules): array {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            foreach ($ruleArray as $singleRule) {
                if ($singleRule === 'required' && empty($data[$field])) {
                    $errors[] = "O campo {$field} é obrigatório";
                }
                if ($singleRule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "O campo {$field} deve ser um email válido";
                }
                if (strpos($singleRule, 'min:') === 0) {
                    $min = substr($singleRule, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[] = "O campo {$field} deve ter no mínimo {$min} caracteres";
                    }
                }
            }
        }
        return $errors;
    }
}
