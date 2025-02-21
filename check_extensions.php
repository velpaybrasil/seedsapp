<?php

// Carregar o autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Carregar variáveis de ambiente
\App\Core\Env::load(__DIR__ . '/.env');

$required_extensions = [
    'pdo_mysql',
    'mysqli',
    'curl',
    'fileinfo',
    'mbstring',
    'openssl'
];

$missing_extensions = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (empty($missing_extensions)) {
    echo "✅ Todas as extensões necessárias estão instaladas e habilitadas.\n";
} else {
    echo "❌ As seguintes extensões estão faltando:\n";
    foreach ($missing_extensions as $ext) {
        echo "   - {$ext}\n";
    }
    echo "\nPor favor, habilite estas extensões no arquivo php.ini.\n";
}

// Testar conexão com o banco de dados
try {
    require_once __DIR__ . '/config/database.php';
    $config = require __DIR__ . '/config/database.php';
    $dbConfig = $config['default'];
    
    $dsn = sprintf(
        '%s:host=%s;dbname=%s;charset=%s',
        $dbConfig['driver'],
        $dbConfig['host'],
        $dbConfig['database'],
        $dbConfig['charset']
    );
    
    $pdo = new PDO(
        $dsn,
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options'] ?? []
    );
    
    echo "\n✅ Conexão com o banco de dados estabelecida com sucesso!\n";
} catch (PDOException $e) {
    echo "\n❌ Erro ao conectar com o banco de dados:\n";
    echo "   " . $e->getMessage() . "\n";
}
