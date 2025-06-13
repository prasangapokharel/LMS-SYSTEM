-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 05:44 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `year_name` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year_name`, `start_date`, `end_date`, `is_current`, `created_at`) VALUES
(1, '2024-2025', '2024-04-01', '2025-03-31', 0, '2025-06-02 02:52:54');

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
  `due_date` date NOT NULL,
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

INSERT INTO `assignments` (`id`, `title`, `description`, `class_id`, `subject_id`, `teacher_id`, `assigned_date`, `due_date`, `max_marks`, `assignment_type`, `instructions`, `attachment_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Math Chapter 1 Exercises', 'Complete exercises 1-10 from Chapter 1: Basic Arithmetic', 1, 1, 2, '2024-01-15', '2024-01-22', 50, 'homework', 'Show all working steps clearly', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(2, 'English Essay Writing', 'Write a 200-word essay on \"My School\"', 1, 2, 3, '2024-01-16', '2024-01-25', 25, 'homework', 'Use proper grammar and punctuation', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(3, 'Science Project', 'Create a simple volcano model', 2, 3, 4, '2024-01-18', '2024-02-01', 100, 'project', 'Include a written report explaining the process', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42'),
(4, 'Math Quiz Preparation', 'Study multiplication tables 1-12', 2, 4, 2, '2024-01-20', '2024-01-27', 30, 'quiz', 'Quiz will be conducted in class', NULL, 1, '2025-06-02 04:27:42', '2025-06-02 04:27:42');

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
(5, 1, 6, '2025-06-02 15:09:30', 'test', 'uploads/assignments/6_1_1748876970.jpeg', 'submitted', NULL, NULL, NULL, NULL, 0);

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

INSERT INTO `attendance` (`id`, `student_id`, `class_id`, `teacher_id`, `attendance_date`, `status`, `check_in_time`, `check_out_time`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, '2024-01-15', 'present', '08:30:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(2, 1, 1, 2, '2024-01-16', 'present', '08:25:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(3, 1, 1, 2, '2024-01-17', 'late', '09:15:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(4, 1, 1, 2, '2024-01-18', 'present', '08:20:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(5, 1, 1, 2, '2024-01-19', 'absent', NULL, NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(6, 1, 1, 2, '2024-01-22', 'present', '08:35:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(7, 1, 1, 2, '2024-01-23', 'present', '08:30:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(8, 1, 1, 2, '2024-01-24', 'present', '08:28:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(9, 1, 1, 2, '2024-01-25', 'late', '09:05:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(10, 1, 1, 2, '2024-01-26', 'present', '08:32:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(11, 2, 1, 2, '2024-01-15', 'present', '08:32:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(12, 2, 1, 2, '2024-01-16', 'absent', NULL, NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(13, 2, 1, 2, '2024-01-17', 'present', '08:28:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(14, 3, 2, 4, '2024-01-15', 'present', '08:35:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(15, 3, 2, 4, '2024-01-16', 'late', '09:10:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(16, 4, 2, 4, '2024-01-15', 'present', '08:25:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41'),
(17, 5, 3, 3, '2024-01-15', 'present', '08:30:00', NULL, NULL, '2025-06-02 04:27:41', '2025-06-02 04:27:41');

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
(4, 'Class 10', 1, 'A', 1, 40, 1, '2025-06-02 16:46:17');

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
(11, 3, 5, 3, 1, '2024-04-01', 1, '2025-06-02 02:52:54');

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
  `to_date` date NOT NULL,
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

INSERT INTO `leave_applications` (`id`, `user_id`, `user_type`, `leave_type`, `from_date`, `to_date`, `total_days`, `reason`, `leave_details`, `attachment_url`, `status`, `applied_date`, `approved_by`, `approved_date`, `rejection_reason`, `emergency_contact`) VALUES
(1, 5, 'student', 'sick', '2024-01-19', '2024-01-19', 1, 'Fever and cold symptoms', NULL, NULL, 'approved', '2024-01-18 04:45:00', NULL, NULL, NULL, NULL),
(2, 6, 'student', 'personal', '2024-02-05', '2024-02-06', 2, 'Family function attendance', NULL, NULL, 'pending', '2024-01-25 08:35:00', NULL, NULL, NULL, NULL),
(3, 7, 'student', 'sick', '2024-01-20', '2024-01-21', 2, 'Stomach flu', NULL, NULL, 'approved', '2024-01-19 10:00:00', NULL, NULL, NULL, NULL),
(4, 10, 'student', 'sick', '2025-06-02', '2025-06-03', 2, 'due to treatment', 'reer', NULL, 'pending', '2025-06-02 04:30:33', NULL, NULL, NULL, ''),
(5, 10, 'student', 'sick', '2025-06-02', '2025-06-03', 2, 'due to treatment', 'reer', NULL, 'approved', '2025-06-02 04:31:36', 1, '2025-06-02 16:20:59', '', ''),
(6, 10, 'student', 'personal', '2025-06-02', '2025-06-03', 2, 'tested', '', 'uploads/leave_attachments/10_1748839848.jpeg', 'approved', '2025-06-02 04:50:57', 1, '2025-06-02 16:18:46', '', '');

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
(4, 'assignment_submission_buffer_days', '2', 'number', 'Extra days allowed for late submission', NULL, '2025-06-01 07:02:36');

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
(5, 'English', 'ENG201', 3, 3, NULL, 1, '2025-06-02 02:52:54');

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
(21, 10, 'user_login', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-06-03 05:35:23');

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
(1, 'principal', 'principal@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', '9841234567', NULL, NULL, 1, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(2, 'teacher001', 'math.teacher@school.com', '$2y$10$8K1p/a96pBKFaEgbuS4WOdSrvAiC7kwxVjsAHlyRc8em/fxIgHZ5W', 'Sarah', 'Johnson', '9841234568', NULL, NULL, 2, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(3, 'teacher002', 'english.teacher@school.com', '$2y$10$8K1p/a96pBKFaEgbuS4WOdSrvAiC7kwxVjsAHlyRc8em/fxIgHZ5W', 'Michael', 'Brown', '9841234569', NULL, NULL, 2, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(4, 'teacher003', 'science.teacher@school.com', '$2y$10$8K1p/a96pBKFaEgbuS4WOdSrvAiC7kwxVjsAHlyRc8em/fxIgHZ5W', 'Emily', 'Davis', '9841234570', NULL, NULL, 2, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(5, 'student001', 'student001@school.com', '$2y$10$ihyQn17RGGhRkwLVEMq9aOIeUUO3La8SNqhixWMTMD/DdXXjii9Qq', 'Alice', 'Wilson', '9841234571', NULL, NULL, 3, 0, '2025-06-02 02:52:54', '2025-06-02 04:02:58'),
(6, 'student002', 'student002@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Bob', 'Anderson', '9841234572', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(7, 'student003', 'student003@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Carol', 'Martinez', '9841234573', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(8, 'student004', 'student004@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'David', 'Garcia', '9841234574', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(9, 'student005', 'student005@school.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Eva', 'Rodriguez', '9841234575', NULL, NULL, 3, 1, '2025-06-02 02:52:54', '2025-06-02 02:52:54'),
(10, 'student006', 'kapil@gmail.com', '$2y$10$3.RsDr6taNQas0wayBoOK.tJAk33WcrYgOWKWJ4GuyTpBhjDEP8oO', 'kapil', 'Tamang', '97675655', 'biratchowl', NULL, 3, 1, '2025-06-02 04:09:12', '2025-06-02 04:09:12'),
(11, 'teacher004', 'prasanga@gmail.com', '$2y$10$V992qAfjM42ZAFtc3DArB.gJx81Jyv9jbU4vwiZmkvl7lHv33wMKi', 'prasanga', 'pokharel', '9765470926', 'test', NULL, 2, 1, '2025-06-02 16:25:23', '2025-06-02 16:25:23');

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
  ADD KEY `idx_active` (`is_active`);

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
  ADD KEY `idx_submission_date` (`submission_date`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_date` (`student_id`,`attendance_date`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `idx_date_class` (`attendance_date`,`class_id`),
  ADD KEY `idx_student_date` (`student_id`,`attendance_date`),
  ADD KEY `idx_teacher_date` (`teacher_id`,`attendance_date`),
  ADD KEY `idx_status` (`status`);

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
  ADD KEY `idx_active` (`is_active`);

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
  ADD KEY `idx_active` (`is_active`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assignment_grades`
--
ALTER TABLE `assignment_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class_subject_teachers`
--
ALTER TABLE `class_subject_teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `school_settings`
--
ALTER TABLE `school_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `teacher_logs`
--
ALTER TABLE `teacher_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leave_applications_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

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
