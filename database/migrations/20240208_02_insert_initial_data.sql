-- Limpar dados existentes (na ordem correta devido às chaves estrangeiras)
DELETE FROM role_permissions;
DELETE FROM permissions;
DELETE FROM user_roles;
DELETE FROM roles;
DELETE FROM modules;

-- Inserir módulos padrão
INSERT INTO modules (name, description, icon, slug) VALUES
('Usuários', 'Gerenciamento de usuários do sistema', 'users', 'usuarios'),
('Grupos', 'Gerenciamento de grupos de crescimento', 'users-class', 'grupos'),
('Visitantes', 'Gerenciamento de visitantes', 'user-plus', 'visitantes'),
('Ministérios', 'Gerenciamento de ministérios', 'church', 'ministerios'),
('Financeiro', 'Gerenciamento financeiro', 'dollar-sign', 'financeiro'),
('Relatórios', 'Relatórios e estatísticas', 'chart-bar', 'relatorios');

-- Inserir papéis padrão
INSERT INTO roles (name, description) VALUES
('Administrador', 'Acesso total ao sistema'),
('Líder', 'Líder de grupo de crescimento'),
('Membro', 'Membro da igreja');

-- Inserir permissões padrão
INSERT INTO permissions (module_id, name, slug, description)
SELECT 
    m.id,
    CONCAT(m.name, ' - Visualizar'),
    CONCAT(m.slug, '_view'),
    CONCAT('Permissão para visualizar ', LOWER(m.name))
FROM modules m
UNION ALL
SELECT 
    m.id,
    CONCAT(m.name, ' - Criar'),
    CONCAT(m.slug, '_create'),
    CONCAT('Permissão para criar ', LOWER(m.name))
FROM modules m
UNION ALL
SELECT 
    m.id,
    CONCAT(m.name, ' - Editar'),
    CONCAT(m.slug, '_edit'),
    CONCAT('Permissão para editar ', LOWER(m.name))
FROM modules m
UNION ALL
SELECT 
    m.id,
    CONCAT(m.name, ' - Excluir'),
    CONCAT(m.slug, '_delete'),
    CONCAT('Permissão para excluir ', LOWER(m.name))
FROM modules m;

-- Atribuir todas as permissões ao papel de Administrador
INSERT INTO role_permissions (role_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'Administrador'),
    p.id
FROM permissions p;

-- Atribuir permissões básicas ao papel de Líder
INSERT INTO role_permissions (role_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'Líder'),
    p.id
FROM permissions p
WHERE p.slug IN (
    'grupos_view',
    'grupos_create',
    'grupos_edit',
    'visitantes_view',
    'visitantes_create',
    'visitantes_edit'
);

-- Atribuir permissões básicas ao papel de Membro
INSERT INTO role_permissions (role_id, permission_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'Membro'),
    p.id
FROM permissions p
WHERE p.slug IN (
    'grupos_view',
    'visitantes_view'
);
