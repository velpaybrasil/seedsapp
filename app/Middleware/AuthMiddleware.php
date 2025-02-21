<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Exceptions\AuthException;

class AuthMiddleware implements Middleware {
    public function handle(): bool {
        // Inicia a sessão se ainda não foi iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            // Salva a URL atual para redirecionamento após o login
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            
            // Redireciona para a página de login
            header('Location: /login');
            exit;
        }

        // Verifica se o usuário tem permissão para acessar a rota atual
        return $this->checkPermission();
    }

    private function checkPermission(): bool {
        // Obtém a rota atual
        $currentRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Rotas que requerem permissão de administrador
        $adminRoutes = [
            '/users',
            '/users/create',
            '/settings',
            '/logs'
        ];

        // Verifica se é uma rota administrativa
        if (in_array($currentRoute, $adminRoutes)) {
            // Verifica se o usuário é administrador
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                http_response_code(403);
                require_once VIEWS_PATH . '/errors/403.php';
                return false;
            }
        }

        return true;
    }
}
