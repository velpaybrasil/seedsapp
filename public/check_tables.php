<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;

// Carregar variáveis de ambiente
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die('.env file not found');
}
Env::load($envFile);

try {
    $host = Env::get('DB_HOST');
    $dbname = Env::get('DB_NAME');
    $user = Env::get('DB_USER');
    $pass = Env::get('DB_PASS');

    echo "Conectando ao banco de dados...\n";
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "Conexão estabelecida com sucesso!\n\n";

    // Listar todas as tabelas
    echo "Tabelas existentes:\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- {$table}\n";
        
        // Mostrar estrutura da tabela
        $stmt = $pdo->query("DESCRIBE {$table}");
        $columns = $stmt->fetchAll();
        foreach ($columns as $column) {
            echo "  * {$column['Field']} ({$column['Type']})\n";
        }
        echo "\n";
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
