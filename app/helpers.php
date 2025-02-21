<?php

use App\Core\Router;

if (!function_exists('asset')) {
    function asset(string $path): string {
        $basePath = Router::getBasePath();
        return $basePath . '/public/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path): string {
        $basePath = Router::getBasePath();
        return $basePath . '/' . trim($path, '/');
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = ''): mixed {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_meta')) {
    function csrf_meta(): string {
        return '<meta name="csrf-token" content="' . csrf_token() . '">';
    }
}

if (!function_exists('format_date')) {
    function format_date(string $date, string $format = 'd/m/Y'): string {
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_money')) {
    function format_money(float $value): string {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('is_active')) {
    function is_active(string $path): string {
        $currentPath = $_SERVER['REQUEST_URI'] ?? '/';
        $basePath = Router::getBasePath();
        
        // Remove o basePath da URI atual se ele existir
        if ($basePath && strpos($currentPath, $basePath) === 0) {
            $currentPath = substr($currentPath, strlen($basePath));
        }
        
        return strpos($currentPath, $path) === 0 ? 'active' : '';
    }
}

if (!function_exists('flash')) {
    function flash(string $message, string $type = 'success'): void {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        // Clear any output that might have been sent
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        $url = url($path);
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('back')) {
    function back(): void {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: {$referer}");
        exit;
    }
}

if (!function_exists('auth')) {
    function auth(): ?array {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool {
        $user = auth();
        return $user !== null && $user['role'] === 'admin';
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        static $config = [];

        if (empty($config)) {
            $files = glob(CONFIG_PATH . '/*.php');
            foreach ($files as $file) {
                $name = basename($file, '.php');
                $config[$name] = require $file;
            }
        }

        $parts = explode('.', $key);
        $value = $config;

        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }
}

if (!function_exists('formatPhoneForWhatsApp')) {
    /**
     * Format a phone number for WhatsApp API
     * Removes all non-numeric characters and adds country code if not present
     * 
     * @param string $phone Phone number to format
     * @return string Formatted phone number for WhatsApp API
     */
    function formatPhoneForWhatsApp($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If number starts with 0, remove it
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }
        
        // If number doesn't start with country code (55 for Brazil), add it
        if (substr($phone, 0, 2) !== '55') {
            $phone = '55' . $phone;
        }
        
        return $phone;
    }
}

if (!function_exists('hasPermission')) {
    /**
     * Check if the current user has a specific permission
     * 
     * @param string $module Module name or 'admin' for admin check
     * @param string $permission Permission name (view, create, edit, delete)
     * @return bool
     */
    function hasPermission(string $module = 'admin', string $permission = 'view'): bool {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if ($module === 'admin' && isset($_SESSION['user_role'])) {
            return $_SESSION['user_role'] === 'admin';
        }

        $userModel = new \App\Models\User();
        return $userModel->hasPermission($_SESSION['user_id'], $module, $permission);
    }
}
