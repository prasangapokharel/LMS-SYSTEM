<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";
$error = "";

// Get class ID from URL parameter
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

if (!$class_id) {
    header('Location: createclass.php');
    exit;
}

// Get current academic year
$stmt = $pdo->prepare("SELECT * FROM academic_years WHERE is_current = 1");
$stmt->execute();
$current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);

// If no current academic year is found, get the most recent one instead
if (!$current_academic_year) {
    $stmt = $pdo->prepare("SELECT * FROM academic_years ORDER BY year_name DESC LIMIT 1");
    $stmt->execute();
    $current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If still no academic year found, create a default one
    if (!$current_academic_year) {
        // Insert a new academic year
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 year'));
        $yearName = $currentYear . '-' . $nextYear;
        
        $stmt = $pdo->prepare("INSERT INTO academic_years (year_name, start_date, end_date, is_current) VALUES (?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 1)");
        $stmt->execute([$yearName]);
        
        // Get the newly created academic year
        $stmt = $pdo->prepare("SELECT * FROM academic_years WHERE year_name = ?");
        $stmt->execute([$yearName]);
        $current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $msg = "No active academic year found. A new academic year '$yearName' has been created.";
    } else {
        // Set the found academic year as current
        $stmt = $pdo->prepare("UPDATE academic_years SET is_current = 1 WHERE id = ?");
        $stmt->execute([$current_academic_year['id']]);
        
        $msg = "No active academic year found. The most recent academic year has been set as current.";
    }
}

// Add a check before using academic_year_id in queries
if (!isset($current_academic_year['id']) || empty($current_academic_year['id'])) {
    $error = "Critical error: Unable to determine academic year. Please contact system administrator.";
    // Set a fallback value to prevent SQL errors
    $current_academic_year['id'] = 0;
}

// Get class details
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    header('Location: createclass.php');
    exit;
}

// Get student count for this class
$stmt = $pdo->prepare("SELECT COUNT(*) FROM student_classes WHERE class_id = ? AND academic_year_id = ?");
$stmt->execute([$class_id, $current_academic_year['id']]);
$student_count = $stmt->fetchColumn();

// Get all teachers
$stmt = $pdo->prepare("SELECT u.* FROM users u 
                      JOIN user_roles r ON u.role_id = r.id 
                      WHERE r.role_name = 'teacher' AND u.is_active = 1
                      ORDER BY u.first_name, u.last_name");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all subjects
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE is_active = 1 ORDER BY subject_name");
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current assignments for this class
$stmt = $pdo->prepare("SELECT cst.*, s.subject_name, s.subject_code, 
                      u.first_name, u.last_name, u.email
                      FROM class_subject_teachers cst
                      JOIN subjects s ON cst.subject_id = s.id
                      JOIN users u ON cst.teacher_id = u.id
                      WHERE cst.class_id = ? AND cst.academic_year_id = ? AND cst.is_active = 1
                      ORDER BY s.subject_name");
$stmt->execute([$class_id, $current_academic_year['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize assignments array if query returns false
if (!$assignments) {
    $assignments = [];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new subject
    if (isset($_POST['add_subject'])) {
        $subject_name = trim($_POST['subject_name']);
        $subject_code = trim($_POST['subject_code']);
        
        // Validate input
        if (empty($subject_name) || empty($subject_code)) {
            $error = "Subject name and code are required.";
        } else {
            // Check if subject code already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE subject_code = ?");
            $stmt->execute([$subject_code]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Subject code already exists. Please use a different code.";
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, subject_code, class_id) 
                                          VALUES (?, ?, ?)");
                    $stmt->execute([$subject_name, $subject_code, $class_id]);
                    
                    $msg = "Subject added successfully.";
                    
                    // Refresh subjects list
                    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE is_active = 1 ORDER BY subject_name");
                    $stmt->execute();
                    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $error = "Error adding subject: " . $e->getMessage();
                }
            }
        }
    }
    
    // Assign teacher to subject
    if (isset($_POST['assign_teacher'])) {
        $subject_id = (int)$_POST['subject_id'];
        $teacher_id = (int)$_POST['teacher_id'];
        
        // Validate input
        if (!$subject_id || !$teacher_id) {
            $error = "Please select both subject and teacher.";
        } else if (!isset($current_academic_year['id']) || $current_academic_year['id'] <= 0) {
            $error = "No valid academic year found. Cannot assign teachers without an academic year.";
        } else {
            try {
                // Verify that the academic_year_id exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM academic_years WHERE id = ?");
                $stmt->execute([$current_academic_year['id']]);
                if ($stmt->fetchColumn() == 0) {
                    $error = "The selected academic year does not exist in the database.";
                } else {
                    // Check if assignment already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM class_subject_teachers 
                                          WHERE class_id = ? AND subject_id = ? AND academic_year_id = ?");
                    $stmt->execute([$class_id, $subject_id, $current_academic_year['id']]);
                    
                    if ($stmt->fetchColumn() > 0) {
                        // Update existing assignment
                        $stmt = $pdo->prepare("UPDATE class_subject_teachers 
                                              SET teacher_id = ?, assigned_date = CURDATE() 
                                              WHERE class_id = ? AND subject_id = ? AND academic_year_id = ?");
                        $stmt->execute([$teacher_id, $class_id, $subject_id, $current_academic_year['id']]);
                        $msg = "Teacher assignment updated successfully.";
                    } else {
                        // Create new assignment
                        $stmt = $pdo->prepare("INSERT INTO class_subject_teachers 
                                              (class_id, subject_id, teacher_id, academic_year_id, assigned_date) 
                                              VALUES (?, ?, ?, ?, CURDATE())");
                        $stmt->execute([$class_id, $subject_id, $teacher_id, $current_academic_year['id']]);
                        $msg = "Teacher assigned successfully.";
                    }
                    
                    // Log the activity
                    logActivity($pdo, 'teacher_assigned', 'class_subject_teachers', $pdo->lastInsertId());
                    
                    // Refresh assignments list
                    $stmt = $pdo->prepare("SELECT cst.*, s.subject_name, s.subject_code, 
                                          u.first_name, u.last_name, u.email
                                          FROM class_subject_teachers cst
                                          JOIN subjects s ON cst.subject_id = s.id
                                          JOIN users u ON cst.teacher_id = u.id
                                          WHERE cst.class_id = ? AND cst.academic_year_id = ? AND cst.is_active = 1
                                          ORDER BY s.subject_name");
                    $stmt->execute([$class_id, $current_academic_year['id']]);
                    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Initialize assignments array if query returns false
                    if (!$assignments) {
                        $assignments = [];
                    }
                }
            } catch (PDOException $e) {
                $error = "Error assigning teacher: " . $e->getMessage();
            }
        }
    }
    
    // Remove assignment
    if (isset($_POST['remove_assignment'])) {
        $assignment_id = (int)$_POST['assignment_id'];
        
        try {
            $stmt = $pdo->prepare("UPDATE class_subject_teachers SET is_active = 0 WHERE id = ?");
            $stmt->execute([$assignment_id]);
            
            $msg = "Teacher assignment removed successfully.";
            
            // Log the activity
            logActivity($pdo, 'teacher_assignment_removed', 'class_subject_teachers', $assignment_id);
            
            // Refresh assignments list
            $stmt = $pdo->prepare("SELECT cst.*, s.subject_name, s.subject_code, 
                                  u.first_name, u.last_name, u.email
                                  FROM class_subject_teachers cst
                                  JOIN subjects s ON cst.subject_id = s.id
                                  JOIN users u ON cst.teacher_id = u.id
                                  WHERE cst.class_id = ? AND cst.academic_year_id = ? AND cst.is_active = 1
                                  ORDER BY s.subject_name");
            $stmt->execute([$class_id, $current_academic_year['id']]);
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Initialize assignments array if query returns false
            if (!$assignments) {
                $assignments = [];
            }
        } catch (PDOException $e) {
            $error = "Error removing assignment: " . $e->getMessage();
        }
    }
    
    // Bulk assign teachers
    if (isset($_POST['bulk_assign'])) {
        $teacher_id = (int)$_POST['bulk_teacher_id'];
        $subject_ids = isset($_POST['bulk_subjects']) ? $_POST['bulk_subjects'] : [];
        
        if (!$teacher_id || empty($subject_ids)) {
            $error = "Please select a teacher and at least one subject.";
        } else if (!isset($current_academic_year['id']) || $current_academic_year['id'] <= 0) {
            $error = "No valid academic year found. Cannot assign teachers without an academic year.";
        } else {
            try {
                // Verify that the academic_year_id exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM academic_years WHERE id = ?");
                $stmt->execute([$current_academic_year['id']]);
                if ($stmt->fetchColumn() == 0) {
                    $error = "The selected academic year does not exist in the database.";
                } else {
                    $pdo->beginTransaction();
                    
                    foreach ($subject_ids as $subject_id) {
                        // Check if assignment already exists
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM class_subject_teachers 
                                              WHERE class_id = ? AND subject_id = ? AND academic_year_id = ?");
                        $stmt->execute([$class_id, $subject_id, $current_academic_year['id']]);
                        
                        if ($stmt->fetchColumn() > 0) {
                            // Update existing assignment
                            $stmt = $pdo->prepare("UPDATE class_subject_teachers 
                                                  SET teacher_id = ?, assigned_date = CURDATE() 
                                                  WHERE class_id = ? AND subject_id = ? AND academic_year_id = ?");
                            $stmt->execute([$teacher_id, $class_id, $subject_id, $current_academic_year['id']]);
                        } else {
                            // Create new assignment
                            $stmt = $pdo->prepare("INSERT INTO class_subject_teachers 
                                                  (class_id, subject_id, teacher_id, academic_year_id, assigned_date) 
                                                  VALUES (?, ?, ?, ?, CURDATE())");
                            $stmt->execute([$class_id, $subject_id, $teacher_id, $current_academic_year['id']]);
                        }
                    }
                    
                    $pdo->commit();
                    $msg = "Bulk teacher assignment completed successfully.";
                    
                    // Log the activity
                    logActivity($pdo, 'bulk_teacher_assigned', 'class_subject_teachers', $class_id);
                    
                    // Refresh assignments list
                    $stmt = $pdo->prepare("SELECT cst.*, s.subject_name, s.subject_code, 
                                          u.first_name, u.last_name, u.email
                                          FROM class_subject_teachers cst
                                          JOIN subjects s ON cst.subject_id = s.id
                                          JOIN users u ON cst.teacher_id = u.id
                                          WHERE cst.class_id = ? AND cst.academic_year_id = ? AND cst.is_active = 1
                                          ORDER BY s.subject_name");
                    $stmt->execute([$class_id, $current_academic_year['id']]);
                    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Initialize assignments array if query returns false
                    if (!$assignments) {
                        $assignments = [];
                    }
                }
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = "Error in bulk assignment: " . $e->getMessage();
            }
        }
    }
}

?>
