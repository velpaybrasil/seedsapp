<?php

// Define o diretório raiz
define('ROOT_PATH', __DIR__);

// Debug
error_log('Starting application from index.php...');
error_log('ROOT_PATH: ' . ROOT_PATH);
error_log('Request URI: ' . $_SERVER['REQUEST_URI']);
error_log('Script Name: ' . $_SERVER['SCRIPT_NAME']);
error_log('PHP_SELF: ' . $_SERVER['PHP_SELF']);
error_log('DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT']);
error_log('HTTP Host: ' . $_SERVER['HTTP_HOST']);

// Carrega o autoloader do Composer
require_once ROOT_PATH . '/vendor/autoload.php';

// Carrega o arquivo de helpers
require_once ROOT_PATH . '/app/helpers.php';

// Carrega o arquivo .env
use App\Core\Env;
use App\Core\Router;

$envFile = ROOT_PATH . '/.env';
if (!file_exists($envFile)) {
    die('.env file not found. Please create one based on .env.example');
}

try {
    // Carrega as variáveis de ambiente
    Env::load($envFile);
    error_log("Environment variables loaded");

    // Carrega as configurações
    $appConfig = require_once ROOT_PATH . '/app/config/app.php';
    error_log("App configuration loaded");

    // Define constantes da aplicação
    if (!defined('APP_NAME')) define('APP_NAME', $appConfig['name']);
    if (!defined('APP_ENV')) define('APP_ENV', $appConfig['env']);
    if (!defined('APP_DEBUG')) define('APP_DEBUG', $appConfig['debug']);
    if (!defined('APP_URL')) define('APP_URL', $appConfig['url']);

    // Verifica o diretório de logs
    if (!file_exists(ROOT_PATH . '/storage/logs')) {
        mkdir(ROOT_PATH . '/storage/logs', 0755, true);
        error_log("Logs directory created");
    }

    // Carrega o bootstrap
    require_once ROOT_PATH . '/bootstrap/app.php';
    error_log("Bootstrap file loaded");

    // Carrega as rotas
    require_once ROOT_PATH . '/app/routes.php';
    error_log("Routes loaded");

    // Despacha a requisição
    Router::dispatch();
    error_log("Request dispatched");
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo "Internal Server Error";
}
