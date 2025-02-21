-- Primeiro, vamos limpar usuários antigos de teste
DELETE FROM users WHERE email IN ('admin@paulogustavo.me', 'oi@paulogustavo.me');

-- Agora criar o novo usuário admin
INSERT INTO users (
    name,
    email,
    password,
    role,
    is_owner,
    active,
    created_at,
    updated_at
) VALUES (
    'Paulo Gustavo',
    'admin@paulogustavo.me',
    '$2y$10$QjmAMtMcctzNxBDCn9uaMeJCrRUNlPs2NdVzrOFwFq/1xjE2EoelG',  -- senha: 12345678
    'admin',
    1,
    1,
    NOW(),
    NOW()
);
