-- Create ministry_leaders junction table
CREATE TABLE IF NOT EXISTS ministry_leaders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ministry_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('leader', 'co_leader') NOT NULL DEFAULT 'leader',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ministry_user (ministry_id, user_id)
);
