<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get class_id from URL parameter
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// Validate that this teacher has access to this class
$stmt = $pdo->prepare("SELECT c.* FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND c.id = ? AND cst.is_active = 1
                      LIMIT 1");
$stmt->execute([$user['id'], $class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class && $class_id > 0) {
    // Redirect to teacher's classes page if they don't have access to this class
    header("Location: index.php");
    exit;
}

// Get teacher's classes if no specific class is selected
if ($class_id == 0) {
    $stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section
                          FROM classes c
                          JOIN class_subject_teachers cst ON c.id = cst.class_id
                          WHERE cst.teacher_id = ? AND cst.is_active = 1
                          ORDER BY c.class_level, c.class_name, c.section");
    $stmt->execute([$user['id']]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($classes) == 1) {
        // If teacher has only one class, redirect to that class's log page
        header("Location: teacher_log.php?class_id=" . $classes[0]['id']);
        exit;
    }
}

// Get subjects for selected class
$subjects = [];
if ($class_id > 0) {
    $stmt = $pdo->prepare("SELECT s.* 
                          FROM subjects s
                          JOIN class_subject_teachers cst ON s.id = cst.subject_id
                          WHERE cst.class_id = ? AND cst.teacher_id = ? AND cst.is_active = 1
                          ORDER BY s.subject_name");
    $stmt->execute([$class_id, $user['id']]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get subject filter
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_log') {
    $subject_id_post = $_POST['subject_id'];
    $log_date = $_POST['log_date'];
    $chapter_title = $_POST['chapter_title'];
    $chapter_content = $_POST['chapter_content'];
    $topics_covered = $_POST['topics_covered'];
    $teaching_method = $_POST['teaching_method'];
    $homework_assigned = $_POST['homework_assigned'];
    $notes = $_POST['notes'];
    $lesson_duration = $_POST['lesson_duration'];
    
    // Validate inputs
    if (empty($subject_id_post) || empty($log_date) || empty($chapter_title) || empty($topics_covered)) {
        $msg = "<div class='alert alert-danger'>
                    <strong>Error!</strong> Please fill in all required fields.
                </div>";
    } else {
        // Get students present count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance 
                              WHERE class_id = ? AND teacher_id = ? AND attendance_date = ? AND status = 'present'");
        $stmt->execute([$class_id, $user['id'], $log_date]);
        $students_present = $stmt->fetchColumn();
        
        // Check if log already exists
        $stmt = $pdo->prepare("SELECT id FROM teacher_logs 
                              WHERE teacher_id = ? AND class_id = ? AND subject_id = ? AND log_date = ?");
        $stmt->execute([$user['id'], $class_id, $subject_id_post, $log_date]);
        $existing_log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        try {
            if ($existing_log) {
                // Update existing log
                $stmt = $pdo->prepare("UPDATE teacher_logs SET 
                                      chapter_title = ?, chapter_content = ?, topics_covered = ?,
                                      teaching_method = ?, homework_assigned = ?, notes = ?,
                                      lesson_duration = ?, students_present = ?, updated_at = NOW()
                                      WHERE id = ?");
                $stmt->execute([
                    $chapter_title, $chapter_content, $topics_covered,
                    $teaching_method, $homework_assigned, $notes,
                    $lesson_duration, $students_present, $existing_log['id']
                ]);
                $msg = "<div class='alert alert-success'>
                            <strong>Success!</strong> Teacher log updated successfully.
                        </div>";
                
                // Log activity
                if (function_exists('logActivity')) {
                    logActivity($pdo, 'update_teacher_log', 'teacher_logs', $existing_log['id']);
                }
            } else {
                // Insert new log
                $stmt = $pdo->prepare("INSERT INTO teacher_logs 
                                      (teacher_id, class_id, subject_id, log_date, chapter_title, 
                                      chapter_content, topics_covered, teaching_method, homework_assigned, 
                                      notes, lesson_duration, students_present)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user['id'], $class_id, $subject_id_post, $log_date, $chapter_title,
                    $chapter_content, $topics_covered, $teaching_method, $homework_assigned,
                    $notes, $lesson_duration, $students_present
                ]);
                
                $log_id = $pdo->lastInsertId();
                $msg = "<div class='alert alert-success'>
                            <strong>Success!</strong> Teacher log added successfully.
                        </div>";
                
                // Log activity
                if (function_exists('logActivity')) {
                    logActivity($pdo, 'create_teacher_log', 'teacher_logs', $log_id);
                }
            }
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>
                        <strong>Error!</strong> " . htmlspecialchars($e->getMessage()) . "
                    </div>";
        }
    }
}

// Get recent logs
$recent_logs = [];
if ($class_id > 0) {
    $where_clause = "tl.teacher_id = ? AND tl.class_id = ?";
    $params = [$user['id'], $class_id];
    
    if ($subject_id > 0) {
        $where_clause .= " AND tl.subject_id = ?";
        $params[] = $subject_id;
    }
    
    $stmt = $pdo->prepare("SELECT tl.*, c.class_name, c.section, s.subject_name
                          FROM teacher_logs tl
                          JOIN classes c ON tl.class_id = c.id
                          JOIN subjects s ON tl.subject_id = s.id
                          WHERE $where_clause
                          ORDER BY tl.log_date DESC
                          LIMIT 20");
    $stmt->execute($params);
    $recent_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get class details for header
$class_details = "";
if ($class) {
    $class_details = htmlspecialchars($class['class_name'] . ' ' . $class['section']);
}

// Get log for editing if edit_id is provided
$edit_log = null;
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM teacher_logs WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$edit_id, $user['id']]);
    $edit_log = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$edit_log) {
        header("Location: teacher_log.php?class_id=$class_id");
        exit;
    }
}

?>