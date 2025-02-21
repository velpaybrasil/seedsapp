<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;

// Carregar variáveis de ambiente
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die('.env file not found. Please create one based on .env.example');
}
Env::load($envFile);

// Lista de migrações em ordem de execução
$migrations = [
    'create_visitor_follow_ups_table',
    'create_visitor_visits_table',
    'create_visitor_group_history_table'
];

// Carregar e executar cada migração
foreach ($migrations as $migrationName) {
    try {
        echo "Executando migração: {$migrationName}\n";
        
        // Carregar o arquivo da migração
        require_once __DIR__ . "/migrations/{$migrationName}.php";
        
        // Extrair o nome da classe da migração
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));
        
        // Instanciar e executar a migração
        $migration = new $className();
        $migration->up();
        
        echo "Migração {$migrationName} executada com sucesso!\n";
    } catch (\Exception $e) {
        echo "Erro ao executar migração {$migrationName}: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
        exit(1);
    }
}

echo "Todas as migrações foram executadas com sucesso!\n";
