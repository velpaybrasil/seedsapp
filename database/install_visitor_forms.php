<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    
    // Ler e executar o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/migrations/20240208_create_visitor_forms_tables.sql');
    
    // Dividir o SQL em comandos individuais
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    // Executar cada comando
    foreach ($commands as $command) {
        if (!empty($command)) {
            $db->exec($command);
            echo "Comando executado com sucesso: " . substr($command, 0, 50) . "...\n";
        }
    }
    
    echo "\nTabelas do módulo de formulários de visitantes criadas com sucesso!\n";
    
} catch (PDOException $e) {
    echo "Erro ao criar tabelas: " . $e->getMessage() . "\n";
    exit(1);
}
