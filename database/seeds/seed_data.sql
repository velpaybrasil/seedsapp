    -- Desativa verificação de chaves estrangeiras temporariamente
    SET FOREIGN_KEY_CHECKS = 0;

    -- Limpa dados existentes (ordem correta para respeitar foreign keys)
    DELETE FROM growth_group_attendance;
    DELETE FROM growth_group_meetings;
    DELETE FROM growth_group_participants;
    DELETE FROM growth_groups;
    DELETE FROM volunteers;
    DELETE FROM visitors;
    DELETE FROM users;

    -- Reativa verificação de chaves estrangeiras
    SET FOREIGN_KEY_CHECKS = 1;

    -- Inserir usuários e guardar seus IDs em variáveis
    INSERT INTO users (name, email, password, active) VALUES
    ('João Silva', 'joao@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
    SET @user1_id = LAST_INSERT_ID();

    INSERT INTO users (name, email, password, active) VALUES
    ('Maria Santos', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
    SET @user2_id = LAST_INSERT_ID();

    INSERT INTO users (name, email, password, active) VALUES
    ('Pedro Oliveira', 'pedro@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
    SET @user3_id = LAST_INSERT_ID();

    INSERT INTO users (name, email, password, active) VALUES
    ('Ana Costa', 'ana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
    SET @user4_id = LAST_INSERT_ID();

    INSERT INTO users (name, email, password, active) VALUES
    ('Lucas Pereira', 'lucas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
    SET @user5_id = LAST_INSERT_ID();

    INSERT INTO users (name, email, password, active) VALUES
    ('Carla Souza', 'carla@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
    SET @user6_id = LAST_INSERT_ID();

    -- Inserir voluntários (líderes)
    INSERT INTO volunteers (user_id, ministry) VALUES
    (@user1_id, 'Grupos de Crescimento');
    SET @volunteer1_id = LAST_INSERT_ID();

    INSERT INTO volunteers (user_id, ministry) VALUES
    (@user2_id, 'Grupos de Crescimento');
    SET @volunteer2_id = LAST_INSERT_ID();

    INSERT INTO volunteers (user_id, ministry) VALUES
    (@user3_id, 'Grupos de Crescimento');
    SET @volunteer3_id = LAST_INSERT_ID();

    INSERT INTO volunteers (user_id, ministry) VALUES
    (@user4_id, 'Grupos de Crescimento');
    SET @volunteer4_id = LAST_INSERT_ID();

    INSERT INTO volunteers (user_id, ministry) VALUES
    (@user5_id, 'Grupos de Crescimento');
    SET @volunteer5_id = LAST_INSERT_ID();

    -- Inserir visitantes
    INSERT INTO visitors (name, email, phone, birth_date, address, neighborhood, city, first_visit_date, status, observations) VALUES
    ('Roberto Almeida', 'roberto@example.com', '(11) 98765-4321', '1990-05-15', 'Rua A, 123', 'Centro', 'São Paulo', '2024-01-01', 'new', 'Interessado em grupos de casais');
    SET @visitor1_id = LAST_INSERT_ID();

    INSERT INTO visitors (name, email, phone, birth_date, address, neighborhood, city, first_visit_date, status, observations) VALUES
    ('Fernanda Lima', 'fernanda@example.com', '(11) 98765-4322', '1995-08-20', 'Rua B, 456', 'Jardins', 'São Paulo', '2024-01-02', 'new', 'Primeira vez na igreja');
    SET @visitor2_id = LAST_INSERT_ID();

    INSERT INTO visitors (name, email, phone, birth_date, address, neighborhood, city, first_visit_date, status, observations) VALUES
    ('Carlos Santos', 'carlos@example.com', '(11) 98765-4323', '1988-03-10', 'Rua C, 789', 'Pinheiros', 'São Paulo', '2024-01-03', 'converted', 'Recém-batizado');
    SET @visitor3_id = LAST_INSERT_ID();

    INSERT INTO visitors (name, email, phone, birth_date, address, neighborhood, city, first_visit_date, status, observations) VALUES
    ('Patricia Costa', 'patricia@example.com', '(11) 98765-4324', '1992-11-25', 'Rua D, 321', 'Vila Mariana', 'São Paulo', '2024-01-04', 'new', 'Conheceu através de amigos');
    SET @visitor4_id = LAST_INSERT_ID();

    INSERT INTO visitors (name, email, phone, birth_date, address, neighborhood, city, first_visit_date, status, observations) VALUES
    ('Marcos Oliveira', 'marcos@example.com', '(11) 98765-4325', '1985-07-30', 'Rua E, 654', 'Moema', 'São Paulo', '2024-01-05', 'converted', 'Frequenta há 2 meses');
    SET @visitor5_id = LAST_INSERT_ID();

    -- Inserir grupos de crescimento
    INSERT INTO growth_groups (name, description, meeting_day, meeting_time, leader_id, co_leader_id, max_participants, status) VALUES
    ('Grupo Vida Nova', 'Grupo para novos convertidos', 'Segunda-feira', '19:30:00', @user1_id, @volunteer2_id, 12, 'active');
    SET @group1_id = LAST_INSERT_ID();

    INSERT INTO growth_groups (name, description, meeting_day, meeting_time, leader_id, co_leader_id, max_participants, status) VALUES
    ('Grupo Jovem', 'Grupo para jovens entre 18-30 anos', 'Terça-feira', '20:00:00', @user3_id, NULL, 15, 'active');
    SET @group2_id = LAST_INSERT_ID();

    INSERT INTO growth_groups (name, description, meeting_day, meeting_time, leader_id, co_leader_id, max_participants, status) VALUES
    ('Grupo Casais', 'Grupo para casais', 'Quarta-feira', '20:00:00', @user4_id, @volunteer5_id, 10, 'active');
    SET @group3_id = LAST_INSERT_ID();

    -- Inserir participantes nos grupos
    INSERT INTO growth_group_participants (group_id, visitor_id, join_date, status) VALUES
    (@group1_id, @visitor1_id, '2024-01-15', 'active');
    SET @participant1_id = LAST_INSERT_ID();

    INSERT INTO growth_group_participants (group_id, visitor_id, join_date, status) VALUES
    (@group1_id, @visitor2_id, '2024-01-15', 'active');
    SET @participant2_id = LAST_INSERT_ID();

    INSERT INTO growth_group_participants (group_id, visitor_id, join_date, status) VALUES
    (@group2_id, @visitor3_id, '2024-01-16', 'active');
    SET @participant3_id = LAST_INSERT_ID();

    INSERT INTO growth_group_participants (group_id, visitor_id, join_date, status) VALUES
    (@group2_id, @visitor4_id, '2024-01-16', 'active');
    SET @participant4_id = LAST_INSERT_ID();

    INSERT INTO growth_group_participants (group_id, visitor_id, join_date, status) VALUES
    (@group3_id, @visitor5_id, '2024-01-17', 'active');
    SET @participant5_id = LAST_INSERT_ID();

    -- Inserir reuniões dos grupos
    INSERT INTO growth_group_meetings (group_id, meeting_date, topic, notes) VALUES
    (@group1_id, '2024-01-15', 'Introdução à Vida Cristã', 'Primeira reunião do ano');
    SET @meeting1_id = LAST_INSERT_ID();

    INSERT INTO growth_group_meetings (group_id, meeting_date, topic, notes) VALUES
    (@group1_id, '2024-01-22', 'Fundamentos da Fé', 'Segunda reunião');
    SET @meeting2_id = LAST_INSERT_ID();

    INSERT INTO growth_group_meetings (group_id, meeting_date, topic, notes) VALUES
    (@group2_id, '2024-01-16', 'Desafios da Juventude', 'Boa participação');
    SET @meeting3_id = LAST_INSERT_ID();

    INSERT INTO growth_group_meetings (group_id, meeting_date, topic, notes) VALUES
    (@group2_id, '2024-01-23', 'Relacionamentos', 'Discussão produtiva');
    SET @meeting4_id = LAST_INSERT_ID();

    INSERT INTO growth_group_meetings (group_id, meeting_date, topic, notes) VALUES
    (@group3_id, '2024-01-17', 'Casamento Cristão', 'Todos os casais presentes');
    SET @meeting5_id = LAST_INSERT_ID();

    INSERT INTO growth_group_meetings (group_id, meeting_date, topic, notes) VALUES
    (@group3_id, '2024-01-24', 'Finanças no Casamento', 'Tema importante');
    SET @meeting6_id = LAST_INSERT_ID();

    -- Inserir presenças nas reuniões
    INSERT INTO growth_group_attendance (meeting_id, participant_id, present, notes) VALUES
    (@meeting1_id, @participant1_id, 1, 'Participou ativamente'),
    (@meeting1_id, @participant2_id, 1, 'Fez várias perguntas'),
    (@meeting2_id, @participant1_id, 1, 'Trouxe um visitante'),
    (@meeting2_id, @participant2_id, 0, 'Avisou que não poderia vir'),
    (@meeting3_id, @participant3_id, 1, 'Compartilhou testemunho'),
    (@meeting3_id, @participant4_id, 1, 'Participou do louvor'),
    (@meeting4_id, @participant3_id, 1, 'Contribuiu na discussão'),
    (@meeting4_id, @participant4_id, 1, 'Trouxe lanche'),
    (@meeting5_id, @participant5_id, 1, 'Casal muito participativo'),
    (@meeting6_id, @participant5_id, 1, 'Compartilhou experiências');

    -- Senha padrão para todos os usuários: 'password'
