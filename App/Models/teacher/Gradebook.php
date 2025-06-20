<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get class and subject from URL
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

// Get teacher's classes for dropdown
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section, s.id as subject_id, s.subject_name
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      JOIN subjects s ON cst.subject_id = s.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$teacher_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables
$course_info = null;
$students = [];
$assignments = [];
$grades = [];
$student_averages = [];

// If class and subject are selected, validate and get data
if ($class_id && $subject_id) {
    // Validate teacher access
    $stmt = $pdo->prepare("SELECT c.*, s.subject_name, s.subject_code
                          FROM classes c
                          JOIN class_subject_teachers cst ON c.id = cst.class_id
                          JOIN subjects s ON cst.subject_id = s.id
                          WHERE cst.teacher_id = ? AND c.id = ? AND s.id = ? AND cst.is_active = 1");
    $stmt->execute([$user['id'], $class_id, $subject_id]);
    $course_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course_info) {
        // Get students in the class
        $stmt = $pdo->prepare("SELECT s.id, u.first_name, u.last_name, s.student_id
                              FROM students s
                              JOIN users u ON s.user_id = u.id
                              JOIN student_classes sc ON s.id = sc.student_id
                              WHERE sc.class_id = ? AND sc.status = 'enrolled' AND u.is_active = 1
                              ORDER BY u.first_name, u.last_name");
        $stmt->execute([$class_id]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get assignments for this class and subject
        $stmt = $pdo->prepare("SELECT id, title, max_marks, due_date, assignment_type
                              FROM assignments
                              WHERE class_id = ? AND subject_id = ? AND teacher_id = ? AND is_active = 1
                              ORDER BY due_date DESC");
        $stmt->execute([$class_id, $subject_id, $user['id']]);
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get grades for all students and assignments
        if (!empty($students) && !empty($assignments)) {
            $assignment_ids = array_column($assignments, 'id');
            $student_ids = array_column($students, 'id');
            
            $placeholders_assignments = str_repeat('?,', count($assignment_ids) - 1) . '?';
            $placeholders_students = str_repeat('?,', count($student_ids) - 1) . '?';
            
            $stmt = $pdo->prepare("SELECT asub.assignment_id, asub.student_id, asub.grade, asub.status
                                  FROM assignment_submissions asub
                                  WHERE asub.assignment_id IN ($placeholders_assignments)
                                  AND asub.student_id IN ($placeholders_students)");
            $stmt->execute(array_merge($assignment_ids, $student_ids));
            $grade_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($grade_results as $grade) {
                $grades[$grade['student_id']][$grade['assignment_id']] = $grade;
            }
        }

        // Calculate student averages
        foreach ($students as $student) {
            $total_points = 0;
            $max_points = 0;
            $graded_assignments = 0;
            
            foreach ($assignments as $assignment) {
                if (isset($grades[$student['id']][$assignment['id']])) {
                    $grade_data = $grades[$student['id']][$assignment['id']];
                    if ($grade_data['grade'] !== null) {
                        $total_points += $grade_data['grade'];
                        $max_points += $assignment['max_marks'];
                        $graded_assignments++;
                    }
                }
            }
            
            $average = $max_points > 0 ? round(($total_points / $max_points) * 100, 2) : 0;
            $student_averages[$student['id']] = [
                'average' => $average,
                'total_points' => $total_points,
                'max_points' => $max_points,
                'graded_assignments' => $graded_assignments
            ];
        }
    }
}

// Handle grade updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_grade') {
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_POST['student_id'];
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'] ?? '';
    
    try {
        // Check if submission exists
        $stmt = $pdo->prepare("SELECT id FROM assignment_submissions 
                              WHERE assignment_id = ? AND student_id = ?");
        $stmt->execute([$assignment_id, $student_id]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($submission) {
            // Update existing submission
            $stmt = $pdo->prepare("UPDATE assignment_submissions 
                                  SET grade = ?, feedback = ?, graded_by = ?, graded_at = NOW(), status = 'graded'
                                  WHERE assignment_id = ? AND student_id = ?");
            $stmt->execute([$grade, $feedback, $user['id'], $assignment_id, $student_id]);
        } else {
            // Create new submission record
            $stmt = $pdo->prepare("INSERT INTO assignment_submissions 
                                  (assignment_id, student_id, grade, feedback, graded_by, graded_at, status)
                                  VALUES (?, ?, ?, ?, ?, NOW(), 'graded')");
            $stmt->execute([$assignment_id, $student_id, $grade, $feedback, $user['id']]);
        }
        
        $msg = "<div class='alert alert-success'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                        <path d='M9 12l2 2 4-4'/>
                        <circle cx='12' cy='12' r='10'/>
                    </svg>
                    Grade updated successfully!
                </div>";
        
        // Refresh the page to show updated grades
        header("Location: gradebook.php?class_id=$class_id&subject_id=$subject_id");
        exit;
        
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                        <circle cx='12' cy='12' r='10'/>
                        <line x1='15' y1='9' x2='9' y2='15'/>
                        <line x1='9' y1='9' x2='15' y2='15'/>
                    </svg>
                    Error updating grade: " . htmlspecialchars($e->getMessage()) . "
                </div>";
    }
}
?>
