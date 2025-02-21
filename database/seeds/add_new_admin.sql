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
    'oi@paulogustavo.me',
    '$2y$10$zKv3.e9KVgTXXWqMSvgVr.cYKGJ3wPa5gUOxGYqV8q9RbO3.YNBP.',  -- senha: 12345678
    'admin',
    1,
    1,
    NOW(),
    NOW()
);
