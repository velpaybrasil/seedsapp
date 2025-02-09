<?php

try {
    // Criar conexão com o MySQL (sem selecionar banco de dados)
    $pdo = new PDO(
        'mysql:host=127.0.0.1',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    // Criar o banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS u315624178_gcmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco de dados criado com sucesso!\n";
    
    // Selecionar o banco de dados
    $pdo->exec("USE u315624178_gcmanager");
    echo "✅ Banco de dados selecionado!\n";
    
    // Ler e executar o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/database/u315624178_gcmanager.sql');
    
    // Dividir o SQL em comandos individuais
    $commands = array_filter(
        array_map(
            'trim',
            explode(';', $sql)
        ),
        function($cmd) { return !empty($cmd); }
    );
    
    // Executar cada comando
    foreach ($commands as $command) {
        try {
            $pdo->exec($command);
        } catch (PDOException $e) {
            // Ignorar erros de tabela já existente
            if (strpos($e->getMessage(), "already exists") === false) {
                throw $e;
            }
        }
    }
    
    echo "✅ Estrutura do banco de dados importada com sucesso!\n";
    
    // Verificar se as tabelas foram criadas
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "\nTabelas criadas:\n";
    foreach ($tables as $table) {
        echo "- {$table}\n";
    }
    
    // Verificar a estrutura da tabela visitors
    echo "\nEstrutura da tabela visitors:\n";
    echo str_repeat('-', 80) . "\n";
    $stmt = $pdo->query('DESCRIBE visitors');
    echo sprintf("%-20s %-20s %-10s %-10s %-20s\n", 'Campo', 'Tipo', 'Nulo', 'Chave', 'Padrão');
    echo str_repeat('-', 80) . "\n";
    
    while ($row = $stmt->fetch()) {
        echo sprintf(
            "%-20s %-20s %-10s %-10s %-20s\n",
            $row['Field'],
            $row['Type'],
            $row['Null'],
            $row['Key'],
            $row['Default'] ?? 'NULL'
        );
    }
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    
    // Se o erro for de conexão, dar dicas específicas
    if (strpos($e->getMessage(), "Connection refused") !== false) {
        echo "\nDicas:\n";
        echo "1. Verifique se o MySQL está instalado e rodando\n";
        echo "2. Verifique se as credenciais estão corretas\n";
        echo "3. Verifique se a porta 3306 está disponível\n";
    }
}
