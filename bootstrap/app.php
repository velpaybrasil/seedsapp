<?php

// Debug
error_log('Bootstrapping application...');

// Start output buffering
ob_start();

// Configuração de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/storage/logs/error.log');

// Configuração de sessão (deve ser feita antes de iniciar a sessão)
ini_set('session.name', 'gcmanager_session');
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 1800); // 30 minutos

// Define o diretório raiz se ainda não estiver definido
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
    error_log("ROOT_PATH defined as: " . ROOT_PATH);
}

// Carrega o autoloader do Composer
require_once ROOT_PATH . '/vendor/autoload.php';
error_log("Composer autoloader loaded");

// Carrega o arquivo de helpers
require_once ROOT_PATH . '/app/helpers.php';
error_log("Helpers loaded");

// Carrega as constantes da aplicação se ainda não foram carregadas
if (!defined('APP_NAME')) {
    require_once ROOT_PATH . '/app/constants.php';
    error_log("Constants loaded");
}

// Inicializa o View com o VIEWS_PATH
\App\Core\View::init(VIEWS_PATH);
error_log("View initialized with VIEWS_PATH: " . VIEWS_PATH);

// Inicia a sessão antes de qualquer output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_log("Session started");
    
    // Gera um novo token CSRF se não existir
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        error_log("CSRF token generated");
    }
}

// Configuração de segurança para headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Strict Transport Security em produção
if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Configurações do banco de dados
try {
    // Obtém a conexão do banco de dados
    $db = \App\Core\Database::getConnection();
    error_log("Database connection established");
} catch (\PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}

// Configuração de localização
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil');
error_log("Locale configured");

error_log("Bootstrap completed successfully");
