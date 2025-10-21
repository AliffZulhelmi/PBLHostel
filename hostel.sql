-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2025 at 09:51 AM
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
-- Database: `hostel`
--

-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE `blocks` (
  `block_id` varchar(10) NOT NULL,
  `block_name` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blocks`
--

INSERT INTO `blocks` (`block_id`, `block_name`, `gender`, `created_at`) VALUES
('A1', 'Block A1 (Female)', 'female', '2025-10-15 07:39:19'),
('A4', 'Block A4 (Male)', 'male', '2025-10-15 07:39:19'),
('A5', 'Block A5 (Male)', 'male', '2025-10-15 07:39:19');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `block_id` varchar(10) DEFAULT NULL,
  `floor_no` varchar(10) DEFAULT NULL,
  `room_no` varchar(10) DEFAULT NULL,
  `partition_capacity` varchar(10) DEFAULT '6',
  `status` varchar(50) DEFAULT 'Unoccupied',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `block_id`, `floor_no`, `room_no`, `partition_capacity`, `status`, `created_at`) VALUES
(1, 'A1', '01', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(2, 'A1', '01', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(3, 'A1', '01', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(4, 'A1', '01', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(5, 'A1', '01', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(6, 'A1', '02', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(7, 'A1', '02', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(8, 'A1', '02', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(9, 'A1', '02', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(10, 'A1', '02', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(11, 'A1', '03', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(12, 'A1', '03', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(13, 'A1', '03', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(14, 'A1', '03', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(15, 'A1', '03', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(16, 'A1', '04', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(17, 'A1', '04', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(18, 'A1', '04', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(19, 'A1', '04', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(20, 'A1', '04', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(21, 'A1', '05', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(22, 'A1', '05', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(23, 'A1', '05', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(24, 'A1', '05', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(25, 'A1', '05', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(26, 'A4', '01', '001', '6', 'Occupied', '2025-10-15 07:39:19'),
(27, 'A4', '01', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(28, 'A4', '01', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(29, 'A4', '01', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(30, 'A4', '01', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(31, 'A4', '02', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(32, 'A4', '02', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(33, 'A4', '02', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(34, 'A4', '02', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(35, 'A4', '02', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(36, 'A4', '03', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(37, 'A4', '03', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(38, 'A4', '03', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(39, 'A4', '03', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(40, 'A4', '03', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(41, 'A4', '04', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(42, 'A4', '04', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(43, 'A4', '04', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(44, 'A4', '04', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(45, 'A4', '04', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(46, 'A4', '05', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(47, 'A4', '05', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(48, 'A4', '05', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(49, 'A4', '05', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(50, 'A4', '05', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(51, 'A5', '01', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(52, 'A5', '01', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(53, 'A5', '01', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(54, 'A5', '01', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(55, 'A5', '01', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(56, 'A5', '02', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(57, 'A5', '02', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(58, 'A5', '02', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(59, 'A5', '02', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(60, 'A5', '02', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(61, 'A5', '03', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(62, 'A5', '03', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(63, 'A5', '03', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(64, 'A5', '03', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(65, 'A5', '03', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(66, 'A5', '04', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(67, 'A5', '04', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(68, 'A5', '04', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(69, 'A5', '04', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(70, 'A5', '04', '005', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(71, 'A5', '05', '001', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(72, 'A5', '05', '002', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(73, 'A5', '05', '003', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(74, 'A5', '05', '004', '6', 'Unoccupied', '2025-10-15 07:39:19'),
(75, 'A5', '05', '005', '6', 'Unoccupied', '2025-10-15 07:39:19');

-- --------------------------------------------------------

--
-- Table structure for table `student_rooms`
--

CREATE TABLE `student_rooms` (
  `sr_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `released_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_rooms`
--

INSERT INTO `student_rooms` (`sr_id`, `student_id`, `room_id`, `semester`, `status`, `assigned_at`, `released_at`) VALUES
(1, 2, 1, '2025/1', 'Active', '2025-10-15 07:39:19', NULL),
(2, 4, 26, '2025/1', 'Active', '2025-10-15 07:39:19', NULL),
(3, 5, 26, '2025/1', 'Active', '2025-10-15 07:39:19', NULL),
(4, 6, 26, '2025/1', 'Active', '2025-10-15 07:39:19', NULL),
(5, 10, 26, '2025/1', 'Active', '2025-10-15 07:39:19', NULL),
(6, 11, 26, '2025/1', 'Active', '2025-10-15 07:39:19', NULL),
(7, 12, 26, '2025/1', 'Active', '2025-10-15 07:39:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `student_id`, `email`, `phone`, `password`, `role`, `gender`, `created_at`, `updated_at`) VALUES
(1, 'System Admin', NULL, 'admin.system@gmi.edu.my', NULL, 'admin', 'admin', NULL, '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(2, 'Siti Nurhaliza', 'CBS24010030', 'siti.nurhaliza@student.gmi.edu.my', '0177329210', 'pass123', 'student', 'female', '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(3, 'Dahlia Binti Kasim', 'NWS24070025', 'dahlia.kasim@student.gmi.edu.my', '0112345678', 'pass123', 'student', 'female', '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(4, 'Ali Bin Abu', 'SWE24070001', 'ali.abu@student.gmi.edu.my', '0101112222', 'pass123', 'student', 'male', '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(5, 'Kumar A/L Mani', 'SWE24070002', 'kumar.mani@student.gmi.edu.my', '0103334444', 'pass123', 'student', 'male', '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(6, 'John Tan Chin Wei', 'SWE24070003', 'john.wei@student.gmi.edu.my', '0105556666', 'pass123', 'student', 'male', '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(10, 'Dummy Placeholder 1', 'DUM24070010', 'dummy1@test.com', NULL, 'pass', 'student', 'male', '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(11, 'Dummy Placeholder 2', 'DUM24070011', 'dummy2@test.com', NULL, 'pass', 'student', 'male', '2025-10-15 07:39:19', '2025-10-15 07:39:19'),
(12, 'Dummy Placeholder 3', 'DUM24070012', 'dummy3@test.com', NULL, 'pass', 'student', 'male', '2025-10-15 07:39:19', '2025-10-15 07:39:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`block_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `block_id` (`block_id`);

--
-- Indexes for table `student_rooms`
--
ALTER TABLE `student_rooms`
  ADD PRIMARY KEY (`sr_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `student_rooms`
--
ALTER TABLE `student_rooms`
  MODIFY `sr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`block_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_rooms`
--
ALTER TABLE `student_rooms`
  ADD CONSTRAINT `student_rooms_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
