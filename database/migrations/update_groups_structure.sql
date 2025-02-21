-- Adicionando novas colunas
ALTER TABLE groups
ADD COLUMN leader_id INT,
ADD COLUMN co_leader_id INT,
ADD COLUMN active TINYINT(1) DEFAULT 1;

-- Adicionando as foreign keys
ALTER TABLE groups
ADD FOREIGN KEY (leader_id) REFERENCES users(id),
ADD FOREIGN KEY (co_leader_id) REFERENCES users(id);

-- Removendo a coluna antiga (depois de migrar os dados)
-- ALTER TABLE groups DROP COLUMN leader_name;
