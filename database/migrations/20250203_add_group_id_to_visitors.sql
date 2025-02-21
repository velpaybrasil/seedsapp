-- Adiciona a coluna group_id à tabela visitors
ALTER TABLE `visitors` ADD COLUMN `group_id` int(11) DEFAULT NULL;

-- Adiciona a chave estrangeira
ALTER TABLE `visitors` ADD CONSTRAINT `fk_visitors_group` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
