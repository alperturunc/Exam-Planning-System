DROP DATABASE IF EXISTS examplanning_system;
CREATE DATABASE examplanning_system;
USE examplanning_system;


CREATE TABLE IF NOT EXISTS `faculty` (
  `faculty_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_name` varchar(50) NOT NULL,
  PRIMARY KEY(`faculty_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(50) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  PRIMARY KEY(`department_id`),
  FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_username` varchar(50) NOT NULL,
  `employee_password` varchar(50) NOT NULL,
  `employee_name` varchar(50) NOT NULL,
  `employee_score` varchar(50),
  `employee_type` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL,
  PRIMARY KEY(`employee_id`),
  FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(50) NOT NULL,
  `course_semester` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL,
  PRIMARY KEY(`course_id`),
  FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `registration` (
  `registration_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY(`registration_id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `lectures` (
  `lecture_id` int(11) NOT NULL AUTO_INCREMENT,
  `lecture_start_hour` varchar(50) NOT NULL,
  `lecture_end_hour` varchar(50) NOT NULL,
  `lecture_day` varchar(50) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY(`lecture_id`),
  FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `exam` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_semester`varchar(50) NOT NULL,
  `exam_date` Date NOT NULL,
  `exam_start_hour` varchar(50) NOT NULL,
  `exam_end_hour` varchar(50) NOT NULL,
  `exam_day` varchar(50) NOT NULL,
  `exam_asistant_num` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY(`exam_id`),
  FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `observer` (
  `observer_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  PRIMARY KEY(`observer_id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  FOREIGN KEY (`exam_id`) REFERENCES `exam` (`exam_id`)
) ENGINE=InnoDB;

INSERT INTO `faculty` (`faculty_id`, `faculty_name`) VALUES
(1,'Engineering Faculty'),
(2,'Commercial Sciences Faculty');

INSERT INTO `department` (`department_id`, `department_name`, `faculty_id`) VALUES
(1,'Computer Engineering', 1),
(2,'Mechanical Engineering', 1),
(3,'Information Technologies', 2),
(4,'Administration Economics', 2),
(5,"Engineering Faculty",1),
(6,"Commercial Sciences Faculty",2);

INSERT INTO `course` (`course_id`, `course_code`,`course_semester`,`department_id`) VALUES
(1,'CSE348','Spring', 1),
(2,'ACM101','Spring', 3),
(3,'ACM202','Spring', 3),
(4,'ACM303','Spring',3),
(5,'AE101','Spring', 4),
(6,'CSE331','Spring', 1),
(7,'CSE344','Spring',1),
(8,'CSE354','Spring', 1),
(9,'EC222','Spring', 6),
(10,'ES272','Spring', 5),
(11,'ME101','Spring',2),
(12,'ME202','Spring', 2);

INSERT INTO `lectures` (`lecture_id`, `lecture_day`,`lecture_start_hour`, `lecture_end_hour`, `course_id`) VALUES
(1,'Monday','10:00','12:00', 1),
(2,'Monday','09:00','12:00', 2),
(3,'Wednesday','11:00','12:00', 3),
(4,'Tuesday','09:00','10:00', 4),
(5,'Wednesday','09:00','10:00', 5),
(6,'Thursday','09:00','11:00', 6),
(7,'Tuesday','11:00','12:00', 7),
(8,'Friday','13:00','15:00', 8),
(9,'Monday','09:00','10:00', 9),
(10,'Friday','15:00','17:00', 10),
(11,'Monday','09:00','10:00', 11),
(12,'Monday','12:00','14:00', 12);

INSERT INTO `employee` (`employee_id`, `employee_name`,`employee_type`,`employee_username`,`employee_password`,`employee_score`,`department_id`) VALUES

(1,'Gülşah Gökhan Gökçek','Assistant','ggokcek', '12', 0,1),
(2,'Mehmet Ali Aydın','Assistant','mali', '12', 0,1),
(3,'Burcu Selçuk','Assistant','bselcuk', '12', 0,1),
(4,'Kerem Perente','Assistant','kperente','12',0,1),
(5,'Mehmet Onal','Secretaries','mehmet','12',NULL, 1),
(6,'Alper Turunç','Head of Department','alperturunc','12',NULL, 1),

(7,'Ecem Keskin','Assistant','ecem','12', 0,2),
(8,'Doruk Güngör','Assistant','doruk','12',0,2),
(9,'Özgür Bilmem','Secretaries','ozgur','12',NULL, 2),
(10,'Bulut Ekinci','Head of Department','bulut','12',NULL, 2),

(11,'Eren Utku Karataş','Head of Secretary','eren','12',NULL, 5),

(12,'Ceren Sivri','Secretaries','ceren','12',NULL, 3),
(13,'Zeynep Er','Assistant','zeysuer','12',0, 3),
(14,'Nilay Kalay','Assistant','nilay','12',0, 3),
(15,'Ali Emir Altın','Head of Department','ali','12',NULL, 3),
(16,'Özge Balcı','Head of Secretary','ozge','12', NULL,6),


(17,'Metin Toprak','Dean','metin','12',NULL,5),
(18,'Filiz Aktürk','Dean','filiz','12',NULL,6);




