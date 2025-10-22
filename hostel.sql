-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 07:07 AM
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
('A1', 'Block A1', 'female', '2025-10-08 05:25:46'),
('A2', 'Block A2', 'male', '2025-10-08 05:25:46'),
('A3', 'Block A3', 'male', '2025-10-08 05:25:46'),
('A4', 'Block A4', 'male', '2025-10-08 05:25:46'),
('A5', 'Block A5', 'male', '2025-10-08 05:25:46'),
('A7', 'Block A7', 'female', '2025-10-08 05:25:46');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_identifier` varchar(20) NOT NULL,
  `block_id` varchar(10) DEFAULT NULL,
  `floor_no` varchar(10) DEFAULT NULL,
  `room_no` varchar(10) DEFAULT NULL,
  `total_capacity` int(11) DEFAULT 6,
  `available_capacity` int(11) DEFAULT 6,
  `status` varchar(50) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_identifier`, `block_id`, `floor_no`, `room_no`, `total_capacity`, `available_capacity`, `status`, `created_at`) VALUES
('A1-01-001', 'A1', '01', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-01-002', 'A1', '01', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-01-003', 'A1', '01', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-01-004', 'A1', '01', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-01-005', 'A1', '01', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-02-001', 'A1', '02', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-02-002', 'A1', '02', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-02-003', 'A1', '02', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-02-004', 'A1', '02', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-02-005', 'A1', '02', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-03-001', 'A1', '03', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-03-002', 'A1', '03', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-03-003', 'A1', '03', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-03-004', 'A1', '03', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-03-005', 'A1', '03', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-04-001', 'A1', '04', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-04-002', 'A1', '04', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-04-003', 'A1', '04', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-04-004', 'A1', '04', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-04-005', 'A1', '04', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-05-001', 'A1', '05', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-05-002', 'A1', '05', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-05-003', 'A1', '05', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-05-004', 'A1', '05', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A1-05-005', 'A1', '05', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-01-001', 'A4', '01', '001', 6, 4, 'Available', '2025-10-21 04:19:44'),
('A4-01-002', 'A4', '01', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-01-003', 'A4', '01', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-01-004', 'A4', '01', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-01-005', 'A4', '01', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-02-001', 'A4', '02', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-02-002', 'A4', '02', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-02-003', 'A4', '02', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-02-004', 'A4', '02', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-02-005', 'A4', '02', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-03-001', 'A4', '03', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-03-002', 'A4', '03', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-03-003', 'A4', '03', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-03-004', 'A4', '03', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-03-005', 'A4', '03', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-04-001', 'A4', '04', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-04-002', 'A4', '04', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-04-003', 'A4', '04', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-04-004', 'A4', '04', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-04-005', 'A4', '04', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-05-001', 'A4', '05', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-05-002', 'A4', '05', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-05-003', 'A4', '05', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-05-004', 'A4', '05', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A4-05-005', 'A4', '05', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-01-001', 'A5', '01', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-01-002', 'A5', '01', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-01-003', 'A5', '01', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-01-004', 'A5', '01', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-01-005', 'A5', '01', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-02-001', 'A5', '02', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-02-002', 'A5', '02', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-02-003', 'A5', '02', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-02-004', 'A5', '02', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-02-005', 'A5', '02', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-03-001', 'A5', '03', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-03-002', 'A5', '03', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-03-003', 'A5', '03', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-03-004', 'A5', '03', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-03-005', 'A5', '03', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-04-001', 'A5', '04', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-04-002', 'A5', '04', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-04-003', 'A5', '04', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-04-004', 'A5', '04', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-04-005', 'A5', '04', '005', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-05-001', 'A5', '05', '001', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-05-002', 'A5', '05', '002', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-05-003', 'A5', '05', '003', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-05-004', 'A5', '05', '004', 6, 6, 'Available', '2025-10-21 04:19:44'),
('A5-05-005', 'A5', '05', '005', 6, 6, 'Available', '2025-10-21 04:19:44');

-- --------------------------------------------------------

--
-- Table structure for table `student_rooms`
--

CREATE TABLE `student_rooms` (
  `sr_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `room_identifier` varchar(20) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `released_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_rooms`
--

INSERT INTO `student_rooms` (`sr_id`, `student_id`, `room_identifier`, `semester`, `status`, `assigned_at`, `released_at`) VALUES
(1, 'CBS24001002', 'A4-01-001', '2025/1', 'Released', '2025-10-21 05:29:49', '2025-10-21 00:42:09'),
(2, 'CBS24001002', 'A4-01-001', '2025/1', 'Released', '2025-10-21 06:42:12', '2025-10-21 00:42:19'),
(3, 'CBS24001002', 'A4-01-001', '2025/1', 'Released', '2025-10-21 06:42:20', '2025-10-21 01:01:13'),
(4, 'CBS24001002', 'A4-01-001', '2025/1', 'Released', '2025-10-21 07:01:17', '2025-10-21 22:02:43'),
(5, 'CBS24001002', 'A4-01-001', '2025/1', 'Released', '2025-10-22 04:03:38', '2025-10-21 22:29:36'),
(6, 'CBS24001002', 'A5-05-005', '2025/1', 'Released', '2025-10-22 04:29:36', '2025-10-21 22:33:35'),
(7, 'CBS24001002', 'A5-05-001', '2025/1', 'Released', '2025-10-22 04:33:35', '2025-10-21 22:33:45'),
(8, 'CBS24001002', 'A4-01-001', '2025/1', 'Active', '2025-10-22 04:34:10', NULL),
(9, 'CBS24001010', 'A4-01-001', '2025/1', 'Active', '2025-10-22 04:38:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `student_id`, `category`, `description`, `attachment_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CBS24001002', 'Room Change', 'REQUEST: Student CBS24001002 requests room change from A4-01-001 to A5-05-005.', NULL, 'Rejected', '2025-10-21 06:54:40', '2025-10-22 04:29:42'),
(2, 'CBS24001002', 'Room Change', 'REQUEST: Student CBS24001002 requests room change from A4-01-001 to A5-05-005.', NULL, 'Approved', '2025-10-21 07:01:36', '2025-10-22 04:29:36'),
(3, 'CBS24001002', 'Broken Furniture', 'Meja belajar rosak, kaki senget', 'uploads/complaints/CBS24001002_1761107247_images.jpeg', 'Under Review', '2025-10-22 04:27:27', '2025-10-22 04:35:20'),
(4, 'CBS24001002', 'Room Change', 'REQUEST: Student CBS24001002 requests room change from A5-05-005 to A5-05-001.', NULL, 'Approved', '2025-10-22 04:33:20', '2025-10-22 04:33:35'),
(6, 'CBS24001002', 'Broken Furniture', 'Meja rosakk', 'uploads/6_1761109048.gif', 'In Progress', '2025-10-22 04:57:28', '2025-10-22 05:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `student_id` varchar(50) NOT NULL,
  `full_name` varchar(200) NOT NULL,
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

INSERT INTO `users` (`student_id`, `full_name`, `email`, `phone`, `password`, `role`, `gender`, `created_at`, `updated_at`) VALUES
('ADMIN001', 'System Admin', 'admin@gmi.edu.my', NULL, 'admin123', 'admin', NULL, '2025-10-08 05:25:46', '2025-10-08 05:25:46'),
('CBS24001001', 'Ali Bin Abu', 'ali.abu@student.gmi.edu.my', '0177159590', 'Password123', 'student', 'male', '2025-10-10 03:35:17', '2025-10-10 03:35:17'),
('CBS24001002', 'MUHAMMAD ABU BIN ALSAGOS', 'abu.alsagos@student.gmi.edu.my', '0144153120', 'Password123', 'student', 'male', '2025-10-21 04:23:38', '2025-10-21 04:23:38'),
('CBS24001010', 'Aizat Bin Khamis', 'aizat.khamis@student.gmi.edu.my', '0165437590', 'Password123', 'student', 'male', '2025-10-22 04:38:11', '2025-10-22 04:38:11');

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
  ADD PRIMARY KEY (`room_identifier`),
  ADD KEY `block_id` (`block_id`);

--
-- Indexes for table `student_rooms`
--
ALTER TABLE `student_rooms`
  ADD PRIMARY KEY (`sr_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `room_identifier` (`room_identifier`);

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
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student_rooms`
--
ALTER TABLE `student_rooms`
  MODIFY `sr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  ADD CONSTRAINT `student_rooms_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_rooms_ibfk_2` FOREIGN KEY (`room_identifier`) REFERENCES `rooms` (`room_identifier`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
