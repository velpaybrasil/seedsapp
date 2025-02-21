<?php

use App\Core\Database\Migration;

class CreateVisitorGroupHistoryTable extends Migration {
    public function up(): void {
        $sql = "CREATE TABLE IF NOT EXISTS visitor_group_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visitor_id INT NOT NULL,
            group_id INT NOT NULL,
            join_date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
            FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->execute($sql);

        $sql = "CREATE TABLE IF NOT EXISTS visitor_group_changes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visitor_id INT NOT NULL,
            old_group_id INT,
            new_group_id INT NOT NULL,
            change_date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
            FOREIGN KEY (old_group_id) REFERENCES growth_groups(id) ON DELETE SET NULL,
            FOREIGN KEY (new_group_id) REFERENCES growth_groups(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->execute($sql);
    }

    public function down(): void {
        $this->execute("DROP TABLE IF EXISTS visitor_group_changes;");
        $this->execute("DROP TABLE IF EXISTS visitor_group_history;");
    }
}
