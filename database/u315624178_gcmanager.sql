-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 21/02/2025 às 12:09
-- Versão do servidor: 10.11.10-MariaDB
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u315624178_gcmanager`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `gc_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `meeting_date` date NOT NULL,
  `present` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `entity` varchar(50) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financial_transactions`
--

CREATE TABLE `financial_transactions` (
  `id` int(11) NOT NULL,
  `contributor_name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('tithe','offering') NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_date` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `follow_ups`
--

CREATE TABLE `follow_ups` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `contact_date` datetime NOT NULL,
  `next_contact` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gc_members`
--

CREATE TABLE `gc_members` (
  `id` int(11) NOT NULL,
  `gc_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `join_date` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `meeting_day` varchar(20) NOT NULL,
  `meeting_time` time NOT NULL,
  `address` varchar(200) NOT NULL,
  `neighborhood` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` char(2) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 12,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `leader_id` int(11) DEFAULT NULL,
  `co_leader_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `groups`
--

INSERT INTO `groups` (`id`, `name`, `meeting_day`, `meeting_time`, `address`, `neighborhood`, `city`, `state`, `capacity`, `description`, `photo`, `created_at`, `updated_at`, `leader_id`, `co_leader_id`, `active`) VALUES
(2, 'GC Família Feliz', 'Quarta', '20:00:00', 'Av. Principal, 456', 'Jardim América', 'São Paulo', 'SP', 12, 'Grupo para casais e famílias', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 92, NULL, 1),
(3, 'GC Universitários', 'Sexta', '19:00:00', 'Rua do Conhecimento, 789', 'Vila Universitária', 'São Paulo', 'SP', 20, 'Grupo dedicado a estudantes universitários', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 102, NULL, 1),
(4, 'GC Mulheres de Fé', 'Terça', '15:00:00', 'Rua das Margaridas, 321', 'Jardim Flora', 'São Paulo', 'SP', 15, 'Grupo exclusivo para mulheres', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 103, NULL, 1),
(5, 'GC Homens de Valor', 'Quinta', '19:30:00', 'Rua dos Ipês, 654', 'Vila Nova', 'São Paulo', 'SP', 15, 'Grupo exclusivo para homens', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 104, NULL, 1),
(6, 'GC Terceira Idade', 'Segunda', '14:00:00', 'Av. da Sabedoria, 987', 'Bela Vista', 'São Paulo', 'SP', 12, 'Grupo para pessoas da melhor idade', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 105, NULL, 1),
(7, 'GC Profissionais', 'Quarta', '19:00:00', 'Rua do Trabalho, 147', 'Moema', 'São Paulo', 'SP', 15, 'Grupo para profissionais e empreendedores', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 106, NULL, 1),
(8, 'GC Arte e Fé', 'Sábado', '15:00:00', 'Rua das Artes, 258', 'Vila Madalena', 'São Paulo', 'SP', 15, 'Grupo com foco em artes e criatividade', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 107, NULL, 1),
(9, 'GC Esporte e Vida', 'Domingo', '08:00:00', 'Av. do Esporte, 369', 'Pacaembu', 'São Paulo', 'SP', 20, 'Grupo para praticantes de esportes', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 108, NULL, 1),
(10, 'GC Novos na Fé', 'Segunda', '19:30:00', 'Rua da Esperança, 741', 'Santana', 'São Paulo', 'SP', 12, 'Grupo para novos convertidos', NULL, '2025-01-25 17:26:52', '2025-01-26 02:51:11', 109, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `group_attendance`
--

CREATE TABLE `group_attendance` (
  `id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `present` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `group_leaders`
--

CREATE TABLE `group_leaders` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('leader','co_leader') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `group_meetings`
--

CREATE TABLE `group_meetings` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `meeting_date` date NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `role` enum('member','leader','co-leader') DEFAULT 'member',
  `joined_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `group_member_history`
--

CREATE TABLE `group_member_history` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `old_status` enum('pending','approved','rejected') NOT NULL,
  `new_status` enum('pending','approved','rejected') NOT NULL,
  `changed_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `group_participants`
--

CREATE TABLE `group_participants` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `join_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `growth_groups`
--

CREATE TABLE `growth_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('coordination','supervision','members') NOT NULL,
  `parent_gc_id` int(11) DEFAULT NULL,
  `leader_id` int(11) NOT NULL,
  `co_leader_id` int(11) DEFAULT NULL,
  `host_id` int(11) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `frequency` enum('weekly','biweekly','monthly') NOT NULL,
  `meeting_day` varchar(20) NOT NULL,
  `meeting_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `neighborhood` varchar(255) NOT NULL COMMENT 'Bairro principal onde acontece a maior parte dos encontros',
  `extra_neighborhoods` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ministry_id` int(11) DEFAULT NULL,
  `leader_name` varchar(255) DEFAULT NULL,
  `meeting_address` text NOT NULL COMMENT 'Endereço principal onde acontece a maior parte dos encontros'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `growth_group_attendance`
--

CREATE TABLE `growth_group_attendance` (
  `id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `growth_group_leaders`
--

CREATE TABLE `growth_group_leaders` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('leader','co_leader') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `growth_group_meetings`
--

CREATE TABLE `growth_group_meetings` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `meeting_date` date NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `growth_group_participants`
--

CREATE TABLE `growth_group_participants` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `join_date` date NOT NULL,
  `status` enum('active','inactive','graduated') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ministries`
--

CREATE TABLE `ministries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ministries`
--

INSERT INTO `ministries` (`id`, `name`, `description`, `active`, `created_at`, `updated_at`) VALUES
(9, 'Somos Um', 'teste', 1, '2025-02-04 18:04:42', '2025-02-04 18:04:42'),
(12, 'teste', 'teste', 1, '2025-02-07 18:12:37', '2025-02-07 18:12:37'),
(13, 'teste', 'teste', 1, '2025-02-14 15:54:32', '2025-02-14 15:54:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ministry_leaders`
--

CREATE TABLE `ministry_leaders` (
  `id` int(11) NOT NULL,
  `ministry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('leader','co_leader') NOT NULL DEFAULT 'leader',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `ministry_leaders`
--

INSERT INTO `ministry_leaders` (`id`, `ministry_id`, `user_id`, `role`, `created_at`, `updated_at`) VALUES
(5, 9, 92, 'leader', '2025-02-04 18:04:42', '2025-02-04 18:04:42'),
(6, 9, 102, 'leader', '2025-02-04 18:04:42', '2025-02-04 18:04:42'),
(11, 12, 97, 'leader', '2025-02-07 18:12:37', '2025-02-07 18:12:37'),
(12, 12, 102, 'leader', '2025-02-07 18:12:37', '2025-02-07 18:12:37'),
(13, 13, 119, 'leader', '2025-02-14 15:54:32', '2025-02-14 15:54:32'),
(14, 13, 97, 'leader', '2025-02-14 15:54:32', '2025-02-14 15:54:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `modules`
--

INSERT INTO `modules` (`id`, `name`, `description`, `slug`, `icon`, `order_index`, `is_active`, `created_at`) VALUES
(30, 'Usuários', 'Gerenciamento de usuários do sistema', 'usuarios', 'users', 0, 1, '2025-02-09 01:17:48'),
(31, 'Grupos', 'Gerenciamento de grupos de crescimento', 'grupos', 'users-class', 0, 1, '2025-02-09 01:17:48'),
(32, 'Visitantes', 'Gerenciamento de visitantes', 'visitantes', 'user-plus', 0, 1, '2025-02-09 01:17:48'),
(33, 'Ministérios', 'Gerenciamento de ministérios', 'ministerios', 'church', 0, 1, '2025-02-09 01:17:48'),
(34, 'Financeiro', 'Gerenciamento financeiro', 'financeiro', 'dollar-sign', 0, 1, '2025-02-09 01:17:48'),
(35, 'Relatórios', 'Relatórios e estatísticas', 'relatorios', 'chart-bar', 0, 1, '2025-02-09 01:17:48'),
(59, 'settings', 'Configurações do Sistema', 'settings', NULL, 0, 1, '2025-02-19 15:20:03'),
(61, 'settings', 'Configurações do Sistema', '', NULL, 0, 1, '2025-02-20 17:19:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `permissions`
--

INSERT INTO `permissions` (`id`, `module_id`, `name`, `slug`, `description`, `created_at`) VALUES
(19, 30, 'Usuários - Visualizar', 'usuarios_view', 'Permissão para visualizar usuários', '2025-02-09 01:17:48'),
(20, 31, 'Grupos - Visualizar', 'grupos_view', 'Permissão para visualizar grupos', '2025-02-09 01:17:48'),
(21, 32, 'Visitantes - Visualizar', 'visitantes_view', 'Permissão para visualizar visitantes', '2025-02-09 01:17:48'),
(22, 33, 'Ministérios - Visualizar', 'ministerios_view', 'Permissão para visualizar ministérios', '2025-02-09 01:17:48'),
(23, 34, 'Financeiro - Visualizar', 'financeiro_view', 'Permissão para visualizar financeiro', '2025-02-09 01:17:48'),
(24, 35, 'Relatórios - Visualizar', 'relatorios_view', 'Permissão para visualizar relatórios', '2025-02-09 01:17:48'),
(25, 30, 'Usuários - Criar', 'usuarios_create', 'Permissão para criar usuários', '2025-02-09 01:17:48'),
(26, 31, 'Grupos - Criar', 'grupos_create', 'Permissão para criar grupos', '2025-02-09 01:17:48'),
(27, 32, 'Visitantes - Criar', 'visitantes_create', 'Permissão para criar visitantes', '2025-02-09 01:17:48'),
(28, 33, 'Ministérios - Criar', 'ministerios_create', 'Permissão para criar ministérios', '2025-02-09 01:17:48'),
(29, 34, 'Financeiro - Criar', 'financeiro_create', 'Permissão para criar financeiro', '2025-02-09 01:17:48'),
(30, 35, 'Relatórios - Criar', 'relatorios_create', 'Permissão para criar relatórios', '2025-02-09 01:17:48'),
(31, 30, 'Usuários - Editar', 'usuarios_edit', 'Permissão para editar usuários', '2025-02-09 01:17:48'),
(32, 31, 'Grupos - Editar', 'grupos_edit', 'Permissão para editar grupos', '2025-02-09 01:17:48'),
(33, 32, 'Visitantes - Editar', 'visitantes_edit', 'Permissão para editar visitantes', '2025-02-09 01:17:48'),
(34, 33, 'Ministérios - Editar', 'ministerios_edit', 'Permissão para editar ministérios', '2025-02-09 01:17:48'),
(35, 34, 'Financeiro - Editar', 'financeiro_edit', 'Permissão para editar financeiro', '2025-02-09 01:17:48'),
(36, 35, 'Relatórios - Editar', 'relatorios_edit', 'Permissão para editar relatórios', '2025-02-09 01:17:48'),
(37, 30, 'Usuários - Excluir', 'usuarios_delete', 'Permissão para excluir usuários', '2025-02-09 01:17:48'),
(38, 31, 'Grupos - Excluir', 'grupos_delete', 'Permissão para excluir grupos', '2025-02-09 01:17:48'),
(39, 32, 'Visitantes - Excluir', 'visitantes_delete', 'Permissão para excluir visitantes', '2025-02-09 01:17:48'),
(40, 33, 'Ministérios - Excluir', 'ministerios_delete', 'Permissão para excluir ministérios', '2025-02-09 01:17:48'),
(41, 34, 'Financeiro - Excluir', 'financeiro_delete', 'Permissão para excluir financeiro', '2025-02-09 01:17:48'),
(42, 35, 'Relatórios - Excluir', 'relatorios_delete', 'Permissão para excluir relatórios', '2025-02-09 01:17:48'),
(55, 59, 'Configurações - Gerenciar', 'settings_manage', 'Gerenciar configurações do sistema', '2025-02-19 15:25:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `prayer_requests`
--

CREATE TABLE `prayer_requests` (
  `id` int(11) NOT NULL,
  `visitor_name` varchar(255) NOT NULL,
  `request` text NOT NULL,
  `status` enum('pending','praying','completed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `prayer_requests`
--

INSERT INTO `prayer_requests` (`id`, `visitor_name`, `request`, `status`, `created_at`, `updated_at`) VALUES
(1, 'João da Silva', 'Pedido de oração de teste', 'completed', '2025-01-30 17:12:40', '2025-02-12 00:32:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `qr_code_config`
--

CREATE TABLE `qr_code_config` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `qr_code_config`
--

INSERT INTO `qr_code_config` (`id`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '910c0564-e426-11ef-8590-983d67505cdb', 'QR Code padrão para registro de presenças', 1, '2025-02-06 01:06:16', '2025-02-06 01:06:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fields`)),
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filters`)),
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(7, 'Administrador', 'Acesso total ao sistema', 0, '2025-02-09 01:17:48', '2025-02-09 01:17:48'),
(8, 'Líder', 'Líder de grupo de crescimento', 0, '2025-02-09 01:17:48', '2025-02-09 01:17:48'),
(9, 'Membro', 'Membro da igreja', 0, '2025-02-09 01:17:48', '2025-02-09 01:17:48'),
(10, 'admin', 'Administrador do sistema', 0, '2025-02-19 15:03:45', '2025-02-19 15:03:45'),
(11, 'admin', 'Administrador do sistema', 0, '2025-02-19 15:05:05', '2025-02-19 15:05:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES
(7, 19, '2025-02-09 01:17:48'),
(7, 20, '2025-02-09 01:17:48'),
(7, 21, '2025-02-09 01:17:48'),
(7, 22, '2025-02-09 01:17:48'),
(7, 23, '2025-02-09 01:17:48'),
(7, 24, '2025-02-09 01:17:48'),
(7, 25, '2025-02-09 01:17:48'),
(7, 26, '2025-02-09 01:17:48'),
(7, 27, '2025-02-09 01:17:48'),
(7, 28, '2025-02-09 01:17:48'),
(7, 29, '2025-02-09 01:17:48'),
(7, 30, '2025-02-09 01:17:48'),
(7, 31, '2025-02-09 01:17:48'),
(7, 32, '2025-02-09 01:17:48'),
(7, 33, '2025-02-09 01:17:48'),
(7, 34, '2025-02-09 01:17:48'),
(7, 35, '2025-02-09 01:17:48'),
(7, 36, '2025-02-09 01:17:48'),
(7, 37, '2025-02-09 01:17:48'),
(7, 38, '2025-02-09 01:17:48'),
(7, 39, '2025-02-09 01:17:48'),
(7, 40, '2025-02-09 01:17:48'),
(7, 41, '2025-02-09 01:17:48'),
(7, 42, '2025-02-09 01:17:48'),
(7, 55, '2025-02-19 15:25:30'),
(8, 20, '2025-02-09 01:17:48'),
(8, 21, '2025-02-09 01:17:48'),
(8, 26, '2025-02-09 01:17:48'),
(8, 27, '2025-02-09 01:17:48'),
(8, 32, '2025-02-09 01:17:48'),
(8, 33, '2025-02-09 01:17:48'),
(9, 20, '2025-02-09 01:17:48'),
(9, 21, '2025-02-09 01:17:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `activity` varchar(255) NOT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_modules`
--

CREATE TABLE `system_modules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `system_modules`
--

INSERT INTO `system_modules` (`id`, `name`, `description`, `slug`, `icon`, `parent_id`, `order_index`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'Página inicial do sistema', 'dashboard', 'dashboard', NULL, 1, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(2, 'Usuários', 'Gerenciamento de usuários', 'users', 'users', NULL, 2, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(3, 'Grupos', 'Gerenciamento de grupos de crescimento', 'groups', 'users-group', NULL, 3, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(4, 'Visitantes', 'Gerenciamento de visitantes', 'visitors', 'user-plus', NULL, 4, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(5, 'Ministérios', 'Gerenciamento de ministérios', 'ministries', 'church', NULL, 5, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(6, 'Escalas', 'Gerenciamento de escalas', 'schedules', 'calendar', NULL, 6, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(7, 'Financeiro', 'Gerenciamento financeiro', 'financial', 'money-bill', NULL, 7, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(8, 'Dízimos e Ofertas', 'Gerenciamento de dízimos e ofertas', 'tithes', 'hand-holding-heart', NULL, 8, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01'),
(9, 'Configurações', 'Configurações do sistema', 'settings', 'cog', NULL, 9, 1, '2025-01-30 17:29:01', '2025-01-30 17:29:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `value_type` enum('string','integer','boolean','json','array') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `system_settings`
--

INSERT INTO `system_settings` (`id`, `category`, `key_name`, `value`, `value_type`, `description`, `is_public`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'visitor_rules', 'auto_approve_visitors', '0', 'boolean', 'Se habilitado, visitantes serão automaticamente aprovados como usuários do sistema', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(2, 'visitor_rules', 'required_visits', '2', 'integer', 'Número de visitas necessárias antes de se tornar membro', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(3, 'visitor_rules', 'follow_up_days', '7', 'integer', 'Dias para fazer follow-up após a primeira visita', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(4, 'visitor_rules', 'default_visitor_role', 'visitor', 'string', 'Papel padrão atribuído a novos visitantes', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(5, 'group_settings', 'max_members', '15', 'integer', 'Número máximo de membros por grupo', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(6, 'group_settings', 'allow_multiple_groups', '1', 'boolean', 'Permite que membros participem de múltiplos grupos', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(7, 'group_settings', 'auto_approve_requests', '0', 'boolean', 'Aprova automaticamente pedidos de participação em grupos', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(8, 'notification_settings', 'email_notifications', '1', 'boolean', 'Habilita notificações por e-mail', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(9, 'notification_settings', 'notify_leaders', '1', 'boolean', 'Notifica líderes sobre novos membros e solicitações', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(10, 'notification_settings', 'notification_types', '[\"email\", \"system\", \"sms\"]', 'array', 'Tipos de notificação disponíveis', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(11, 'report_settings', 'attendance_threshold', '75', 'integer', 'Porcentagem mínima de presença para membros ativos', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(12, 'report_settings', 'inactive_days', '30', 'integer', 'Dias sem atividade para considerar membro inativo', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL),
(13, 'report_settings', 'report_metrics', '{\"attendance\": true, \"growth\": true, \"engagement\": true}', 'json', 'Métricas a serem incluídas nos relatórios', 0, '2025-02-19 13:31:04', '2025-02-19 13:31:04', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','leader','followup','financial') NOT NULL,
  `is_owner` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `remember_token_expires_at` datetime DEFAULT NULL,
  `theme` varchar(20) NOT NULL DEFAULT 'light',
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `email_notifications` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_owner`, `created_at`, `updated_at`, `active`, `last_login`, `must_change_password`, `failed_login_attempts`, `locked_until`, `remember_token`, `remember_token_expires_at`, `theme`, `notifications_enabled`, `email_notifications`) VALUES
(97, 'Paulo Gustavo', 'pgustavodlima@gmail.com', '$2y$10$Hh5lwuP4iN9V8h2qzEb0OOu1z1Vx8/YwvHYHLvjTHN9KL4jk4IXLC', 'admin', 1, '2025-01-24 18:44:35', '2025-02-06 01:15:22', 1, NULL, 0, 0, NULL, NULL, NULL, 'light', 1, 1),
(100, 'Paulo Gustavo', 'admin@paulogustavo.me', '$2y$10$QjmAMtMcctzNxBDCn9uaMeJCrRUNlPs2NdVzrOFwFq/1xjE2EoelG', 'admin', 1, '2025-01-25 03:14:39', '2025-02-21 11:27:40', 1, '2025-02-21 11:27:40', 0, 0, NULL, NULL, '2025-03-07 22:15:54', 'light', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_create` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `complement` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `neighborhood` varchar(100) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `gender` enum('M','F') DEFAULT NULL,
  `first_visit_date` date DEFAULT NULL,
  `how_knew_church` varchar(50) DEFAULT NULL,
  `prayer_requests` text DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `follow_up_notes` text DEFAULT NULL,
  `follow_up_status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('not_contacted','contacted','forwarded_to_group','group_member','not_interested','wants_online_group','already_in_group') NOT NULL DEFAULT 'not_contacted',
  `wants_group` enum('yes','no') DEFAULT NULL,
  `available_days` varchar(255) DEFAULT NULL,
  `consent_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `group_id` int(11) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `has_children` tinyint(1) DEFAULT 0,
  `number_of_children` int(11) DEFAULT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `church_member` tinyint(1) DEFAULT 0,
  `previous_church` varchar(255) DEFAULT NULL,
  `conversion_date` date DEFAULT NULL,
  `baptism_date` date DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `visitors`
--

INSERT INTO `visitors` (`id`, `name`, `birth_date`, `marital_status`, `phone`, `whatsapp`, `address`, `number`, `complement`, `email`, `neighborhood`, `zipcode`, `gender`, `first_visit_date`, `how_knew_church`, `prayer_requests`, `observations`, `follow_up_date`, `follow_up_notes`, `follow_up_status`, `photo`, `status`, `wants_group`, `available_days`, `consent_date`, `created_at`, `updated_at`, `group_id`, `city`, `state`, `zip_code`, `has_children`, `number_of_children`, `profession`, `church_member`, `previous_church`, `conversion_date`, `baptism_date`, `source`, `assigned_to`) VALUES
(3, 'Cintia Mendonça ', NULL, 'married', '85999567338', NULL, NULL, NULL, NULL, 'cintiambslima@gmail.com', 'Cajazeiras ', NULL, 'F', NULL, NULL, '', NULL, NULL, NULL, 'pending', NULL, '', 'yes', NULL, NULL, '2025-02-20 18:58:36', '2025-02-20 18:58:36', NULL, 'Fortaleza', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'PAULO GUSTAVO', '1986-04-08', 'married', '(85) 99763-7850', NULL, NULL, NULL, NULL, 'pgustavodlima@gmail.com', 'Cajazeiras', NULL, 'M', NULL, NULL, 'teste', NULL, NULL, NULL, 'pending', NULL, '', 'yes', NULL, NULL, '2025-02-21 11:46:40', '2025-02-21 11:46:40', NULL, 'Fortaleza', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_attendance`
--

CREATE TABLE `visitor_attendance` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `registered_by_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_contact_logs`
--

CREATE TABLE `visitor_contact_logs` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `follow_up_date` date DEFAULT NULL,
  `follow_up_notes` text DEFAULT NULL,
  `follow_up_status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_follow_ups`
--

CREATE TABLE `visitor_follow_ups` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `contact_date` date NOT NULL,
  `contact_type` enum('phone','whatsapp','email','visit','other') NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','completed','canceled') DEFAULT 'pending',
  `next_contact` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_forms`
--

CREATE TABLE `visitor_forms` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `header_text` text DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `theme_color` varchar(7) DEFAULT '#007bff',
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `visitor_forms`
--

INSERT INTO `visitor_forms` (`id`, `title`, `slug`, `description`, `logo_url`, `header_text`, `footer_text`, `theme_color`, `active`, `created_at`, `updated_at`) VALUES
(1, 'teste', 'ccvideira', 'teste', NULL, NULL, 'TEXTO DO RAPIDA', '#007bff', 1, '2025-02-10 16:39:57', '2025-02-10 16:39:57'),
(2, 'tyeste 2', 'tyeste-2', 'desc', 'https://igrejamodelo.alfadev.online/ccvideira', NULL, 'rodape', '#0d6efd', 1, '2025-02-10 17:19:09', '2025-02-10 17:19:09'),
(3, 'rwarw', 'rwarw', 'rwarw', 'https://igrejamodelo.alfadev.online/ccvideira', NULL, 'rwsrwrw', '#0d6efd', 1, '2025-02-14 13:08:09', '2025-02-14 13:08:09'),
(4, 'teste', 'teste', 'teste', 'https://igrejamodelo.alfadev.online/ccvideira', NULL, 'teste', '#0d6efd', 1, '2025-02-14 11:17:58', '2025-02-14 11:17:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_form_fields`
--

CREATE TABLE `visitor_form_fields` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` enum('text','email','phone','date','select','radio','checkbox') NOT NULL,
  `field_options` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `placeholder` varchar(255) DEFAULT NULL,
  `help_text` text DEFAULT NULL,
  `validation_rules` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_form_submissions`
--

CREATE TABLE `visitor_form_submissions` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `visitor_id` int(11) DEFAULT NULL,
  `submission_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`submission_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_group_changes`
--

CREATE TABLE `visitor_group_changes` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `old_group_id` int(11) DEFAULT NULL,
  `new_group_id` int(11) NOT NULL,
  `change_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_group_history`
--

CREATE TABLE `visitor_group_history` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `join_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitor_visits`
--

CREATE TABLE `visitor_visits` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `service_type` enum('sunday','midweek','special','other') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `volunteers`
--

CREATE TABLE `volunteers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ministry` varchar(100) NOT NULL,
  `availability` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `volunteers`
--

INSERT INTO `volunteers` (`id`, `user_id`, `ministry`, `availability`, `created_at`, `updated_at`) VALUES
(56, 91, 'Grupos de Crescimento', NULL, '2025-01-24 16:17:56', '2025-01-24 16:17:56'),
(57, 92, 'Grupos de Crescimento', NULL, '2025-01-24 16:17:56', '2025-01-24 16:17:56'),
(58, 93, 'Grupos de Crescimento', NULL, '2025-01-24 16:17:56', '2025-01-24 16:17:56'),
(59, 94, 'Grupos de Crescimento', NULL, '2025-01-24 16:17:56', '2025-01-24 16:17:56'),
(60, 95, 'Grupos de Crescimento', NULL, '2025-01-24 16:17:56', '2025-01-24 16:17:56'),
(61, 97, 'Administrador', NULL, '2025-01-24 18:44:35', '2025-01-24 18:44:35'),
(62, 91, 'Grupos de Crescimento', 'Domingos - Manhã e Noite', '2025-01-29 20:59:56', '2025-01-29 20:59:56'),
(63, 92, 'Grupos de Crescimento', 'Domingos - Manhã', '2025-01-29 20:59:56', '2025-01-29 20:59:56'),
(64, 93, 'Grupos de Crescimento', 'Quartas e Domingos', '2025-01-29 20:59:56', '2025-01-29 20:59:56'),
(65, 94, 'Grupos de Crescimento', 'Domingos - Noite', '2025-01-29 20:59:56', '2025-01-29 20:59:56'),
(66, 97, 'Grupos de Crescimento', 'Terças e Quintas', '2025-01-29 20:59:56', '2025-01-29 20:59:56');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gc_id` (`gc_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Índices de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Índices de tabela `follow_ups`
--
ALTER TABLE `follow_ups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Índices de tabela `gc_members`
--
ALTER TABLE `gc_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gc_id` (`gc_id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- Índices de tabela `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_group_name` (`name`),
  ADD KEY `leader_id` (`leader_id`),
  ADD KEY `co_leader_id` (`co_leader_id`);

--
-- Índices de tabela `group_attendance`
--
ALTER TABLE `group_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_meeting_participant` (`meeting_id`,`participant_id`),
  ADD KEY `participant_id` (`participant_id`);

--
-- Índices de tabela `group_leaders`
--
ALTER TABLE `group_leaders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_group_user_role` (`group_id`,`user_id`,`role`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `group_meetings`
--
ALTER TABLE `group_meetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_group_meeting` (`group_id`,`meeting_date`);

--
-- Índices de tabela `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_group_member` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `group_member_history`
--
ALTER TABLE `group_member_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Índices de tabela `group_participants`
--
ALTER TABLE `group_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_group_user` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `growth_groups`
--
ALTER TABLE `growth_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_gc_id` (`parent_gc_id`),
  ADD KEY `leader_id` (`leader_id`),
  ADD KEY `co_leader_id` (`co_leader_id`),
  ADD KEY `host_id` (`host_id`),
  ADD KEY `fk_ministry_id` (`ministry_id`);

--
-- Índices de tabela `growth_group_attendance`
--
ALTER TABLE `growth_group_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `growth_group_attendance_meeting_fk` (`meeting_id`),
  ADD KEY `growth_group_attendance_participant_fk` (`participant_id`);

--
-- Índices de tabela `growth_group_leaders`
--
ALTER TABLE `growth_group_leaders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `growth_group_meetings`
--
ALTER TABLE `growth_group_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `growth_group_meetings_group_fk` (`group_id`);

--
-- Índices de tabela `growth_group_participants`
--
ALTER TABLE `growth_group_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `growth_group_participants_group_fk` (`group_id`),
  ADD KEY `growth_group_participants_visitor_fk` (`visitor_id`);

--
-- Índices de tabela `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member_group` (`group_id`),
  ADD KEY `idx_member_name` (`name`);

--
-- Índices de tabela `ministries`
--
ALTER TABLE `ministries`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ministry_leaders`
--
ALTER TABLE `ministry_leaders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ministry_user` (`ministry_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `module_id` (`module_id`);

--
-- Índices de tabela `prayer_requests`
--
ALTER TABLE `prayer_requests`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `qr_code_config`
--
ALTER TABLE `qr_code_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Índices de tabela `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Índices de tabela `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Índices de tabela `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- Índices de tabela `system_modules`
--
ALTER TABLE `system_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Índices de tabela `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_setting` (`category`,`key_name`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_module` (`user_id`,`module_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Índices de tabela `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Índices de tabela `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_status` (`status`),
  ADD KEY `fk_visitors_group` (`group_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Índices de tabela `visitor_attendance`
--
ALTER TABLE `visitor_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `registered_by_id` (`registered_by_id`);

--
-- Índices de tabela `visitor_contact_logs`
--
ALTER TABLE `visitor_contact_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_visitor_contact_logs_visitor` (`visitor_id`),
  ADD KEY `fk_visitor_contact_logs_user` (`user_id`);

--
-- Índices de tabela `visitor_follow_ups`
--
ALTER TABLE `visitor_follow_ups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- Índices de tabela `visitor_forms`
--
ALTER TABLE `visitor_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_visitor_forms_slug` (`slug`);

--
-- Índices de tabela `visitor_form_fields`
--
ALTER TABLE `visitor_form_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_form_fields_form` (`form_id`);

--
-- Índices de tabela `visitor_form_submissions`
--
ALTER TABLE `visitor_form_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_form_submissions_form` (`form_id`),
  ADD KEY `idx_visitor_form_submissions_visitor` (`visitor_id`);

--
-- Índices de tabela `visitor_group_changes`
--
ALTER TABLE `visitor_group_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `old_group_id` (`old_group_id`),
  ADD KEY `new_group_id` (`new_group_id`);

--
-- Índices de tabela `visitor_group_history`
--
ALTER TABLE `visitor_group_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Índices de tabela `visitor_visits`
--
ALTER TABLE `visitor_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- Índices de tabela `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `financial_transactions`
--
ALTER TABLE `financial_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `follow_ups`
--
ALTER TABLE `follow_ups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gc_members`
--
ALTER TABLE `gc_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `group_attendance`
--
ALTER TABLE `group_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `group_leaders`
--
ALTER TABLE `group_leaders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `group_meetings`
--
ALTER TABLE `group_meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `group_member_history`
--
ALTER TABLE `group_member_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `group_participants`
--
ALTER TABLE `group_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `growth_groups`
--
ALTER TABLE `growth_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `growth_group_attendance`
--
ALTER TABLE `growth_group_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `growth_group_leaders`
--
ALTER TABLE `growth_group_leaders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `growth_group_meetings`
--
ALTER TABLE `growth_group_meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `growth_group_participants`
--
ALTER TABLE `growth_group_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ministries`
--
ALTER TABLE `ministries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `ministry_leaders`
--
ALTER TABLE `ministry_leaders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de tabela `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de tabela `prayer_requests`
--
ALTER TABLE `prayer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `qr_code_config`
--
ALTER TABLE `qr_code_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_modules`
--
ALTER TABLE `system_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT de tabela `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `visitor_attendance`
--
ALTER TABLE `visitor_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `visitor_contact_logs`
--
ALTER TABLE `visitor_contact_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `visitor_follow_ups`
--
ALTER TABLE `visitor_follow_ups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `visitor_forms`
--
ALTER TABLE `visitor_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `visitor_form_fields`
--
ALTER TABLE `visitor_form_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `visitor_form_submissions`
--
ALTER TABLE `visitor_form_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `visitor_group_changes`
--
ALTER TABLE `visitor_group_changes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `visitor_group_history`
--
ALTER TABLE `visitor_group_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `visitor_visits`
--
ALTER TABLE `visitor_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`gc_id`) REFERENCES `growth_groups` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `gc_members` (`id`);

--
-- Restrições para tabelas `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD CONSTRAINT `financial_transactions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `follow_ups`
--
ALTER TABLE `follow_ups`
  ADD CONSTRAINT `follow_ups_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follow_ups_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `follow_ups_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `gc_members`
--
ALTER TABLE `gc_members`
  ADD CONSTRAINT `gc_members_ibfk_1` FOREIGN KEY (`gc_id`) REFERENCES `growth_groups` (`id`),
  ADD CONSTRAINT `gc_members_ibfk_2` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`);

--
-- Restrições para tabelas `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`co_leader_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `group_attendance`
--
ALTER TABLE `group_attendance`
  ADD CONSTRAINT `group_attendance_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `group_meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_attendance_ibfk_2` FOREIGN KEY (`participant_id`) REFERENCES `group_participants` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `group_leaders`
--
ALTER TABLE `group_leaders`
  ADD CONSTRAINT `group_leaders_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_leaders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `group_meetings`
--
ALTER TABLE `group_meetings`
  ADD CONSTRAINT `group_meetings_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `group_member_history`
--
ALTER TABLE `group_member_history`
  ADD CONSTRAINT `group_member_history_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `group_members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_member_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION;

--
-- Restrições para tabelas `group_participants`
--
ALTER TABLE `group_participants`
  ADD CONSTRAINT `group_participants_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `growth_groups`
--
ALTER TABLE `growth_groups`
  ADD CONSTRAINT `fk_ministry_id` FOREIGN KEY (`ministry_id`) REFERENCES `ministries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `growth_groups_ibfk_1` FOREIGN KEY (`parent_gc_id`) REFERENCES `growth_groups` (`id`),
  ADD CONSTRAINT `growth_groups_ibfk_2` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `growth_groups_ibfk_3` FOREIGN KEY (`co_leader_id`) REFERENCES `volunteers` (`id`),
  ADD CONSTRAINT `growth_groups_ibfk_4` FOREIGN KEY (`host_id`) REFERENCES `volunteers` (`id`),
  ADD CONSTRAINT `growth_groups_ibfk_5` FOREIGN KEY (`ministry_id`) REFERENCES `ministries` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `growth_group_attendance`
--
ALTER TABLE `growth_group_attendance`
  ADD CONSTRAINT `growth_group_attendance_meeting_fk` FOREIGN KEY (`meeting_id`) REFERENCES `growth_group_meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `growth_group_attendance_participant_fk` FOREIGN KEY (`participant_id`) REFERENCES `growth_group_participants` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `growth_group_leaders`
--
ALTER TABLE `growth_group_leaders`
  ADD CONSTRAINT `growth_group_leaders_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`),
  ADD CONSTRAINT `growth_group_leaders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `growth_group_meetings`
--
ALTER TABLE `growth_group_meetings`
  ADD CONSTRAINT `growth_group_meetings_group_fk` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `growth_group_participants`
--
ALTER TABLE `growth_group_participants`
  ADD CONSTRAINT `growth_group_participants_group_fk` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `growth_group_participants_visitor_fk` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `ministry_leaders`
--
ALTER TABLE `ministry_leaders`
  ADD CONSTRAINT `ministry_leaders_ibfk_1` FOREIGN KEY (`ministry_id`) REFERENCES `ministries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ministry_leaders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`);

--
-- Restrições para tabelas `system_modules`
--
ALTER TABLE `system_modules`
  ADD CONSTRAINT `system_modules_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `system_modules` (`id`);

--
-- Restrições para tabelas `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `system_settings_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `system_modules` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `fk_visitors_group` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `visitor_attendance`
--
ALTER TABLE `visitor_attendance`
  ADD CONSTRAINT `visitor_attendance_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitor_attendance_ibfk_2` FOREIGN KEY (`registered_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `visitor_contact_logs`
--
ALTER TABLE `visitor_contact_logs`
  ADD CONSTRAINT `fk_visitor_contact_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_visitor_contact_logs_visitor` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `visitor_follow_ups`
--
ALTER TABLE `visitor_follow_ups`
  ADD CONSTRAINT `visitor_follow_ups_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `visitor_form_fields`
--
ALTER TABLE `visitor_form_fields`
  ADD CONSTRAINT `visitor_form_fields_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `visitor_forms` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `visitor_form_submissions`
--
ALTER TABLE `visitor_form_submissions`
  ADD CONSTRAINT `visitor_form_submissions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `visitor_forms` (`id`),
  ADD CONSTRAINT `visitor_form_submissions_ibfk_2` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `visitor_group_changes`
--
ALTER TABLE `visitor_group_changes`
  ADD CONSTRAINT `visitor_group_changes_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitor_group_changes_ibfk_2` FOREIGN KEY (`old_group_id`) REFERENCES `growth_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitor_group_changes_ibfk_3` FOREIGN KEY (`new_group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `visitor_group_history`
--
ALTER TABLE `visitor_group_history`
  ADD CONSTRAINT `visitor_group_history_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitor_group_history_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `visitor_visits`
--
ALTER TABLE `visitor_visits`
  ADD CONSTRAINT `visitor_visits_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `volunteers`
--
ALTER TABLE `volunteers`
  ADD CONSTRAINT `volunteers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
