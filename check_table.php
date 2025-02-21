<?php

try {
    $pdo = new PDO(
        'mysql:host=sql946.main-hosting.eu;dbname=u315624178_gcmanager;charset=utf8mb4',
        'u315624178_gcmanager',
        'gugaLima8*',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
    
    // Verificar estrutura da tabela
    $stmt = $pdo->query('DESCRIBE visitors');
    echo "Estrutura da tabela 'visitors':\n";
    echo str_repeat('-', 80) . "\n";
    echo sprintf("%-20s %-20s %-10s %-10s %-20s\n", 'Campo', 'Tipo', 'Nulo', 'Chave', 'Padrão');
    echo str_repeat('-', 80) . "\n";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf(
            "%-20s %-20s %-10s %-10s %-20s\n",
            $row['Field'],
            $row['Type'],
            $row['Null'],
            $row['Key'],
            $row['Default'] ?? 'NULL'
        );
    }
    
    // Verificar índices
    $stmt = $pdo->query('SHOW INDEX FROM visitors');
    echo "\nÍndices da tabela 'visitors':\n";
    echo str_repeat('-', 80) . "\n";
    echo sprintf("%-30s %-10s %-20s\n", 'Nome do Índice', 'Único', 'Colunas');
    echo str_repeat('-', 80) . "\n";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf(
            "%-30s %-10s %-20s\n",
            $row['Key_name'],
            $row['Non_unique'] ? 'Não' : 'Sim',
            $row['Column_name']
        );
    }
    
    // Verificar quantidade de registros
    $stmt = $pdo->query('SELECT COUNT(*) FROM visitors');
    $count = $stmt->fetchColumn();
    echo "\nTotal de registros: " . $count . "\n";
    
    // Verificar se a tabela growth_groups existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'growth_groups'");
    if ($stmt->rowCount() > 0) {
        echo "\nTabela 'growth_groups' existe.\n";
        
        // Verificar estrutura da tabela growth_groups
        $stmt = $pdo->query('DESCRIBE growth_groups');
        echo "\nEstrutura da tabela 'growth_groups':\n";
        echo str_repeat('-', 80) . "\n";
        echo sprintf("%-20s %-20s %-10s %-10s %-20s\n", 'Campo', 'Tipo', 'Nulo', 'Chave', 'Padrão');
        echo str_repeat('-', 80) . "\n";
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo sprintf(
                "%-20s %-20s %-10s %-10s %-20s\n",
                $row['Field'],
                $row['Type'],
                $row['Null'],
                $row['Key'],
                $row['Default'] ?? 'NULL'
            );
        }
    } else {
        echo "\nTabela 'growth_groups' não existe!\n";
    }
    
} catch (PDOException $e) {
    echo "Erro ao verificar tabela: " . $e->getMessage() . "\n";
}
