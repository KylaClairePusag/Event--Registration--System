-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 20, 2023 at 07:51 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ba3101`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbstudinfo`
--

CREATE TABLE `tbstudinfo` (
  `studid` int(11) NOT NULL,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `course` varchar(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `student_email` varchar(255) NOT NULL,
  `student_profile` longblob DEFAULT NULL,
  `student_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `admin_ID` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_password` varchar(20) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_profile` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tb_attendees`
--

CREATE TABLE `tb_attendees` (
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `rso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_department`
--

CREATE TABLE `tb_department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tb_event`
--

CREATE TABLE `tb_event` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_detail` text NOT NULL,
  `event_date` date NOT NULL,
  `header_image` longblob DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_event_images`
--

CREATE TABLE `tb_event_images` (
  `image_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `image_filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_faculty`
--

CREATE TABLE `tb_faculty` (
  `faculty_id` int(11) NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `faculty_password` varchar(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `faculty_email` varchar(255) NOT NULL,
  `faculty_profile` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_rso`
--

CREATE TABLE `tb_rso` (
  `rso_id` int(11) NOT NULL,
  `rso_name` varchar(255) NOT NULL,
  `rso_password` varchar(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `rso_email` varchar(255) NOT NULL,
  `rso_profile` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbstudinfo`
--
ALTER TABLE `tbstudinfo`
  ADD PRIMARY KEY (`studid`),
  ADD KEY `fk_department` (`department_id`);

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`admin_ID`),
  ADD UNIQUE KEY `unique_admin_email` (`admin_email`);

--
-- Indexes for table `tb_attendees`
--
ALTER TABLE `tb_attendees`
  ADD PRIMARY KEY (`attendee_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `rso_id` (`rso_id`);

--
-- Indexes for table `tb_department`
--
ALTER TABLE `tb_department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `tb_event`
--
ALTER TABLE `tb_event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `tb_event_images`
--
ALTER TABLE `tb_event_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `tb_faculty`
--
ALTER TABLE `tb_faculty`
  ADD PRIMARY KEY (`faculty_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `tb_rso`
--
ALTER TABLE `tb_rso`
  ADD PRIMARY KEY (`rso_id`),
  ADD UNIQUE KEY `unique_rso_email` (`rso_email`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbstudinfo`
--
ALTER TABLE `tbstudinfo`
  MODIFY `studid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `tb_attendees`
--
ALTER TABLE `tb_attendees`
  MODIFY `attendee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_department`
--
ALTER TABLE `tb_department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2206;

--
-- AUTO_INCREMENT for table `tb_event`
--
ALTER TABLE `tb_event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3138;

--
-- AUTO_INCREMENT for table `tb_event_images`
--
ALTER TABLE `tb_event_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_faculty`
--
ALTER TABLE `tb_faculty`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_rso`
--
ALTER TABLE `tb_rso`
  MODIFY `rso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1435;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbstudinfo`
--
ALTER TABLE `tbstudinfo`
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`);

--
-- Constraints for table `tb_attendees`
--
ALTER TABLE `tb_attendees`
  ADD CONSTRAINT `tb_attendees_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `tb_event` (`event_id`),
  ADD CONSTRAINT `tb_attendees_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `tbstudinfo` (`studid`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_attendees_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `tb_faculty` (`faculty_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_attendees_ibfk_4` FOREIGN KEY (`rso_id`) REFERENCES `tb_rso` (`rso_id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_event`
--
ALTER TABLE `tb_event`
  ADD CONSTRAINT `tb_event_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`);

--
-- Constraints for table `tb_event_images`
--
ALTER TABLE `tb_event_images`
  ADD CONSTRAINT `tb_event_images_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `tb_event` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_faculty`
--
ALTER TABLE `tb_faculty`
  ADD CONSTRAINT `tb_faculty_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`);

--
-- Constraints for table `tb_rso`
--
ALTER TABLE `tb_rso`
  ADD CONSTRAINT `tb_rso_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_event_status` ON SCHEDULE EVERY 1 SECOND STARTS '2023-11-20 12:35:58' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE `tb_event` SET `status` = 'upcoming' WHERE `event_date` > CURDATE();
    UPDATE `tb_event` SET `status` = 'ongoing' WHERE `event_date` = CURDATE();
    UPDATE `tb_event` SET `status` = 'ended' WHERE `event_date` < CURDATE();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
