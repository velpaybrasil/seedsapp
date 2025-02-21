<?php

namespace App\Middleware;

use App\Core\Middleware;

class CsrfMiddleware implements Middleware {
    public function handle(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Apenas verifica o token em requisições POST, PUT, DELETE
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Tenta pegar o token do header ou do POST
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? null;
            
            if (!$token || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
                exit;
            }
        }

        return true;
    }
}
