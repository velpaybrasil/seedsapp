ALTER TABLE visitors
ADD COLUMN follow_up_date date DEFAULT NULL AFTER observations,
ADD COLUMN follow_up_notes text DEFAULT NULL AFTER follow_up_date,
ADD COLUMN follow_up_status enum('pending','completed','cancelled') DEFAULT 'pending' AFTER follow_up_notes;
