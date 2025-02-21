-- Create database
CREATE DATABASE IF NOT EXISTS u315624178_gcmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u315624178_gcmanager;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'leader', 'member') NOT NULL DEFAULT 'member',
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

-- Tabela de visitantes
CREATE TABLE IF NOT EXISTS visitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    birth_date DATE,
    first_visit_date DATE NOT NULL,
    how_knew_church VARCHAR(100),
    prayer_requests TEXT,
    observations TEXT,
    status ENUM('new', 'in_follow_up', 'converted', 'member', 'inactive') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

-- Tabela de acompanhamento de visitantes
CREATE TABLE IF NOT EXISTS visitor_follow_ups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    visitor_id INT NOT NULL,
    contact_date DATE NOT NULL,
    contact_type ENUM('phone', 'email', 'visit', 'whatsapp') NOT NULL,
    notes TEXT,
    next_contact_date DATE,
    responsible_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (visitor_id) REFERENCES visitors(id),
    FOREIGN KEY (responsible_id) REFERENCES users(id)
);

-- Tabela de grupos de crescimento
CREATE TABLE IF NOT EXISTS growth_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    meeting_day ENUM('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday') NOT NULL,
    meeting_time TIME NOT NULL,
    address TEXT NOT NULL,
    leader_id INT NOT NULL,
    co_leader_id INT,
    max_participants INT DEFAULT 12,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (leader_id) REFERENCES users(id),
    FOREIGN KEY (co_leader_id) REFERENCES users(id)
);

-- Tabela de participantes dos grupos
CREATE TABLE IF NOT EXISTS group_participants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('member', 'host', 'helper') NOT NULL DEFAULT 'member',
    join_date DATE NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabela de presença nos grupos
CREATE TABLE IF NOT EXISTS group_attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    meeting_date DATE NOT NULL,
    participant_id INT NOT NULL,
    present BOOLEAN NOT NULL DEFAULT FALSE,
    notes TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id),
    FOREIGN KEY (participant_id) REFERENCES group_participants(id)
);

-- Tabela de ministérios
CREATE TABLE IF NOT EXISTS ministries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    leader_id INT NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (leader_id) REFERENCES users(id)
);

-- Tabela de voluntários
CREATE TABLE IF NOT EXISTS volunteers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    ministry_id INT NOT NULL,
    role VARCHAR(100) NOT NULL,
    availability TEXT,
    start_date DATE NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (ministry_id) REFERENCES ministries(id)
);

-- Tabela de escalas
CREATE TABLE IF NOT EXISTS schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ministry_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    description TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id)
);

-- Tabela de voluntários nas escalas
CREATE TABLE IF NOT EXISTS schedule_volunteers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    schedule_id INT NOT NULL,
    volunteer_id INT NOT NULL,
    confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    confirmation_date DATETIME,
    notes TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id)
);

-- Tabela de categorias financeiras
CREATE TABLE IF NOT EXISTS financial_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    description TEXT,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

-- Tabela de transações financeiras
CREATE TABLE IF NOT EXISTS financial_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    payment_method ENUM('cash', 'pix', 'credit_card', 'debit_card', 'bank_transfer', 'other') NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    document_number VARCHAR(50),
    user_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (category_id) REFERENCES financial_categories(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabela de dízimos e ofertas
CREATE TABLE IF NOT EXISTS tithes_offerings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    type ENUM('tithe', 'offering') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    payment_method ENUM('cash', 'pix', 'credit_card', 'debit_card', 'bank_transfer', 'other') NOT NULL,
    anonymous BOOLEAN NOT NULL DEFAULT FALSE,
    notes TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabela de mensagens/comunicações
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('email', 'notification', 'announcement') NOT NULL,
    status ENUM('draft', 'sent', 'scheduled') NOT NULL DEFAULT 'draft',
    scheduled_date DATETIME,
    sender_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id)
);

-- Tabela de destinatários das mensagens
CREATE TABLE IF NOT EXISTS message_recipients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message_id INT NOT NULL,
    recipient_type ENUM('user', 'group', 'ministry', 'all') NOT NULL,
    recipient_id INT,
    read_at DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (message_id) REFERENCES messages(id)
);

-- Insere categorias financeiras padrão
INSERT INTO financial_categories (name, type, description, active, created_at, updated_at) VALUES
('Dízimos', 'income', 'Dízimos dos membros', 1, NOW(), NOW()),
('Ofertas', 'income', 'Ofertas gerais', 1, NOW(), NOW()),
('Doações', 'income', 'Doações específicas', 1, NOW(), NOW()),
('Salários', 'expense', 'Pagamento de funcionários', 1, NOW(), NOW()),
('Aluguel', 'expense', 'Aluguel do espaço', 1, NOW(), NOW()),
('Utilities', 'expense', 'Água, luz, internet, etc', 1, NOW(), NOW()),
('Manutenção', 'expense', 'Manutenção e reparos', 1, NOW(), NOW()),
('Material', 'expense', 'Material de escritório e consumo', 1, NOW(), NOW());
