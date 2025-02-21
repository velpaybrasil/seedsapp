<?php

namespace App\Core;

class SessionManager {
    private static bool $initialized = false;

    public static function initialize(): void {
        if (self::$initialized) {
            return;
        }

        // Configurações de sessão
        ini_set('session.name', 'gcmanager_session');
        ini_set('session.cookie_lifetime', 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', 1800); // 30 minutos

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::$initialized = true;
    }

    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void {
        session_destroy();
        self::$initialized = false;
    }
}
