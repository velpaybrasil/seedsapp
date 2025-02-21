<?php

if (!function_exists('base_path')) {
    function base_path(): string {
        return ROOT_PATH;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        $path = trim($path, '/');
        return APP_URL . '/public/' . $path;
    }
}

if (!function_exists('url')) {
    function url($path = ''): string {
        $baseUrl = rtrim(APP_URL, '/');
        $path = ltrim($path, '/');
        return $path ? "{$baseUrl}/{$path}" : $baseUrl;
    }
}

if (!function_exists('redirect')) {
    function redirect($path) {
        $url = url($path);
        error_log('Redirecting to: ' . $url);
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('setFlash')) {
    function setFlash(string $type, string $message): void {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

if (!function_exists('getFlash')) {
    function getFlash(): array {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
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

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('is_authenticated')) {
    function is_authenticated(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array {
        if (!is_authenticated()) {
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

if (!function_exists('error')) {
    function error(string $key): ?string {
        if (!isset($_SESSION['errors'])) {
            return null;
        }
        $error = $_SESSION['errors'][$key] ?? null;
        unset($_SESSION['errors'][$key]);
        return $error;
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string {
        return '<input type="hidden" name="_method" value="' . $method . '">';
    }
}

if (!function_exists('flash')) {
    function flash(string $key = 'message'): ?string {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $key, $value): void {
        $_SESSION['flash'][$key] = $value;
    }
}

if (!function_exists('auth')) {
    function auth(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
}

if (!function_exists('user')) {
    function user(): ?array {
        if (!auth()) {
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
        $user = user();
        return $user !== null && $user['role'] === 'admin';
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

if (!function_exists('str_slug')) {
    function str_slug(string $text): string {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        $text = trim($text, '-');

        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // Lowercase
        $text = strtolower($text);

        return $text;
    }
}

if (!function_exists('str_limit')) {
    function str_limit(string $text, int $limit = 100): string {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $limit)) . '...';
    }
}

if (!function_exists('is_active')) {
    function is_active(string $path): string {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $currentPath === '/' . ltrim($path, '/') ? 'active' : '';
    }
}
