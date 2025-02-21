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
    require_once ROOT_PATH . '/config/database.php';

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
    error_log("Application error: " . $e->getMessage());
    if (APP_DEBUG) {
        echo "<h1>Application Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "<h1>Internal Server Error</h1>";
        echo "<p>An error occurred. Please try again later.</p>";
    }
}
