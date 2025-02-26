<?php

namespace App\Core;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configura cookies seguros
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            session_start();

            // Regenera o ID da sessão se ela for antiga
            if (!isset($_SESSION['CREATED'])) {
                $_SESSION['CREATED'] = time();
            } else if (time() - $_SESSION['CREATED'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['CREATED'] = time();
            }

            // Atualiza o timestamp da última atividade
            $_SESSION['LAST_ACTIVITY'] = time();
        }
    }

    public function isValid(): bool
    {
        if (!isset($_SESSION['LAST_ACTIVITY'])) {
            return false;
        }

        // Sessão expira após 30 minutos de inatividade
        if (time() - $_SESSION['LAST_ACTIVITY'] > 1800) {
            $this->destroy();
            return false;
        }

        // Atualiza o timestamp
        $_SESSION['LAST_ACTIVITY'] = time();
        return true;
    }

    public function setFlash(string $type, mixed $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        if ($type === 'errors' || $type === 'old') {
            $_SESSION['flash'][$type] = $message;
        } else {
            $_SESSION['flash'] = [
                'type' => $type,
                'message' => $message
            ];
        }
    }

    public function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        // Remove todos os dados da sessão
        $_SESSION = array();

        // Destrói o cookie da sessão
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        // Destrói a sessão
        session_destroy();
    }
}
