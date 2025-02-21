-- Adiciona coluna logo na tabela visitors
ALTER TABLE visitors ADD COLUMN logo VARCHAR(255) NULL AFTER photo;
