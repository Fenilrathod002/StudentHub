-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2026 at 03:31 PM
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
-- Database: `studenthub`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`) VALUES
(1, 'B.Tech IT'),
(2, 'B.Tech CSE'),
(3, 'BCA');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `event_date` date NOT NULL,
  `location` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `event_date`, `location`, `category`) VALUES
(1, 'Tech Fest 2026', '2026-09-20', 'Main Auditorium', 'Technical'),
(2, 'Coding Competition', '2026-09-22', 'Computer Lab 1', 'Technical'),
(3, 'Web Development Workshop', '2026-09-25', 'Seminar Hall', 'Workshop'),
(4, 'Sports Festival', '2026-09-28', 'University Ground', 'Sports'),
(5, 'AI and Machine Learning Seminar', '2026-10-02', 'Seminar Hall', 'Technical');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `reg_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `registered_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`reg_id`, `student_id`, `event_id`, `registered_on`) VALUES
(1, 2, 2, '2026-10-17 13:44:14');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `role` varchar(20) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `mobile`, `role`, `course_id`, `year`, `gender`, `password`) VALUES
(1, 'FENIL THAKARSHI RATHOD', 'rathodfenil2008@gmail.com', '9099511611', 'student', 1, 2, 'Male', '$2y$10$F02ZkGgVTy27/GSJoFvzVu/ixosYkot0twA6G0zFixSPCEt1Xa5rq'),
(2, 'FENIL THAKARSHI RATHOD', 'test@example.com', '9099511611', 'student', 2, 2, 'Male', '$2y$10$ZyupyGaGbg2Q2QBdyVRKluLLgqyP0XpwPBJ5DJKAhkXyH9fu6qXPS'),
(7, 'Elise', 'elise@gmail.com', '7077411711', 'student', 3, 2, 'Male', '$2y$10$lzvDOCwZuyQF6AFj0WjrxuiubKhZZcAybFPFhXDHFNTDPlR1ezo3C'),
(8, 'kevin', 'kevin@gmail.com', '6066311411', 'student', 2, 4, 'Male', '$2y$10$NdEaIDYzSk8i.uHM.fiqTucUe2eWooeV3jG78V3kjaOjZlLvsNI2K');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Priya', 'priya@gmail.com', '$2y$10$FyI6p6cdUaEqENIejWX.IORLYLhRb3BJwr.9Wdn5PEyOVGeCr.8W6', 'student', '2026-10-08 20:32:45'),
(2, 'Hash One', 'hash-one@example.com', '$2y$10$icGiIDsYGrz9mnPMvNzstuBRG90JCVyORWvg3Jec80LfwyehxKrfO', 'student', '2026-10-08 20:32:46'),
(3, 'Hash Two', 'hash-two@example.com', '$2y$10$CLpnbozylKotMiZHwEXA0uf2gPrNHT7EM/VtwwqnGQ9zYMtVDLi.W', 'student', '2026-10-08 20:32:46'),
(4, 'Raw Check', 'raw-check@example.com', '$2y$10$dDmAj9LF28u0WpmhZxhllO5UicnBr5xNwgVoME1BUCkbtKFi67.UO', 'student', '2026-10-08 20:33:06'),
(5, '\' OR \'1\'=\'1', 'injection-check@example.com', '$2y$10$JpzNPfaYIlrZc4MOZbnJ6ukBWIkwayUaVgj.O6ehuUUXYBsp1g5Uu', 'student', '2026-10-08 20:33:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`reg_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `course_id` (`course_id`);

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
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
