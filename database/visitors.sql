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

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_status` (`status`),
  ADD KEY `fk_visitors_group` (`group_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `fk_visitors_group` FOREIGN KEY (`group_id`) REFERENCES `growth_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
