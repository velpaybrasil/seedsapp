<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Lista de arquivos de migração para executar
    $migrations = [
        __DIR__ . '/migrations/20240219_01_create_system_settings.sql',
        __DIR__ . '/migrations/20240219_02_insert_default_settings.sql',
        __DIR__ . '/migrations/20240219_03_add_settings_permissions.sql'
    ];
    
    // Executar cada migração
    foreach ($migrations as $migrationFile) {
        if (!file_exists($migrationFile)) {
            echo "Arquivo de migração não encontrado: " . basename($migrationFile) . "\n";
            continue;
        }
        
        echo "Executando migração: " . basename($migrationFile) . "\n";
        
        // Ler e executar o arquivo SQL
        $sql = file_get_contents($migrationFile);
        $db->exec($sql);
        
        echo "Migração " . basename($migrationFile) . " executada com sucesso!\n";
    }
    
    echo "\nTodas as migrações foram executadas com sucesso!\n";
} catch (Exception $e) {
    echo "Erro ao executar migração: " . $e->getMessage() . "\n";
    exit(1);
}
