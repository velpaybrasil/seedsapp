-- Renomeando a tabela growth_groups para groups
RENAME TABLE growth_groups TO `groups`;

-- Atualizando as foreign keys
ALTER TABLE group_participants DROP FOREIGN KEY group_participants_ibfk_1;
ALTER TABLE group_participants ADD CONSTRAINT group_participants_ibfk_1 FOREIGN KEY (group_id) REFERENCES `groups`(id);

ALTER TABLE group_attendance DROP FOREIGN KEY group_attendance_ibfk_1;
ALTER TABLE group_attendance ADD CONSTRAINT group_attendance_ibfk_1 FOREIGN KEY (group_id) REFERENCES `groups`(id);
