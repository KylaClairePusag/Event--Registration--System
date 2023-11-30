-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 30, 2023 at 11:28 AM
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
-- Table structure for table `tbempaccount`
--

CREATE TABLE `tbempaccount` (
  `empaccountId` int(11) NOT NULL,
  `empid` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `emp_email` varchar(255) NOT NULL,
  `emp_profile` longblob DEFAULT NULL,
  `emp_password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbempinfo`
--

DROP TABLE IF EXISTS `tbempinfo`;
CREATE TABLE IF NOT EXISTS `tbempinfo` (
  `empid` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `department` varchar(20) NOT NULL,
  PRIMARY KEY (`empid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbempinfo`
--

INSERT INTO `tbempinfo` (`empid`, `lastname`, `firstname`, `department`) VALUES
(1, 'aguila', 'nina', 'cics');

-- --------------------------------------------------------

--
-- Table structure for table `tbstudentaccount`
--

CREATE TABLE `tbstudentaccount` (
  `studaccountid` int(11) NOT NULL,
  `studid` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `student_email` varchar(255) NOT NULL,
  `student_profile` longblob DEFAULT NULL,
  `student_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbstudinfo`
--

DROP TABLE IF EXISTS `tbstudinfo`;
CREATE TABLE IF NOT EXISTS `tbstudinfo` (
  `studid` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `course` varchar(20) NOT NULL,
  PRIMARY KEY (`studid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbstudinfo`
--

INSERT INTO `tbstudinfo` (`studid`, `lastname`, `firstname`, `course`) VALUES
(1, 'parker', 'peter', 'bsit'),
(2, 'kent', 'clark', 'bscs');

-- --------------------------------------------------------

--
-- Table structure for table `tb_attendees`
--

CREATE TABLE `tb_attendees` (
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `empid` int(11) DEFAULT NULL
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
-- Dumping data for table `tb_department`
--

INSERT INTO `tb_department` (`department_id`, `department_name`) VALUES
(1, 'CICS'),
(2, 'CABE'),
(3, 'CAS');

-- --------------------------------------------------------

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
-- Table structure for table `tb_roles`
--

CREATE TABLE `tb_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_roles`
--

INSERT INTO `tb_roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Teacher');

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbempaccount`
--
ALTER TABLE `tbempaccount`
  ADD PRIMARY KEY (`empaccountId`),
  ADD KEY `fk_empinfo_changes` (`empid`),
  ADD KEY `fk_department_emp` (`department_id`),
  ADD KEY `fk_emp_roles` (`role_id`);

--
-- Indexes for table `tbstudentaccount`
--
ALTER TABLE `tbstudentaccount`
  ADD PRIMARY KEY (`studaccountid`),
  ADD KEY `fk_department_student` (`department_id`),
  ADD KEY `fk_studinfo_changes` (`studid`);

--
-- Indexes for table `tb_attendees`
--
ALTER TABLE `tb_attendees`
  ADD PRIMARY KEY (`attendee_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `empid` (`empid`);

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
-- Indexes for table `tb_roles`
--
ALTER TABLE `tb_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `unique_role_name` (`role_name`);

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
-- AUTO_INCREMENT for table `tbempaccount`
--
ALTER TABLE `tbempaccount`
  MODIFY `empaccountId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbstudentaccount`
--
ALTER TABLE `tbstudentaccount`
  MODIFY `studaccountid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_attendees`
--
ALTER TABLE `tb_attendees`
  MODIFY `attendee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_department`
--
ALTER TABLE `tb_department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_event`
--
ALTER TABLE `tb_event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_event_images`
--
ALTER TABLE `tb_event_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_roles`
--
ALTER TABLE `tb_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_rso`
--
ALTER TABLE `tb_rso`
  MODIFY `rso_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbempaccount`
--
ALTER TABLE `tbempaccount`
  ADD CONSTRAINT `fk_department_emp` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_emp_roles` FOREIGN KEY (`role_id`) REFERENCES `tb_roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_empinfo_changes` FOREIGN KEY (`empid`) REFERENCES `tbempinfo` (`empid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbstudentaccount`
--
ALTER TABLE `tbstudentaccount`
  ADD CONSTRAINT `fk_department_student` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_studinfo_changes` FOREIGN KEY (`studid`) REFERENCES `tbstudinfo` (`studid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_attendees`
--
ALTER TABLE `tb_attendees`
  ADD CONSTRAINT `fk_employee_attendee` FOREIGN KEY (`empid`) REFERENCES `tbempinfo` (`empid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_attendee` FOREIGN KEY (`event_id`) REFERENCES `tb_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_attendee` FOREIGN KEY (`student_id`) REFERENCES `tbstudinfo` (`studid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_event`
--
ALTER TABLE `tb_event`
  ADD CONSTRAINT `fk_event_department` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_event_images`
--
ALTER TABLE `tb_event_images`
  ADD CONSTRAINT `fk_image_event` FOREIGN KEY (`event_id`) REFERENCES `tb_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_rso`
--
ALTER TABLE `tb_rso`
  ADD CONSTRAINT `fk_rso_department` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
