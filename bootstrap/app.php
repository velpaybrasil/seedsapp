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

// Carrega o arquivo .env
use App\Core\Env;
use App\Core\View;

$envFile = ROOT_PATH . '/.env';
if (!file_exists($envFile)) {
    error_log(".env file not found at: " . $envFile);
    die('.env file not found. Please create one based on .env.example');
}

try {
    Env::load($envFile);
    error_log("Environment file loaded successfully");
} catch (\Exception $e) {
    error_log("Error loading environment file: " . $e->getMessage());
    die("Error loading environment file: " . $e->getMessage());
}

// Define constantes se ainda não estiverem definidas
if (!defined('APP_NAME')) {
    define('APP_NAME', Env::get('APP_NAME', 'GC Manager'));
}

if (!defined('APP_ENV')) {
    define('APP_ENV', Env::get('APP_ENV', 'production'));
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', Env::get('APP_DEBUG', 'false') === 'true');
}

if (!defined('APP_URL')) {
    define('APP_URL', Env::get('APP_URL', 'https://igrejamodelo.alfadev.online'));
}

// Define a BASE_URL
if (!defined('BASE_URL')) {
    $baseUrl = rtrim(APP_URL, '/');
    define('BASE_URL', $baseUrl);
    error_log("BASE_URL defined as: " . BASE_URL);
}

// Define caminhos se ainda não estiverem definidos
if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', ROOT_PATH . '/storage');
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}

if (!defined('VIEWS_PATH')) {
    define('VIEWS_PATH', ROOT_PATH . '/views');
    error_log("VIEWS_PATH defined as: " . VIEWS_PATH);
}

// Inicializa a View
View::init(VIEWS_PATH);
error_log("View initialized with path: " . VIEWS_PATH);

// Configurações do banco de dados
try {
    $dbConfig = require_once ROOT_PATH . '/config/database.php';
    
    if (!defined('DB_HOST')) define('DB_HOST', $dbConfig['default']['host'] ?? 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', $dbConfig['default']['database'] ?? 'u315624178_gcmanager');
    if (!defined('DB_USER')) define('DB_USER', $dbConfig['default']['username'] ?? 'u315624178_gcmanager');
    if (!defined('DB_PASS')) define('DB_PASS', $dbConfig['default']['password'] ?? 'gugaLima8*');
    
    error_log("Database configuration loaded successfully");
} catch (Exception $e) {
    error_log("Error loading database configuration: " . $e->getMessage());
    die("Error loading database configuration. Please check your settings.");
}

// Verifica e cria diretórios necessários
$directories = [
    STORAGE_PATH . '/logs',
    STORAGE_PATH . '/cache',
    STORAGE_PATH . '/uploads',
    PUBLIC_PATH . '/assets',
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        if (mkdir($directory, 0755, true)) {
            error_log("Created directory: " . $directory);
        } else {
            error_log("Failed to create directory: " . $directory);
        }
    }
}

// Configuração de timezone
date_default_timezone_set('America/Sao_Paulo');
error_log("Timezone set to: America/Sao_Paulo");

// Configuração de locale
setlocale(LC_ALL, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil');
error_log("Locale configured");

// Configuração de headers
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

error_log("Bootstrap completed successfully");
