-- Desabilitando verificação de chave estrangeira
SET FOREIGN_KEY_CHECKS = 0;

-- Removendo chaves estrangeiras
ALTER TABLE visitors DROP FOREIGN KEY IF EXISTS visitors_ibfk_1;
ALTER TABLE members DROP FOREIGN KEY IF EXISTS members_ibfk_1;

-- Removendo tabelas
DROP TABLE IF EXISTS visitors;
DROP TABLE IF EXISTS members;
DROP TABLE IF EXISTS groups;

-- Reabilitando verificação de chave estrangeira
SET FOREIGN_KEY_CHECKS = 1;
