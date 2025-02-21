-- Adiciona usuário administrativo
INSERT INTO users (name, email, password, active, role) VALUES 
('Paulo Gustavo', 'pgustavodlima@gmail.com', '$2y$10$Hh5lwuP4iN9V8h2qzEb0OOu1z1Vx8/YwvHYHLvjTHN9KL4jk4IXLC', 1, 'admin');

-- Pega o ID do usuário inserido
SET @admin_user_id = LAST_INSERT_ID();

-- Adiciona como voluntário
INSERT INTO volunteers (user_id, ministry) VALUES
(@admin_user_id, 'Administrador');
