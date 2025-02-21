<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Ler o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/migrations/20240210_01_create_visitor_forms_tables.sql');
    
    // Remover o comando USE
    $sql = preg_replace('/USE\s+[^;]+;/', '', $sql);
    
    // Executar as queries
    $db->exec($sql);
    
    echo "Migração das tabelas de formulários de visitantes executada com sucesso!\n";
} catch (Exception $e) {
    echo "Erro ao executar migração: " . $e->getMessage() . "\n";
    exit(1);
}
