-- Adiciona novos campos na tabela visitors
ALTER TABLE visitors
ADD COLUMN whatsapp VARCHAR(20) NULL AFTER phone,
ADD COLUMN number VARCHAR(20) NULL AFTER address,
ADD COLUMN complement VARCHAR(100) NULL AFTER number,
ADD COLUMN neighborhood VARCHAR(100) NULL AFTER complement,
ADD COLUMN city VARCHAR(100) NULL AFTER neighborhood,
ADD COLUMN zipcode VARCHAR(10) NULL AFTER city,
ADD COLUMN gender ENUM('M', 'F') NULL AFTER birth_date,
ADD COLUMN photo VARCHAR(255) NULL AFTER status;

-- Atualiza campos existentes para permitir NULL
ALTER TABLE visitors
MODIFY COLUMN email VARCHAR(255) NULL,
MODIFY COLUMN phone VARCHAR(20) NULL,
MODIFY COLUMN address VARCHAR(255) NULL,
MODIFY COLUMN birth_date DATE NULL,
MODIFY COLUMN first_visit_date DATE NULL,
MODIFY COLUMN how_knew_church VARCHAR(50) NULL,
MODIFY COLUMN prayer_requests TEXT NULL,
MODIFY COLUMN observations TEXT NULL,
MODIFY COLUMN status ENUM('new', 'in_progress', 'converted', 'member', 'inactive') NOT NULL DEFAULT 'new';
