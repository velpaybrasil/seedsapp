<?php

// Diretório raiz da aplicação
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Diretório público
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}

// Diretório de views
if (!defined('VIEWS_PATH')) {
    define('VIEWS_PATH', ROOT_PATH . '/app/Views');
}

// Diretório de storage
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', ROOT_PATH . '/storage');
}

// Diretório de logs
if (!defined('LOGS_PATH')) {
    define('LOGS_PATH', STORAGE_PATH . '/logs');
}

// Diretório de uploads
if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', STORAGE_PATH . '/uploads');
}

// URL base da aplicação
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . $host . '/seedsapp');
}

// Verifica e cria os diretórios necessários
$directories = [
    STORAGE_PATH,
    LOGS_PATH,
    UPLOADS_PATH
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
}

// Funções auxiliares
if (!function_exists('url')) {
    function url($path = '') {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        return url('public/' . ltrim($path, '/'));
    }
}

if (!function_exists('view_path')) {
    function view_path($view) {
        return VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
    }
}
