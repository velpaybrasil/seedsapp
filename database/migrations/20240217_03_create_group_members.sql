-- Seleciona o banco de dados
USE u315624178_gcmanager;

-- Criar tabela de membros dos grupos
CREATE TABLE IF NOT EXISTS group_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    visitor_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    role ENUM('member', 'leader', 'co-leader') NOT NULL DEFAULT 'member',
    joined_at DATETIME NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_visitor (group_id, visitor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela para histórico de status
CREATE TABLE IF NOT EXISTS group_member_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    old_status ENUM('pending', 'approved', 'rejected') NOT NULL,
    new_status ENUM('pending', 'approved', 'rejected') NOT NULL,
    changed_by INT NOT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES group_members(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
