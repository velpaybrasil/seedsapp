-- Criar tabela de ministérios se não existir
CREATE TABLE IF NOT EXISTS ministries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Criar tabela de grupos
CREATE TABLE IF NOT EXISTS growth_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    meeting_day VARCHAR(20),
    meeting_time TIME,
    meeting_address TEXT,
    neighborhood VARCHAR(255),
    max_participants INT DEFAULT 12,
    status ENUM('active', 'inactive') DEFAULT 'active',
    ministry_id INT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE SET NULL
);

-- Criar tabela de líderes de grupos
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

-- Criar tabela de participantes dos grupos
CREATE TABLE IF NOT EXISTS group_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    join_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_user (group_id, user_id)
);

-- Criar tabela de reuniões dos grupos
CREATE TABLE IF NOT EXISTS group_meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    meeting_date DATE NOT NULL,
    topic VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_meeting (group_id, meeting_date)
);

-- Criar tabela de presença nas reuniões
CREATE TABLE IF NOT EXISTS group_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_id INT NOT NULL,
    participant_id INT NOT NULL,
    present BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (meeting_id) REFERENCES group_meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES group_participants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_meeting_participant (meeting_id, participant_id)
);

-- Criar tabela de líderes de ministérios
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
