-- Primeiro, vamos limpar os módulos duplicados mantendo apenas o mais recente
DELETE m1 FROM modules m1
INNER JOIN modules m2 
WHERE m1.name = m2.name 
AND m1.name = 'settings'
AND m1.id < m2.id;

-- Atualiza o módulo de configurações existente
UPDATE modules 
SET slug = 'settings', description = 'Configurações do Sistema'
WHERE name = 'settings';

-- Adiciona as permissões para o módulo de configurações se não existirem
INSERT IGNORE INTO permissions (module_id, name, slug, description)
SELECT 
    m.id,
    'Configurações - Gerenciar',
    'settings_manage',
    'Gerenciar configurações do sistema'
FROM modules m
WHERE m.name = 'settings'
ORDER BY m.id DESC
LIMIT 1;

-- Adiciona as permissões ao papel de administrador se não existirem
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT 
    r.id,
    p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'Administrador'
AND p.slug = 'settings_manage';
