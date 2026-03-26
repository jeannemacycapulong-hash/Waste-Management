-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 08:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wastemanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`, `read_at`) VALUES
(1, 1, 'Welcome!', 'Your account has been created successfully.', 'success', 0, '2026-02-21 07:22:19', NULL),
(2, 2, 'Collection Day Tomorrow', 'Please place your waste bins out by 8:30 AM.', 'info', 0, '2026-02-21 07:22:19', NULL),
(3, 3, 'Route Assigned', 'You have been assigned to Pampang Purok route.', 'info', 0, '2026-02-21 07:22:19', NULL),
(4, 4, 'System Ready', 'Waste Management System is now active.', 'success', 0, '2026-02-21 07:22:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reporter_type` enum('villager','collector') NOT NULL,
  `issue_type` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `urgency` enum('low','medium','high') DEFAULT 'low',
  `status` enum('pending','resolved') DEFAULT 'pending',
  `admin_response` text DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reporter_id`, `reporter_type`, `issue_type`, `location`, `description`, `urgency`, `status`, `admin_response`, `resolved_by`, `resolved_at`, `created_at`) VALUES
(1, 1, 'villager', 'missed_collection', 'Blk 1 Lot 2, Pampang Purok', 'Collection was missed on Thursday', 'medium', 'pending', NULL, NULL, NULL, '2026-02-21 07:22:19'),
(2, 2, 'villager', 'spilled_garbage', 'Blk 2 Lot 5, Pampang Purok', 'Garbage spilled on the road', 'high', 'pending', NULL, NULL, NULL, '2026-02-21 07:22:19'),
(3, 3, 'collector', 'vehicle_issue', 'Baranggay Pampang Purok', 'Truck needs maintenance', 'low', 'pending', NULL, NULL, NULL, '2026-02-21 07:22:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('villager','collector','admin') NOT NULL DEFAULT 'villager',
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `role`, `email`, `contact_number`, `address`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'user1', 'pass1', 'Juan Dela Cruz', 'villager', 'juan@email.com', '09123456789', 'Blk 1 Lot 2, Pampang Purok, Angeles City', '2026-02-21 07:22:19', NULL, 1),
(2, 'villager', 'demo', 'Maria Santos', 'villager', 'maria@email.com', '09187654321', 'Blk 2 Lot 5, Pampang Purok, Angeles City', '2026-02-21 07:22:19', NULL, 1),
(3, 'collector', 'demo', 'Pedro Reyes', 'collector', 'pedro@email.com', '09234567890', 'Blk 3 Lot 8, Pampang Purok, Angeles City', '2026-02-21 07:22:19', NULL, 1),
(4, 'admin', 'demo', 'Admin User', 'admin', 'admin@email.com', '09345678901', 'Admin Office, Angeles City', '2026-02-21 07:22:19', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reporter_id` (`reporter_id`),
  ADD KEY `resolved_by` (`resolved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
-- --------------------------------------------------------
--
-- Table structure for table `monthly_dues`
--

CREATE TABLE `monthly_dues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `villager_id` int(11) NOT NULL,
  `due_month` date NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 1000.00,
  `status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_method` enum('cash','gcash','card') DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `villager_month` (`villager_id`, `due_month`),
  CONSTRAINT `monthly_dues_ibfk_1` FOREIGN KEY (`villager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sample dues data
--

INSERT INTO `monthly_dues` (`villager_id`, `due_month`, `amount`, `status`, `payment_method`, `paid_at`) VALUES
(1, '2026-01-01', 1000.00, 'unpaid', NULL, NULL),
(1, '2025-12-01', 1000.00, 'paid', 'cash', '2025-12-10 08:00:00'),
(1, '2025-11-01', 1000.00, 'paid', 'gcash', '2025-11-10 08:00:00'),
(2, '2026-01-01', 1000.00, 'unpaid', NULL, NULL),
(2, '2025-12-01', 1000.00, 'paid', 'cash', '2025-12-12 09:00:00');

-- --------------------------------------------------------
--
-- Table structure for table `collection_statuses`
--

CREATE TABLE `collection_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `villager_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `collection_date` date NOT NULL,
  `status` enum('pending','completed','missed','no_waste') NOT NULL DEFAULT 'pending',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `villager_date` (`villager_id`,`collection_date`),
  CONSTRAINT `cs_ibfk_1` FOREIGN KEY (`villager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cs_ibfk_2` FOREIGN KEY (`collector_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;