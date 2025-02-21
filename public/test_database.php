<?php

// Define o diretório raiz
define('ROOT_PATH', dirname(__DIR__));

// Carrega o autoloader do Composer
require_once ROOT_PATH . '/vendor/autoload.php';

// Carrega o arquivo .env
use App\Core\Env;
use App\Core\Database;

$envFile = ROOT_PATH . '/.env';
if (!file_exists($envFile)) {
    die('.env file not found');
}
Env::load($envFile);

try {
    echo "Iniciando teste da classe Database...<br>";
    
    $db = Database::getInstance();
    echo "Instância do Database criada com sucesso<br>";
    
    $connection = $db->getConnection();
    echo "Conexão obtida com sucesso<br>";
    
    $stmt = $connection->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "Teste de consulta: " . print_r($result, true);

} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
    echo "Arquivo: " . $e->getFile() . "<br>";
    echo "Linha: " . $e->getLine() . "<br>";
    echo "Stack trace:<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
