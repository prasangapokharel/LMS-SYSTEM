<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher's classes
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section 
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, c.section");
$stmt->execute([$user['id']]);
$teacher_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get subjects for selected class
$subjects = [];
$selected_class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
if ($selected_class_id) {
    $stmt = $pdo->prepare("SELECT s.id, s.subject_name, s.subject_code
                          FROM subjects s
                          JOIN class_subject_teachers cst ON s.id = cst.subject_id
                          WHERE cst.class_id = ? AND cst.teacher_id = ? AND cst.is_active = 1
                          ORDER BY s.subject_name");
    $stmt->execute([$selected_class_id, $user['id']]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get exams created by this teacher
$stmt = $pdo->prepare("SELECT e.*, c.class_name, c.section,
                      COUNT(es.id) as subject_count,
                      COUNT(DISTINCT er.student_id) as students_with_results
                      FROM exams e
                      JOIN classes c ON e.class_id = c.id
                      LEFT JOIN exam_subjects es ON e.id = es.exam_id
                      LEFT JOIN exam_results er ON e.id = er.exam_id
                      WHERE e.created_by = ?
                      GROUP BY e.id
                      ORDER BY e.created_at DESC");
$stmt->execute([$user['id']]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle exam creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_exam') {
    $exam_name = trim($_POST['exam_name']);
    $exam_type = $_POST['exam_type'];
    $class_id = intval($_POST['class_id']);
    $academic_year = trim($_POST['academic_year']);
    $exam_date_start = $_POST['exam_date_start'];
    $exam_date_end = $_POST['exam_date_end'];
    $total_marks = intval($_POST['total_marks']);
    $pass_marks = intval($_POST['pass_marks']);
    $instructions = trim($_POST['instructions']);
    $selected_subjects = $_POST['subjects'] ?? [];
    
    try {
        $pdo->beginTransaction();
        
        // Create exam
        $stmt = $pdo->prepare("INSERT INTO exams (exam_name, exam_type, class_id, academic_year, 
                              exam_date_start, exam_date_end, total_marks, pass_marks, 
                              created_by, instructions) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$exam_name, $exam_type, $class_id, $academic_year, 
                       $exam_date_start, $exam_date_end, $total_marks, $pass_marks, 
                       $user['id'], $instructions]);
        
        $exam_id = $pdo->lastInsertId();
        
        // Add subjects to exam
        foreach ($selected_subjects as $subject_id) {
            $subject_marks = intval($_POST['subject_marks_' . $subject_id] ?? $total_marks);
            $subject_pass = intval($_POST['subject_pass_' . $subject_id] ?? $pass_marks);
            $exam_date = $_POST['subject_date_' . $subject_id] ?? $exam_date_start;
            $exam_time = $_POST['subject_time_' . $subject_id] ?? '10:00';
            
            $stmt = $pdo->prepare("INSERT INTO exam_subjects (exam_id, subject_id, full_marks, 
                                  pass_marks, exam_date, exam_time) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$exam_id, $subject_id, $subject_marks, $subject_pass, $exam_date, $exam_time]);
        }
        
        $pdo->commit();
        $msg = "<div class='alert alert-success'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                        <path d='M9 12l2 2 4-4'/>
                        <circle cx='12' cy='12' r='10'/>
                    </svg>
                    Exam created successfully!
                </div>";
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $msg = "<div class='alert alert-danger'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                        <circle cx='12' cy='12' r='10'/>
                        <line x1='15' y1='9' x2='9' y2='15'/>
                        <line x1='9' y1='9' x2='15' y2='15'/>
                    </svg>
                    Error creating exam: " . htmlspecialchars($e->getMessage()) . "
                </div>";
    }
}

// Handle result entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'enter_results') {
    $exam_id = intval($_POST['exam_id']);
    $results = $_POST['results'] ?? [];
    
    try {
        $pdo->beginTransaction();
        
        foreach ($results as $student_id => $student_results) {
            foreach ($student_results as $subject_id => $marks) {
                if ($marks !== '' && is_numeric($marks)) {
                    $marks_obtained = floatval($marks);
                    
                    // Get full marks for this subject
                    $stmt = $pdo->prepare("SELECT full_marks FROM exam_subjects 
                                          WHERE exam_id = ? AND subject_id = ?");
                    $stmt->execute([$exam_id, $subject_id]);
                    $subject_info = $stmt->fetch(PDO::FETCH_ASSOC);
                    $full_marks = $subject_info['full_marks'];
                    
                    // Calculate percentage, grade, and GPA
                    $percentage = ($marks_obtained / $full_marks) * 100;
                    $grade = calculateGrade($percentage);
                    $gpa = calculateGPA($percentage);
                    $remarks = getRemarks($percentage);
                    
                    // Insert or update result
                    $stmt = $pdo->prepare("INSERT INTO exam_results 
                                          (exam_id, student_id, subject_id, full_marks, marks_obtained, 
                                           grade, gpa, percentage, remarks, entered_by)
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                          ON DUPLICATE KEY UPDATE
                                          marks_obtained = VALUES(marks_obtained),
                                          grade = VALUES(grade),
                                          gpa = VALUES(gpa),
                                          percentage = VALUES(percentage),
                                          remarks = VALUES(remarks),
                                          entered_by = VALUES(entered_by)");
                    $stmt->execute([$exam_id, $student_id, $subject_id, $full_marks, 
                                   $marks_obtained, $grade, $gpa, $percentage, $remarks, $user['id']]);
                }
            }
        }
        
        $pdo->commit();
        $msg = "<div class='alert alert-success'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                        <path d='M9 12l2 2 4-4'/>
                        <circle cx='12' cy='12' r='10'/>
                    </svg>
                    Results entered successfully!
                </div>";
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $msg = "<div class='alert alert-danger'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                        <circle cx='12' cy='12' r='10'/>
                        <line x1='15' y1='9' x2='9' y2='15'/>
                        <line x1='9' y1='9' x2='15' y2='15'/>
                    </svg>
                    Error entering results: " . htmlspecialchars($e->getMessage()) . "
                </div>";
    }
}

// Helper functions
function calculateGrade($percentage) {
    if ($percentage >= 90) return 'A+';
    elseif ($percentage >= 80) return 'A';
    elseif ($percentage >= 70) return 'A-';
    elseif ($percentage >= 60) return 'B+';
    elseif ($percentage >= 50) return 'B';
    elseif ($percentage >= 40) return 'B-';
    elseif ($percentage >= 30) return 'C+';
    elseif ($percentage >= 20) return 'C';
    else return 'F';
}

function calculateGPA($percentage) {
    if ($percentage >= 90) return 4.00;
    elseif ($percentage >= 80) return 3.60;
    elseif ($percentage >= 70) return 3.20;
    elseif ($percentage >= 60) return 2.80;
    elseif ($percentage >= 50) return 2.40;
    elseif ($percentage >= 40) return 2.00;
    elseif ($percentage >= 30) return 1.60;
    elseif ($percentage >= 20) return 1.20;
    else return 0.00;
}

function getRemarks($percentage) {
    if ($percentage >= 90) return 'Outstanding';
    elseif ($percentage >= 80) return 'Excellent';
    elseif ($percentage >= 70) return 'Very Good';
    elseif ($percentage >= 60) return 'Good';
    elseif ($percentage >= 50) return 'Satisfactory';
    elseif ($percentage >= 40) return 'Pass';
    else return 'Fail';
}

// Get exam details for result entry
$exam_details = null;
$exam_students = [];
$exam_subjects_list = [];
$existing_results = [];

if (isset($_GET['exam_id']) && $_GET['action'] == 'enter_results') {
    $exam_id = intval($_GET['exam_id']);
    
    // Get exam details
    $stmt = $pdo->prepare("SELECT e.*, c.class_name, c.section
                          FROM exams e
                          JOIN classes c ON e.class_id = c.id
                          WHERE e.id = ? AND e.created_by = ?");
    $stmt->execute([$exam_id, $user['id']]);
    $exam_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($exam_details) {
        // Get students in this class
        $stmt = $pdo->prepare("SELECT s.id, u.first_name, u.last_name, s.student_id
                              FROM students s
                              JOIN users u ON s.user_id = u.id
                              JOIN student_classes sc ON s.id = sc.student_id
                              WHERE sc.class_id = ? AND sc.status = 'enrolled' AND u.is_active = 1
                              ORDER BY u.first_name, u.last_name");
        $stmt->execute([$exam_details['class_id']]);
        $exam_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get subjects for this exam
        $stmt = $pdo->prepare("SELECT es.*, s.subject_name, s.subject_code
                              FROM exam_subjects es
                              JOIN subjects s ON es.subject_id = s.id
                              WHERE es.exam_id = ?
                              ORDER BY s.subject_name");
        $stmt->execute([$exam_id]);
        $exam_subjects_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get existing results
        $stmt = $pdo->prepare("SELECT student_id, subject_id, marks_obtained
                              FROM exam_results
                              WHERE exam_id = ?");
        $stmt->execute([$exam_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $result) {
            $existing_results[$result['student_id']][$result['subject_id']] = $result['marks_obtained'];
        }
    }
}
?>
