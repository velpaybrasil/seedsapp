-- Configurações para visitantes
INSERT IGNORE INTO system_settings (category, key_name, value, value_type, description, is_public) VALUES
('visitor_rules', 'auto_approve_visitors', '0', 'boolean', 'Se habilitado, visitantes serão automaticamente aprovados como usuários do sistema', false),
('visitor_rules', 'required_visits', '2', 'integer', 'Número de visitas necessárias antes de se tornar membro', false),
('visitor_rules', 'follow_up_days', '7', 'integer', 'Dias para fazer follow-up após a primeira visita', false),
('visitor_rules', 'default_visitor_role', 'visitor', 'string', 'Papel padrão atribuído a novos visitantes', false);

-- Configurações de grupos
INSERT IGNORE INTO system_settings (category, key_name, value, value_type, description, is_public) VALUES
('group_settings', 'max_members', '15', 'integer', 'Número máximo de membros por grupo', false),
('group_settings', 'allow_multiple_groups', '1', 'boolean', 'Permite que membros participem de múltiplos grupos', false),
('group_settings', 'auto_approve_requests', '0', 'boolean', 'Aprova automaticamente pedidos de participação em grupos', false);

-- Configurações de notificações
INSERT IGNORE INTO system_settings (category, key_name, value, value_type, description, is_public) VALUES
('notification_settings', 'email_notifications', '1', 'boolean', 'Habilita notificações por e-mail', false),
('notification_settings', 'notify_leaders', '1', 'boolean', 'Notifica líderes sobre novos membros e solicitações', false),
('notification_settings', 'notification_types', '["email", "system", "sms"]', 'array', 'Tipos de notificação disponíveis', false);

-- Configurações de relatórios
INSERT IGNORE INTO system_settings (category, key_name, value, value_type, description, is_public) VALUES
('report_settings', 'attendance_threshold', '75', 'integer', 'Porcentagem mínima de presença para membros ativos', false),
('report_settings', 'inactive_days', '30', 'integer', 'Dias sem atividade para considerar membro inativo', false),
('report_settings', 'report_metrics', '{"attendance": true, "growth": true, "engagement": true}', 'json', 'Métricas a serem incluídas nos relatórios', false);
