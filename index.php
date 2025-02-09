<?php
// Habilita exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o diretório raiz
define('ROOT_PATH', __DIR__);

// Debug
error_log('Starting application from index.php...');
error_log('ROOT_PATH: ' . ROOT_PATH);
error_log('Request URI: ' . $_SERVER['REQUEST_URI']);

// Carrega o autoloader do Composer
require_once ROOT_PATH . '/vendor/autoload.php';

// Carrega o arquivo de helpers
require_once ROOT_PATH . '/app/helpers.php';

// Carrega o arquivo .env
use App\Core\Env;
use App\Core\Router;

try {
    // Carrega as variáveis de ambiente
    Env::load(ROOT_PATH . '/.env');
    
    // Carrega o bootstrap
    require_once ROOT_PATH . '/bootstrap.php';
    
    // Carrega as rotas
    require_once ROOT_PATH . '/app/routes.php';
    
    // Processa a requisição
    Router::dispatch();
    
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo "<h1>Erro na Aplicação</h1>";
    echo "<p>Ocorreu um erro ao processar sua requisição. Por favor, verifique os logs para mais detalhes.</p>";
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "<pre>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString();
        echo "</pre>";
    }
}
