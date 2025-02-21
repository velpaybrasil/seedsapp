-- Add ministry_id column to growth_groups table
ALTER TABLE growth_groups
ADD COLUMN ministry_id INT NULL,
ADD FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE SET NULL;
