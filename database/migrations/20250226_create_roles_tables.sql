-- Drop existing tables in reverse order to handle foreign keys
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS modules;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create modules table
CREATE TABLE IF NOT EXISTS modules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    slug VARCHAR(255) NOT NULL UNIQUE,
    icon VARCHAR(50),
    order_index INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_system BOOLEAN DEFAULT FALSE,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create permissions table
CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slug (module_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create role_permissions table
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user_roles table
CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert default system modules
INSERT INTO modules (name, description, slug, icon, order_index, is_active) VALUES
('Dashboard', 'Módulo principal do sistema', 'dashboard', 'fas fa-tachometer-alt', 1, TRUE),
('Usuários', 'Gerenciamento de usuários e permissões', 'users', 'fas fa-users', 2, TRUE),
('Grupos', 'Gerenciamento de grupos', 'groups', 'fas fa-users-cog', 3, TRUE),
('Visitantes', 'Gerenciamento de visitantes', 'visitors', 'fas fa-user-friends', 4, TRUE),
('Ministérios', 'Gerenciamento de ministérios', 'ministries', 'fas fa-church', 5, TRUE),
('Relatórios', 'Relatórios e estatísticas', 'reports', 'fas fa-chart-bar', 6, TRUE),
('Configurações', 'Configurações do sistema', 'settings', 'fas fa-cog', 7, TRUE);

-- Insert permissions for Dashboard module
INSERT INTO permissions (module_id, name, slug, description) VALUES
(1, 'Visualizar Dashboard', 'view', 'Permite visualizar o dashboard');

-- Insert permissions for Users module
INSERT INTO permissions (module_id, name, slug, description) VALUES
(2, 'Visualizar Usuários', 'view', 'Permite visualizar a lista de usuários'),
(2, 'Criar Usuários', 'create', 'Permite criar novos usuários'),
(2, 'Editar Usuários', 'edit', 'Permite editar usuários existentes'),
(2, 'Excluir Usuários', 'delete', 'Permite excluir usuários'),
(2, 'Gerenciar Papéis', 'manage_roles', 'Permite gerenciar papéis de usuário');

-- Insert permissions for Groups module
INSERT INTO permissions (module_id, name, slug, description) VALUES
(3, 'Visualizar Grupos', 'view', 'Permite visualizar a lista de grupos'),
(3, 'Criar Grupos', 'create', 'Permite criar novos grupos'),
(3, 'Editar Grupos', 'edit', 'Permite editar grupos existentes'),
(3, 'Excluir Grupos', 'delete', 'Permite excluir grupos'),
(3, 'Gerenciar Membros', 'manage_members', 'Permite gerenciar membros dos grupos');

-- Insert permissions for Visitors module
INSERT INTO permissions (module_id, name, slug, description) VALUES
(4, 'Visualizar Visitantes', 'view', 'Permite visualizar a lista de visitantes'),
(4, 'Criar Visitantes', 'create', 'Permite criar novos visitantes'),
(4, 'Editar Visitantes', 'edit', 'Permite editar visitantes existentes'),
(4, 'Excluir Visitantes', 'delete', 'Permite excluir visitantes'),
(4, 'Exportar Visitantes', 'export', 'Permite exportar dados de visitantes');

-- Insert permissions for Ministries module
INSERT INTO permissions (module_id, name, slug, description) VALUES
(5, 'Visualizar Ministérios', 'view', 'Permite visualizar a lista de ministérios'),
(5, 'Criar Ministérios', 'create', 'Permite criar novos ministérios'),
(5, 'Editar Ministérios', 'edit', 'Permite editar ministérios existentes'),
(5, 'Excluir Ministérios', 'delete', 'Permite excluir ministérios');

-- Insert permissions for Reports module
INSERT INTO permissions (module_id, name, slug, description) VALUES
(6, 'Visualizar Relatórios', 'view', 'Permite visualizar relatórios'),
(6, 'Exportar Relatórios', 'export', 'Permite exportar relatórios');

-- Insert permissions for Settings module
INSERT INTO permissions (module_id, name, slug, description) VALUES
(7, 'Visualizar Configurações', 'view', 'Permite visualizar as configurações do sistema'),
(7, 'Editar Configurações', 'edit', 'Permite editar as configurações do sistema');

-- Insert default system roles
INSERT INTO roles (name, description, is_system, active) VALUES
('Administrador', 'Acesso total ao sistema', TRUE, TRUE),
('Usuário', 'Acesso básico ao sistema', TRUE, TRUE);

-- Grant all permissions to the Administrator role
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;
