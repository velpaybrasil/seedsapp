-- Insert default admin user if it doesn't exist
INSERT INTO users (name, email, password, active)
SELECT 'Paulo Gustavo de Lima', 'pgustavodlima@gmail.com', '$2y$10$Hs3HVDjwmGJzYDCfYCGBZOsGkFxXwVDLIlhZTKPnXWmxhJgQCuM1.', 1
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'pgustavodlima@gmail.com');

-- Assign admin role to admin user
INSERT INTO user_roles (user_id, role_id)
SELECT u.id, r.id
FROM users u
JOIN roles r ON r.name = 'Administrador'
WHERE u.email = 'pgustavodlima@gmail.com'
AND NOT EXISTS (
    SELECT 1 
    FROM user_roles ur 
    WHERE ur.user_id = u.id 
    AND ur.role_id = r.id
);
