-- Criar tabela de permissões
CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    UNIQUE KEY unique_menu_action (menu_id, action),
    FOREIGN KEY (menu_id) REFERENCES system_menu(id)
);

-- Criar tabela de papéis
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    UNIQUE KEY unique_role_name (name)
);

-- Criar tabela de permissões de papéis
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
);

-- Criar tabela de papéis de usuários
CREATE TABLE IF NOT EXISTS user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    UNIQUE KEY unique_user_role (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Inserir papel de administrador
INSERT INTO roles (name, description) VALUES
('admin', 'Administrador do sistema');

-- Inserir permissões para cada módulo do menu
INSERT INTO permissions (menu_id, action, description)
SELECT 
    id,
    'manage',
    CONCAT('Gerenciar ', name)
FROM system_menu;

-- Dar todas as permissões ao papel de administrador
INSERT INTO role_permissions (role_id, permission_id)
SELECT 
    r.id,
    p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'admin';

-- Dar papel de administrador ao usuário 1 (primeiro usuário)
INSERT INTO user_roles (user_id, role_id)
SELECT 
    1,
    r.id
FROM roles r
WHERE r.name = 'admin';
