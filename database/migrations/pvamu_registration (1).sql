-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 05:53 PM
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
-- Database: `pvamu_registration`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `calculate_priority_score` (`p_student_id` INT, `p_section_id` INT, `p_timestamp` DATETIME) RETURNS INT(11) DETERMINISTIC READS SQL DATA BEGIN
		    DECLARE score INT DEFAULT 0;
    DECLARE v_major_id INT;
    DECLARE v_grad_month VARCHAR(15);
    DECLARE v_grad_year INT;
    DECLARE v_course_id INT;
    DECLARE grad_date DATE;
    DECLARE months_to_graduation INT;
    DECLARE wait_days INT DEFAULT 0;

	SELECT major_id, graduation_month, graduation_year
INTO v_major_id, v_grad_month, v_grad_year
FROM student
WHERE student_id = p_student_id;

SELECT course_id
INTO v_course_id
FROM section
WHERE section_id = p_section_id;

SET grad_date = STR_TO_DATE(
		CONCAT('01', v_grad_month, ' ', v_grad_year),
		'%d %M %Y'
);

SET months_to_graduation = TIMESTAMPDIFF(
		MONTH,
		CURDATE(),
		grad_date
	);

 IF months_to_graduation BETWEEN 0 AND 6 THEN
        SET score = score + 50;
    ELSEIF months_to_graduation BETWEEN 7 AND 12 THEN
        SET score = score + 40;
    ELSEIF months_to_graduation BETWEEN 13 AND 18 THEN
        SET score = score + 25;
    END IF;

  
    IF EXISTS (
        SELECT 1
        FROM degree_plan
        WHERE major_id = v_major_id
          AND course_id = v_course_id
    ) THEN
        SET score = score + 30;
    END IF;

    
    SET wait_days = TIMESTAMPDIFF(DAY, p_timestamp, NOW());
    SET score = score + LEAST(wait_days * 2, 20);

    RETURN score;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `credit_hours` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `degree_plan`
--

CREATE TABLE `degree_plan` (
  `degree_plan_id` int(11) NOT NULL,
  `major_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `is_required` tinyint(1) NOT NULL COMMENT '// is the course required depending upon the degree plan',
  `recommended_semester` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL COMMENT 'Enrolled, Dropped, Completed',
  `grade` varchar(2) NOT NULL,
  `date_enrolled` datetime NOT NULL,
  `credits_earned` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `major`
--

CREATE TABLE `major` (
  `major_id` int(11) NOT NULL,
  `major_name` varchar(100) NOT NULL,
  `total_credits_required` int(11) NOT NULL,
  `degree_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `major`
--

INSERT INTO `major` (`major_id`, `major_name`, `total_credits_required`, `degree_type`) VALUES
(1, 'Computer Science', 120, 'BS'),
(2, 'Mathematics', 120, 'BS'),
(3, 'Biology', 120, 'BS');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `year` tinyint(4) NOT NULL,
  `capacity` tinyint(4) NOT NULL,
  `enrollment_count` int(11) DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `email` text DEFAULT NULL,
  `classification` varchar(10) NOT NULL,
  `major_id` int(11) DEFAULT NULL,
  `total_credits_completed` tinyint(4) DEFAULT 0,
  `graduation_month` varchar(15) DEFAULT NULL,
  `graduation_year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `first_name`, `last_name`, `email`, `classification`, `major_id`, `total_credits_completed`, `graduation_month`, `graduation_year`) VALUES
(101, 'India', 'Hoover', 'india.hoover@email.com', 'Senior', 1, 105, 'May', 2026),
(102, 'Sarah', 'Obeng', 'sarah.obeng@email.com', 'Junior', 1, 75, 'May', 2027),
(103, 'Raylen', 'Williams', 'raylen.w@email.com', 'Senior', 2, 110, 'December', 2026),
(104, 'James', 'Carter', 'james.c@email.com', 'Sophomore', 3, 45, 'May', 2028),
(105, 'Kaitlyn', 'Smith', 'kaitlyn.s@email.com', 'Senior', 1, 98, 'May', 2026),
(106, 'Denise', 'Williams', 'denise.w@email.com', 'Freshman', 2, 15, 'May', 2029);

-- --------------------------------------------------------

--
-- Table structure for table `waitlist`
--

CREATE TABLE `waitlist` (
  `waitlist_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `priority_score` int(11) NOT NULL,
  `timestamp_joined` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Tiebreaker',
  `notification_sent` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 --> not notified\r\n1 --> notified',
  `expiration_time` datetime NOT NULL COMMENT 'Automates next selection',
  `notified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Triggers `waitlist`
--
DELIMITER $$
CREATE TRIGGER `trg_before_insert_waitlist` BEFORE INSERT ON `waitlist` FOR EACH ROW BEGIN
    
    IF NEW.timestamp_joined IS NULL THEN
        SET NEW.timestamp_joined = CURRENT_TIMESTAMP;
    END IF;

    
    SET NEW.priority_score =
        calculate_priority_score(
            NEW.student_id,
            NEW.section_id,
            NEW.timestamp_joined
        );
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `degree_plan`
--
ALTER TABLE `degree_plan`
  ADD PRIMARY KEY (`degree_plan_id`),
  ADD KEY `fk_course_id` (`course_id`),
  ADD KEY `fk_major_id` (`major_id`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `fk_student_id_enrollemtn` (`student_id`),
  ADD KEY `fk_section_id_enrollment` (`section_id`);

--
-- Indexes for table `major`
--
ALTER TABLE `major`
  ADD PRIMARY KEY (`major_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`) USING HASH,
  ADD KEY `fk_student_major` (`major_id`);

--
-- Indexes for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD PRIMARY KEY (`waitlist_id`),
  ADD KEY `fk_student_id` (`student_id`),
  ADD KEY `fk_section_id` (`section_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `degree_plan`
--
ALTER TABLE `degree_plan`
  MODIFY `degree_plan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `major`
--
ALTER TABLE `major`
  MODIFY `major_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `waitlist`
--
ALTER TABLE `waitlist`
  MODIFY `waitlist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `degree_plan`
--
ALTER TABLE `degree_plan`
  ADD CONSTRAINT `fk_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  ADD CONSTRAINT `fk_major_id` FOREIGN KEY (`major_id`) REFERENCES `major` (`major_id`);

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `fk_section_id_enrollment` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`),
  ADD CONSTRAINT `fk_student_id_enrollemtn` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `fk_section_course` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_student_major` FOREIGN KEY (`major_id`) REFERENCES `major` (`major_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD CONSTRAINT `fk_section_id` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`),
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
