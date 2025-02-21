-- Adiciona as novas colunas necessárias
ALTER TABLE visitors
ADD COLUMN whatsapp VARCHAR(20) NULL AFTER phone,
ADD COLUMN address VARCHAR(255) NULL AFTER whatsapp,
ADD COLUMN number VARCHAR(20) NULL AFTER address,
ADD COLUMN complement VARCHAR(100) NULL AFTER number,
ADD COLUMN city VARCHAR(100) NULL AFTER neighborhood,
ADD COLUMN zipcode VARCHAR(10) NULL AFTER city,
ADD COLUMN gender ENUM('M', 'F') NULL AFTER zipcode,
ADD COLUMN first_visit_date DATE NULL AFTER gender,
ADD COLUMN how_knew_church VARCHAR(50) NULL AFTER first_visit_date,
ADD COLUMN prayer_requests TEXT NULL AFTER how_knew_church,
ADD COLUMN observations TEXT NULL AFTER prayer_requests;

-- Renomeia e modifica colunas existentes
ALTER TABLE visitors
CHANGE birthday birth_date DATE NULL,
CHANGE profile_photo photo VARCHAR(255) NULL;

-- Modifica os tipos das colunas existentes
ALTER TABLE visitors
MODIFY COLUMN name VARCHAR(255) NOT NULL,
MODIFY COLUMN phone VARCHAR(20) NULL,
MODIFY COLUMN email VARCHAR(255) NULL,
MODIFY COLUMN neighborhood VARCHAR(100) NULL,
MODIFY COLUMN status ENUM('new', 'in_progress', 'converted', 'member', 'inactive') NOT NULL DEFAULT 'new';
