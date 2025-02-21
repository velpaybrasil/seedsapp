-- Primeiro, vamos desativar o usuário anterior
UPDATE users SET active = 0 WHERE email = 'oi@paulogustavo.me';

-- Agora, criar um novo usuário admin
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
    '$2y$10$QjmAMtMcctzNxBDCn9uaMeJCrRUNlPs2NdVzrOFwFq/1xjE2EoelG',  -- senha: 12345678 (hash do último log)
    'admin',
    1,
    1,
    NOW(),
    NOW()
);
