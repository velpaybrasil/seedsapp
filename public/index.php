<?php

// Define o diretório raiz
define('ROOT_PATH', dirname(__DIR__));

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

    // Carrega as configurações
    require_once ROOT_PATH . '/config/app.php';

    // Verifica o diretório de logs
    if (!file_exists(ROOT_PATH . '/storage/logs')) {
        mkdir(ROOT_PATH . '/storage/logs', 0755, true);
    }

    // Carrega o bootstrap
    require_once ROOT_PATH . '/bootstrap/app.php';

    // Define o base path do Router
    $parsedUrl = parse_url(APP_URL);
    $basePath = isset($parsedUrl['path']) ? rtrim($parsedUrl['path'], '/') : '';
    
    // Define o base path do Router
    Router::setBasePath($basePath);
    error_log("Base path set to: " . $basePath);

    // Carrega as rotas
    require_once ROOT_PATH . '/routes/web.php';
    
    // Despacha a requisição
    Router::dispatch();

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo "<pre>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString();
        echo "</pre>";
    } else {
        http_response_code(500);
        echo "Internal Server Error";
    }
}
