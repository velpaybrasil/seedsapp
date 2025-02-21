-- Verificar se há registros órfãos em members
SELECT m.id, m.group_id
FROM members m
LEFT JOIN growth_groups g ON m.group_id = g.id
WHERE m.group_id IS NOT NULL 
AND g.id IS NULL;

-- Verificar se há registros órfãos em visitors
SELECT v.id, v.group_id
FROM visitors v
LEFT JOIN growth_groups g ON v.group_id = g.id
WHERE v.group_id IS NOT NULL 
AND g.id IS NULL;

-- Verificar se há registros órfãos em attendances
SELECT a.id, a.group_id
FROM attendances a
LEFT JOIN growth_groups g ON a.group_id = g.id
WHERE a.group_id IS NOT NULL 
AND g.id IS NULL;
