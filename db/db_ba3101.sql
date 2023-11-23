SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `tb_department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `tbstudinfo`;
CREATE TABLE IF NOT EXISTS `tbstudinfo` (
  `studid` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `course` varchar(20) NOT NULL,
  PRIMARY KEY (`studid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `tb_admin` (
  `admin_ID` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(255) NOT NULL,
  `admin_password` varchar(20) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_profile` longblob DEFAULT NULL,
  PRIMARY KEY (`admin_ID`),
  UNIQUE KEY `unique_admin_email` (`admin_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tb_event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_title` varchar(255) NOT NULL,
  `event_detail` text NOT NULL,
  `event_date` date NOT NULL,
  `header_image` longblob DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `tb_event_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tb_event_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `tb_event_images_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `tb_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `tbempinfo`;
CREATE TABLE IF NOT EXISTS `tbempinfo` (
  `empid` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `department` varchar(20) NOT NULL,
  PRIMARY KEY (`empid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tbempaccount` (
  `empid` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `emp_email` varchar(255) NOT NULL,
  `emp_profile` longblob DEFAULT NULL,
  `emp_password` varchar(255) NOT NULL,
  PRIMARY KEY (`empid`),
  CONSTRAINT `fk_empinfo_changes` FOREIGN KEY (`empid`) REFERENCES `tbempinfo` (`empid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_department_emp` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tb_rso` (
  `rso_id` int(11) NOT NULL AUTO_INCREMENT,
  `rso_name` varchar(255) NOT NULL,
  `rso_password` varchar(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `rso_email` varchar(255) NOT NULL,
  `rso_profile` longblob DEFAULT NULL,
  PRIMARY KEY (`rso_id`),
  UNIQUE KEY `unique_rso_email` (`rso_email`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `tb_rso_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tb_attendees` (
  `attendee_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `empid` int(11) DEFAULT NULL,
  `rso_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`attendee_id`),
  KEY `event_id` (`event_id`),
  KEY `student_id` (`student_id`),
  KEY `empid` (`empid`),
  KEY `rso_id` (`rso_id`),
  CONSTRAINT `tb_attendees_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `tb_event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tb_attendees_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `tbstudinfo` (`studid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tb_attendees_ibfk_3` FOREIGN KEY (`empid`) REFERENCES `tbempinfo` (`empid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tb_attendees_ibfk_4` FOREIGN KEY (`rso_id`) REFERENCES `tb_rso` (`rso_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbstudentaccount` (
  `studid` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `student_email` varchar(255) NOT NULL,
  `student_profile` longblob DEFAULT NULL,
  `student_password` varchar(255) NOT NULL,
  PRIMARY KEY (`studid`),
  CONSTRAINT `fk_studinfo_changes` FOREIGN KEY (`studid`) REFERENCES `tbstudinfo` (`studid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_department_student` FOREIGN KEY (`department_id`) REFERENCES `tb_department` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$

CREATE DEFINER=`root`@`localhost` EVENT `update_event_status` ON SCHEDULE EVERY 1 SECOND STARTS CURRENT_TIMESTAMP ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE `tb_event` SET `status` = 'upcoming' WHERE `event_date` > CURRENT_TIMESTAMP;
    UPDATE `tb_event` SET `status` = 'ongoing' WHERE DATE(`event_date`) = CURDATE();
    UPDATE `tb_event` SET `status` = 'ended' WHERE `event_date` < CURRENT_TIMESTAMP;
END$$

DELIMITER ;
COMMIT;
DELIMITER $$

CREATE TRIGGER `after_insert_studinfo`
AFTER INSERT ON `tbstudinfo`
FOR EACH ROW
BEGIN
    DECLARE `stud_id_suffix` VARCHAR(255);
    DECLARE `stud_email` VARCHAR(255);
    
    -- Extract the last four digits of the studid
    SET `stud_id_suffix` = RIGHT(NEW.`studid`, LENGTH(NEW.`studid`) - 2);
    
    -- Generate the student email
    SET `stud_email` = CONCAT(LEFT(NEW.`studid`, 2), '-', `stud_id_suffix`, '@g.batstate-u.edu.ph');
    
    -- Insert into tbstudentaccount
    INSERT INTO `tbstudentaccount` (`studid`, `department_id`, `student_email`, `student_profile`, `student_password`)
    VALUES (NEW.`studid`, NULL, `stud_email`, NULL, NEW.`studid`);
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER `after_insert_empinfo`
AFTER INSERT ON `tbempinfo`
FOR EACH ROW
BEGIN
    DECLARE `emp_id_suffix` VARCHAR(255);
    DECLARE `emp_email` VARCHAR(255);
    
    SET `emp_id_suffix` = RIGHT(NEW.`empid`, LENGTH(NEW.`empid`) - 2);
    
    SET `emp_email` = CONCAT(LEFT(NEW.`empid`, 2), '-', `emp_id_suffix`, '@g.batstate-u.edu.ph');
    
    INSERT INTO `tbempaccount` (`empid`, `department_id`, `emp_email`, `emp_profile`, `emp_password`)
    VALUES (NEW.`empid`, NULL, `emp_email`, NULL, NEW.`empid`);
END$$

DELIMITER ;
COMMIT;
DELIMITER $$

