-- Add group_id column to visitors table
ALTER TABLE visitors
ADD COLUMN group_id INT NULL,
ADD CONSTRAINT fk_visitors_group
FOREIGN KEY (group_id) REFERENCES growth_groups(id)
ON DELETE SET NULL;
