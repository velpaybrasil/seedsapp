<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar tabela ministries
    echo "=== Verificando tabela ministries ===\n";
    $stmt = $db->query("SHOW TABLES LIKE 'ministries'");
    if ($stmt->rowCount() > 0) {
        echo "Tabela ministries existe\n";
        
        $stmt = $db->query("SELECT * FROM ministries");
        $ministries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Total de ministérios: " . count($ministries) . "\n";
        print_r($ministries);
    } else {
        echo "Tabela ministries não existe\n";
        
        // Criar tabela ministries
        $sql = "CREATE TABLE ministries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->exec($sql);
        echo "Tabela ministries criada\n";
        
        // Inserir alguns ministérios de exemplo
        $sql = "INSERT INTO ministries (name, description) VALUES 
            ('Ministério de Louvor', 'Ministério responsável pela música e adoração'),
            ('Ministério Infantil', 'Ministério responsável pelo trabalho com crianças'),
            ('Ministério de Jovens', 'Ministério responsável pelo trabalho com jovens')";
        
        $db->exec($sql);
        echo "Ministérios de exemplo inseridos\n";
    }

    // Verificar tabela growth_groups
    echo "\n=== Verificando tabela growth_groups ===\n";
    $stmt = $db->query("SHOW TABLES LIKE 'growth_groups'");
    if ($stmt->rowCount() > 0) {
        echo "Tabela growth_groups existe\n";
        
        $stmt = $db->query("SELECT * FROM growth_groups");
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Total de grupos: " . count($groups) . "\n";
        print_r($groups);
    } else {
        echo "Tabela growth_groups não existe\n";
    }

    // Verificar tabela group_participants
    echo "\n=== Verificando tabela group_participants ===\n";
    $stmt = $db->query("SHOW TABLES LIKE 'group_participants'");
    if ($stmt->rowCount() > 0) {
        echo "Tabela group_participants existe\n";
        
        $stmt = $db->query("SELECT * FROM group_participants");
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Total de participantes: " . count($participants) . "\n";
        print_r($participants);
    } else {
        echo "Tabela group_participants não existe\n";
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
