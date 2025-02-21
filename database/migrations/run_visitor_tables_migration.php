<?php

try {
    $host = 'localhost';
    $dbname = 'u315624178_gcmanager';
    $username = 'root';
    $password = '';
    
    $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ler e executar o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/create_visitor_tables.sql');
    $db->exec($sql);
    
    echo "Tabelas de visitas e follow-ups criadas com sucesso!\n";
} catch (PDOException $e) {
    echo "Erro durante a migração: " . $e->getMessage() . "\n";
    exit(1);
}
