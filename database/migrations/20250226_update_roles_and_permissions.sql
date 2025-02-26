-- Atualizar os papéis do sistema
UPDATE roles 
SET name = 'admin', description = 'Administrador do sistema com acesso total'
WHERE id = 1;

-- Adicionar novos papéis
INSERT INTO roles (name, description, is_system, active) VALUES
('coordinator', 'Coordenador com acesso a gerenciamento de grupos', TRUE, TRUE),
('leader', 'Líder de grupo com acesso limitado', TRUE, TRUE);

-- Permissões para Coordenador
INSERT INTO role_permissions (role_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'coordinator'),
    p.id
FROM permissions p
INNER JOIN modules m ON p.module_id = m.id
WHERE m.slug IN ('groups', 'visitors', 'dashboard')
OR (m.slug = 'reports' AND p.slug = 'view');

-- Permissões para Líder
INSERT INTO role_permissions (role_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'leader'),
    p.id
FROM permissions p
INNER JOIN modules m ON p.module_id = m.id
WHERE (m.slug = 'groups' AND p.slug IN ('view', 'manage_members'))
OR (m.slug = 'dashboard' AND p.slug = 'view');
