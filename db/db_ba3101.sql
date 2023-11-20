-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2023 at 11:29 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
  `student_profile` longblob,
  `student_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbstudinfo`
--

INSERT INTO `tbstudinfo` (`studid`, `lastname`, `firstname`, `course`, `department_id`, `student_email`, `student_profile`, `student_password`) VALUES
(-39889, 'GALVERO', 'MATTHEW DANIELL', 'BSIT', 3101, '21-39910@g.batstate-u.edu.ph', NULL, '123'),
(-39807, 'MANALO', 'ZEUS ANDREI', 'BSIT', 3101, '21-39828@g.batstate-u.edu.ph', NULL, '123'),
(-39797, 'FRUELDA', 'MARK JOSEPH', 'BSIT', 3101, '21-39818@g.batstate-u.edu.ph', NULL, '123'),
(-39733, 'DIMAANO', 'JOSHUA', 'BSIT', 3101, '21-39754@g.batstate-u.edu.ph', NULL, '123'),
(-39393, 'RUBION', 'IVAN GABRIEL', 'BSIT', 3101, '21-39414@g.batstate-u.edu.ph', NULL, '123'),
(-39320, 'OLAN', 'JAKE DARREN', 'BSIT', 3101, '21-39341@g.batstate-u.edu.ph', NULL, '123'),
(-39084, 'HERNANDEZ', 'AARON CHRISTIAN', 'BSIT', 3101, '21-39105@g.batstate-u.edu.ph', NULL, '123'),
(-38481, 'CARILLO', 'JAMES KEOCH', 'BSIT', 3101, '21-38502@g.batstate-u.edu.ph', NULL, '123'),
(-38270, 'PURIO', 'JOHN ANDREI', 'BSIT', 3101, '21-38291@g.batstate-u.edu.ph', NULL, '123'),
(-37455, 'TELESFORO', 'LEANNE FRANK', 'BSIT', 3101, '21-37476@g.batstate-u.edu.ph', NULL, '123'),
(-37431, 'ACDA', 'CHRISTIAN RAFAEL', 'BSIT', 3101, '21-37452@g.batstate-u.edu.ph', NULL, '123'),
(-36658, 'CASTILLO', 'DIANNE KRISTEL', 'BSIT', 3101, '21-36679@g.batstate-u.edu.ph', NULL, '123'),
(-35567, 'CUYA', 'LESTER RHOY', 'BSIT', 3101, '21-35588@g.batstate-u.edu.ph', NULL, '123'),
(-35498, 'DE CHAVEZ', 'JHUNCEN', 'BSIT', 3101, '21-35519@g.batstate-u.edu.ph', NULL, '123'),
(-34973, 'CEREZO', 'EZEKIEL EISEN', 'BSIT', 3101, '21-34994@g.batstate-u.edu.ph', NULL, '123'),
(-34154, 'FABELLON', 'JAN PATRICK', 'BSIT', 3101, '21-34175@g.batstate-u.edu.ph', NULL, '123'),
(-33759, 'BACONG', 'JOHN OLIVER', 'BSIT', 3101, '21-33780@g.batstate-u.edu.ph', NULL, '123'),
(-33457, 'PEREZ', 'JAZZMIN', 'BSIT', 3101, '21-33478@g.batstate-u.edu.ph', NULL, '123'),
(-33036, 'ALDAU', 'KEON CHESTER', 'BSIT', 3101, '21-33057@g.batstate-u.edu.ph', NULL, '123'),
(-32963, 'CANIETE', 'CRISTEL MAE ', 'BSIT', 3101, '21-32984@g.batstate-u.edu.ph', NULL, '123'),
(-32450, ' MACALLA', 'DOROTHY', 'BSIT', 3101, '21-32471@g.batstate-u.edu.ph', NULL, '123'),
(-31341, 'POBLETE', 'JAMES REMUEL', 'BSIT', 3101, '21-31362@g.batstate-u.edu.ph', NULL, '123'),
(-31033, ' TADA', 'SETH ALDOUS', 'BSIT', 3101, '21-31054@g.batstate-u.edu.ph', NULL, '123'),
(-30843, 'MARASIGAN', 'HARVEY NICOLE', 'BSIT', 3101, '21-30864@g.batstate-u.edu.ph', NULL, '123'),
(-30823, 'RAZ', 'DANNIEL DAVID', 'BSIT', 3101, '21-30844@g.batstate-u.edu.ph', NULL, '123'),
(-30776, 'NOVICIO', 'NICKO LOUISE', 'BSIT', 3101, '21-30797@g.batstate-u.edu.ph', NULL, '123'),
(-30734, 'SOLIS II', 'ROBERTO ANTONIO', 'BSIT', 3101, '21-30755@g.batstate-u.edu.ph', NULL, '123'),
(-30542, 'MACARAIG', 'NIEL KEVIN', 'BSIT', 3101, '21-30563@g.batstate-u.edu.ph', NULL, '123'),
(-30360, 'FABREGAS', 'IRELLE KERVIN', 'BSIT', 3101, '21-30381@g.batstate-u.edu.ph', NULL, '123'),
(-30052, 'TRAYFALGAR', 'BELLE COLLEEN', 'BSIT', 3101, '21-30073@g.batstate-u.edu.ph', NULL, '123'),
(-29987, 'MAGSINO', 'JOHN ADERIEL', 'BSIT', 3101, '21-30008@g.batstate-u.edu.ph', NULL, '123');

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `admin_ID` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_password` varchar(20) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_profile` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`admin_ID`, `admin_name`, `admin_password`, `admin_email`, `admin_profile`) VALUES
(-39284, 'Marvin Kristian M. Cruz', 'marvincute', '21-39305@g.batstate-u.edu.ph', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_attendees`
--

CREATE TABLE `tb_attendees` (
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `rso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_department`
--

CREATE TABLE `tb_department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_event`
--

CREATE TABLE `tb_event` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_detail` text NOT NULL,
  `event_date` date NOT NULL,
  `header_image` longblob,
  `department_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_event_images`
--

CREATE TABLE `tb_event_images` (
  `image_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `image_filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `faculty_profile` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `rso_profile` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbstudinfo`
--
ALTER TABLE `tbstudinfo`
  ADD PRIMARY KEY (`studid`),
  ADD KEY `fk_department` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
