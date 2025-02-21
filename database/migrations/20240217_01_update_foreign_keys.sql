-- Seleciona o banco de dados
USE u315624178_gcmanager;

-- Desabilita verificação de chaves estrangeiras temporariamente
SET FOREIGN_KEY_CHECKS=0;

-- Remove e recria as chaves estrangeiras da tabela growth_group_participants
ALTER TABLE growth_group_participants
DROP FOREIGN KEY IF EXISTS growth_group_participants_ibfk_1,
DROP FOREIGN KEY IF EXISTS growth_group_participants_ibfk_2;

ALTER TABLE growth_group_participants
ADD CONSTRAINT growth_group_participants_group_fk
    FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
ADD CONSTRAINT growth_group_participants_visitor_fk
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE;

-- Remove e recria as chaves estrangeiras da tabela growth_group_meetings
ALTER TABLE growth_group_meetings
DROP FOREIGN KEY IF EXISTS growth_group_meetings_ibfk_1;

ALTER TABLE growth_group_meetings
ADD CONSTRAINT growth_group_meetings_group_fk
    FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE;

-- Remove e recria as chaves estrangeiras da tabela growth_group_attendance
ALTER TABLE growth_group_attendance
DROP FOREIGN KEY IF EXISTS growth_group_attendance_ibfk_1,
DROP FOREIGN KEY IF EXISTS growth_group_attendance_ibfk_2;

ALTER TABLE growth_group_attendance
ADD CONSTRAINT growth_group_attendance_meeting_fk
    FOREIGN KEY (meeting_id) REFERENCES growth_group_meetings(id) ON DELETE CASCADE,
ADD CONSTRAINT growth_group_attendance_participant_fk
    FOREIGN KEY (participant_id) REFERENCES growth_group_participants(id) ON DELETE CASCADE;

-- Reabilita verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS=1;
