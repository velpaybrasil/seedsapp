-- Seleciona o banco de dados
USE u315624178_gcmanager;

-- Remove campos não utilizados
ALTER TABLE growth_groups
    DROP COLUMN IF EXISTS latitude,
    DROP COLUMN IF EXISTS longitude;

-- Adiciona campo para bairros extras e atualiza os campos existentes
ALTER TABLE growth_groups
    ADD COLUMN extra_neighborhoods TEXT NULL AFTER neighborhood,
    MODIFY COLUMN meeting_address TEXT NOT NULL COMMENT 'Endereço principal onde acontece a maior parte dos encontros',
    MODIFY COLUMN neighborhood VARCHAR(255) NOT NULL COMMENT 'Bairro principal onde acontece a maior parte dos encontros';
