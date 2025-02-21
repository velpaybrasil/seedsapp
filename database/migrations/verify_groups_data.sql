-- Verificar se todos os grupos têm líderes válidos
SELECT g.id, g.name, u.name as leader_name, u2.name as co_leader_name
FROM groups g
LEFT JOIN users u ON g.leader_id = u.id
LEFT JOIN users u2 ON g.co_leader_id = u2.id;
