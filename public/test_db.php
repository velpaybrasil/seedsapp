<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar estrutura da tabela ministries
    $stmt = $db->query("SHOW COLUMNS FROM ministries");
    echo "Estrutura da tabela ministries:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
    // Verificar dados
    $stmt = $db->query("SELECT * FROM ministries LIMIT 1");
    echo "\n\nPrimeiro registro da tabela ministries:\n";
    $ministry = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($ministry);
    
    // Verificar contagem
    $stmt = $db->query("SELECT COUNT(*) as total FROM ministries");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n\nTotal de ministérios: " . $count['total'];
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
