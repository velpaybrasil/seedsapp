-- Primeiro, adiciona as colunas básicas que não dependem de outras
ALTER TABLE visitors
ADD COLUMN address VARCHAR(255) NULL,
ADD COLUMN birth_date DATE NULL,
ADD COLUMN email VARCHAR(255) NULL,
ADD COLUMN phone VARCHAR(20) NULL,
ADD COLUMN status ENUM('new', 'in_progress', 'converted', 'member', 'inactive') NOT NULL DEFAULT 'new';

-- Agora adiciona as colunas que dependem das anteriores
ALTER TABLE visitors
ADD COLUMN whatsapp VARCHAR(20) NULL AFTER phone,
ADD COLUMN number VARCHAR(20) NULL AFTER address,
ADD COLUMN complement VARCHAR(100) NULL AFTER number,
ADD COLUMN neighborhood VARCHAR(100) NULL AFTER complement,
ADD COLUMN city VARCHAR(100) NULL AFTER neighborhood,
ADD COLUMN zipcode VARCHAR(10) NULL AFTER city,
ADD COLUMN gender ENUM('M', 'F') NULL AFTER birth_date,
ADD COLUMN first_visit_date DATE NULL,
ADD COLUMN how_knew_church VARCHAR(50) NULL,
ADD COLUMN prayer_requests TEXT NULL,
ADD COLUMN observations TEXT NULL,
ADD COLUMN photo VARCHAR(255) NULL AFTER status;

-- Adiciona as colunas de timestamp se não existirem
ALTER TABLE visitors
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
