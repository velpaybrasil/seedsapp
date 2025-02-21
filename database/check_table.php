<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database\Database;

try {
    $db = Database::getInstance()->getConnection();

    // Verificar se a tabela existe
    $sql = "SHOW TABLES LIKE 'group_participants'";
    $stmt = $db->query($sql);
    $exists = $stmt->rowCount() > 0;

    if (!$exists) {
        echo "Criando tabela group_participants...\n";
        
        $sql = "CREATE TABLE group_participants (
            id INT PRIMARY KEY AUTO_INCREMENT,
            group_id INT NOT NULL,
            visitor_id INT NOT NULL,
            join_date DATE NOT NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES growth_groups(id),
            FOREIGN KEY (visitor_id) REFERENCES visitors(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->exec($sql);
        echo "Tabela group_participants criada com sucesso!\n";
    } else {
        echo "Tabela group_participants já existe.\n";
        
        // Mostrar a estrutura da tabela
        $sql = "SHOW CREATE TABLE group_participants";
        $stmt = $db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nEstrutura atual:\n";
        print_r($result);
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
