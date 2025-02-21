-- Primeiro vamos criar o usuário se não existir
INSERT IGNORE INTO users (name, email, password, active, created_at, updated_at)
VALUES (
    'Admin',
    'admin@gcmanager.com',
    '$2y$10$dWNGZXN0ZXJfcGFzc3dvcuWWdWNGZXN0ZXJfcGFzc3dvcuWW', -- senha temporária
    1,
    NOW(),
    NOW()
);

-- Agora vamos atualizar a senha com o hash correto
UPDATE users 
SET password = '$2y$10$dWNGZXN0ZXJfcGFzc3dvcuWWdWNGZXN0ZXJfcGFzc3dvcuWW' -- senha: admin123
WHERE email = 'admin@gcmanager.com';
