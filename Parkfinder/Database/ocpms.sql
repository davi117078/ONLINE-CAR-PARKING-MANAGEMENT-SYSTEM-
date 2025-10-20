-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2025 at 10:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ocpms`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(50) NOT NULL,
  `checkin_time` datetime DEFAULT NULL,
  `checkout_time` datetime DEFAULT NULL,
  `total_fee` decimal(10,2) DEFAULT 0.00,
  `user_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `vehicle_no` varchar(50) DEFAULT NULL,
  `vehicle_type` enum('motorcycle','car','van','truck') DEFAULT 'car',
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `status` enum('reserved','active','completed','cancelled','no_show') DEFAULT 'reserved',
  `price` decimal(10,2) DEFAULT 0.00,
  `paid` tinyint(1) DEFAULT 0,
  `payment_method` varchar(50) DEFAULT NULL,
  `receipt_url` varchar(255) DEFAULT NULL,
  `qr_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `checkin_time`, `checkout_time`, `total_fee`, `user_id`, `slot_id`, `vehicle_no`, `vehicle_type`, `start_time`, `end_time`, `amount_paid`, `status`, `price`, `paid`, `payment_method`, `receipt_url`, `qr_image`, `created_at`, `updated_at`) VALUES
(1, 'BK73372', NULL, NULL, 0.00, 1, 4, 'KDV 694H', 'car', '2025-10-08 08:30:00', '2025-10-08 18:30:00', 0.00, 'cancelled', 0.00, 0, NULL, NULL, NULL, '2025-10-09 06:07:51', '2025-10-09 06:56:13'),
(2, 'BK33518', '2025-10-08 21:35:10', '2025-10-08 21:35:37', 1200.00, 1, 6, 'KMGA 225K', 'motorcycle', '2025-10-08 11:10:00', '2025-10-08 12:10:00', 0.00, 'completed', 0.00, 0, NULL, NULL, NULL, '2025-10-09 06:11:46', '2025-10-09 06:56:13'),
(3, 'BK38852', '2025-10-13 08:48:00', '2025-10-13 08:48:27', 100.00, 8, 4, 'KDS 112S', 'car', '2025-10-09 14:00:00', '2025-10-10 14:01:00', 0.00, 'completed', 0.00, 0, NULL, NULL, NULL, '2025-10-09 11:01:07', '2025-10-13 05:48:27'),
(4, 'BK85893', '2025-10-13 09:41:54', '2025-10-13 09:42:41', 100.00, 9, 5, 'KDM 742A', 'truck', '2025-10-13 09:34:00', '2025-10-16 09:34:00', 0.00, 'completed', 0.00, 0, NULL, NULL, NULL, '2025-10-13 06:35:26', '2025-10-13 06:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact_number` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `location`, `contact_number`, `capacity`, `created_at`) VALUES
(1, 'Downtown Parking Lot', 'Central Business District, Nairobi', '+254712345678', 100, '2025-10-08 09:16:37'),
(2, 'Westlands Car Park', 'Westlands, Nairobi', '+254711223344', 80, '2025-10-08 09:16:37'),
(3, 'Upper Hill Parking', 'Upper Hill Road, Nairobi', '+254733445566', 120, '2025-10-08 09:16:37'),
(4, 'Industrial Area Lot A', 'Enterprise Road, Industrial Area', '+254701998877', 60, '2025-10-08 09:16:37'),
(5, 'Airport Express Parking', 'Jomo Kenyatta International Airport', '+254722334455', 150, '2025-10-08 09:16:37'),
(6, 'Thika Road Mall Parking', 'Thika Road, Kasarani', '+254790112233', 90, '2025-10-08 09:16:37'),
(7, 'Karen Hub Parking', 'Karen Shopping Centre, Nairobi', '+254799887766', 70, '2025-10-08 09:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `user_role` varchar(50) DEFAULT 'driver',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_slots`
--

CREATE TABLE `parking_slots` (
  `id` int(11) NOT NULL,
  `slot_name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `slot_code` varchar(50) NOT NULL,
  `type` enum('motorcycle','car','van','truck') DEFAULT 'car',
  `hourly_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('vacant','occupied','reserved','out_of_service') DEFAULT 'vacant',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_slots`
--

INSERT INTO `parking_slots` (`id`, `slot_name`, `location`, `branch_id`, `slot_code`, `type`, `hourly_rate`, `status`, `created_at`) VALUES
(4, 'A1', 'Nairobi CBD', 1, '', 'car', 100.00, 'reserved', '2025-10-08 09:38:35'),
(5, 'A2', 'Nairobi CBD', 1, '', 'car', 120.00, 'reserved', '2025-10-08 09:38:35'),
(6, 'B1', 'Nairobi CBD', 1, '', 'motorcycle', 50.00, 'vacant', '2025-10-08 09:38:35'),
(7, 'C1', 'Westlands', 2, '', 'car', 150.00, 'vacant', '2025-10-08 09:38:35'),
(8, 'C2', 'Westlands', 2, '', 'truck', 200.00, 'vacant', '2025-10-08 09:38:35'),
(9, 'D1', 'Kilimani', 3, '', 'car', 130.00, 'vacant', '2025-10-08 09:38:35'),
(10, 'D2', 'Kilimani', 3, '', 'motorcycle', 60.00, 'vacant', '2025-10-08 09:38:35'),
(11, 'E1', 'Upper Hill', 4, '', 'car', 140.00, 'vacant', '2025-10-08 09:38:35'),
(12, 'E2', 'Upper Hill', 4, '', 'truck', 220.00, 'vacant', '2025-10-08 09:38:35'),
(13, 'F1', 'Karen', 5, '', 'car', 110.00, 'vacant', '2025-10-08 09:38:35'),
(15, 'A001', NULL, 4, '', 'motorcycle', 100.00, 'vacant', '2025-10-09 05:30:58'),
(16, 'W45', NULL, 1, '', 'car', 150.00, 'vacant', '2025-10-09 05:31:29');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(150) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('driver','attendant','admin') NOT NULL DEFAULT 'driver',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `role`, `created_at`) VALUES
(1, 'john', 'john1234@gmail.com', '0788888888', '$2y$10$HXokbA3pAFkFOltDhmHhRu/bFoSfpFVGnFb2QebmkTal2fh.rzmQa', 'driver', '2025-10-08 02:31:35'),
(4, 'David', 'david@gmail.com', NULL, '$2y$10$4s8tsS..G4al9bjuufm3yeutSKDHBtsejGbxdT9c7kN30puVe2xEW', 'admin', '2025-10-08 08:17:07'),
(5, 'Ken', 'ken@gmail.com', NULL, '$2y$10$vF6MmD2hMIgrp88kXDsIYuc6YyUlHSor64SxD9kXGS.Rvh1wb.fyO', 'attendant', '2025-10-08 08:30:14'),
(6, 'alex', 'alex@gmail.com', NULL, '$2y$10$3trAGmAWEXRWZ2I/7Ngz3OoFcCDXanirdS6kkqtXmcC/zd9nL40ne', 'driver', '2025-10-09 05:13:18'),
(8, 'felix', 'felix@gmail.com', NULL, '$2y$10$88XfzYGPKQ1v.4e9kWvRDOIHncM4d3cMaOj/WIg8VkLFZDWXUTx2O', 'driver', '2025-10-09 10:57:13'),
(9, 'GITS', 'GITS@gmail.com', NULL, '$2y$10$Vsa24SgAX/qsY956uUOFIu2/y8sk4i27GyBY/6umHjwB1p0bAXcF2', 'driver', '2025-10-13 06:33:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parking_slots`
--
ALTER TABLE `parking_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`slot_id`) REFERENCES `parking_slots` (`id`);

--
-- Constraints for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD CONSTRAINT `parking_slots_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
