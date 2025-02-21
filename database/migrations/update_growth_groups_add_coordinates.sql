-- Seleciona o banco de dados
USE u315624178_gcmanager;

-- Adiciona as colunas de latitude e longitude
ALTER TABLE growth_groups
ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER neighborhood,
ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude;
