-- Tabela de módulos
CREATE TABLE IF NOT EXISTS modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

-- Tabela de papéis (roles)
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

-- Tabela de papéis dos usuários
CREATE TABLE IF NOT EXISTS user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_role (user_id, role_id)
);

-- Tabela de permissões
CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_permission_slug (slug)
);

-- Tabela de permissões dos papéis
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
);

-- Inserir módulos padrão
INSERT INTO modules (name, description, icon, created_at, updated_at) VALUES
('Usuários', 'Gerenciamento de usuários do sistema', 'users', NOW(), NOW()),
('Grupos', 'Gerenciamento de grupos de crescimento', 'users-class', NOW(), NOW()),
('Visitantes', 'Gerenciamento de visitantes', 'user-plus', NOW(), NOW()),
('Ministérios', 'Gerenciamento de ministérios', 'church', NOW(), NOW()),
('Financeiro', 'Gerenciamento financeiro', 'dollar-sign', NOW(), NOW()),
('Relatórios', 'Relatórios e estatísticas', 'chart-bar', NOW(), NOW());

-- Inserir papéis padrão
INSERT INTO roles (name, description, active, created_at, updated_at) VALUES
('Administrador', 'Acesso total ao sistema', 1, NOW(), NOW()),
('Líder', 'Líder de grupo de crescimento', 1, NOW(), NOW()),
('Membro', 'Membro da igreja', 1, NOW(), NOW());

-- Inserir permissões padrão
INSERT INTO permissions (module_id, name, slug, description, active, created_at, updated_at)
SELECT 
    m.id,
    CONCAT(m.name, ' - Visualizar'),
    CONCAT(LOWER(REPLACE(m.name, ' ', '_')), '_view'),
    CONCAT('Permissão para visualizar ', LOWER(m.name)),
    1,
    NOW(),
    NOW()
FROM modules m
UNION ALL
SELECT 
    m.id,
    CONCAT(m.name, ' - Criar'),
    CONCAT(LOWER(REPLACE(m.name, ' ', '_')), '_create'),
    CONCAT('Permissão para criar ', LOWER(m.name)),
    1,
    NOW(),
    NOW()
FROM modules m
UNION ALL
SELECT 
    m.id,
    CONCAT(m.name, ' - Editar'),
    CONCAT(LOWER(REPLACE(m.name, ' ', '_')), '_edit'),
    CONCAT('Permissão para editar ', LOWER(m.name)),
    1,
    NOW(),
    NOW()
FROM modules m
UNION ALL
SELECT 
    m.id,
    CONCAT(m.name, ' - Excluir'),
    CONCAT(LOWER(REPLACE(m.name, ' ', '_')), '_delete'),
    CONCAT('Permissão para excluir ', LOWER(m.name)),
    1,
    NOW(),
    NOW()
FROM modules m;

-- Atribuir todas as permissões ao papel de Administrador
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Administrador'),
    p.id,
    NOW()
FROM permissions p;

-- Atribuir permissões básicas ao papel de Líder
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Líder'),
    p.id,
    NOW()
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
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 
    (SELECT id FROM roles WHERE name = 'Membro'),
    p.id,
    NOW()
FROM permissions p
WHERE p.slug IN (
    'grupos_view',
    'visitantes_view'
);
