-- Inserindo Grupos de Crescimento
INSERT INTO groups (name, leader_name, meeting_day, meeting_time, address, neighborhood, city, state, capacity, description, created_at, updated_at) VALUES
('GC Jovens Adoradores', 'Lucas Silva', 'Sábado', '19:30:00', 'Rua das Flores, 123', 'Centro', 'São Paulo', 'SP', 15, 'Grupo focado em jovens com ênfase em louvor e adoração', NOW(), NOW()),
('GC Família Feliz', 'Maria Santos', 'Quarta', '20:00:00', 'Av. Principal, 456', 'Jardim América', 'São Paulo', 'SP', 12, 'Grupo para casais e famílias', NOW(), NOW()),
('GC Universitários', 'Pedro Costa', 'Sexta', '19:00:00', 'Rua do Conhecimento, 789', 'Vila Universitária', 'São Paulo', 'SP', 20, 'Grupo dedicado a estudantes universitários', NOW(), NOW()),
('GC Mulheres de Fé', 'Ana Oliveira', 'Terça', '15:00:00', 'Rua das Margaridas, 321', 'Jardim Flora', 'São Paulo', 'SP', 15, 'Grupo exclusivo para mulheres', NOW(), NOW()),
('GC Homens de Valor', 'João Pereira', 'Quinta', '19:30:00', 'Rua dos Ipês, 654', 'Vila Nova', 'São Paulo', 'SP', 15, 'Grupo exclusivo para homens', NOW(), NOW()),
('GC Terceira Idade', 'Beatriz Lima', 'Segunda', '14:00:00', 'Av. da Sabedoria, 987', 'Bela Vista', 'São Paulo', 'SP', 12, 'Grupo para pessoas da melhor idade', NOW(), NOW()),
('GC Profissionais', 'Carlos Mendes', 'Quarta', '19:00:00', 'Rua do Trabalho, 147', 'Moema', 'São Paulo', 'SP', 15, 'Grupo para profissionais e empreendedores', NOW(), NOW()),
('GC Arte e Fé', 'Juliana Martins', 'Sábado', '15:00:00', 'Rua das Artes, 258', 'Vila Madalena', 'São Paulo', 'SP', 15, 'Grupo com foco em artes e criatividade', NOW(), NOW()),
('GC Esporte e Vida', 'Rafael Souza', 'Domingo', '08:00:00', 'Av. do Esporte, 369', 'Pacaembu', 'São Paulo', 'SP', 20, 'Grupo para praticantes de esportes', NOW(), NOW()),
('GC Novos na Fé', 'Fernanda Lima', 'Segunda', '19:30:00', 'Rua da Esperança, 741', 'Santana', 'São Paulo', 'SP', 12, 'Grupo para novos convertidos', NOW(), NOW());

-- Inserindo dados de teste para grupos
INSERT INTO groups (name, description, leader_id, co_leader_id, active) VALUES
('Grupo de Teste 1', 'Descrição do Grupo de Teste 1', 1, 2, 1),
('Grupo de Teste 2', 'Descrição do Grupo de Teste 2', 1, 3, 1);

-- Inserindo Membros nos Grupos
INSERT INTO members (group_id, name, email, phone, birth_date, address, created_at, updated_at) VALUES
-- GC Jovens Adoradores
(1, 'Gabriel Santos', 'gabriel@email.com', '11999991111', '2000-03-15', 'Rua A, 123', NOW(), NOW()),
(1, 'Isabella Oliveira', 'isabella@email.com', '11999992222', '2001-07-22', 'Rua B, 456', NOW(), NOW()),
(1, 'Matheus Lima', 'matheus@email.com', '11999993333', '1999-11-30', 'Rua C, 789', NOW(), NOW()),

-- GC Família Feliz
(2, 'Ricardo e Ana Silva', 'ricardo@email.com', '11999994444', '1985-04-10', 'Av. D, 147', NOW(), NOW()),
(2, 'Paulo e Carla Souza', 'paulo@email.com', '11999995555', '1982-08-25', 'Av. E, 258', NOW(), NOW()),
(2, 'José e Maria Santos', 'jose@email.com', '11999996666', '1979-12-03', 'Av. F, 369', NOW(), NOW()),

-- GC Universitários
(3, 'Larissa Costa', 'larissa@email.com', '11999997777', '2002-01-20', 'Rua G, 147', NOW(), NOW()),
(3, 'Felipe Santos', 'felipe@email.com', '11999998888', '2001-05-15', 'Rua H, 258', NOW(), NOW()),
(3, 'Marina Lima', 'marina@email.com', '11999999999', '2003-09-08', 'Rua I, 369', NOW(), NOW()),

-- GC Mulheres de Fé
(4, 'Sandra Oliveira', 'sandra@email.com', '11988881111', '1975-06-12', 'Av. J, 147', NOW(), NOW()),
(4, 'Regina Santos', 'regina@email.com', '11988882222', '1980-02-28', 'Av. K, 258', NOW(), NOW()),
(4, 'Patrícia Lima', 'patricia@email.com', '11988883333', '1978-10-15', 'Av. L, 369', NOW(), NOW()),

-- GC Homens de Valor
(5, 'Roberto Silva', 'roberto@email.com', '11988884444', '1970-07-20', 'Rua M, 147', NOW(), NOW()),
(5, 'Marcos Souza', 'marcos@email.com', '11988885555', '1973-03-05', 'Rua N, 258', NOW(), NOW()),
(5, 'André Lima', 'andre@email.com', '11988886666', '1968-11-18', 'Rua O, 369', NOW(), NOW()),

-- GC Terceira Idade
(6, 'Antônio Santos', 'antonio@email.com', '11988887777', '1955-08-30', 'Av. P, 147', NOW(), NOW()),
(6, 'Teresa Silva', 'teresa@email.com', '11988888888', '1958-04-25', 'Av. Q, 258', NOW(), NOW()),
(6, 'José Carlos Lima', 'josecarlos@email.com', '11988889999', '1952-12-10', 'Av. R, 369', NOW(), NOW()),

-- GC Profissionais
(7, 'Eduardo Mendes', 'eduardo@email.com', '11977771111', '1988-09-15', 'Rua S, 147', NOW(), NOW()),
(7, 'Camila Santos', 'camila@email.com', '11977772222', '1990-05-20', 'Rua T, 258', NOW(), NOW()),
(7, 'Rodrigo Lima', 'rodrigo@email.com', '11977773333', '1985-01-08', 'Rua U, 369', NOW(), NOW()),

-- GC Arte e Fé
(8, 'Bianca Oliveira', 'bianca@email.com', '11977774444', '1995-02-28', 'Av. V, 147', NOW(), NOW()),
(8, 'Thiago Santos', 'thiago@email.com', '11977775555', '1993-06-15', 'Av. W, 258', NOW(), NOW()),
(8, 'Laura Lima', 'laura@email.com', '11977776666', '1997-10-22', 'Av. X, 369', NOW(), NOW()),

-- GC Esporte e Vida
(9, 'Bruno Souza', 'bruno@email.com', '11977777777', '1992-03-18', 'Rua Y, 147', NOW(), NOW()),
(9, 'Carla Santos', 'carla@email.com', '11977778888', '1994-07-25', 'Rua Z, 258', NOW(), NOW()),
(9, 'Diego Lima', 'diego@email.com', '11977779999', '1991-11-30', 'Rua AA, 369', NOW(), NOW()),

-- GC Novos na Fé
(10, 'Fábio Oliveira', 'fabio@email.com', '11966661111', '1987-04-12', 'Av. BB, 147', NOW(), NOW()),
(10, 'Renata Santos', 'renata@email.com', '11966662222', '1989-08-28', 'Av. CC, 258', NOW(), NOW()),
(10, 'Gustavo Lima', 'gustavo@email.com', '11966663333', '1986-12-15', 'Av. DD, 369', NOW(), NOW());

-- Inserindo alguns visitantes para os grupos
INSERT INTO visitors (name, email, phone, status, gc_id, created_at, updated_at) VALUES
('Amanda Silva', 'amanda@email.com', '11966664444', 'lead', 1, NOW(), NOW()),
('Ricardo Oliveira', 'ricardo.o@email.com', '11966665555', 'contacted', 2, NOW(), NOW()),
('Mariana Santos', 'mariana@email.com', '11966666666', 'gc_linked', 3, NOW(), NOW()),
('Paulo Souza', 'paulo.s@email.com', '11966667777', 'lead', 4, NOW(), NOW()),
('Cristina Lima', 'cristina@email.com', '11966668888', 'contacted', 5, NOW(), NOW()),
('Fernando Silva', 'fernando@email.com', '11966669999', 'gc_linked', 6, NOW(), NOW()),
('Luciana Santos', 'luciana@email.com', '11955551111', 'lead', 7, NOW(), NOW()),
('Roberto Oliveira', 'roberto.o@email.com', '11955552222', 'contacted', 8, NOW(), NOW()),
('Carolina Lima', 'carolina@email.com', '11955553333', 'gc_linked', 9, NOW(), NOW()),
('Daniel Souza', 'daniel@email.com', '11955554444', 'lead', 10, NOW(), NOW());
