<?php
include '../include/connect.php';
include '../include/session.php';

// Ensure user has principal role
requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";
$error = "";

// Get current academic year
$stmt = $pdo->query("SELECT * FROM academic_years WHERE is_current = 1");
$current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current_academic_year) {
    $error = "No active academic year found. Please set an active academic year first.";
}

// Check if class_id is provided
if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
    header("Location: createclass.php");
    exit;
}

$class_id = (int)$_GET['class_id'];

// Get class details
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    header("Location: createclass.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assign teacher to subject
    if (isset($_POST['assign_teacher'])) {
        $subject_id = (int)$_POST['subject_id'];
        $teacher_id = (int)$_POST['teacher_id'];
        $academic_year_id = (int)$current_academic_year['id'];
        
        // Check if assignment already exists
        $stmt = $pdo->prepare("SELECT * FROM class_subject_teachers 
                              WHERE class_id = ? AND subject_id = ? AND academic_year_id = ?");
        $stmt->execute([$class_id, $subject_id, $academic_year_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update existing assignment
            $stmt = $pdo->prepare("UPDATE class_subject_teachers 
                                  SET teacher_id = ?, assigned_date = CURDATE() 
                                  WHERE id = ?");
            $stmt->execute([$teacher_id, $existing['id']]);
            $msg = "Teacher assignment updated successfully!";
        } else {
            // Create new assignment
            $stmt = $pdo->prepare("INSERT INTO class_subject_teachers 
                                  (class_id, subject_id, teacher_id, academic_year_id, assigned_date) 
                                  VALUES (?, ?, ?, ?, CURDATE())");
            $stmt->execute([$class_id, $subject_id, $teacher_id, $academic_year_id]);
            $msg = "Teacher assigned successfully!";
        }
        
        // Log the activity
        logActivity($pdo, 'teacher_assigned', 'class_subject_teachers', $subject_id);
    }
    
    // Remove teacher assignment
    if (isset($_POST['remove_assignment'])) {
        $assignment_id = (int)$_POST['assignment_id'];
        
        $stmt = $pdo->prepare("DELETE FROM class_subject_teachers WHERE id = ?");
        $stmt->execute([$assignment_id]);
        
        $msg = "Teacher assignment removed successfully!";
        
        // Log the activity
        logActivity($pdo, 'teacher_assignment_removed', 'class_subject_teachers', $assignment_id);
    }
    
    // Add new subject to class
    if (isset($_POST['add_subject'])) {
        $subject_name = trim($_POST['subject_name']);
        $subject_code = trim($_POST['subject_code']);
        
        if (empty($subject_name) || empty($subject_code)) {
            $error = "Subject name and code are required!";
        } else {
            // Check if subject code already exists
            $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_code = ?");
            $stmt->execute([$subject_code]);
            if ($stmt->fetch()) {
                $error = "Subject code already exists!";
            } else {
                // Add new subject
                $stmt = $pdo->prepare("INSERT INTO subjects 
                                      (subject_name, subject_code, class_id) 
                                      VALUES (?, ?, ?)");
                $stmt->execute([$subject_name, $subject_code, $class_id]);
                
                $msg = "New subject added successfully!";
                
                // Log the activity
                logActivity($pdo, 'subject_added', 'subjects', $pdo->lastInsertId());
            }
        }
    }
}

// Get all subjects for this class
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE class_id = ? OR class_id IS NULL ORDER BY subject_name");
$stmt->execute([$class_id]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all teachers
$stmt = $pdo->prepare("SELECT u.* FROM users u 
                      JOIN user_roles ur ON u.role_id = ur.id 
                      WHERE ur.role_name = 'teacher' AND u.is_active = 1
                      ORDER BY u.first_name, u.last_name");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current assignments
$stmt = $pdo->prepare("SELECT cst.*, s.subject_name, s.subject_code, 
                      u.first_name, u.last_name, u.email, u.phone
                      FROM class_subject_teachers cst
                      JOIN subjects s ON cst.subject_id = s.id
                      JOIN users u ON cst.teacher_id = u.id
                      WHERE cst.class_id = ? AND cst.academic_year_id = ?
                      ORDER BY s.subject_name");
$stmt->execute([$class_id, $current_academic_year['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get student count for this class
$stmt = $pdo->prepare("SELECT COUNT(*) as student_count FROM student_classes 
                      WHERE class_id = ? AND academic_year_id = ?");
$stmt->execute([$class_id, $current_academic_year['id']]);
$student_count = $stmt->fetch(PDO::FETCH_ASSOC)['student_count'];

include '../include/sidebar.php';
?>
