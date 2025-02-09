<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Env;
use App\Core\Database\Database;

try {
    // Carrega variáveis de ambiente
    $envFile = __DIR__ . '/.env';
    echo "Carregando arquivo .env de: " . $envFile . "\n";
    Env::load($envFile);

    // Tenta conectar ao banco
    echo "Tentando conectar ao banco...\n";
    $db = Database::getInstance()->getConnection();
    echo "Conexão estabelecida com sucesso!\n";

    // Testa uma query simples
    $stmt = $db->query("SELECT 1");
    $result = $stmt->fetch();
    echo "Query executada com sucesso!\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
