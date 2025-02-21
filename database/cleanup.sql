-- Primeiro, vamos descobrir todas as chaves estrangeiras
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME
FROM
    information_schema.TABLE_CONSTRAINTS
WHERE
    CONSTRAINT_TYPE = 'FOREIGN KEY'
    AND TABLE_SCHEMA = 'u315624178_gcmanager';

-- Depois, vamos tentar remover todas as tabelas diretamente
SET @database = 'u315624178_gcmanager';

SELECT CONCAT('SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `', table_name, '`; SET FOREIGN_KEY_CHECKS = 1;') 
FROM information_schema.tables 
WHERE table_schema = @database;
