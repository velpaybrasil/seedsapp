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
                $this->setFlash('warning', 'Por favor, preencha seu email e senha para fazer login.');
                $this->redirect('/login');
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('warning', 'O formato do email informado não é válido. Por favor, verifique e tente novamente.');
                $this->redirect('/login');
                return;
            }

            // Tenta autenticar o usuário
            $user = User::validateLogin($email, $password);
            error_log('[AuthController] Resultado da autenticação: ' . ($user ? 'Sucesso' : 'Falha'));

            if (!$user) {
                $this->setFlash('danger', 'Email ou senha incorretos. Por favor, verifique suas credenciais e tente novamente.');
                $this->redirect('/login');
                return;
            }

            // Verifica se a conta está ativa
            if (!$user['active']) {
                $this->setFlash('warning', 'Sua conta está inativa. Por favor, entre em contato com o administrador do sistema para reativá-la.');
                $this->redirect('/login');
                return;
            }

            // Verifica se a conta está bloqueada
            if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
                $lockTime = strtotime($user['locked_until']) - time();
                $minutes = ceil($lockTime / 60);
                $this->setFlash('danger', "Sua conta está temporariamente bloqueada por motivos de segurança. Você poderá tentar novamente em {$minutes} minutos.");
                $this->redirect('/login');
                return;
            }

            error_log('[AuthController] Configurando sessão para o usuário: ' . $user['id']);

            // Busca as permissões do usuário
            $permissions = User::getUserPermissions($user['id']);
            
            // Busca o papel do usuário
            $role = User::getUserRole($user['id']);
            
            // Atualiza o último login
            User::updateLastLogin($user['id']);

            // Inicia a sessão se ainda não foi iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Regenera o ID da sessão de forma segura
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            // Define as variáveis de sessão
            $_SESSION = [
                'logged_in' => true,
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_email' => $user['email'],
                'user_role' => $role,
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
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        View::render('auth/forgot-password', [
            'title' => 'Recuperar Senha - ' . APP_NAME
        ], 'auth');
    }

    public function forgotPassword(): void {
        if (!$this->isPost()) {
            $this->redirect('/forgot-password');
            return;
        }

        try {
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('warning', 'Por favor, forneça um email válido.');
                $this->redirect('/forgot-password');
                return;
            }

            // Verifica se o usuário existe
            $user = User::findByEmail($email);
            if (!$user) {
                // Por segurança, não informamos se o email existe ou não
                $this->setFlash('success', 'Se o email existir em nossa base, você receberá as instruções para redefinir sua senha.');
                $this->redirect('/forgot-password');
                return;
            }

            // Gera um token único
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Salva o token no banco
            User::saveResetToken($user['id'], $token, $expiry);

            // Envia o email
            $resetLink = APP_URL . "/reset-password?token=" . urlencode($token);
            $to = $email;
            $subject = "Recuperação de Senha - " . APP_NAME;
            $message = "Olá,\n\n";
            $message .= "Você solicitou a recuperação de senha. Clique no link abaixo para redefinir sua senha:\n\n";
            $message .= $resetLink . "\n\n";
            $message .= "Este link é válido por 1 hora.\n\n";
            $message .= "Se você não solicitou esta recuperação, ignore este email.\n\n";
            $message .= "Atenciosamente,\n";
            $message .= APP_NAME;

            $headers = "From: " . APP_EMAIL . "\r\n";
            $headers .= "Reply-To: " . APP_EMAIL . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            mail($to, $subject, $message, $headers);

            $this->setFlash('success', 'Se o email existir em nossa base, você receberá as instruções para redefinir sua senha.');
            $this->redirect('/forgot-password');

        } catch (\Exception $e) {
            error_log('[AuthController] Erro ao processar recuperação de senha: ' . $e->getMessage());
            $this->setFlash('danger', 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.');
            $this->redirect('/forgot-password');
        }
    }

    public function resetPasswordForm(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->setFlash('danger', 'Token de recuperação inválido.');
            $this->redirect('/login');
            return;
        }

        // Verifica se o token é válido e não expirou
        $user = User::findByResetToken($token);
        if (!$user || strtotime($user['reset_token_expires']) < time()) {
            $this->setFlash('danger', 'O link de recuperação é inválido ou expirou.');
            $this->redirect('/login');
            return;
        }

        View::render('auth/reset-password', [
            'title' => 'Redefinir Senha - ' . APP_NAME,
            'token' => $token
        ], 'auth');
    }

    public function resetPassword(): void {
        if (!$this->isPost()) {
            $this->redirect('/login');
            return;
        }

        try {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($token) || empty($password) || empty($passwordConfirm)) {
                $this->setFlash('warning', 'Todos os campos são obrigatórios.');
                $this->redirect("/reset-password?token=" . urlencode($token));
                return;
            }

            if ($password !== $passwordConfirm) {
                $this->setFlash('warning', 'As senhas não coincidem.');
                $this->redirect("/reset-password?token=" . urlencode($token));
                return;
            }

            if (strlen($password) < 8) {
                $this->setFlash('warning', 'A senha deve ter pelo menos 8 caracteres.');
                $this->redirect("/reset-password?token=" . urlencode($token));
                return;
            }

            // Verifica se o token é válido e não expirou
            $user = User::findByResetToken($token);
            if (!$user || strtotime($user['reset_token_expires']) < time()) {
                $this->setFlash('danger', 'O link de recuperação é inválido ou expirou.');
                $this->redirect('/login');
                return;
            }

            // Atualiza a senha e limpa o token
            User::updatePassword($user['id'], password_hash($password, PASSWORD_DEFAULT));
            User::clearResetToken($user['id']);

            $this->setFlash('success', 'Sua senha foi atualizada com sucesso! Você já pode fazer login com sua nova senha.');
            $this->redirect('/login');

        } catch (\Exception $e) {
            error_log('[AuthController] Erro ao redefinir senha: ' . $e->getMessage());
            $this->setFlash('danger', 'Ocorreu um erro ao redefinir sua senha. Por favor, tente novamente.');
            $this->redirect('/login');
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
