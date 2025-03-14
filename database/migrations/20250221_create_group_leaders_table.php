<?php

use App\Core\Database\Migration;

class CreateGroupLeadersTable extends Migration {
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS growth_group_leaders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                user_id INT NOT NULL,
                role ENUM('leader', 'co-leader') NOT NULL DEFAULT 'leader',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_group_user (group_id, user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->execute($sql);
    }

    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS growth_group_leaders");
    }
}
