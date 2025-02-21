-- Desabilitar verificação de chaves estrangeiras temporariamente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpar tabelas mais dependentes (nível 3)
DELETE FROM group_member_history;
DELETE FROM visitor_form_submissions;

-- Limpar tabelas dependentes (nível 2)
DELETE FROM attendance;
DELETE FROM group_members;
DELETE FROM growth_group_leaders;

-- Limpar tabelas principais (nível 1)
DELETE FROM growth_groups;
DELETE FROM visitors;

-- Resetar os auto_increment
ALTER TABLE group_member_history AUTO_INCREMENT = 1;
ALTER TABLE visitor_form_submissions AUTO_INCREMENT = 1;
ALTER TABLE attendance AUTO_INCREMENT = 1;
ALTER TABLE group_members AUTO_INCREMENT = 1;
ALTER TABLE growth_group_leaders AUTO_INCREMENT = 1;
ALTER TABLE growth_groups AUTO_INCREMENT = 1;
ALTER TABLE visitors AUTO_INCREMENT = 1;

-- Reabilitar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS = 1;
