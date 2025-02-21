<?php

try {
    $host = 'localhost';
    $dbname = 'u315624178_gcmanager';
    $username = 'root';
    $password = '';
    
    $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ler e executar o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/add_visitor_contact_logs.sql');
    $db->exec($sql);
    
    echo "Tabela de logs de contato criada com sucesso!\n";
} catch (PDOException $e) {
    echo "Erro durante a migração: " . $e->getMessage() . "\n";
    exit(1);
}
