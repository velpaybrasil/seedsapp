<?php

use App\Core\Database\Migration;

class CreateVisitorFollowUpsTable extends Migration {
    public function up(): void {
        $sql = "CREATE TABLE IF NOT EXISTS visitor_follow_ups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visitor_id INT NOT NULL,
            contact_date DATE NOT NULL,
            type ENUM('phone', 'whatsapp', 'email', 'visit', 'other') NOT NULL,
            notes TEXT,
            next_contact DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->execute($sql);
    }

    public function down(): void {
        $this->execute("DROP TABLE IF EXISTS visitor_follow_ups;");
    }
}
