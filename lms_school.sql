-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 08:20 PM
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
-- Database: `lms_school`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetNepaliDayOfWeek` (`day_name` VARCHAR(10)) RETURNS VARCHAR(15) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE nepali_day VARCHAR(15);
    CASE day_name
        WHEN 'Sunday' THEN SET nepali_day = 'आइतबार';
        WHEN 'Monday' THEN SET nepali_day = 'सोमबार';
        WHEN 'Tuesday' THEN SET nepali_day = 'मंगलबार';
        WHEN 'Wednesday' THEN SET nepali_day = 'बुधबार';
        WHEN 'Thursday' THEN SET nepali_day = 'बिहिबार';
        WHEN 'Friday' THEN SET nepali_day = 'शुक्रबार';
        WHEN 'Saturday' THEN SET nepali_day = 'शनिबार';
        ELSE SET nepali_day = 'अज्ञात';
    END CASE;
    RETURN nepali_day;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `GetNepaliMonthNameEn` (`month_num` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE month_name VARCHAR(20);
    CASE month_num
        WHEN 1 THEN SET month_name = 'Baisakh';
        WHEN 2 THEN SET month_name = 'Jestha';
        WHEN 3 THEN SET month_name = 'Ashadh';
        WHEN 4 THEN SET month_name = 'Shrawan';
        WHEN 5 THEN SET month_name = 'Bhadra';
        WHEN 6 THEN SET month_name = 'Ashwin';
        WHEN 7 THEN SET month_name = 'Kartik';
        WHEN 8 THEN SET month_name = 'Mangsir';
        WHEN 9 THEN SET month_name = 'Poush';
        WHEN 10 THEN SET month_name = 'Magh';
        WHEN 11 THEN SET month_name = 'Falgun';
        WHEN 12 THEN SET month_name = 'Chaitra';
        ELSE SET month_name = 'Unknown';
    END CASE;
    RETURN month_name;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `GetNepaliMonthNameNp` (`month_num` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE month_name VARCHAR(20);
    CASE month_num
        WHEN 1 THEN SET month_name = 'बैशाख';
        WHEN 2 THEN SET month_name = 'जेठ';
        WHEN 3 THEN SET month_name = 'आषाढ';
        WHEN 4 THEN SET month_name = 'श्रावण';
        WHEN 5 THEN SET month_name = 'भाद्र';
        WHEN 6 THEN SET month_name = 'आश्विन';
        WHEN 7 THEN SET month_name = 'कार्तिक';
        WHEN 8 THEN SET month_name = 'मंसिर';
        WHEN 9 THEN SET month_name = 'पौष';
        WHEN 10 THEN SET month_name = 'माघ';
        WHEN 11 THEN SET month_name = 'फाल्गुन';
        WHEN 12 THEN SET month_name = 'चैत्र';
        ELSE SET month_name = 'अज्ञात';
    END CASE;
    RETURN month_name;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `year_name` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `nepali_start_year` int(11) DEFAULT NULL,
  `nepali_start_month` int(11) DEFAULT NULL,
  `nepali_start_day` int(11) DEFAULT NULL,
  `nepali_end_year` int(11) DEFAULT NULL,
  `nepali_end_month` int(11) DEFAULT NULL,
  `nepali_end_day` int(11) DEFAULT NULL,
  `nepali_year_name` varchar(20) DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year_name`, `start_date`, `end_date`, `nepali_start_year`, `nepali_start_month`, `nepali_start_day`, `nepali_end_year`, `nepali_end_month`, `nepali_end_day`, `nepali_year_name`, `is_current`, `created_at`) VALUES
(1, '2024-2025', '2024-04-01', '2025-03-31', 2081, 1, 1, 2081, 12, 30, '२०८१', 1, '2025-06-02 02:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `assigned_date` date DEFAULT curdate(),
  `nepali_assigned_year` int(11) DEFAULT NULL,
  `nepali_assigned_month` int(11) DEFAULT NULL,
  `nepali_assigned_day` int(11) DEFAULT NULL,
  `due_date` date NOT NULL,
  `nepali_due_year` int(11) DEFAULT NULL,
  `nepali_due_month` int(11) DEFAULT NULL,
  `nepali_due_day` int(11) DEFAULT NULL,
  `max_marks` int(11) DEFAULT 100,
  `assignment_type` enum('homework','project','quiz','exam') DEFAULT 'homework',
  `instructions` text DEFAULT NULL,
  `attachment_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `title`, `description`, `class_id`, `subject_id`, `teacher_id`, `assigned_date`, `nepali_assigned_year`, `nepali_assigned_month`, `nepali_assigned_day`, `due_date`, `nepali_due_year`, `nepali_due_month`, `nepali_due_day`, `max_marks`, `assignment_type`, `instructions`, `attachment_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Math Chapter 1 Exercises', 'Complete exercises 1-10 from Chapter 1: Basic Arithmetic', 1, 1, 2, '2024-01-15', NULL, NULL, NULL, '2024-01-22', NULL, NULL, NULL, 50, 'homework', 'Show all working steps clearly', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(2, 'English Essay Writing', 'Write a 200-word essay on \"My School\"', 1, 2, 3, '2024-01-16', NULL, NULL, NULL, '2024-01-25', NULL, NULL, NULL, 25, 'homework', 'Use proper grammar and punctuation', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(3, 'Science Project', 'Create a simple volcano model', 2, 3, 4, '2024-01-18', NULL, NULL, NULL, '2024-02-01', NULL, NULL, NULL, 100, 'project', 'Include a written report explaining the process', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(4, 'Math Quiz Preparation', 'Study multiplication tables 1-12', 2, 4, 2, '2024-01-20', NULL, NULL, NULL, '2024-01-27', NULL, NULL, NULL, 30, 'quiz', 'Quiz will be conducted in class', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(5, 'Math Algebra Worksheet', 'Solve problems 1-15 from Algebra section', 1, 1, 2, '2024-02-01', NULL, NULL, NULL, '2024-02-08', NULL, NULL, NULL, 60, 'homework', 'Show all steps', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(6, 'English Poetry Analysis', 'Analyze a poem of your choice (200 words)', 1, 2, 3, '2024-02-05', NULL, NULL, NULL, '2024-02-12', NULL, NULL, NULL, 30, 'homework', 'Include poetic devices', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(7, 'Science Experiment Report', 'Write a report on plant growth experiment', 2, 3, 4, '2024-02-10', NULL, NULL, NULL, '2024-02-17', NULL, NULL, NULL, 80, 'project', 'Include diagrams', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(8, 'Math Geometry Quiz Prep', 'Prepare for geometry quiz on triangles', 2, 4, 2, '2024-02-15', NULL, NULL, NULL, '2024-02-22', NULL, NULL, NULL, 40, 'quiz', 'Review chapters 3-4', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(9, 'English Grammar Exercises', 'Complete grammar workbook pages 20-25', 3, 5, 3, '2024-02-20', NULL, NULL, NULL, '2024-02-27', NULL, NULL, NULL, 50, 'homework', 'Submit in class', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(10, 'Math Fractions Assignment', 'Solve fraction problems 1-20', 1, 1, 2, '2024-03-01', NULL, NULL, NULL, '2024-03-08', NULL, NULL, NULL, 50, 'homework', 'Use proper notation', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(11, 'English Short Story', 'Write a 300-word short story', 1, 2, 3, '2024-03-05', NULL, NULL, NULL, '2024-03-12', NULL, NULL, NULL, 35, 'homework', 'Focus on narrative structure', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(12, 'Science Chemistry Basics', 'Create a poster on chemical reactions', 2, 3, 4, '2024-03-10', NULL, NULL, NULL, '2024-03-17', NULL, NULL, NULL, 70, 'project', 'Include examples', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(13, 'Math Trigonometry Test Prep', 'Study trigonometric ratios', 2, 4, 2, '2024-03-15', NULL, NULL, NULL, '2024-03-22', NULL, NULL, NULL, 30, 'quiz', 'Practice problems provided', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(14, 'English Vocabulary Quiz', 'Learn 50 new vocabulary words', 3, 5, 3, '2024-03-20', NULL, NULL, NULL, '2024-03-27', NULL, NULL, NULL, 25, 'quiz', 'Quiz in class', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(15, 'Math Linear Equations', 'Solve linear equations set 1', 1, 1, 2, '2024-04-01', NULL, NULL, NULL, '2024-04-08', NULL, NULL, NULL, 60, 'homework', 'Show all work', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(16, 'English Book Review', 'Write a review of a novel', 1, 2, 3, '2024-04-05', NULL, NULL, NULL, '2024-04-12', NULL, NULL, NULL, 40, 'homework', 'Minimum 250 words', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(17, 'Science Physics Experiment', 'Conduct simple pendulum experiment', 2, 3, 4, '2024-04-10', NULL, NULL, NULL, '2024-04-17', NULL, NULL, NULL, 90, 'project', 'Submit report', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(18, 'Math Statistics Assignment', 'Analyze given data set', 2, 4, 2, '2024-04-15', NULL, NULL, NULL, '2024-04-22', NULL, NULL, NULL, 50, 'homework', 'Use graphs', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(19, 'English Debate Preparation', 'Prepare arguments for class debate', 3, 5, 3, '2024-04-20', NULL, NULL, NULL, '2024-04-27', NULL, NULL, NULL, 30, 'homework', 'Submit outline', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(20, 'Math Probability Problems', 'Solve probability exercises', 1, 1, 2, '2024-05-01', NULL, NULL, NULL, '2024-05-08', NULL, NULL, NULL, 50, 'homework', 'Show calculations', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(21, 'English Speech Writing', 'Write a 2-minute speech', 1, 2, 3, '2024-05-05', NULL, NULL, NULL, '2024-05-12', NULL, NULL, NULL, 25, 'homework', 'Practice delivery', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(22, 'Science Biology Project', 'Create a model of a cell', 2, 3, 4, '2024-05-10', NULL, NULL, NULL, '2024-05-17', NULL, NULL, NULL, 100, 'project', 'Label all parts', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(23, 'Math Calculus Prep', 'Review differentiation basics', 2, 4, 2, '2024-05-15', NULL, NULL, NULL, '2024-05-22', NULL, NULL, NULL, 40, 'quiz', 'Study examples', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(24, 'English Essay on Environment', 'Write essay on climate change', 3, 5, 3, '2024-05-20', NULL, NULL, NULL, '2024-05-27', NULL, NULL, NULL, 35, 'homework', 'Cite sources', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(25, 'Math Final Review', 'Complete review packet', 1, 1, 2, '2024-06-01', NULL, NULL, NULL, '2024-06-08', NULL, NULL, NULL, 60, 'homework', 'Prepare for exam', NULL, 1, '2025-06-13 10:15:57', '2025-06-13 10:15:57'),
(26, 'Federal Civil Service Bill moves on after deal at parliamentary panel', 'do this', 5, 6, 11, '2025-06-19', NULL, NULL, NULL, '2025-06-21', NULL, NULL, NULL, 10, 'homework', '', 'uploads/assignments/6853d707d418c.pdf', 1, '2025-06-19 09:23:19', '2025-06-19 09:23:19');

--
-- Triggers `assignments`
--
DELIMITER $$
CREATE TRIGGER `assignments_nepali_date_insert` BEFORE INSERT ON `assignments` FOR EACH ROW BEGIN
    DECLARE assigned_nepali JSON;
    DECLARE due_nepali JSON;
    
    -- Get Nepali date for assigned date
    SELECT JSON_OBJECT('year', nepali_year, 'month', nepali_month, 'day', nepali_day) 
    INTO assigned_nepali
    FROM nepali_date_mapping 
    WHERE gregorian_date = NEW.assigned_date;
    
    -- Get Nepali date for due date
    SELECT JSON_OBJECT('year', nepali_year, 'month', nepali_month, 'day', nepali_day) 
    INTO due_nepali
    FROM nepali_date_mapping 
    WHERE gregorian_date = NEW.due_date;
    
    -- Update assigned date Nepali fields
    IF assigned_nepali IS NOT NULL THEN
        SET NEW.nepali_assigned_year = JSON_UNQUOTE(JSON_EXTRACT(assigned_nepali, '$.year'));
        SET NEW.nepali_assigned_month = JSON_UNQUOTE(JSON_EXTRACT(assigned_nepali, '$.month'));
        SET NEW.nepali_assigned_day = JSON_UNQUOTE(JSON_EXTRACT(assigned_nepali, '$.day'));
    END IF;
    
    -- Update due date Nepali fields
    IF due_nepali IS NOT NULL THEN
        SET NEW.nepali_due_year = JSON_UNQUOTE(JSON_EXTRACT(due_nepali, '$.year'));
        SET NEW.nepali_due_month = JSON_UNQUOTE(JSON_EXTRACT(due_nepali, '$.month'));
        SET NEW.nepali_due_day = JSON_UNQUOTE(JSON_EXTRACT(due_nepali, '$.day'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `assignments_nepali_view`
-- (See below for the actual view)
--
CREATE TABLE `assignments_nepali_view` (
`id` int(11)
,`title` varchar(200)
,`description` text
,`class_id` int(11)
,`subject_id` int(11)
,`teacher_id` int(11)
,`assigned_date` date
,`nepali_assigned_year` int(11)
,`nepali_assigned_month` int(11)
,`nepali_assigned_day` int(11)
,`due_date` date
,`nepali_due_year` int(11)
,`nepali_due_month` int(11)
,`nepali_due_day` int(11)
,`max_marks` int(11)
,`assignment_type` enum('homework','project','quiz','exam')
,`instructions` text
,`attachment_url` varchar(500)
,`is_active` tinyint(1)
,`created_at` timestamp
,`updated_at` timestamp
,`nepali_assigned_date` varchar(17)
,`nepali_due_date` varchar(17)
,`assigned_month_name` varchar(20)
,`due_month_name` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `assignment_grades`
--

CREATE TABLE `assignment_grades` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `max_grade` decimal(5,2) DEFAULT 100.00,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `submission_text` text DEFAULT NULL,
  `attachment_url` varchar(500) DEFAULT NULL,
  `status` enum('submitted','late','graded','returned') DEFAULT 'submitted',
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `assignment_id`, `student_id`, `submission_date`, `submission_text`, `attachment_url`, `status`, `grade`, `feedback`, `graded_by`, `graded_at`, `is_completed`) VALUES
(1, 1, 1, '2024-01-21 08:45:00', 'Completed all exercises with working shown', NULL, 'graded', 45.00, 'Good work! Minor calculation error in question 7', NULL, NULL, 0),
(2, 2, 1, '2024-01-24 11:00:00', 'My school is a wonderful place where I learn many things...', NULL, 'graded', 22.00, 'Well written essay with good vocabulary', NULL, NULL, 0),
(3, 1, 2, '2024-01-22 04:30:00', 'All exercises completed', NULL, 'graded', 38.00, 'Need to show more working steps', NULL, NULL, 0),
(4, 3, 3, '2024-01-30 05:35:00', 'Volcano model completed with baking soda and vinegar reaction', NULL, 'submitted', NULL, NULL, NULL, NULL, 0),
(5, 1, 6, '2025-06-02 15:09:30', 'test', 'uploads/assignments/6_1_1748876970.jpeg', 'submitted', NULL, NULL, NULL, NULL, 0),
(6, 2, 6, '2025-06-19 17:13:41', 'test', 'uploads/assignments/6_2_1750353221.png', 'submitted', NULL, NULL, NULL, NULL, 0),
(7, 5, 1, '2025-06-20 17:22:15', 'testing', 'uploads/assignments/1_5_1750440135.jpeg', 'submitted', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `nepali_year` int(11) DEFAULT NULL,
  `nepali_month` int(11) DEFAULT NULL,
  `nepali_day` int(11) DEFAULT NULL,
  `nepali_date_string` varchar(15) DEFAULT NULL,
  `status` enum('present','absent','late','half_day') NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `class_id`, `teacher_id`, `attendance_date`, `nepali_year`, `nepali_month`, `nepali_day`, `nepali_date_string`, `status`, `check_in_time`, `check_out_time`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, '2024-01-15', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(2, 1, 1, 2, '2024-01-16', NULL, NULL, NULL, NULL, 'present', '08:25:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(3, 1, 1, 2, '2024-01-17', NULL, NULL, NULL, NULL, 'late', '09:15:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(4, 1, 1, 2, '2024-01-18', NULL, NULL, NULL, NULL, 'present', '08:20:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(5, 1, 1, 2, '2024-01-19', NULL, NULL, NULL, NULL, 'absent', NULL, NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(6, 1, 1, 2, '2024-01-22', NULL, NULL, NULL, NULL, 'present', '08:35:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(7, 1, 1, 2, '2024-01-23', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(8, 1, 1, 2, '2024-01-24', NULL, NULL, NULL, NULL, 'present', '08:28:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(9, 1, 1, 2, '2024-01-25', NULL, NULL, NULL, NULL, 'late', '09:05:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(10, 1, 1, 2, '2024-01-26', NULL, NULL, NULL, NULL, 'present', '08:32:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(11, 2, 1, 2, '2024-01-15', NULL, NULL, NULL, NULL, 'present', '08:32:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(12, 2, 1, 2, '2024-01-16', NULL, NULL, NULL, NULL, 'absent', NULL, NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(13, 2, 1, 2, '2024-01-17', NULL, NULL, NULL, NULL, 'present', '08:28:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(14, 3, 2, 4, '2024-01-15', NULL, NULL, NULL, NULL, 'present', '08:35:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(15, 3, 2, 4, '2024-01-16', NULL, NULL, NULL, NULL, 'late', '09:10:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(16, 4, 2, 4, '2024-01-15', NULL, NULL, NULL, NULL, 'present', '08:25:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(17, 5, 3, 3, '2024-01-15', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(18, 1, 1, 2, '2024-02-01', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(19, 2, 1, 2, '2024-02-01', NULL, NULL, NULL, NULL, 'present', '08:28:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(20, 3, 2, 4, '2024-02-01', NULL, NULL, NULL, NULL, 'late', '09:10:00', NULL, 'Arrived late', '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(21, 4, 2, 4, '2024-02-01', NULL, NULL, NULL, NULL, 'present', '08:25:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(22, 5, 3, 3, '2024-02-01', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(23, 6, 1, 2, '2024-02-01', NULL, NULL, NULL, NULL, 'absent', NULL, NULL, 'Sick leave', '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(24, 1, 1, 2, '2024-02-02', NULL, NULL, NULL, NULL, 'present', '08:32:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(25, 2, 1, 2, '2024-02-02', NULL, NULL, NULL, NULL, 'late', '09:05:00', NULL, 'Traffic delay', '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(26, 3, 2, 4, '2024-02-02', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(27, 4, 2, 4, '2024-02-02', NULL, NULL, NULL, NULL, 'present', '08:27:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(28, 5, 3, 3, '2024-02-02', NULL, NULL, NULL, NULL, 'present', '08:29:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(29, 6, 1, 2, '2024-02-02', NULL, NULL, NULL, NULL, 'present', '08:31:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(30, 1, 1, 2, '2024-02-03', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(31, 2, 1, 2, '2024-02-03', NULL, NULL, NULL, NULL, 'present', '08:28:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(32, 3, 2, 4, '2024-02-03', NULL, NULL, NULL, NULL, 'absent', NULL, NULL, 'Family event', '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(33, 4, 2, 4, '2024-02-03', NULL, NULL, NULL, NULL, 'present', '08:25:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(34, 5, 3, 3, '2024-02-03', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(35, 6, 1, 2, '2024-02-03', NULL, NULL, NULL, NULL, 'present', '08:32:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(36, 1, 1, 2, '2024-02-04', NULL, NULL, NULL, NULL, 'late', '09:15:00', NULL, 'Overslept', '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(37, 2, 1, 2, '2024-02-04', NULL, NULL, NULL, NULL, 'present', '08:29:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(38, 3, 2, 4, '2024-02-04', NULL, NULL, NULL, NULL, 'present', '08:30:00', NULL, NULL, '2025-06-13 10:16:44', '2025-06-13 10:16:44'),
(39, 1, 1, 2, '2025-06-13', NULL, NULL, NULL, NULL, 'present', NULL, NULL, '', '2025-06-13 10:29:36', '2025-06-13 10:29:36'),
(40, 2, 1, 2, '2025-06-13', NULL, NULL, NULL, NULL, 'present', NULL, NULL, '', '2025-06-13 10:29:36', '2025-06-13 10:29:36'),
(41, 6, 1, 2, '2025-06-13', NULL, NULL, NULL, NULL, 'absent', NULL, NULL, '', '2025-06-13 10:29:36', '2025-06-13 10:29:36'),
(48, 1, 1, 11, '2025-06-19', NULL, NULL, NULL, NULL, 'half_day', NULL, NULL, '', '2025-06-20 08:24:30', '2025-06-20 08:24:30'),
(49, 2, 1, 11, '2025-06-19', NULL, NULL, NULL, NULL, 'half_day', NULL, NULL, '', '2025-06-20 08:24:30', '2025-06-20 08:24:30'),
(50, 6, 1, 11, '2025-06-19', NULL, NULL, NULL, NULL, 'half_day', NULL, NULL, '', '2025-06-20 08:24:30', '2025-06-20 08:24:30'),
(60, 1, 1, 11, '2025-06-20', NULL, NULL, NULL, NULL, 'present', NULL, NULL, '', '2025-06-20 15:05:04', '2025-06-20 15:05:04'),
(61, 2, 1, 11, '2025-06-20', NULL, NULL, NULL, NULL, 'present', NULL, NULL, '', '2025-06-20 15:05:04', '2025-06-20 15:05:04'),
(62, 6, 1, 11, '2025-06-20', NULL, NULL, NULL, NULL, 'absent', NULL, NULL, '', '2025-06-20 15:05:04', '2025-06-20 15:05:04');

--
-- Triggers `attendance`
--
DELIMITER $$
CREATE TRIGGER `attendance_nepali_date_insert` BEFORE INSERT ON `attendance` FOR EACH ROW BEGIN
    DECLARE nepali_data JSON;
    
    -- Get Nepali date mapping for the attendance date
    SELECT JSON_OBJECT(
        'year', nepali_year,
        'month', nepali_month, 
        'day', nepali_day,
        'date_string', nepali_date_string
    ) INTO nepali_data
    FROM nepali_date_mapping 
    WHERE gregorian_date = NEW.attendance_date;
    
    -- Update Nepali date fields if mapping exists
    IF nepali_data IS NOT NULL THEN
        SET NEW.nepali_year = JSON_UNQUOTE(JSON_EXTRACT(nepali_data, '$.year'));
        SET NEW.nepali_month = JSON_UNQUOTE(JSON_EXTRACT(nepali_data, '$.month'));
        SET NEW.nepali_day = JSON_UNQUOTE(JSON_EXTRACT(nepali_data, '$.day'));
        SET NEW.nepali_date_string = JSON_UNQUOTE(JSON_EXTRACT(nepali_data, '$.date_string'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `attendance_nepali_view`
-- (See below for the actual view)
--
CREATE TABLE `attendance_nepali_view` (
`id` int(11)
,`student_id` int(11)
,`class_id` int(11)
,`teacher_id` int(11)
,`attendance_date` date
,`nepali_year` int(11)
,`nepali_month` int(11)
,`nepali_day` int(11)
,`nepali_date_string` varchar(15)
,`status` enum('present','absent','late','half_day')
,`check_in_time` time
,`check_out_time` time
,`remarks` text
,`created_at` timestamp
,`updated_at` timestamp
,`nepali_date_formatted` varchar(17)
,`nepali_month_name` varchar(20)
,`nepali_month_name_np` varchar(20)
,`english_day_name` varchar(9)
,`nepali_day_name` varchar(15)
);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `class_level` int(11) NOT NULL,
  `section` varchar(10) DEFAULT 'A',
  `academic_year_id` int(11) NOT NULL,
  `capacity` int(11) DEFAULT 40,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `class_level`, `section`, `academic_year_id`, `capacity`, `is_active`, `created_at`) VALUES
(1, 'Class 1', 1, 'A', 1, 40, 1, '2025-06-02 02:52:54'),
(2, 'Class 2', 2, 'A', 1, 40, 1, '2025-06-02 02:52:54'),
(3, 'Class 3', 3, 'A', 1, 40, 1, '2025-06-02 02:52:54'),
(4, 'Class 10', 1, 'A', 1, 40, 1, '2025-06-02 16:46:17'),
(5, 'Class8', 8, 'A', 1, 40, 1, '2025-06-19 09:20:50');

-- --------------------------------------------------------

--
-- Table structure for table `class_subject_teachers`
--

CREATE TABLE `class_subject_teachers` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `assigned_date` date DEFAULT curdate(),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_subject_teachers`
--

INSERT INTO `class_subject_teachers` (`id`, `class_id`, `subject_id`, `teacher_id`, `academic_year_id`, `assigned_date`, `is_active`, `created_at`) VALUES
(7, 1, 1, 2, 1, '2024-04-01', 1, '2025-06-02 02:52:54'),
(8, 1, 2, 3, 1, '2024-04-01', 1, '2025-06-02 02:52:54'),
(9, 2, 3, 4, 1, '2024-04-01', 1, '2025-06-02 02:52:54'),
(10, 2, 4, 2, 1, '2024-04-01', 1, '2025-06-02 02:52:54'),
(11, 3, 5, 3, 1, '2024-04-01', 1, '2025-06-02 02:52:54'),
(14, 5, 6, 11, 1, '2025-06-19', 1, '2025-06-19 09:21:48'),
(15, 1, 4, 11, 1, '2025-06-19', 1, '2025-06-19 17:11:37'),
(16, 1, 3, 12, 1, '2025-06-20', 1, '2025-06-20 07:03:39');

-- --------------------------------------------------------

--
-- Stand-in structure for view `current_academic_year_nepali`
-- (See below for the actual view)
--
CREATE TABLE `current_academic_year_nepali` (
`id` int(11)
,`year_name` varchar(20)
,`start_date` date
,`end_date` date
,`nepali_start_year` int(11)
,`nepali_start_month` int(11)
,`nepali_start_day` int(11)
,`nepali_end_year` int(11)
,`nepali_end_month` int(11)
,`nepali_end_day` int(11)
,`nepali_year_name` varchar(20)
,`is_current` tinyint(1)
,`created_at` timestamp
,`nepali_start_date_formatted` varchar(44)
,`nepali_end_date_formatted` varchar(44)
,`nepali_start_date_string` varchar(17)
,`nepali_end_date_string` varchar(17)
);

-- --------------------------------------------------------

--
-- Table structure for table `discussion_forums`
--

CREATE TABLE `discussion_forums` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `forum_type` enum('general','class','subject','announcement') DEFAULT 'general',
  `is_active` tinyint(1) DEFAULT 1,
  `is_moderated` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discussion_forums`
--

INSERT INTO `discussion_forums` (`id`, `title`, `description`, `class_id`, `subject_id`, `created_by`, `forum_type`, `is_active`, `is_moderated`, `created_at`) VALUES
(1, 'Class 1 Mathematics Discussion', 'Discuss math problems and solutions', 1, 1, 2, 'subject', 1, 1, '2025-06-20 06:21:52'),
(2, 'General Announcements', 'School-wide announcements and updates', NULL, NULL, 1, 'announcement', 1, 1, '2025-06-20 06:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_type` enum('class','exam','assignment','holiday','meeting','announcement','other') NOT NULL,
  `start_date` date NOT NULL,
  `nepali_start_year` int(11) DEFAULT NULL,
  `nepali_start_month` int(11) DEFAULT NULL,
  `nepali_start_day` int(11) DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `nepali_end_year` int(11) DEFAULT NULL,
  `nepali_end_month` int(11) DEFAULT NULL,
  `nepali_end_day` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `recurrence_pattern` varchar(100) DEFAULT NULL,
  `is_all_day` tinyint(1) DEFAULT 0,
  `reminder_minutes` int(11) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `color` varchar(7) DEFAULT '#3498db',
  `event_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_type`, `start_date`, `nepali_start_year`, `nepali_start_month`, `nepali_start_day`, `end_date`, `nepali_end_year`, `nepali_end_month`, `nepali_end_day`, `start_time`, `end_time`, `location`, `class_id`, `subject_id`, `created_by`, `is_recurring`, `recurrence_pattern`, `is_all_day`, `reminder_minutes`, `is_public`, `color`, `event_image`, `created_at`, `updated_at`) VALUES
(4, 'Ram Namami', 'A ram day', 'holiday', '2025-06-20', NULL, NULL, NULL, '2025-06-23', NULL, NULL, NULL, NULL, NULL, 'Itahari Namuna Collage', NULL, NULL, 11, 0, NULL, 0, 60, 1, '#3498db', 'uploads/events/event_68559aacc854c.webp', '2025-06-20 17:30:20', '2025-06-20 17:30:20');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(200) NOT NULL,
  `exam_type` enum('unit_test','mid_term','final','annual','monthly','weekly') NOT NULL,
  `class_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `exam_date_start` date NOT NULL,
  `exam_date_end` date NOT NULL,
  `total_marks` int(11) DEFAULT 100,
  `pass_marks` int(11) DEFAULT 40,
  `created_by` int(11) NOT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `exam_name`, `exam_type`, `class_id`, `academic_year`, `exam_date_start`, `exam_date_end`, `total_marks`, `pass_marks`, `created_by`, `status`, `instructions`, `created_at`, `updated_at`) VALUES
(1, 'First Unit Test', 'unit_test', 1, '2024-2025', '2024-07-15', '2024-07-20', 100, 40, 2, 'completed', NULL, '2025-06-20 17:47:35', '2025-06-20 17:47:35'),
(2, 'Mid Term Examination', 'mid_term', 1, '2024-2025', '2024-09-01', '2024-09-10', 100, 40, 2, 'completed', NULL, '2025-06-20 17:47:35', '2025-06-20 17:47:35'),
(3, 'Final Examination', 'final', 1, '2024-2025', '2024-12-01', '2024-12-15', 100, 40, 2, 'scheduled', NULL, '2025-06-20 17:47:35', '2025-06-20 17:47:35'),
(4, 'INC EXAM', 'mid_term', 1, '2024-2025', '2025-06-23', '2025-06-30', 60, 24, 11, 'scheduled', 'Must be in fomrmal and no mobile or any electronic or digital devices otherwise finr 1000rs', '2025-06-20 17:52:40', '2025-06-20 17:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `full_marks` int(11) NOT NULL,
  `marks_obtained` decimal(5,2) NOT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `entered_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `exam_id`, `student_id`, `subject_id`, `full_marks`, `marks_obtained`, `grade`, `gpa`, `percentage`, `remarks`, `entered_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 100, 85.00, 'A', 3.60, 85.00, 'Excellent', 2, '2025-06-20 17:47:36', '2025-06-20 17:47:36'),
(2, 1, 1, 2, 100, 78.00, 'B+', 3.20, 78.00, 'Good', 2, '2025-06-20 17:47:36', '2025-06-20 17:47:36'),
(3, 1, 1, 3, 100, 92.00, 'A+', 4.00, 92.00, 'Outstanding', 2, '2025-06-20 17:47:36', '2025-06-20 17:47:36'),
(4, 2, 1, 1, 100, 88.00, 'A', 3.60, 88.00, 'Very Good', 2, '2025-06-20 17:47:36', '2025-06-20 17:47:36'),
(5, 2, 1, 2, 100, 82.00, 'A-', 3.40, 82.00, 'Good', 2, '2025-06-20 17:47:36', '2025-06-20 17:47:36'),
(6, 2, 1, 3, 100, 95.00, 'A+', 4.00, 95.00, 'Excellent', 2, '2025-06-20 17:47:36', '2025-06-20 17:47:36'),
(7, 4, 1, 4, 60, 24.00, 'B-', 2.00, 40.00, 'Pass', 11, '2025-06-20 17:53:11', '2025-06-20 17:53:11'),
(8, 4, 2, 4, 60, 32.00, 'B', 2.40, 53.33, 'Satisfactory', 11, '2025-06-20 17:53:11', '2025-06-20 17:53:11'),
(9, 4, 6, 4, 60, 42.00, 'A-', 3.20, 70.00, 'Very Good', 11, '2025-06-20 17:53:11', '2025-06-20 17:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `exam_subjects`
--

CREATE TABLE `exam_subjects` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `full_marks` int(11) NOT NULL DEFAULT 100,
  `pass_marks` int(11) NOT NULL DEFAULT 40,
  `exam_date` date DEFAULT NULL,
  `exam_time` time DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 180,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_subjects`
--

INSERT INTO `exam_subjects` (`id`, `exam_id`, `subject_id`, `full_marks`, `pass_marks`, `exam_date`, `exam_time`, `duration_minutes`, `created_at`) VALUES
(1, 1, 1, 100, 40, '2024-07-15', '10:00:00', 180, '2025-06-20 17:47:36'),
(2, 1, 2, 100, 40, '2024-07-16', '10:00:00', 180, '2025-06-20 17:47:36'),
(3, 1, 3, 100, 40, '2024-07-17', '10:00:00', 180, '2025-06-20 17:47:36'),
(4, 2, 1, 100, 40, '2024-09-01', '10:00:00', 180, '2025-06-20 17:47:36'),
(5, 2, 2, 100, 40, '2024-09-02', '10:00:00', 180, '2025-06-20 17:47:36'),
(6, 2, 3, 100, 40, '2024-09-03', '10:00:00', 180, '2025-06-20 17:47:36'),
(7, 4, 4, 60, 24, '2025-06-23', '10:00:00', 180, '2025-06-20 17:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_post_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text NOT NULL,
  `attachment_url` varchar(500) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `is_pinned` tinyint(1) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `replies_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade_categories`
--

CREATE TABLE `grade_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `weight_percentage` decimal(5,2) DEFAULT 100.00,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grade_categories`
--

INSERT INTO `grade_categories` (`id`, `name`, `description`, `weight_percentage`, `class_id`, `subject_id`, `academic_year_id`, `is_active`, `created_at`) VALUES
(1, 'Assignments', 'Regular homework and assignments', 30.00, 1, 1, 1, 1, '2025-06-20 06:21:52'),
(2, 'Quizzes', 'Regular quizzes and tests', 25.00, 1, 1, 1, 1, '2025-06-20 06:21:52'),
(3, 'Midterm Exam', 'Mid-semester examination', 20.00, 1, 1, 1, 1, '2025-06-20 06:21:52'),
(4, 'Final Exam', 'Final semester examination', 25.00, 1, 1, 1, 1, '2025-06-20 06:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `learning_resources`
--

CREATE TABLE `learning_resources` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `resource_type` enum('document','video','audio','link','image','presentation','ebook') NOT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `external_url` varchar(500) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_format` varchar(20) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `download_count` int(11) DEFAULT 0,
  `tags` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_resources`
--

INSERT INTO `learning_resources` (`id`, `title`, `description`, `resource_type`, `file_url`, `external_url`, `file_size`, `file_format`, `class_id`, `subject_id`, `uploaded_by`, `is_public`, `download_count`, `tags`, `created_at`, `updated_at`) VALUES
(3, 'World #1 Cheapest SMM', 'learn', 'image', 'uploads/resources/68558f26a4aa6.png', NULL, 18223, 'png', 1, NULL, 11, 1, 3, NULL, '2025-06-20 16:41:10', '2025-06-20 17:45:10');

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','teacher') NOT NULL,
  `leave_type` enum('sick','personal','emergency','vacation','other') NOT NULL,
  `from_date` date NOT NULL,
  `nepali_from_year` int(11) DEFAULT NULL,
  `nepali_from_month` int(11) DEFAULT NULL,
  `nepali_from_day` int(11) DEFAULT NULL,
  `to_date` date NOT NULL,
  `nepali_to_year` int(11) DEFAULT NULL,
  `nepali_to_month` int(11) DEFAULT NULL,
  `nepali_to_day` int(11) DEFAULT NULL,
  `total_days` int(11) NOT NULL,
  `reason` text NOT NULL,
  `leave_details` text DEFAULT NULL,
  `attachment_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  `approved_date` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `user_id`, `user_type`, `leave_type`, `from_date`, `nepali_from_year`, `nepali_from_month`, `nepali_from_day`, `to_date`, `nepali_to_year`, `nepali_to_month`, `nepali_to_day`, `total_days`, `reason`, `leave_details`, `attachment_url`, `status`, `applied_date`, `approved_by`, `approved_date`, `rejection_reason`, `emergency_contact`) VALUES
(1, 5, 'student', 'sick', '2024-01-19', NULL, NULL, NULL, '2024-01-19', NULL, NULL, NULL, 1, 'Fever and cold symptoms', NULL, NULL, 'approved', '2024-01-18 04:45:00', NULL, NULL, NULL, NULL),
(2, 6, 'student', 'personal', '2024-02-05', NULL, NULL, NULL, '2024-02-06', NULL, NULL, NULL, 2, 'Family function attendance', NULL, NULL, 'pending', '2024-01-25 08:35:00', NULL, NULL, NULL, NULL),
(3, 7, 'student', 'sick', '2024-01-20', NULL, NULL, NULL, '2024-01-21', NULL, NULL, NULL, 2, 'Stomach flu', NULL, NULL, 'approved', '2024-01-19 10:00:00', NULL, NULL, NULL, NULL),
(4, 10, 'student', 'sick', '2025-06-02', NULL, NULL, NULL, '2025-06-03', NULL, NULL, NULL, 2, 'due to treatment', 'reer', NULL, 'pending', '2025-06-02 04:30:33', NULL, NULL, NULL, ''),
(5, 10, 'student', 'sick', '2025-06-02', NULL, NULL, NULL, '2025-06-03', NULL, NULL, NULL, 2, 'due to treatment', 'reer', NULL, 'approved', '2025-06-02 04:31:36', 1, '2025-06-02 16:20:59', '', ''),
(6, 10, 'student', 'personal', '2025-06-02', NULL, NULL, NULL, '2025-06-03', NULL, NULL, NULL, 2, 'tested', '', 'uploads/leave_attachments/10_1748839848.jpeg', 'approved', '2025-06-02 04:50:57', 1, '2025-06-02 16:18:46', '', ''),
(7, 10, 'student', 'sick', '2025-06-19', NULL, NULL, NULL, '2025-06-21', NULL, NULL, NULL, 3, 'feaver', '', 'uploads/leave_attachments/10_1750353180.png', 'rejected', '2025-06-19 17:13:01', 23, '2025-06-20 05:56:20', 'not applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message_body` text NOT NULL,
  `message_type` enum('personal','announcement','system','assignment') DEFAULT 'personal',
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `attachment_url` varchar(500) DEFAULT NULL,
  `parent_message_id` int(11) DEFAULT NULL,
  `is_deleted_by_sender` tinyint(1) DEFAULT 0,
  `is_deleted_by_recipient` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `recipient_id`, `subject`, `message_body`, `message_type`, `priority`, `is_read`, `read_at`, `attachment_url`, `parent_message_id`, `is_deleted_by_sender`, `is_deleted_by_recipient`, `sent_at`) VALUES
(1, 11, 1, 'Internal Task', 'you guyes come tommor at 2pm in colleg ecanteen', 'personal', 'normal', 0, NULL, NULL, NULL, 0, 0, '2025-06-20 09:11:26'),
(2, 11, 2, 'Internal Task', 'you guyes come tommor at 2pm in colleg ecanteen', 'personal', 'normal', 0, NULL, NULL, NULL, 0, 0, '2025-06-20 09:11:26'),
(3, 11, 6, 'Internal Task', 'you guyes come tommor at 2pm in colleg ecanteen', 'personal', 'normal', 0, NULL, NULL, NULL, 0, 0, '2025-06-20 09:11:26'),
(4, 11, 5, 'Today Fight', 'You are gonna suspended if you repeat the same action as today.', 'personal', 'high', 1, '2025-06-20 16:48:58', NULL, NULL, 0, 0, '2025-06-20 16:29:40'),
(5, 11, 5, 'Internal Task', 'test', 'personal', 'urgent', 1, '2025-06-20 16:58:46', NULL, NULL, 0, 0, '2025-06-20 16:58:37');

-- --------------------------------------------------------

--
-- Table structure for table `nepali_calendar_config`
--

CREATE TABLE `nepali_calendar_config` (
  `id` int(11) NOT NULL,
  `nepali_year` int(11) NOT NULL,
  `nepali_month` int(11) NOT NULL,
  `nepali_month_name_en` varchar(20) NOT NULL,
  `nepali_month_name_np` varchar(20) NOT NULL,
  `days_in_month` int(11) NOT NULL,
  `gregorian_start_date` date NOT NULL,
  `gregorian_end_date` date NOT NULL,
  `is_leap_year` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nepali_calendar_config`
--

INSERT INTO `nepali_calendar_config` (`id`, `nepali_year`, `nepali_month`, `nepali_month_name_en`, `nepali_month_name_np`, `days_in_month`, `gregorian_start_date`, `gregorian_end_date`, `is_leap_year`, `created_at`) VALUES
(1, 2081, 1, 'Baisakh', 'बैशाख', 31, '2024-04-13', '2024-05-13', 0, '2025-06-20 06:23:12'),
(2, 2081, 2, 'Jestha', 'जेठ', 31, '2024-05-14', '2024-06-13', 0, '2025-06-20 06:23:12'),
(3, 2081, 3, 'Ashadh', 'आषाढ', 32, '2024-06-14', '2024-07-15', 0, '2025-06-20 06:23:12'),
(4, 2081, 4, 'Shrawan', 'श्रावण', 32, '2024-07-16', '2024-08-16', 0, '2025-06-20 06:23:12'),
(5, 2081, 5, 'Bhadra', 'भाद्र', 31, '2024-08-17', '2024-09-16', 0, '2025-06-20 06:23:12'),
(6, 2081, 6, 'Ashwin', 'आश्विन', 30, '2024-09-17', '2024-10-16', 0, '2025-06-20 06:23:12'),
(7, 2081, 7, 'Kartik', 'कार्तिक', 29, '2024-10-17', '2024-11-14', 0, '2025-06-20 06:23:12'),
(8, 2081, 8, 'Mangsir', 'मंसिर', 30, '2024-11-15', '2024-12-14', 0, '2025-06-20 06:23:12'),
(9, 2081, 9, 'Poush', 'पौष', 29, '2024-12-15', '2025-01-12', 0, '2025-06-20 06:23:12'),
(10, 2081, 10, 'Magh', 'माघ', 30, '2025-01-13', '2025-02-11', 0, '2025-06-20 06:23:12'),
(11, 2081, 11, 'Falgun', 'फाल्गुन', 30, '2025-02-12', '2025-03-13', 0, '2025-06-20 06:23:12'),
(12, 2081, 12, 'Chaitra', 'चैत्र', 30, '2025-03-14', '2025-04-12', 0, '2025-06-20 06:23:12');

-- --------------------------------------------------------

--
-- Table structure for table `nepali_date_mapping`
--

CREATE TABLE `nepali_date_mapping` (
  `id` int(11) NOT NULL,
  `gregorian_date` date NOT NULL,
  `nepali_year` int(11) NOT NULL,
  `nepali_month` int(11) NOT NULL,
  `nepali_day` int(11) NOT NULL,
  `nepali_date_string` varchar(15) NOT NULL,
  `day_of_week_en` varchar(10) NOT NULL,
  `day_of_week_np` varchar(15) NOT NULL,
  `is_holiday` tinyint(1) DEFAULT 0,
  `holiday_name` varchar(100) DEFAULT NULL,
  `tithi` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nepali_date_mapping`
--

INSERT INTO `nepali_date_mapping` (`id`, `gregorian_date`, `nepali_year`, `nepali_month`, `nepali_day`, `nepali_date_string`, `day_of_week_en`, `day_of_week_np`, `is_holiday`, `holiday_name`, `tithi`, `created_at`) VALUES
(1, '2024-04-13', 2081, 1, 1, '2081-01-01', 'Saturday', 'शनिबार', 1, 'Nepali New Year', NULL, '2025-06-20 06:23:12'),
(2, '2024-04-14', 2081, 1, 2, '2081-01-02', 'Sunday', 'आइतबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(3, '2024-04-15', 2081, 1, 3, '2081-01-03', 'Monday', 'सोमबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(4, '2024-04-16', 2081, 1, 4, '2081-01-04', 'Tuesday', 'मंगलबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(5, '2024-04-17', 2081, 1, 5, '2081-01-05', 'Wednesday', 'बुधबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(6, '2024-04-18', 2081, 1, 6, '2081-01-06', 'Thursday', 'बिहिबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(7, '2024-04-19', 2081, 1, 7, '2081-01-07', 'Friday', 'शुक्रबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(8, '2024-04-20', 2081, 1, 8, '2081-01-08', 'Saturday', 'शनिबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(9, '2024-04-21', 2081, 1, 9, '2081-01-09', 'Sunday', 'आइतबार', 0, NULL, NULL, '2025-06-20 06:23:12'),
(10, '2024-04-22', 2081, 1, 10, '2081-01-10', 'Monday', 'सोमबार', 0, NULL, NULL, '2025-06-20 06:23:12');

-- --------------------------------------------------------

--
-- Table structure for table `nepali_holidays`
--

CREATE TABLE `nepali_holidays` (
  `id` int(11) NOT NULL,
  `holiday_name_en` varchar(100) NOT NULL,
  `holiday_name_np` varchar(100) NOT NULL,
  `holiday_type` enum('national','religious','cultural','school','other') NOT NULL,
  `nepali_month` int(11) NOT NULL,
  `nepali_day` int(11) NOT NULL,
  `is_fixed_date` tinyint(1) DEFAULT 1,
  `is_recurring` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `is_school_holiday` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nepali_holidays`
--

INSERT INTO `nepali_holidays` (`id`, `holiday_name_en`, `holiday_name_np`, `holiday_type`, `nepali_month`, `nepali_day`, `is_fixed_date`, `is_recurring`, `description`, `is_school_holiday`, `created_at`) VALUES
(1, 'Nepali New Year', 'नेपाली नयाँ वर्ष', 'national', 1, 1, 1, 1, 'Nepali New Year celebration', 1, '2025-06-20 06:23:12'),
(2, 'Buddha Jayanti', 'बुद्ध जयन्ती', 'religious', 1, 15, 0, 1, 'Birth of Lord Buddha', 1, '2025-06-20 06:23:12'),
(3, 'Dashain', 'दशैं', 'religious', 6, 25, 0, 1, 'Major Hindu festival', 1, '2025-06-20 06:23:12'),
(4, 'Tihar', 'तिहार', 'religious', 7, 28, 0, 1, 'Festival of lights', 1, '2025-06-20 06:23:12'),
(5, 'Holi', 'होली', 'religious', 11, 30, 0, 1, 'Festival of colors', 1, '2025-06-20 06:23:12'),
(6, 'Shivaratri', 'शिवरात्री', 'religious', 10, 28, 0, 1, 'Night of Lord Shiva', 1, '2025-06-20 06:23:12'),
(7, 'Constitution Day', 'संविधान दिवस', 'national', 5, 3, 1, 1, 'Constitution Day of Nepal', 1, '2025-06-20 06:23:12'),
(8, 'Democracy Day', 'लोकतन्त्र दिवस', 'national', 11, 7, 1, 1, 'Democracy Day', 1, '2025-06-20 06:23:12');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `notification_type` enum('assignment','grade','attendance','leave','announcement','message','quiz','system') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `related_table` varchar(50) DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_preferences`
--

CREATE TABLE `notification_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_type` enum('assignment','grade','attendance','leave','announcement','message','quiz','system') NOT NULL,
  `email_enabled` tinyint(1) DEFAULT 1,
  `sms_enabled` tinyint(1) DEFAULT 0,
  `push_enabled` tinyint(1) DEFAULT 1,
  `in_app_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_preferences`
--

INSERT INTO `notification_preferences` (`id`, `user_id`, `notification_type`, `email_enabled`, `sms_enabled`, `push_enabled`, `in_app_enabled`, `created_at`, `updated_at`) VALUES
(1, 5, 'assignment', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(2, 6, 'assignment', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(3, 7, 'assignment', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(4, 8, 'assignment', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(5, 9, 'assignment', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(6, 10, 'assignment', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(7, 5, 'grade', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(8, 6, 'grade', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(9, 7, 'grade', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(10, 8, 'grade', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(11, 9, 'grade', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(12, 10, 'grade', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(13, 5, 'attendance', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(14, 6, 'attendance', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(15, 7, 'attendance', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(16, 8, 'attendance', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(17, 9, 'attendance', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52'),
(18, 10, 'attendance', 1, 0, 1, 1, '2025-06-20 06:21:52', '2025-06-20 06:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `online_classes`
--

CREATE TABLE `online_classes` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `start_time` time NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `zoom_url` varchar(500) DEFAULT NULL,
  `meeting_id` varchar(50) DEFAULT NULL,
  `passcode` varchar(50) DEFAULT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `actual_start_time` timestamp NULL DEFAULT NULL,
  `actual_end_time` timestamp NULL DEFAULT NULL,
  `recording_url` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_class_attendance`
--

CREATE TABLE `online_class_attendance` (
  `id` int(11) NOT NULL,
  `online_class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `joined_at` timestamp NULL DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 0,
  `status` enum('present','absent','partial') DEFAULT 'absent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_students`
--

CREATE TABLE `parent_students` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relationship` enum('father','mother','guardian','other') NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `quiz_type` enum('quiz','exam','test','assessment') DEFAULT 'quiz',
  `total_marks` int(11) DEFAULT 100,
  `duration_minutes` int(11) DEFAULT 60,
  `attempts_allowed` int(11) DEFAULT 1,
  `start_date` datetime NOT NULL,
  `nepali_start_year` int(11) DEFAULT NULL,
  `nepali_start_month` int(11) DEFAULT NULL,
  `nepali_start_day` int(11) DEFAULT NULL,
  `end_date` datetime NOT NULL,
  `nepali_end_year` int(11) DEFAULT NULL,
  `nepali_end_month` int(11) DEFAULT NULL,
  `nepali_end_day` int(11) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `show_results` tinyint(1) DEFAULT 1,
  `randomize_questions` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attempt_number` int(11) DEFAULT 1,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` timestamp NULL DEFAULT NULL,
  `time_taken_minutes` int(11) DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `max_score` decimal(5,2) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('in_progress','submitted','graded','expired') DEFAULT 'in_progress',
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `auto_graded` tinyint(1) DEFAULT 0,
  `teacher_feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','short_answer','essay','fill_blank') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `correct_answer` text DEFAULT NULL,
  `marks` decimal(5,2) DEFAULT 1.00,
  `explanation` text DEFAULT NULL,
  `order_number` int(11) DEFAULT 1,
  `is_required` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_access_log`
--

CREATE TABLE `resource_access_log` (
  `id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_type` enum('view','download','share') NOT NULL,
  `accessed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resource_access_log`
--

INSERT INTO `resource_access_log` (`id`, `resource_id`, `user_id`, `access_type`, `accessed_at`, `ip_address`) VALUES
(1, 3, 5, 'download', '2025-06-20 16:41:19', '::1'),
(2, 3, 5, 'download', '2025-06-20 17:22:38', '192.168.1.72'),
(3, 3, 5, 'download', '2025-06-20 17:45:09', '192.168.1.72');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `academic_year_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `subject_id`, `day_of_week`, `start_time`, `end_time`, `room_number`, `academic_year_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Monday', '09:00:00', '10:00:00', 'Room 101', 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(2, 2, 'Monday', '10:15:00', '11:15:00', 'Room 102', 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(3, 1, 'Wednesday', '09:00:00', '10:00:00', 'Room 101', 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(4, 2, 'Wednesday', '10:15:00', '11:15:00', 'Room 102', 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(5, 3, 'Tuesday', '09:00:00', '10:00:00', 'Room 201', 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(6, 4, 'Tuesday', '10:15:00', '11:15:00', 'Room 201', 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(7, 5, 'Thursday', '09:00:00', '10:00:00', 'Room 301', 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42');

-- --------------------------------------------------------

--
-- Table structure for table `school_settings`
--

CREATE TABLE `school_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `school_settings`
--

INSERT INTO `school_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'school_name', 'Sample School', 'string', 'Name of the school', NULL, '2025-06-01 07:02:36'),
(2, 'academic_year_start_month', '4', 'number', 'Month when academic year starts (Baisakh = 4)', NULL, '2025-06-01 07:02:36'),
(3, 'attendance_required_percentage', '75', 'number', 'Minimum attendance percentage required', NULL, '2025-06-01 07:02:36'),
(4, 'assignment_submission_buffer_days', '2', 'number', 'Extra days allowed for late submission', NULL, '2025-06-01 07:02:36'),
(5, 'enable_online_classes', '1', 'boolean', 'Enable online class functionality', NULL, '2025-06-20 06:21:51'),
(6, 'enable_forums', '1', 'boolean', 'Enable discussion forums', NULL, '2025-06-20 06:21:51'),
(7, 'enable_messaging', '1', 'boolean', 'Enable internal messaging system', NULL, '2025-06-20 06:21:51'),
(8, 'max_file_upload_size', '50', 'number', 'Maximum file upload size in MB', NULL, '2025-06-20 06:21:51'),
(9, 'quiz_auto_grade', '1', 'boolean', 'Enable automatic grading for quizzes', NULL, '2025-06-20 06:21:51'),
(10, 'notification_email_enabled', '1', 'boolean', 'Enable email notifications', NULL, '2025-06-20 06:21:51'),
(11, 'grade_scale', '{\"A\": 90, \"B\": 80, \"C\": 70, \"D\": 60, \"F\": 0}', 'json', 'Grade scale configuration', NULL, '2025-06-20 06:21:51'),
(12, 'academic_calendar_start', '2024-04-01', 'string', 'Academic calendar start date', NULL, '2025-06-20 06:21:51'),
(13, 'school_logo_url', '', 'string', 'School logo URL', NULL, '2025-06-20 06:21:51'),
(14, 'school_address', '', 'string', 'School physical address', NULL, '2025-06-20 06:21:51'),
(15, 'school_phone', '', 'string', 'School contact phone', NULL, '2025-06-20 06:21:51'),
(16, 'school_email', '', 'string', 'School contact email', NULL, '2025-06-20 06:21:51'),
(17, 'timezone', 'Asia/Kathmandu', 'string', 'School timezone', NULL, '2025-06-20 06:21:51'),
(18, 'language', 'en', 'string', 'Default system language', NULL, '2025-06-20 06:21:51'),
(19, 'use_nepali_calendar', '1', 'boolean', 'Enable Nepali calendar system', NULL, '2025-06-20 06:23:12'),
(20, 'primary_calendar', 'nepali', 'string', 'Primary calendar system (nepali/gregorian)', NULL, '2025-06-20 06:23:12'),
(21, 'show_both_calendars', '1', 'boolean', 'Show both Nepali and Gregorian dates', NULL, '2025-06-20 06:23:12'),
(22, 'nepali_date_format', 'YYYY-MM-DD', 'string', 'Nepali date display format', NULL, '2025-06-20 06:23:12'),
(23, 'current_nepali_year', '2081', 'string', 'Current Nepali year', NULL, '2025-06-20 06:23:12'),
(24, 'academic_year_starts_month', '1', 'number', 'Academic year starts in Baisakh (month 1)', NULL, '2025-06-20 06:23:12'),
(25, 'weekend_days', '[\"Saturday\"]', 'json', 'Weekend days in Nepal', NULL, '2025-06-20 06:23:12'),
(26, 'working_days_per_week', '6', 'number', 'Working days per week', NULL, '2025-06-20 06:23:12'),
(27, 'school_start_time', '10:00', 'string', 'School start time', NULL, '2025-06-20 06:23:12'),
(28, 'school_end_time', '16:00', 'string', 'School end time', NULL, '2025-06-20 06:23:12');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `admission_date` date NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_phone` varchar(20) DEFAULT NULL,
  `guardian_email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_id`, `admission_date`, `date_of_birth`, `blood_group`, `emergency_contact`, `guardian_name`, `guardian_phone`, `guardian_email`, `is_active`, `created_at`) VALUES
(1, 5, 'STU2024001', '2024-04-01', '2010-05-15', 'A+', NULL, 'Robert Wilson', '9841234580', 'robert.wilson@email.com', 1, '2025-06-02 02:52:54'),
(2, 6, 'STU2024002', '2024-04-01', '2010-08-22', 'B+', NULL, 'Linda Anderson', '9841234581', 'linda.anderson@email.com', 1, '2025-06-02 02:52:54'),
(3, 7, 'STU2024003', '2024-04-01', '2010-12-10', 'O+', NULL, 'Carlos Martinez', '9841234582', 'carlos.martinez@email.com', 1, '2025-06-02 02:52:54'),
(4, 8, 'STU2024004', '2024-04-01', '2011-03-18', 'AB+', NULL, 'Maria Garcia', '9841234583', 'maria.garcia@email.com', 1, '2025-06-02 02:52:54'),
(5, 9, 'STU2024005', '2024-04-01', '2011-07-25', 'A-', NULL, 'Jose Rodriguez', '9841234584', 'jose.rodriguez@email.com', 1, '2025-06-02 02:52:54'),
(6, 10, 'STU2025006', '2025-06-02', '2025-06-02', 'A+', '', 'akadsdd', '324324', 'dadss@gmail.com', 1, '2025-06-02 04:09:12');

-- --------------------------------------------------------

--
-- Table structure for table `student_classes`
--

CREATE TABLE `student_classes` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `enrollment_date` date DEFAULT curdate(),
  `status` enum('enrolled','transferred','graduated','dropped') DEFAULT 'enrolled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_classes`
--

INSERT INTO `student_classes` (`id`, `student_id`, `class_id`, `academic_year_id`, `enrollment_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2024-04-01', 'enrolled', '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(2, 2, 1, 1, '2024-04-01', 'enrolled', '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(3, 3, 2, 1, '2024-04-01', 'enrolled', '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(4, 4, 2, 1, '2024-04-01', 'enrolled', '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(5, 5, 3, 1, '2024-04-01', 'enrolled', '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(6, 6, 1, 1, '2025-06-02', 'enrolled', '2025-06-02 04:27:42', '2025-06-02 04:27:42');

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_dashboard_view`
-- (See below for the actual view)
--
CREATE TABLE `student_dashboard_view` (
`student_id` int(11)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`email` varchar(100)
,`class_name` varchar(50)
,`section` varchar(10)
,`total_assignments` bigint(21)
,`submitted_assignments` bigint(21)
,`total_attendance_days` bigint(21)
,`present_days` bigint(21)
,`attendance_percentage` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `enrollment_date` date DEFAULT curdate(),
  `promotion_date` date DEFAULT NULL,
  `status` enum('enrolled','promoted','transferred','dropped') DEFAULT 'enrolled',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `student_id`, `class_id`, `academic_year_id`, `enrollment_date`, `promotion_date`, `status`, `remarks`, `created_at`) VALUES
(6, 1, 1, 1, '2024-04-01', NULL, 'enrolled', NULL, '2025-06-02 02:52:54'),
(7, 2, 1, 1, '2024-04-01', NULL, 'enrolled', NULL, '2025-06-02 02:52:54'),
(8, 3, 2, 1, '2024-04-01', NULL, 'enrolled', NULL, '2025-06-02 02:52:54'),
(9, 4, 2, 1, '2024-04-01', NULL, 'enrolled', NULL, '2025-06-02 02:52:54'),
(10, 5, 3, 1, '2024-04-01', NULL, 'enrolled', NULL, '2025-06-02 02:52:54'),
(11, 6, 1, 1, '2025-06-02', NULL, 'enrolled', NULL, '2025-06-02 04:09:12');

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `grade_category_id` int(11) DEFAULT NULL,
  `assessment_type` enum('assignment','quiz','exam','project','participation','other') NOT NULL,
  `assessment_id` int(11) DEFAULT NULL,
  `grade_value` decimal(5,2) NOT NULL,
  `max_grade` decimal(5,2) NOT NULL,
  `percentage` decimal(5,2) GENERATED ALWAYS AS (`grade_value` / `max_grade` * 100) STORED,
  `letter_grade` varchar(5) DEFAULT NULL,
  `graded_by` int(11) NOT NULL,
  `graded_date` date NOT NULL,
  `comments` text DEFAULT NULL,
  `is_final` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `subject_code`, `class_id`, `teacher_id`, `description`, `is_active`, `created_at`) VALUES
(1, 'Mathematics', 'MATH101', 1, 2, NULL, 1, '2025-06-02 02:52:54'),
(2, 'English', 'ENG101', 1, 3, NULL, 1, '2025-06-02 02:52:54'),
(3, 'Science', 'SCI101', 2, 4, NULL, 1, '2025-06-02 02:52:54'),
(4, 'Mathematics', 'MATH201', 2, 2, NULL, 1, '2025-06-02 02:52:54'),
(5, 'English', 'ENG201', 3, 3, NULL, 1, '2025-06-02 02:52:54'),
(6, 'MATH', 'MTH2', 5, NULL, NULL, 1, '2025-06-19 09:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 02:53:03'),
(2, 1, 'password_reset', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 02:54:20'),
(3, 1, 'user_deactivated', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:02:49'),
(4, 1, 'password_reset', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:02:58'),
(5, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:06:27'),
(6, 1, 'student_created', 'students', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:09:12'),
(7, 10, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:09:46'),
(8, 10, '10', 'leave_application_submitted', 0, '\"4\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:30:33'),
(9, 10, '10', 'leave_application_submitted', 0, '\"5\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:31:36'),
(10, 10, '10', 'leave_application_submitted', 0, '\"6\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 04:50:57'),
(11, 10, 'assignment_submitted', 'assignment_submissions', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 15:09:30'),
(12, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 15:20:51'),
(13, 1, 'leave_approve', 'leave_applications', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 16:18:46'),
(14, 1, 'leave_approve', 'leave_applications', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 16:20:59'),
(15, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 16:24:50'),
(16, 1, 'teacher_created', 'users', 11, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 16:25:23'),
(17, 11, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 16:26:06'),
(18, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 16:42:36'),
(19, 1, 'class_created', 'classes', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-02 16:46:17'),
(20, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-03 05:01:03'),
(21, 10, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-03 05:35:23'),
(22, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 03:51:17'),
(23, 1, 'password_reset', 'users', 10, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 03:51:58'),
(24, 10, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 03:52:15'),
(25, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 03:53:01'),
(26, 1, 'user_activated', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 06:01:34'),
(27, 1, 'teacher_created', 'users', 12, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 07:57:51'),
(28, 1, 'password_reset', 'users', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 10:29:01'),
(29, 2, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 10:29:16'),
(30, 2, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":\"1\",\"date\":\"2025-06-13\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 10:29:36'),
(31, 1, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 15:32:05'),
(32, 1, 'password_reset', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 15:32:32'),
(33, 1, 'user_deactivated', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 15:53:02'),
(34, 23, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 17:09:04'),
(35, 23, 'password_reset', 'users', 12, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 17:09:24'),
(36, 12, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-13 17:10:10'),
(37, 23, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 09:15:18'),
(38, 11, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 09:19:29'),
(39, 23, 'class_created', 'classes', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 09:20:50'),
(40, 23, 'subject_added', 'subjects', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 09:21:34'),
(41, 23, 'teacher_assigned', 'class_subject_teachers', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 09:21:48'),
(42, 11, 'assignment_created', 'assignments', 26, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 09:23:19'),
(43, 10, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 09:31:05'),
(44, 23, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 16:08:55'),
(45, 23, 'teacher_assigned', 'class_subject_teachers', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 17:11:37'),
(46, 23, 'teacher_assigned', 'class_subject_teachers', 0, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 17:11:37'),
(47, 10, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 17:11:58'),
(48, 10, '10', 'leave_application_submitted', 0, '\"7\"', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 17:13:01'),
(49, 10, 'assignment_submitted', 'assignment_submissions', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-19 17:13:41'),
(50, 23, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 04:49:39'),
(51, 23, 'password_reset', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 05:40:01'),
(52, 23, 'leave_reject', 'leave_applications', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 05:55:21'),
(53, 23, 'leave_reject', 'leave_applications', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 05:56:20'),
(54, 23, 'password_reset', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 06:24:46'),
(55, 5, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 06:25:14'),
(56, 12, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 06:25:33'),
(57, 23, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 07:03:20'),
(58, 23, 'teacher_assigned', 'class_subject_teachers', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 07:03:39'),
(59, 23, 'teacher_assigned', 'class_subject_teachers', 0, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 07:03:39'),
(60, 5, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 07:04:06'),
(61, 11, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 07:04:26'),
(62, 23, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 07:04:44'),
(63, 11, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":1,\"date\":\"2025-06-20\"}', '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 07:57:05'),
(64, 11, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":1,\"date\":\"2025-06-20\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 08:24:13'),
(65, 11, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":1,\"date\":\"2025-06-19\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 08:24:30'),
(66, 11, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":1,\"date\":\"2025-06-20\"}', '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 08:49:13'),
(67, 11, 'create_teacher_log', 'teacher_logs', 22, NULL, NULL, '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 08:54:14'),
(68, 11, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":1,\"date\":\"2025-06-20\"}', '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 09:18:45'),
(69, 11, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":1,\"date\":\"2025-06-20\"}', '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 09:19:51'),
(70, 11, 'update_teacher_log', 'teacher_logs', 22, NULL, NULL, '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 09:20:51'),
(71, 11, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 12:54:01'),
(72, 11, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 13:20:27'),
(73, 11, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-20 13:57:57'),
(74, 11, 'attendance_recorded', 'attendance', NULL, NULL, '{\"class_id\":1,\"date\":\"2025-06-20\"}', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '2025-06-20 15:05:04'),
(75, 5, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 15:44:17'),
(76, 5, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '2025-06-20 15:44:55'),
(77, 11, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 16:27:47'),
(78, 11, 'resource_uploaded', 'learning_resources', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G977N Build/PPR1.180610.011) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/9.2 Chrome/67.0.3396.87 Mobile Safari/537.36', '2025-06-20 16:41:10'),
(79, 5, 'user_login', NULL, NULL, NULL, NULL, '192.168.1.72', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.3 Mobile/15E148 Safari/604.1', '2025-06-20 17:21:33'),
(80, 5, 'assignment_submitted', 'assignment_submissions', 7, NULL, NULL, '192.168.1.72', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.3 Mobile/15E148 Safari/604.1', '2025-06-20 17:22:15'),
(81, 11, 'user_login', NULL, NULL, NULL, NULL, '192.168.1.74', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-20 17:28:31'),
(82, 5, 'user_login', NULL, NULL, NULL, NULL, '192.168.1.74', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-20 17:55:45');

-- --------------------------------------------------------

--
-- Stand-in structure for view `teacher_dashboard_view`
-- (See below for the actual view)
--
CREATE TABLE `teacher_dashboard_view` (
`teacher_id` int(11)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`email` varchar(100)
,`classes_taught` bigint(21)
,`subjects_taught` bigint(21)
,`assignments_created` bigint(21)
,`teaching_logs` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_logs`
--

CREATE TABLE `teacher_logs` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `chapter_title` varchar(200) NOT NULL,
  `chapter_content` text DEFAULT NULL,
  `topics_covered` text DEFAULT NULL,
  `teaching_method` varchar(100) DEFAULT NULL,
  `homework_assigned` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `lesson_duration` int(11) DEFAULT NULL,
  `students_present` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_logs`
--

INSERT INTO `teacher_logs` (`id`, `teacher_id`, `class_id`, `subject_id`, `log_date`, `chapter_title`, `chapter_content`, `topics_covered`, `teaching_method`, `homework_assigned`, `notes`, `lesson_duration`, `students_present`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, '2024-02-01', 'Introduction to Algebra', 'Basics of algebraic expressions', 'Variables, expressions', 'Lecture', 'Solve problems 1-10', 'Students engaged', 60, 35, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(2, 3, 1, 2, '2024-02-02', 'Poetry Basics', 'Introduction to poetic forms', 'Rhyme, meter', 'Discussion', 'Analyze a poem', 'Good participation', 60, 34, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(3, 4, 2, 3, '2024-02-03', 'Plant Growth', 'Factors affecting plant growth', 'Photosynthesis', 'Experiment', 'Write experiment report', 'Lab setup successful', 90, 36, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(4, 2, 2, 4, '2024-02-04', 'Triangles', 'Properties of triangles', 'Types, angles', 'Interactive', 'Practice problems', 'Used visual aids', 60, 35, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(5, 3, 3, 5, '2024-02-05', 'Grammar Rules', 'Parts of speech', 'Nouns, verbs', 'Lecture', 'Workbook pages 20-25', 'Clear explanations', 60, 33, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(6, 2, 1, 1, '2024-02-08', 'Linear Equations', 'Solving linear equations', 'Equations, solutions', 'Problem-solving', 'Solve 10 equations', 'Students struggled with steps', 60, 34, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(7, 3, 1, 2, '2024-02-09', 'Short Stories', 'Elements of a short story', 'Plot, characters', 'Group work', 'Write a story outline', 'Creative ideas shared', 60, 35, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(8, 4, 2, 3, '2024-02-10', 'Chemical Reactions', 'Types of reactions', 'Combination, decomposition', 'Demonstration', 'Draw reaction diagrams', 'Students curious', 90, 36, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(9, 2, 2, 4, '2024-02-11', 'Trigonometric Ratios', 'Sine, cosine, tangent', 'Ratios, applications', 'Lecture', 'Practice problems', 'Board examples helped', 60, 34, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(10, 3, 3, 5, '2024-02-12', 'Vocabulary Building', 'New word acquisition', 'Synonyms, antonyms', 'Interactive', 'Learn 20 words', 'Quiz planned', 60, 32, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(11, 2, 1, 1, '2024-02-15', 'Fractions', 'Operations with fractions', 'Addition, subtraction', 'Lecture', 'Solve fraction problems', 'Students need practice', 60, 35, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(12, 3, 1, 2, '2024-02-16', 'Essay Structure', 'Writing effective essays', 'Introduction, body', 'Discussion', 'Write essay outline', 'Good engagement', 60, 34, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(13, 4, 2, 3, '2024-02-17', 'Physics Basics', 'Laws of motion', 'Newton’s laws', 'Experiment', 'Conduct pendulum experiment', 'Hands-on learning', 90, 36, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(14, 2, 2, 4, '2024-02-18', 'Statistics', 'Data analysis basics', 'Mean, median', 'Interactive', 'Analyze data set', 'Used software tools', 60, 35, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(15, 3, 3, 5, '2024-02-19', 'Debate Skills', 'Constructing arguments', 'Argument structure', 'Group work', 'Prepare debate points', 'Lively discussion', 60, 33, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(16, 2, 1, 1, '2024-02-22', 'Probability', 'Introduction to probability', 'Events, outcomes', 'Lecture', 'Solve probability problems', 'Clear concepts', 60, 34, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(17, 3, 1, 2, '2024-02-23', 'Speech Writing', 'Crafting speeches', 'Structure, delivery', 'Interactive', 'Write a speech', 'Practice sessions planned', 60, 35, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(18, 4, 2, 3, '2024-02-24', 'Cell Structure', 'Basics of cell biology', 'Cell parts, functions', 'Model-making', 'Create cell model', 'Students creative', 90, 36, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(19, 2, 2, 4, '2024-02-25', 'Differentiation', 'Introduction to calculus', 'Derivatives', 'Lecture', 'Practice derivatives', 'Challenging topic', 60, 34, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(20, 3, 3, 5, '2024-02-26', 'Environmental Issues', 'Climate change discussion', 'Global warming', 'Discussion', 'Write essay', 'Students aware', 60, 33, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(21, 2, 1, 1, '2024-03-01', 'Exam Review', 'Review for final exam', 'All topics', 'Review session', 'Complete review packet', 'Intensive session', 60, 35, '2025-06-13 10:16:52', '2025-06-13 10:16:52'),
(22, 11, 1, 4, '2025-06-20', 'Hygine', 'dsdd', 'sdds', 'Project', 'dsddsd', 'sddsd', 45, 1, '2025-06-20 08:54:14', '2025-06-20 09:20:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `address`, `profile_image`, `role_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'principal', 'principal@school.com', '$2y$10$6qipZ0oam5xnLmKVb8nKt.QHP3.W3z2fzWVyy9OEAzWafBwdmrUWm', 'John', 'Smith', '9841234567', NULL, NULL, 1, 0, '2025-06-02 02:52:54', '2025-06-13 16:53:56'),
(2, 'teacher001', 'math.teacher@school.com', '$2y$10$xJdjQp4tX.in3EZwYSczlOt00gYxJlheY1YSA5833rZL2OaNwhuhC', 'Sarah', 'Johnson', '9841234568', NULL, NULL, 2, 1, '2025-06-02 02:52:54', '2025-06-13 10:29:01'),
(3, 'teacher002', 'english.teacher@school.com', '$2y$10$8K1p/a96pBKFaEgbuS4WOdSrvAiC7kwxVjsAHlyRc8em/fxIgHZ5W', 'Michael', 'Brown', '9841234569', NULL, NULL, 2, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(4, 'teacher003', 'science.teacher@school.com', '$2y$10$8K1p/a96pBKFaEgbuS4WOdSrvAiC7kwxVjsAHlyRc8em/fxIgHZ5W', 'Emily', 'Davis', '9841234570', NULL, NULL, 2, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(5, 'student001', 'student001@school.com', '$2y$10$VvRXHy3uFncYOR2v8/FoaOzcUgV3u80hbZ0EtOOHTTD6SHxu389My', 'Alice', 'Wilson', '9841234571', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-20 06:24:46'),
(6, 'student002', 'student002@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Bob', 'Anderson', '9841234572', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(7, 'student003', 'student003@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Carol', 'Martinez', '9841234573', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(8, 'student004', 'student004@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'David', 'Garcia', '9841234574', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(9, 'student005', 'student005@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Eva', 'Rodriguez', '9841234575', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(10, 'student006', 'kapil@gmail.com', '$2y$10$eaMv8d06K/aN6refEDTIMOjXZj.Ap/Ern6yMXNBOnNlTQYQIVwetK', 'kapil', 'Tamang', '97675655', 'biratchowl', NULL, 3, 1, '2025-06-02 04:09:12', '2025-06-13 03:51:58'),
(11, 'teacher004', 'prasanga@gmail.com', '$2y$10$V992qAfjM42ZAFtc3DArB.gJx81Jyv9jbU4vwiZmkvl7lHv33wMKi', 'prasanga', 'pokharel', '9765470926', 'test', 'profile_11_1750433887.png', 2, 1, '2025-06-02 16:25:23', '2025-06-20 15:38:07'),
(12, 'teacher005', 'chandra@gmail.com', '$2y$10$84/WmuM2KXJt.2g3ouvXpuviLEIX8YUsJECEfB0Fx9UaET3XqWSxy', 'Chandra', 'Acharya', '9765470926', 'Itahari-9', NULL, 2, 1, '2025-06-13 07:57:51', '2025-06-13 17:09:24'),
(13, 'principal_john', 'john.principal@school.com', '$2y$10$7X8y9z0w1x2y3z4w5x6y7z8w9x0y1z2w3x4y5z6w7x8y9z0w1x2y3z', 'John', 'Smith', '9841234580', '123 Main St, City', NULL, 1, 1, '2025-06-13 16:49:31', '2025-06-13 16:49:31'),
(14, 'principal_jane', 'jane.principal@school.com', '$2y$10$9a0b1c2d3e4f5g6h7i8j9k0l1m2n3o4p5q6r7s8t9u0v1w2x3y4z5a6', 'Jane', 'Doe', '9841234581', '456 Elm St, City', NULL, 1, 1, '2025-06-13 16:49:31', '2025-06-13 16:49:31'),
(17, 'principal_mark', 'mark.principal@school.com', '$2y$10$1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z7a', 'Dharma', 'Neupane', '9841234582', '789 Oak St, City', NULL, 1, 1, '2025-06-13 16:50:36', '2025-06-19 16:04:02'),
(18, 'principal_lisa', 'lisa.principal@school.com', '$2y$10$9z0y1x2w3v4u5t6s7r8q9p0o1n2m3l4k5j6i7h8g9f0e1d2c3b4a5z', 'Lisa', 'Brown', '9841234583', '101 Pine St, City', NULL, 1, 1, '2025-06-13 16:50:36', '2025-06-13 16:50:36'),
(23, 'prasanga741', 'prasanga741@gmail.com', '$2y$10$eZoflVTmRZ/kkW62XT5FlOUyyVA2g5E43tKMRCCy8v8enan1ErSde', 'Nabin', 'Shrestha', '9841234582', '789 Oak St, City', NULL, 1, 1, '2025-06-13 17:08:12', '2025-06-19 16:03:14'),
(24, 'bhawana741', 'bhawana741@school.com', '$2y$10$z9NWuOvh1t8valpG4k7u2.7YBUb4h4CwFqCdqz1BP/84.3bBjy/q.', 'Lisa', 'Brown', '9841234583', '101 Pine St, City', NULL, 1, 1, '2025-06-13 17:08:12', '2025-06-13 17:08:12');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `role_name` enum('student','teacher','principal','parent') NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `role_name`, `permissions`, `created_at`) VALUES
(1, 'principal', '{\"all\": true, \"manage_users\": true, \"approve_leaves\": true, \"view_all_logs\": true}', '2025-06-02 02:38:23'),
(2, 'teacher', '{\"teach\": true, \"take_attendance\": true, \"create_assignments\": true, \"view_student_data\": true}', '2025-06-02 02:38:23'),
(3, 'student', '{\"view_assignments\": true, \"submit_assignments\": true, \"view_attendance\": true, \"apply_leave\": true}', '2025-06-02 02:38:23'),
(4, 'parent', '{\"view_child_data\": true, \"view_attendance\": true, \"view_assignments\": true}', '2025-06-02 02:38:23');

-- --------------------------------------------------------

--
-- Structure for view `assignments_nepali_view`
--
DROP TABLE IF EXISTS `assignments_nepali_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `assignments_nepali_view`  AS SELECT `a`.`id` AS `id`, `a`.`title` AS `title`, `a`.`description` AS `description`, `a`.`class_id` AS `class_id`, `a`.`subject_id` AS `subject_id`, `a`.`teacher_id` AS `teacher_id`, `a`.`assigned_date` AS `assigned_date`, `a`.`nepali_assigned_year` AS `nepali_assigned_year`, `a`.`nepali_assigned_month` AS `nepali_assigned_month`, `a`.`nepali_assigned_day` AS `nepali_assigned_day`, `a`.`due_date` AS `due_date`, `a`.`nepali_due_year` AS `nepali_due_year`, `a`.`nepali_due_month` AS `nepali_due_month`, `a`.`nepali_due_day` AS `nepali_due_day`, `a`.`max_marks` AS `max_marks`, `a`.`assignment_type` AS `assignment_type`, `a`.`instructions` AS `instructions`, `a`.`attachment_url` AS `attachment_url`, `a`.`is_active` AS `is_active`, `a`.`created_at` AS `created_at`, `a`.`updated_at` AS `updated_at`, concat(`a`.`nepali_assigned_year`,'-',lpad(`a`.`nepali_assigned_month`,2,'0'),'-',lpad(`a`.`nepali_assigned_day`,2,'0')) AS `nepali_assigned_date`, concat(`a`.`nepali_due_year`,'-',lpad(`a`.`nepali_due_month`,2,'0'),'-',lpad(`a`.`nepali_due_day`,2,'0')) AS `nepali_due_date`, `GetNepaliMonthNameEn`(`a`.`nepali_assigned_month`) AS `assigned_month_name`, `GetNepaliMonthNameEn`(`a`.`nepali_due_month`) AS `due_month_name` FROM `assignments` AS `a` WHERE `a`.`nepali_assigned_year` is not null ;

-- --------------------------------------------------------

--
-- Structure for view `attendance_nepali_view`
--
DROP TABLE IF EXISTS `attendance_nepali_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `attendance_nepali_view`  AS SELECT `a`.`id` AS `id`, `a`.`student_id` AS `student_id`, `a`.`class_id` AS `class_id`, `a`.`teacher_id` AS `teacher_id`, `a`.`attendance_date` AS `attendance_date`, `a`.`nepali_year` AS `nepali_year`, `a`.`nepali_month` AS `nepali_month`, `a`.`nepali_day` AS `nepali_day`, `a`.`nepali_date_string` AS `nepali_date_string`, `a`.`status` AS `status`, `a`.`check_in_time` AS `check_in_time`, `a`.`check_out_time` AS `check_out_time`, `a`.`remarks` AS `remarks`, `a`.`created_at` AS `created_at`, `a`.`updated_at` AS `updated_at`, concat(`a`.`nepali_year`,'-',lpad(`a`.`nepali_month`,2,'0'),'-',lpad(`a`.`nepali_day`,2,'0')) AS `nepali_date_formatted`, `GetNepaliMonthNameEn`(`a`.`nepali_month`) AS `nepali_month_name`, `GetNepaliMonthNameNp`(`a`.`nepali_month`) AS `nepali_month_name_np`, dayname(`a`.`attendance_date`) AS `english_day_name`, `GetNepaliDayOfWeek`(dayname(`a`.`attendance_date`)) AS `nepali_day_name` FROM `attendance` AS `a` WHERE `a`.`nepali_year` is not null ;

-- --------------------------------------------------------

--
-- Structure for view `current_academic_year_nepali`
--
DROP TABLE IF EXISTS `current_academic_year_nepali`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `current_academic_year_nepali`  AS SELECT `ay`.`id` AS `id`, `ay`.`year_name` AS `year_name`, `ay`.`start_date` AS `start_date`, `ay`.`end_date` AS `end_date`, `ay`.`nepali_start_year` AS `nepali_start_year`, `ay`.`nepali_start_month` AS `nepali_start_month`, `ay`.`nepali_start_day` AS `nepali_start_day`, `ay`.`nepali_end_year` AS `nepali_end_year`, `ay`.`nepali_end_month` AS `nepali_end_month`, `ay`.`nepali_end_day` AS `nepali_end_day`, `ay`.`nepali_year_name` AS `nepali_year_name`, `ay`.`is_current` AS `is_current`, `ay`.`created_at` AS `created_at`, concat(`ay`.`nepali_start_year`,' ',`GetNepaliMonthNameEn`(`ay`.`nepali_start_month`),' ',`ay`.`nepali_start_day`) AS `nepali_start_date_formatted`, concat(`ay`.`nepali_end_year`,' ',`GetNepaliMonthNameEn`(`ay`.`nepali_end_month`),' ',`ay`.`nepali_end_day`) AS `nepali_end_date_formatted`, concat(`ay`.`nepali_start_year`,'-',lpad(`ay`.`nepali_start_month`,2,'0'),'-',lpad(`ay`.`nepali_start_day`,2,'0')) AS `nepali_start_date_string`, concat(`ay`.`nepali_end_year`,'-',lpad(`ay`.`nepali_end_month`,2,'0'),'-',lpad(`ay`.`nepali_end_day`,2,'0')) AS `nepali_end_date_string` FROM `academic_years` AS `ay` WHERE `ay`.`is_current` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `student_dashboard_view`
--
DROP TABLE IF EXISTS `student_dashboard_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_dashboard_view`  AS SELECT `s`.`id` AS `student_id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `u`.`email` AS `email`, `c`.`class_name` AS `class_name`, `c`.`section` AS `section`, count(distinct `a`.`id`) AS `total_assignments`, count(distinct `asub`.`id`) AS `submitted_assignments`, count(distinct `att`.`id`) AS `total_attendance_days`, count(distinct case when `att`.`status` = 'present' then `att`.`id` end) AS `present_days`, round(count(distinct case when `att`.`status` = 'present' then `att`.`id` end) / count(distinct `att`.`id`) * 100,2) AS `attendance_percentage` FROM ((((((`students` `s` join `users` `u` on(`s`.`user_id` = `u`.`id`)) join `student_classes` `sc` on(`s`.`id` = `sc`.`student_id`)) join `classes` `c` on(`sc`.`class_id` = `c`.`id`)) left join `assignments` `a` on(`c`.`id` = `a`.`class_id`)) left join `assignment_submissions` `asub` on(`a`.`id` = `asub`.`assignment_id` and `s`.`id` = `asub`.`student_id`)) left join `attendance` `att` on(`s`.`id` = `att`.`student_id`)) WHERE `u`.`is_active` = 1 AND `s`.`is_active` = 1 GROUP BY `s`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`, `c`.`class_name`, `c`.`section` ;

-- --------------------------------------------------------

--
-- Structure for view `teacher_dashboard_view`
--
DROP TABLE IF EXISTS `teacher_dashboard_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `teacher_dashboard_view`  AS SELECT `u`.`id` AS `teacher_id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `u`.`email` AS `email`, count(distinct `cst`.`class_id`) AS `classes_taught`, count(distinct `cst`.`subject_id`) AS `subjects_taught`, count(distinct `a`.`id`) AS `assignments_created`, count(distinct `tl`.`id`) AS `teaching_logs` FROM (((`users` `u` join `class_subject_teachers` `cst` on(`u`.`id` = `cst`.`teacher_id`)) left join `assignments` `a` on(`u`.`id` = `a`.`teacher_id`)) left join `teacher_logs` `tl` on(`u`.`id` = `tl`.`teacher_id`)) WHERE `u`.`role_id` = 2 AND `u`.`is_active` = 1 AND `cst`.`is_active` = 1 GROUP BY `u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_current_year` (`is_current`),
  ADD KEY `idx_current` (`is_current`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_teacher` (`teacher_id`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_assigned_date` (`assigned_date`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_type_active` (`assignment_type`,`is_active`);

--
-- Indexes for table `assignment_grades`
--
ALTER TABLE `assignment_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment_student` (`assignment_id`,`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `graded_by` (`graded_by`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment_student` (`assignment_id`,`student_id`),
  ADD KEY `graded_by` (`graded_by`),
  ADD KEY `idx_assignment` (`assignment_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_submission_date` (`submission_date`),
  ADD KEY `idx_due_status` (`assignment_id`,`status`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_date` (`student_id`,`attendance_date`),
  ADD KEY `idx_date_class` (`attendance_date`,`class_id`),
  ADD KEY `idx_student_date` (`student_id`,`attendance_date`),
  ADD KEY `idx_teacher_date` (`teacher_id`,`attendance_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_class_date_status` (`class_id`,`attendance_date`,`status`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_class_year` (`class_name`,`section`,`academic_year_id`),
  ADD KEY `idx_level` (`class_level`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_academic_year` (`academic_year_id`);

--
-- Indexes for table `class_subject_teachers`
--
ALTER TABLE `class_subject_teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`class_id`,`subject_id`,`teacher_id`,`academic_year_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `idx_teacher` (`teacher_id`),
  ADD KEY `idx_class` (`class_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `discussion_forums`
--
ALTER TABLE `discussion_forums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_type` (`forum_type`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_type` (`event_type`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_event_image` (`event_image`),
  ADD KEY `idx_public_events` (`is_public`,`start_date`),
  ADD KEY `idx_class_events` (`class_id`,`start_date`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_exam` (`class_id`,`exam_type`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_exam_subject` (`exam_id`,`student_id`,`subject_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_entered_by` (`entered_by`);

--
-- Indexes for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_exam_subject` (`exam_id`,`subject_id`),
  ADD KEY `idx_subject` (`subject_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_forum` (`forum_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_parent` (`parent_post_id`),
  ADD KEY `idx_approved` (`is_approved`);

--
-- Indexes for table `grade_categories`
--
ALTER TABLE `grade_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_academic_year` (`academic_year_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `learning_resources`
--
ALTER TABLE `learning_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_type` (`resource_type`),
  ADD KEY `idx_public` (`is_public`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_user_dates` (`user_id`,`from_date`,`to_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_applied_date` (`applied_date`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_recipient` (`recipient_id`),
  ADD KEY `idx_read_status` (`recipient_id`,`is_read`),
  ADD KEY `idx_sent_date` (`sent_at`),
  ADD KEY `idx_parent` (`parent_message_id`);

--
-- Indexes for table `nepali_calendar_config`
--
ALTER TABLE `nepali_calendar_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_year_month` (`nepali_year`,`nepali_month`),
  ADD KEY `idx_nepali_year` (`nepali_year`),
  ADD KEY `idx_gregorian_dates` (`gregorian_start_date`,`gregorian_end_date`);

--
-- Indexes for table `nepali_date_mapping`
--
ALTER TABLE `nepali_date_mapping`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_gregorian` (`gregorian_date`),
  ADD UNIQUE KEY `unique_nepali` (`nepali_year`,`nepali_month`,`nepali_day`),
  ADD KEY `idx_nepali_year_month` (`nepali_year`,`nepali_month`),
  ADD KEY `idx_gregorian_date` (`gregorian_date`);

--
-- Indexes for table `nepali_holidays`
--
ALTER TABLE `nepali_holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_month_day` (`nepali_month`,`nepali_day`),
  ADD KEY `idx_type` (`holiday_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_type` (`notification_type`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_type` (`user_id`,`notification_type`);

--
-- Indexes for table `online_classes`
--
ALTER TABLE `online_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_teacher_date` (`teacher_id`,`scheduled_date`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_schedule` (`scheduled_date`,`start_time`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `online_class_attendance`
--
ALTER TABLE `online_class_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_class_student` (`online_class_id`,`student_id`),
  ADD KEY `idx_online_class` (`online_class_id`),
  ADD KEY `idx_student` (`student_id`);

--
-- Indexes for table `parent_students`
--
ALTER TABLE `parent_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_parent_student` (`parent_id`,`student_id`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_primary` (`is_primary`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_teacher` (`teacher_id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`),
  ADD KEY `idx_published` (`is_published`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_quiz_student_attempt` (`quiz_id`,`student_id`,`attempt_number`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quiz` (`quiz_id`),
  ADD KEY `idx_order` (`quiz_id`,`order_number`);

--
-- Indexes for table `resource_access_log`
--
ALTER TABLE `resource_access_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_resource` (`resource_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_access_date` (`accessed_at`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `school_settings`
--
ALTER TABLE `school_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_admission` (`admission_date`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_guardian_phone` (`guardian_phone`);

--
-- Indexes for table `student_classes`
--
ALTER TABLE `student_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_class_year` (`student_id`,`class_id`,`academic_year_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_year` (`student_id`,`academic_year_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `idx_student_class` (`student_id`,`class_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_academic_year` (`academic_year_id`);

--
-- Indexes for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_subject` (`student_id`,`subject_id`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_academic_year` (`academic_year_id`),
  ADD KEY `idx_category` (`grade_category_id`),
  ADD KEY `idx_graded_by` (`graded_by`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `idx_code` (`subject_code`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_action` (`user_id`,`action`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `teacher_logs`
--
ALTER TABLE `teacher_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_class_date` (`teacher_id`,`class_id`,`subject_id`,`log_date`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_teacher_date` (`teacher_id`,`log_date`),
  ADD KEY `idx_class_subject` (`class_id`,`subject_id`),
  ADD KEY `idx_log_date` (`log_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_role_active` (`role_id`,`is_active`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `assignment_grades`
--
ALTER TABLE `assignment_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class_subject_teachers`
--
ALTER TABLE `class_subject_teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `discussion_forums`
--
ALTER TABLE `discussion_forums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grade_categories`
--
ALTER TABLE `grade_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `learning_resources`
--
ALTER TABLE `learning_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nepali_calendar_config`
--
ALTER TABLE `nepali_calendar_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `nepali_date_mapping`
--
ALTER TABLE `nepali_date_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nepali_holidays`
--
ALTER TABLE `nepali_holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `online_classes`
--
ALTER TABLE `online_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_class_attendance`
--
ALTER TABLE `online_class_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_students`
--
ALTER TABLE `parent_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_access_log`
--
ALTER TABLE `resource_access_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `school_settings`
--
ALTER TABLE `school_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_classes`
--
ALTER TABLE `student_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `teacher_logs`
--
ALTER TABLE `teacher_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `assignments_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `assignment_grades`
--
ALTER TABLE `assignment_grades`
  ADD CONSTRAINT `assignment_grades_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_grades_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_grades_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`),
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `assignment_submissions_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `class_subject_teachers`
--
ALTER TABLE `class_subject_teachers`
  ADD CONSTRAINT `class_subject_teachers_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `class_subject_teachers_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `class_subject_teachers_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `class_subject_teachers_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `discussion_forums`
--
ALTER TABLE `discussion_forums`
  ADD CONSTRAINT `discussion_forums_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `discussion_forums_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `discussion_forums_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `exam_results_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `exam_results_ibfk_4` FOREIGN KEY (`entered_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD CONSTRAINT `exam_subjects_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `discussion_forums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `forum_posts_ibfk_3` FOREIGN KEY (`parent_post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grade_categories`
--
ALTER TABLE `grade_categories`
  ADD CONSTRAINT `grade_categories_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `grade_categories_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `grade_categories_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `learning_resources`
--
ALTER TABLE `learning_resources`
  ADD CONSTRAINT `learning_resources_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `learning_resources_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `learning_resources_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leave_applications_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`parent_message_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD CONSTRAINT `notification_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_classes`
--
ALTER TABLE `online_classes`
  ADD CONSTRAINT `online_classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `online_classes_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `online_classes_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `online_class_attendance`
--
ALTER TABLE `online_class_attendance`
  ADD CONSTRAINT `online_class_attendance_ibfk_1` FOREIGN KEY (`online_class_id`) REFERENCES `online_classes` (`id`),
  ADD CONSTRAINT `online_class_attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `parent_students`
--
ALTER TABLE `parent_students`
  ADD CONSTRAINT `parent_students_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `parent_students_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `quizzes_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resource_access_log`
--
ALTER TABLE `resource_access_log`
  ADD CONSTRAINT `resource_access_log_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `learning_resources` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_access_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `school_settings`
--
ALTER TABLE `school_settings`
  ADD CONSTRAINT `school_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_classes`
--
ALTER TABLE `student_classes`
  ADD CONSTRAINT `student_classes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_classes_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_classes_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD CONSTRAINT `student_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_enrollments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `student_enrollments_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD CONSTRAINT `student_grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_grades_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `student_grades_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `student_grades_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `student_grades_ibfk_5` FOREIGN KEY (`grade_category_id`) REFERENCES `grade_categories` (`id`),
  ADD CONSTRAINT `student_grades_ibfk_6` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `teacher_logs`
--
ALTER TABLE `teacher_logs`
  ADD CONSTRAINT `teacher_logs_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `teacher_logs_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `teacher_logs_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
