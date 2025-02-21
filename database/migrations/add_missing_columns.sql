-- Adiciona apenas as novas colunas que não existem
ALTER TABLE visitors
ADD COLUMN whatsapp VARCHAR(20) NULL,
ADD COLUMN number VARCHAR(20) NULL,
ADD COLUMN complement VARCHAR(100) NULL,
ADD COLUMN neighborhood VARCHAR(100) NULL,
ADD COLUMN city VARCHAR(100) NULL,
ADD COLUMN zipcode VARCHAR(10) NULL,
ADD COLUMN gender ENUM('M', 'F') NULL,
ADD COLUMN photo VARCHAR(255) NULL;

-- Modifica as colunas existentes para garantir os tipos corretos
ALTER TABLE visitors
MODIFY COLUMN name VARCHAR(255) NOT NULL,
MODIFY COLUMN email VARCHAR(255) NULL,
MODIFY COLUMN phone VARCHAR(20) NULL,
MODIFY COLUMN address VARCHAR(255) NULL,
MODIFY COLUMN birth_date DATE NULL,
MODIFY COLUMN first_visit_date DATE NULL,
MODIFY COLUMN how_knew_church VARCHAR(50) NULL,
MODIFY COLUMN prayer_requests TEXT NULL,
MODIFY COLUMN observations TEXT NULL,
MODIFY COLUMN status ENUM('new', 'in_progress', 'converted', 'member', 'inactive') NOT NULL DEFAULT 'new';
