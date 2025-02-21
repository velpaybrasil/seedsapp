<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database\Database;

try {
    $db = Database::getInstance()->getConnection();

    // Definir charset
    $db->exec("SET NAMES utf8mb4");

    // Criar tabela group_participants
    $sql = "CREATE TABLE IF NOT EXISTS group_participants (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        visitor_id INT NOT NULL,
        join_date DATE NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
        FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
        UNIQUE KEY unique_group_visitor (group_id, visitor_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "Tabela group_participants criada com sucesso!\n";

} catch (PDOException $e) {
    echo "Erro ao criar tabela: " . $e->getMessage() . "\n";
}
