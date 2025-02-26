-- Criação da tabela users
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL,
    remember_token VARCHAR(255) NULL,
    failed_login_attempts INT NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    last_login DATETIME NULL,
    theme VARCHAR(20) DEFAULT 'light',
    notifications_enabled TINYINT(1) DEFAULT 1,
    email_notifications TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_email (email),
    KEY idx_remember_token (remember_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
