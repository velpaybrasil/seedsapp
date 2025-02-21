-- Verificar e criar a tabela growth_groups se não existir
CREATE TABLE IF NOT EXISTS growth_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    meeting_day VARCHAR(20),
    meeting_time TIME,
    meeting_address VARCHAR(255),
    neighborhood VARCHAR(255),
    max_participants INT DEFAULT 12,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    ministry_id INT,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE SET NULL
);

-- Verificar e criar a tabela group_leaders se não existir
CREATE TABLE IF NOT EXISTS group_leaders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('leader', 'co_leader') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_user_role (group_id, user_id, role)
);

-- Adicionar índices para melhor performance
ALTER TABLE growth_groups
ADD INDEX idx_status (status),
ADD INDEX idx_neighborhood (neighborhood),
ADD INDEX idx_ministry (ministry_id);

ALTER TABLE group_leaders
ADD INDEX idx_group (group_id),
ADD INDEX idx_user (user_id),
ADD INDEX idx_role (role);

-- Verificar e adicionar colunas que podem estar faltando
ALTER TABLE growth_groups
ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 8) NULL,
ADD COLUMN IF NOT EXISTS longitude DECIMAL(11, 8) NULL,
ADD COLUMN IF NOT EXISTS ministry_id INT NULL,
ADD COLUMN IF NOT EXISTS description TEXT NULL,
ADD COLUMN IF NOT EXISTS neighborhood VARCHAR(255) NULL,
MODIFY COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active';

-- Remover colunas antigas se existirem
ALTER TABLE growth_groups
DROP COLUMN IF EXISTS leader_id,
DROP COLUMN IF EXISTS co_leader_id,
DROP COLUMN IF EXISTS host_id;
