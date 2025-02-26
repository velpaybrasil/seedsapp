-- Insert default roles
INSERT INTO roles (name, description, is_system, active) VALUES
('admin', 'Administrador do sistema com acesso total', TRUE, TRUE),
('coordinator', 'Coordenador com acesso a gerenciamento de grupos', TRUE, TRUE),
('leader', 'Líder de grupo com acesso limitado', TRUE, TRUE),
('user', 'Usuário padrão do sistema', TRUE, TRUE);

-- Insert role permissions for admin
INSERT INTO role_permissions (role_id, module_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'admin'),
    module_id,
    id
FROM permissions;

-- Insert role permissions for coordinator
INSERT INTO role_permissions (role_id, module_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'coordinator'),
    module_id,
    id
FROM permissions 
WHERE module_id IN (
    SELECT id FROM modules WHERE slug IN ('groups', 'visitors')
);

-- Insert role permissions for leader
INSERT INTO role_permissions (role_id, module_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'leader'),
    module_id,
    id
FROM permissions 
WHERE module_id IN (
    SELECT id FROM modules WHERE slug = 'groups'
)
AND action IN ('view', 'manage_members');
