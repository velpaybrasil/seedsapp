-- Add neighborhood column to growth_groups table
ALTER TABLE growth_groups
ADD COLUMN neighborhood VARCHAR(255);
