-- Criação dos usuários administradores
INSERT INTO users (email, password, name, active, created_at, updated_at) VALUES
('cintiambslima@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cintia Lima', 1, NOW(), NOW()),
('ceo@alfadev.online', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Paulo Gustavo', 1, NOW(), NOW()),
('mateushenrique@ccvideira.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mateus Henrique', 1, NOW(), NOW());

-- Buscar o ID do papel de administrador
SET @admin_role_id = (SELECT id FROM roles WHERE name = 'Administrador' LIMIT 1);

-- Atribuir papel de administrador aos usuários
INSERT INTO user_roles (user_id, role_id, created_at) 
SELECT u.id, @admin_role_id, NOW()
FROM users u 
WHERE u.email IN ('cintiambslima@gmail.com', 'ceo@alfadev.online', 'mateushenrique@ccvideira.com.br');
