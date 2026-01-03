-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3383
-- Creato il: Gen 03, 2026 alle 13:21
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

--
-- Dump dei dati per la tabella `access_logs`
--

INSERT INTO `access_logs` (`id`, `student_id`, `event`, `ip`, `created_at`) VALUES
(32, 7, 'logout', '::1', '2026-01-03 11:51:16'),
(33, 7, 'login_ok', '::1', '2026-01-03 11:51:18');

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

--
-- Dump dei dati per la tabella `action_logs`
--

INSERT INTO `action_logs` (`id`, `actor_student_id`, `action_type`, `laptop_id`, `customer_id`, `group_id`, `note`, `created_at`) VALUES
(30, 7, 'update_laptop', 1, 1, 1, 'PC-001', '2026-01-03 11:51:48');

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

--
-- Dump dei dati per la tabella `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `notes`, `created_at`, `updated_at`, `pc_assigned_count`, `pc_requested_count`) VALUES
(1, 'Mario', 'Rossi', 'mario.rossi@example.com', '', '2025-12-28 08:50:33', '2026-01-02 11:56:14', 0, 2),
(2, 'Lucia', 'Bianchi', 'lucia.bianchi@example.com', '', '2025-12-28 08:50:33', '2026-01-02 11:56:32', 0, 1),
(3, 'Paolo', 'Verdi', 'paolo.verdi@example.com', '', '2025-12-28 08:50:33', '2026-01-02 11:56:44', 0, 1);

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

--
-- Dump dei dati per la tabella `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `student_id`, `role`, `joined_at`) VALUES
(4, 1, 5, 'leader', '2025-12-28 08:50:33'),
(9, 1, 6, 'installer', '2025-12-31 09:46:37'),
(18, 4, 3, 'leader', '2025-12-31 10:13:14'),
(21, 4, 1, 'installer', '2026-01-02 06:07:58'),
(22, 4, 4, 'installer', '2026-01-02 06:08:02'),
(23, 5, 2, 'leader', '2026-01-02 18:38:12');

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

--
-- Dump dei dati per la tabella `laptops`
--

INSERT INTO `laptops` (`id`, `code`, `brand_model`, `cpu`, `ram`, `storage`, `screen`, `tech_notes`, `scratches`, `physical_condition`, `battery`, `condition_level`, `office_license`, `windows_license`, `other_software_request`, `status`, `customer_id`, `group_id`, `last_operator_student_id`, `created_at`, `updated_at`) VALUES
(1, 'PC-001', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'average', 'OFF-2019-XXXXX', 'WIN-10-PRO-YYYYY', NULL, 'to_check', 1, 1, NULL, '2025-12-28 08:50:33', '2025-12-31 08:38:20'),
(2, 'PC-002', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, 'WIN-10-HOME-ZZZZZ', NULL, 'missing_software', 1, 1, NULL, '2025-12-28 08:50:33', '2025-12-31 08:38:20'),
(3, 'PC-003', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'excellent', 'OFF-2019-AAAAA', 'WIN-11-PRO-BBBBB', NULL, 'to_check', 2, NULL, NULL, '2025-12-28 08:50:33', '2025-12-31 08:38:20'),
(4, 'PC-004', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'fair', NULL, 'WIN-11-HOME-CCCCC', NULL, 'in_progress', NULL, NULL, NULL, '2025-12-28 08:50:33', '2026-01-02 11:51:22'),
(5, 'PC-005', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', 'OFF-2019-DDDDD', 'WIN-11-PRO-EEEEE', NULL, 'ready', 3, 1, NULL, '2025-12-28 08:50:33', '2025-12-31 08:38:20'),
(9, 'PC-006', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', '2025-12-31 09:32:39'),
(10, 'PC-007', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'missing_software', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(11, 'PC-008', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(12, 'PC-009', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'in_progress', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(13, 'PC-010', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'ready', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(14, 'PC-011', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(15, 'PC-012', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'missing_software', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(16, 'PC-013', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(17, 'PC-014', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'in_progress', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(18, 'PC-015', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'ready', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(19, 'PC-016', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(20, 'PC-017', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'missing_software', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(21, 'PC-018', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(22, 'PC-019', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'in_progress', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(23, 'PC-020', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'ready', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(24, 'PC-021', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(25, 'PC-022', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'missing_software', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(26, 'PC-023', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(27, 'PC-024', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'in_progress', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(28, 'PC-025', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'ready', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(29, 'PC-026', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'to_check', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL),
(30, 'PC-027', 'Lenovo ThinkPad L390', 'i5-8365U', '8GB', '256GB SSD', '14\" FHD', '', '', 'very_good', 'average', 'good', NULL, NULL, NULL, 'missing_software', NULL, NULL, NULL, '2025-12-31 08:38:20', NULL);

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

--
-- Dump dei dati per la tabella `laptop_software`
--

INSERT INTO `laptop_software` (`id`, `laptop_id`, `software_id`, `installed_at`) VALUES
(7, 3, 2, '2025-12-28 08:50:33'),
(9, 5, 1, '2025-12-28 08:50:33'),
(10, 5, 2, '2025-12-28 08:50:33'),
(21, 2, 1, '2025-12-31 09:17:38'),
(22, 2, 2, '2025-12-31 09:17:38'),
(23, 2, 3, '2025-12-31 09:17:38'),
(26, 4, 1, '2026-01-02 11:52:58'),
(27, 1, 1, '2026-01-03 11:51:48'),
(28, 1, 2, '2026-01-03 11:51:48'),
(29, 1, 3, '2026-01-03 11:51:48'),
(30, 1, 4, '2026-01-03 11:51:48');

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

--
-- Dump dei dati per la tabella `laptop_state_history`
--

INSERT INTO `laptop_state_history` (`id`, `laptop_id`, `changed_by_student_id`, `previous_status`, `new_status`, `note`, `created_at`) VALUES
(1, 1, 2, 'to_check', 'in_progress', 'Diagnosi completata', '2025-12-28 08:50:33'),
(2, 3, 3, 'in_progress', 'to_check', 'Verifica batteria', '2025-12-28 08:50:33'),
(3, 5, 2, 'in_progress', 'ready', 'Consegna pronta', '2025-12-28 08:50:33'),
(4, 1, 6, 'missing_software', 'to_check', NULL, '2025-12-28 09:16:26');

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

--
-- Dump dei dati per la tabella `payment_transfers`
--

INSERT INTO `payment_transfers` (`id`, `customer_id`, `amount`, `paid_at`, `reference`, `receipt_path`, `status`, `created_at`, `updated_at`, `pcs_paid_count`) VALUES
(1, 1, 600.00, '2025-12-01', 'Bonifico-001', 'public/uploads/bonifico_001.pdf', 'verified', '2025-12-28 08:50:33', NULL, 0),
(2, 2, 400.00, '2025-12-05', 'Bonifico-002', 'public/uploads/bonifico_002.pdf', 'pending', '2025-12-28 08:50:33', NULL, 0),
(6, 2, 11.00, '2025-12-31', '', NULL, 'verified', '2025-12-31 09:34:53', NULL, 0);

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

--
-- Dump dei dati per la tabella `software`
--

INSERT INTO `software` (`id`, `name`, `version`, `license`, `cost`, `notes`) VALUES
(1, 'Windows', '11', 'Commercial', 130.00, NULL),
(2, 'Office', '2024', 'Commercial', 70.00, NULL),
(3, 'Adobe Reader', NULL, 'Free', 0.00, NULL),
(4, 'Chrome', NULL, 'Free', NULL, NULL);

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

--
-- Dump dei dati per la tabella `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Giulia', 'Neri', 'giulia.neri@example.com', '$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K', 'student', 1, '2025-12-28 08:50:33', NULL),
(2, 'Marco', 'Blu', 'marco.blu@example.com', '$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K', 'student', 1, '2025-12-28 08:50:33', NULL),
(3, 'Sara', 'Gialli', 'sara.gialli@example.com', '$2y$10$i05.RFIDbr3GxKhWFBnIE.PQzUZQiCBgBNcDoyFVuURv.O8IEs/y.', 'student', 1, '2025-12-28 08:50:33', '2026-01-02 16:19:51'),
(4, 'Luca', 'Viola', 'luca.viola@example.com', '$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K', 'student', 1, '2025-12-28 08:50:33', NULL),
(5, 'Elena', 'Rosa', 'elena.rosa@example.com', '$2y$10$Qe9cZ8Hh5B4s5JmpswYfGOH3bN0c1mNQeLx1aFzDWjKQ8iJQn5Y5K', 'student', 1, '2025-12-28 08:50:33', NULL),
(6, 'Davide', 'Arancio', 'davide.arancio@example.com', '$2y$10$YdcAbZrzKfxE2Rd1bi2cnuDYe/8.jQzJX9Be2CsWXETgyHS/tIP96', 'student', 1, '2025-12-28 08:50:33', '2026-01-02 13:51:29'),
(7, 'Super', 'Admin', 'admin', '$2y$10$bBoiLYzspR/ZwgIFh0QUFuPkdyAjeoBvOVrI9FE/mFCTJxsSN3uBm', 'admin', 1, '2025-12-28 08:50:37', NULL);

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

--
-- Dump dei dati per la tabella `view_cards`
--

INSERT INTO `view_cards` (`id`, `scope`, `metric`, `value`, `updated_at`) VALUES
(1, 'dashboard', 'laptops_total', 27, '2026-01-03 11:51:48'),
(2, 'dashboard', 'ready', 5, '2026-01-03 11:51:48'),
(3, 'dashboard', 'in_progress', 5, '2026-01-03 11:51:48'),
(4, 'dashboard', 'missing_software', 6, '2026-01-03 11:51:48'),
(5, 'dashboard', 'to_check', 11, '2026-01-03 11:51:48'),
(6, 'dashboard', 'delivered', 0, '2026-01-03 11:51:48'),
(7, 'dashboard', 'customers_total', 3, '2026-01-03 11:51:48'),
(8, 'dashboard', 'students_total', 7, '2026-01-03 11:51:48'),
(9, 'dashboard', 'groups_total', 3, '2026-01-03 11:51:48'),
(10, 'laptops', 'total', 27, '2026-01-03 11:51:48'),
(11, 'laptops', 'ready', 5, '2026-01-03 11:51:48'),
(12, 'laptops', 'in_work', 22, '2026-01-03 11:51:48'),
(13, 'students', 'students', 7, '2026-01-03 08:30:51'),
(14, 'students', 'leaders', 3, '2026-01-03 08:30:51'),
(15, 'students', 'installers', 3, '2026-01-03 08:30:51'),
(16, '', 'total', 4, '2026-01-03 09:57:36'),
(17, '', 'free', 2, '2026-01-03 09:57:36'),
(18, '', 'paid', 2, '2026-01-03 09:57:36'),
(25, 'payments', 'pcs_paid', 2, '2026-01-03 09:54:31'),
(26, 'payments', 'customers', 3, '2026-01-03 09:54:31'),
(27, 'payments', 'pcs_requested', 6, '2026-01-03 09:54:31'),
(28, 'customers', 'docenti', 3, '2026-01-03 11:51:48'),
(29, 'customers', 'pc_richiesti', 4, '2026-01-03 11:51:48'),
(30, 'customers', 'pc_assegnati', 4, '2026-01-03 11:51:48'),
(31, 'customers', 'pc_pagati', 0, '2026-01-03 11:51:48'),
(59, 'software', 'total', 4, '2026-01-03 09:57:54'),
(60, 'software', 'free', 2, '2026-01-03 09:57:54'),
(61, 'software', 'paid', 2, '2026-01-03 09:57:54'),
(62, 'groups', 'groups', 3, '2026-01-03 11:51:48'),
(63, 'groups', 'students', 6, '2026-01-03 11:51:48'),
(64, 'groups', 'laptops', 3, '2026-01-03 11:51:48');

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
-- Dump dei dati per la tabella `work_groups`
--

INSERT INTO `work_groups` (`id`, `name`, `leader_student_id`, `created_at`, `updated_at`) VALUES
(1, 'Team Alpha', 5, '2025-12-28 08:50:33', '2025-12-31 10:10:57'),
(4, 'pippo', 3, '2025-12-31 10:13:14', NULL),
(5, 'gruppoblu', 2, '2026-01-02 18:38:12', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT per la tabella `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT per la tabella `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT per la tabella `laptops`
--
ALTER TABLE `laptops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT per la tabella `laptop_group_assignments`
--
ALTER TABLE `laptop_group_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `laptop_software`
--
ALTER TABLE `laptop_software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT per la tabella `laptop_state_history`
--
ALTER TABLE `laptop_state_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `payment_transfers`
--
ALTER TABLE `payment_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `software`
--
ALTER TABLE `software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `view_cards`
--
ALTER TABLE `view_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT per la tabella `work_groups`
--
ALTER TABLE `work_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
