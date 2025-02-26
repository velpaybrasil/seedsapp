-- Adiciona novo usuário administrativo
INSERT INTO users (
    email,
    password,
    name,
    active,
    created_at,
    updated_at
) VALUES (
    'admin@paulogustavo.me',
    '$2y$10$Wd8/uvqQNAZGg8oNOGWBFOWpVJ4kFl.evi3UZVVvyxZQrFiKWzVPi', -- hash para '123456789'
    'Paulo Gustavo Admin',
    1,
    NOW(),
    NOW()
);

-- Busca o ID do usuário recém criado
SET @new_admin_id = LAST_INSERT_ID();

-- Busca o ID do papel de administrador
SET @admin_role_id = (SELECT id FROM roles WHERE name = 'Administrador' LIMIT 1);

-- Atribui papel de administrador ao novo usuário
INSERT INTO user_roles (user_id, role_id, created_at)
VALUES (@new_admin_id, @admin_role_id, NOW());
