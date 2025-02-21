<?php

require_once 'vendor/autoload.php';
require_once 'config/database.php';

use App\Core\Database\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar se a tabela ministries existe
    $stmt = $db->query("SHOW TABLES LIKE 'ministries'");
    $tableExists = $stmt->fetch();
    
    echo "Tabela ministries existe? " . ($tableExists ? "Sim\n" : "Não\n");
    
    if ($tableExists) {
        // Mostrar estrutura da tabela
        $stmt = $db->query("DESCRIBE ministries");
        echo "\nEstrutura da tabela ministries:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        
        // Contar registros
        $stmt = $db->query("SELECT COUNT(*) as total FROM ministries");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nTotal de registros: " . $count['total'] . "\n";
        
        // Mostrar alguns registros
        $stmt = $db->query("SELECT * FROM ministries LIMIT 5");
        echo "\nPrimeiros 5 registros:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
