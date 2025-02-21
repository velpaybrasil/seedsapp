ALTER TABLE `visitor_contact_logs`
ADD COLUMN `follow_up_date` date DEFAULT NULL AFTER `content`,
ADD COLUMN `follow_up_notes` text AFTER `follow_up_date`,
ADD COLUMN `follow_up_status` enum('pending','completed','cancelled') DEFAULT 'pending' AFTER `follow_up_notes`;
