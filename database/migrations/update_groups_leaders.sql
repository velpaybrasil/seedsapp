-- Primeiro, vamos garantir que os líderes existam como usuários
INSERT INTO users (name, email, password, role, active)
SELECT DISTINCT 
    leader_name,  -- nome do líder
    LOWER(CONCAT(REPLACE(leader_name, ' ', '.'), '@example.com')),  -- email gerado
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- senha padrão: 'password'
    'user',  -- role padrão
    1  -- active
FROM groups 
WHERE leader_name IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM users u WHERE u.name = groups.leader_name
);

-- Agora, vamos atualizar os groups com os IDs dos líderes
UPDATE groups g
INNER JOIN users u ON u.name = g.leader_name
SET g.leader_id = u.id
WHERE g.leader_name IS NOT NULL;

-- Agora podemos remover a coluna leader_name com segurança
ALTER TABLE groups DROP COLUMN leader_name;

-- Criar nova tabela para líderes de grupos
CREATE TABLE IF NOT EXISTS group_leaders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('leader', 'co_leader') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES growth_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_user_role (group_id, user_id, role)
);

-- Adicionar campos de geolocalização
ALTER TABLE growth_groups
ADD COLUMN latitude DECIMAL(10, 8) NULL,
ADD COLUMN longitude DECIMAL(11, 8) NULL;
