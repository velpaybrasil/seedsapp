-- Removendo índices existentes se houver
DROP INDEX IF EXISTS idx_group_name ON groups;
DROP INDEX IF EXISTS idx_member_group ON members;
DROP INDEX IF EXISTS idx_member_name ON members;
DROP INDEX IF EXISTS idx_visitor_status ON visitors;
DROP INDEX IF EXISTS idx_visitor_gc ON visitors;

-- Criação das tabelas
CREATE TABLE IF NOT EXISTS groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    leader_name VARCHAR(100) NOT NULL,
    meeting_day VARCHAR(20) NOT NULL,
    meeting_time TIME NOT NULL,
    address VARCHAR(200) NOT NULL,
    neighborhood VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state CHAR(2) NOT NULL,
    capacity INT NOT NULL DEFAULT 12,
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    birth_date DATE,
    address VARCHAR(200),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS visitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    status ENUM('lead', 'contacted', 'gc_linked') NOT NULL DEFAULT 'lead',
    gc_id INT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (gc_id) REFERENCES groups(id) ON DELETE SET NULL
);

-- Criação de índices
CREATE INDEX idx_group_name ON groups(name);
CREATE INDEX idx_member_group ON members(group_id);
CREATE INDEX idx_member_name ON members(name);
CREATE INDEX idx_visitor_status ON visitors(status);
CREATE INDEX idx_visitor_gc ON visitors(gc_id);
