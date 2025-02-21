<?php

require_once __DIR__ . '/../../bootstrap/app.php';

$sql = "
    -- Remover as foreign keys antigas
    ALTER TABLE growth_groups
    DROP FOREIGN KEY growth_groups_ibfk_1,
    DROP FOREIGN KEY growth_groups_ibfk_2,
    DROP FOREIGN KEY growth_groups_ibfk_3;

    -- Remover as colunas antigas
    ALTER TABLE growth_groups
    DROP COLUMN leader_id,
    DROP COLUMN co_leader_id,
    DROP COLUMN host_id;

    -- Adicionar novas colunas necessárias
    ALTER TABLE growth_groups
    ADD COLUMN ministry_id INT NULL,
    ADD COLUMN neighborhood VARCHAR(100) NOT NULL DEFAULT '',
    ADD COLUMN extra_neighborhoods TEXT NULL,
    ADD FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE SET NULL;
";

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $db->exec($sql);
    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    die("Error running migration: " . $e->getMessage() . "\n");
}
