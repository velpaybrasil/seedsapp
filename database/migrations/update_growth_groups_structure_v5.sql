-- Seleciona o banco de dados
USE u315624178_gcmanager;

-- Adiciona novos campos mantendo os existentes
ALTER TABLE growth_groups
ADD COLUMN description TEXT NULL AFTER name,
ADD COLUMN co_leader_id INT NULL AFTER leader_id,
ADD COLUMN host_id INT NULL AFTER co_leader_id,
ADD COLUMN max_participants INT NULL AFTER host_id,
ADD COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active' AFTER max_participants,
ADD FOREIGN KEY (co_leader_id) REFERENCES volunteers(id),
ADD FOREIGN KEY (host_id) REFERENCES volunteers(id);

-- Tabela de Participantes do Grupo
CREATE TABLE IF NOT EXISTS growth_group_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    visitor_id INT NOT NULL,
    join_date DATE NOT NULL,
    status ENUM('active', 'inactive', 'graduated') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id),
    FOREIGN KEY (visitor_id) REFERENCES visitors(id)
);

-- Tabela de Reuniões do Grupo
CREATE TABLE IF NOT EXISTS growth_group_meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    meeting_date DATE NOT NULL,
    topic VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id)
);

-- Tabela de Presença nas Reuniões
CREATE TABLE IF NOT EXISTS growth_group_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_id INT NOT NULL,
    participant_id INT NOT NULL,
    present BOOLEAN NOT NULL DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (meeting_id) REFERENCES growth_group_meetings(id),
    FOREIGN KEY (participant_id) REFERENCES growth_group_participants(id)
);
