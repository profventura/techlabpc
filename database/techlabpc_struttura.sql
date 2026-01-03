-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3383
-- Creato il: Gen 03, 2026 alle 13:22
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techlabpc`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `event` enum('login_ok','login_ko','logout') NOT NULL,
  `ip` varchar(60) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `action_logs`
--

CREATE TABLE `action_logs` (
  `id` int(11) NOT NULL,
  `actor_student_id` int(11) DEFAULT NULL,
  `action_type` enum('assign_laptop_to_customer','change_laptop_status','upload_receipt','assign_laptop_to_group','create_group','update_group','delete_group','create_student','update_student','delete_student','create_customer','update_customer','delete_customer','create_payment','update_payment','delete_payment','create_software','update_software','delete_software','assign_member_to_group','remove_member_from_group','create_laptop','update_laptop','delete_laptop') NOT NULL,
  `laptop_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `pc_assigned_count` int(11) NOT NULL DEFAULT 0,
  `pc_requested_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `role` enum('leader','installer') NOT NULL DEFAULT 'installer',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `laptops`
--

CREATE TABLE `laptops` (
  `id` int(11) NOT NULL,
  `code` varchar(60) NOT NULL,
  `brand_model` varchar(150) NOT NULL,
  `cpu` varchar(120) DEFAULT NULL,
  `ram` varchar(60) DEFAULT NULL,
  `storage` varchar(120) DEFAULT NULL,
  `screen` varchar(120) DEFAULT NULL,
  `tech_notes` text DEFAULT NULL,
  `scratches` text DEFAULT NULL,
  `physical_condition` varchar(120) DEFAULT NULL,
  `battery` varchar(120) DEFAULT NULL,
  `condition_level` enum('excellent','very_good','good','average','fair','poor') NOT NULL DEFAULT 'good',
  `office_license` varchar(120) DEFAULT NULL,
  `windows_license` varchar(120) DEFAULT NULL,
  `other_software_request` text DEFAULT NULL,
  `status` enum('in_progress','ready','missing_office','missing_software','to_check','delivered') NOT NULL DEFAULT 'in_progress',
  `customer_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `last_operator_student_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `laptop_group_assignments`
--

CREATE TABLE `laptop_group_assignments` (
  `id` int(11) NOT NULL,
  `laptop_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unassigned_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `laptop_software`
--

CREATE TABLE `laptop_software` (
  `id` int(11) NOT NULL,
  `laptop_id` int(11) NOT NULL,
  `software_id` int(11) NOT NULL,
  `installed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `laptop_state_history`
--

CREATE TABLE `laptop_state_history` (
  `id` int(11) NOT NULL,
  `laptop_id` int(11) NOT NULL,
  `changed_by_student_id` int(11) DEFAULT NULL,
  `previous_status` enum('in_progress','ready','missing_office','missing_software','to_check','delivered') DEFAULT NULL,
  `new_status` enum('in_progress','ready','missing_office','missing_software','to_check','delivered') NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `payment_transfers`
--

CREATE TABLE `payment_transfers` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_at` date NOT NULL,
  `reference` varchar(190) DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `pcs_paid_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `software`
--

CREATE TABLE `software` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `version` varchar(100) DEFAULT NULL,
  `license` varchar(120) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `view_cards`
--

CREATE TABLE `view_cards` (
  `id` int(11) NOT NULL,
  `scope` enum('dashboard','customers','students','groups','payments','laptops','software') NOT NULL,
  `metric` varchar(120) NOT NULL,
  `value` bigint(20) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `work_groups`
--

CREATE TABLE `work_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `leader_student_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_access_logs_student` (`student_id`),
  ADD KEY `idx_access_logs_event` (`event`);

--
-- Indici per le tabelle `action_logs`
--
ALTER TABLE `action_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_action_logs_actor` (`actor_student_id`),
  ADD KEY `fk_action_logs_laptop` (`laptop_id`),
  ADD KEY `fk_action_logs_customer` (`customer_id`),
  ADD KEY `fk_action_logs_group` (`group_id`);

--
-- Indici per le tabelle `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_customers_email` (`email`);

--
-- Indici per le tabelle `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_group_member` (`group_id`,`student_id`),
  ADD KEY `idx_group_members_group` (`group_id`),
  ADD KEY `idx_group_members_student` (`student_id`),
  ADD KEY `idx_group_members_role` (`role`);

--
-- Indici per le tabelle `laptops`
--
ALTER TABLE `laptops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `idx_laptops_code` (`code`),
  ADD KEY `fk_laptops_last_operator` (`last_operator_student_id`),
  ADD KEY `idx_laptops_status` (`status`),
  ADD KEY `idx_laptops_customer` (`customer_id`),
  ADD KEY `idx_laptops_group` (`group_id`);

--
-- Indici per le tabelle `laptop_group_assignments`
--
ALTER TABLE `laptop_group_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lga_laptop` (`laptop_id`),
  ADD KEY `idx_lga_group` (`group_id`);

--
-- Indici per le tabelle `laptop_software`
--
ALTER TABLE `laptop_software`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_laptop_software` (`laptop_id`,`software_id`),
  ADD KEY `fk_ls_software` (`software_id`);

--
-- Indici per le tabelle `laptop_state_history`
--
ALTER TABLE `laptop_state_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lsh_laptop` (`laptop_id`),
  ADD KEY `fk_lsh_student` (`changed_by_student_id`);

--
-- Indici per le tabelle `payment_transfers`
--
ALTER TABLE `payment_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payments_customer` (`customer_id`),
  ADD KEY `idx_payments_status` (`status`),
  ADD KEY `idx_payment_transfers_status` (`status`);

--
-- Indici per le tabelle `software`
--
ALTER TABLE `software`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_software_name_version` (`name`,`version`);

--
-- Indici per le tabelle `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_students_email` (`email`),
  ADD KEY `idx_students_role` (`role`),
  ADD KEY `idx_students_active` (`active`);

--
-- Indici per le tabelle `view_cards`
--
ALTER TABLE `view_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_scope_metric` (`scope`,`metric`),
  ADD KEY `idx_metric` (`metric`);

--
-- Indici per le tabelle `work_groups`
--
ALTER TABLE `work_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_work_groups_leader` (`leader_student_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `laptops`
--
ALTER TABLE `laptops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `laptop_group_assignments`
--
ALTER TABLE `laptop_group_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `laptop_software`
--
ALTER TABLE `laptop_software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `laptop_state_history`
--
ALTER TABLE `laptop_state_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `payment_transfers`
--
ALTER TABLE `payment_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `software`
--
ALTER TABLE `software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `view_cards`
--
ALTER TABLE `view_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `work_groups`
--
ALTER TABLE `work_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `access_logs`
--
ALTER TABLE `access_logs`
  ADD CONSTRAINT `fk_access_logs_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `action_logs`
--
ALTER TABLE `action_logs`
  ADD CONSTRAINT `fk_action_logs_actor` FOREIGN KEY (`actor_student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_action_logs_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_action_logs_group` FOREIGN KEY (`group_id`) REFERENCES `work_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_action_logs_laptop` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `fk_group_members_group` FOREIGN KEY (`group_id`) REFERENCES `work_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_group_members_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `laptops`
--
ALTER TABLE `laptops`
  ADD CONSTRAINT `fk_laptops_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_laptops_group` FOREIGN KEY (`group_id`) REFERENCES `work_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_laptops_last_operator` FOREIGN KEY (`last_operator_student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `laptop_group_assignments`
--
ALTER TABLE `laptop_group_assignments`
  ADD CONSTRAINT `fk_lga_group` FOREIGN KEY (`group_id`) REFERENCES `work_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lga_laptop` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `laptop_software`
--
ALTER TABLE `laptop_software`
  ADD CONSTRAINT `fk_ls_laptop` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ls_software` FOREIGN KEY (`software_id`) REFERENCES `software` (`id`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `laptop_state_history`
--
ALTER TABLE `laptop_state_history`
  ADD CONSTRAINT `fk_lsh_laptop` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lsh_student` FOREIGN KEY (`changed_by_student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `payment_transfers`
--
ALTER TABLE `payment_transfers`
  ADD CONSTRAINT `fk_payments_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `work_groups`
--
ALTER TABLE `work_groups`
  ADD CONSTRAINT `fk_work_groups_leader` FOREIGN KEY (`leader_student_id`) REFERENCES `students` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
