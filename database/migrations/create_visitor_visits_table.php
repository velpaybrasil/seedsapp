<?php

use App\Core\Database\Migration;

class CreateVisitorVisitsTable extends Migration {
    public function up(): void {
        $sql = "CREATE TABLE IF NOT EXISTS visitor_visits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visitor_id INT NOT NULL,
            visit_date DATE NOT NULL,
            service_type ENUM('sunday', 'midweek', 'special', 'other') NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->execute($sql);
    }

    public function down(): void {
        $this->execute("DROP TABLE IF EXISTS visitor_visits;");
    }
}
