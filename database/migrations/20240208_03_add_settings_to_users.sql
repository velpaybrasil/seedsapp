-- Adicionar campos de configurações na tabela users
ALTER TABLE users
ADD COLUMN theme VARCHAR(20) DEFAULT 'light' NOT NULL,
ADD COLUMN notifications_enabled BOOLEAN DEFAULT TRUE NOT NULL,
ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE NOT NULL;
