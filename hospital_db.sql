-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 09:40 AM
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
-- Database: `hospital_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `created_at`) VALUES
(1, 4, 3, '2025-10-15', '10:30:00', 'scheduled', '2025-10-07 05:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `recipient_role` enum('admin','doctor','receptionist','patient') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `recipient_role`, `created_at`) VALUES
(1, 'System maintenance scheduled for October 10, 2025.', 'admin', '2025-10-07 05:04:18'),
(2, 'helllo', 'admin', '2025-10-08 20:43:40'),
(3, 'go to registration number 4', 'admin', '2025-10-09 15:13:52'),
(4, 'hello', 'admin', '2025-10-13 06:41:38');

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `ticket_no` int(11) DEFAULT NULL,
  `ticket_number` varchar(20) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `status` enum('waiting','serving','called','done','in_progress','completed') NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`id`, `patient_id`, `patient_name`, `ticket_no`, `ticket_number`, `doctor_id`, `status`, `created_at`) VALUES
(1, NULL, 'John Doe', 1, 'Q0001', NULL, 'completed', '2025-10-07 05:04:18'),
(2, NULL, 'Alice Smith', 2, 'Q0002', NULL, 'completed', '2025-10-07 05:04:18'),
(3, NULL, 'Bob Brown', 3, 'Q0003', NULL, 'completed', '2025-10-07 05:04:18'),
(4, NULL, 'felix', 4, 'Q0004', NULL, 'completed', '2025-10-09 09:29:25'),
(5, NULL, 'victor mbugua', 5, 'Q0005', NULL, 'completed', '2025-10-09 10:11:19'),
(6, NULL, 'felix', 6, 'Q0006', NULL, 'completed', '2025-10-09 15:14:39'),
(7, NULL, 'david', 7, 'Q0007', NULL, 'completed', '2025-10-09 15:15:04'),
(8, NULL, 'joseph', 8, 'Q0008', NULL, 'completed', '2025-10-09 15:15:13'),
(9, NULL, 'peris', 9, 'Q0009', NULL, 'completed', '2025-10-09 16:58:53'),
(10, NULL, 'felix', 10, 'Q0010', NULL, 'serving', '2025-10-13 06:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','receptionist','patient') NOT NULL DEFAULT 'patient',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$uH0XxExqkQb6yXQJgzq5zeYfXXq8coPzFbgUClQBrv1I.9zTrlYbG', 'admin', '2025-10-08 20:10:56'),
(2, 'reception', 'reception@gmail.com', '$2y$10$M5p0Xf2Qq3fNjzZShV/RhuhzFo0j3wQn7sP8Ia0B3mKuU2TZFzT/C', 'receptionist', '2025-10-08 20:10:56'),
(3, 'doctor', 'doctor@gmail.com', '$2y$10$1pQP6Jd.8CQ6QHs2wZtr1evZ1CQChHPz36vKjT4Vx6x0H7m1K/3zC', 'doctor', '2025-10-08 20:10:56'),
(5, 'agriba', 'agriba@gmail.com', '$2y$10$FKvnf7Lj3XDjiMdIB6d9z.IAbLIqp2WU1M8IWdfbVx3U9wp6opO6q', 'admin', '2025-10-08 20:20:12'),
(7, 'peris', 'mawia@gmail.com', '$2y$10$saTydBmlUe6LpMTtnBChMe29ooaR2mNSZiPUKMgn9e8IvRzl4B6pS', 'doctor', '2025-10-09 17:00:38'),
(11, 'qwerty', 'qwerty@gmail.com', '$2y$10$fqZNg9VXWvAVM7mclDfoAOLbbcughUg/fwoFyt3oKp/8xh0FhZd6i', 'receptionist', '2025-10-13 10:29:59'),
(13, 'agriba@gmail.com', 'vmbugua240@gmail.com', '$2y$10$Zhmw5e9Znw7rjZ50GvJrGuwIJcVNLU7PIinSIdU60m8c/LCcqLX6S', 'admin', '2025-10-13 11:05:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `status` (`status`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
