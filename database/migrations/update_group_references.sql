-- Atualizar referências de groups para growth_groups
-- Primeiro, verificar e remover chaves estrangeiras existentes
SET @schema = 'u315624178_gcmanager';
SET @table = 'members';

SELECT CONCAT('ALTER TABLE ', @table, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
INTO @dropFK
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = @schema
  AND TABLE_NAME = @table
  AND REFERENCED_TABLE_NAME = 'groups'
  AND CONSTRAINT_NAME != 'PRIMARY'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

SET @alterMembers = CONCAT('ALTER TABLE members 
  DROP FOREIGN KEY IF EXISTS ', @dropFK, ',
  ADD CONSTRAINT fk_members_growth_group 
  FOREIGN KEY (group_id) REFERENCES growth_groups(id) 
  ON DELETE SET NULL;');

PREPARE stmt FROM @alterMembers;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Repetir para visitors
SET @table = 'visitors';

SELECT CONCAT('ALTER TABLE ', @table, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
INTO @dropFK
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = @schema
  AND TABLE_NAME = @table
  AND REFERENCED_TABLE_NAME = 'groups'
  AND CONSTRAINT_NAME != 'PRIMARY'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

SET @alterVisitors = CONCAT('ALTER TABLE visitors 
  DROP FOREIGN KEY IF EXISTS ', @dropFK, ',
  ADD CONSTRAINT fk_visitors_growth_group 
  FOREIGN KEY (group_id) REFERENCES growth_groups(id) 
  ON DELETE SET NULL;');

PREPARE stmt FROM @alterVisitors;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Repetir para attendances
SET @table = 'attendances';

SELECT CONCAT('ALTER TABLE ', @table, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
INTO @dropFK
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = @schema
  AND TABLE_NAME = @table
  AND REFERENCED_TABLE_NAME = 'groups'
  AND CONSTRAINT_NAME != 'PRIMARY'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

SET @alterAttendances = CONCAT('ALTER TABLE attendances 
  DROP FOREIGN KEY IF EXISTS ', @dropFK, ',
  ADD CONSTRAINT fk_attendances_growth_group 
  FOREIGN KEY (group_id) REFERENCES growth_groups(id) 
  ON DELETE SET NULL;');

PREPARE stmt FROM @alterAttendances;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Depois que todas as referências forem atualizadas, você pode remover a tabela groups
-- DROP TABLE groups;
