<?php

try {
    // Configurações do banco de dados
    $host = 'localhost';
    $dbname = 'u315624178_gcmanager';
    $user = 'u315624178_gcmanager';
    $pass = 'gugaLima8*';

    error_log("Tentando conectar ao banco de dados...");
    
    // Criar conexão PDO
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    error_log("Conexão estabelecida com sucesso");

    // Ler o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/migrations/update_growth_groups_add_coordinates.sql');
    
    // Remover o comando USE
    $sql = preg_replace('/USE\s+[^;]+;/', '', $sql);
    
    error_log("Executando migração...");
    error_log("SQL: " . $sql);
    
    // Executar a query
    $pdo->exec($sql);
    
    echo "Migração executada com sucesso!\n";
    error_log("Migração executada com sucesso");
    
} catch (PDOException $e) {
    $error = "Erro ao conectar/executar migração: " . $e->getMessage();
    echo $error . "\n";
    error_log($error);
    exit(1);
} catch (Exception $e) {
    $error = "Erro: " . $e->getMessage();
    echo $error . "\n";
    error_log($error);
    exit(1);
}
