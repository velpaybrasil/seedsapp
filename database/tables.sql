-- Tabela de usuários (necessária para as foreign keys)
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
