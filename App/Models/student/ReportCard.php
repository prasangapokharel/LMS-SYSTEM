<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('student');

$user = getCurrentUser($pdo);
$msg = "";

// Get student info
$stmt = $pdo->prepare("SELECT s.*, u.first_name, u.last_name, u.email, c.class_name, c.section
                      FROM students s
                      JOIN users u ON s.user_id = u.id
                      JOIN student_classes sc ON s.id = sc.student_id
                      JOIN classes c ON sc.class_id = c.id
                      WHERE s.user_id = ? AND sc.status = 'enrolled'");
$stmt->execute([$user['id']]);
$student_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student_info) {
    header('Location: ../login.php');
    exit;
}

// Get available exams for this student's class
$stmt = $pdo->prepare("SELECT e.*, COUNT(er.id) as has_results
                      FROM exams e
                      LEFT JOIN exam_results er ON e.id = er.exam_id AND er.student_id = ?
                      WHERE e.class_id = (SELECT class_id FROM student_classes WHERE student_id = ? AND status = 'enrolled')
                      AND e.status = 'completed'
                      GROUP BY e.id
                      HAVING has_results > 0
                      ORDER BY e.exam_date_start DESC");
$stmt->execute([$student_info['id'], $student_info['id']]);
$available_exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle report card generation
$report_data = null;
$selected_exam = null;

if (isset($_GET['exam_id']) && $_GET['action'] == 'generate_report') {
    $exam_id = intval($_GET['exam_id']);
    
    // Get exam details
    $stmt = $pdo->prepare("SELECT e.*, c.class_name, c.section
                          FROM exams e
                          JOIN classes c ON e.class_id = c.id
                          WHERE e.id = ?");
    $stmt->execute([$exam_id]);
    $selected_exam = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($selected_exam) {
        // Get student results for this exam
        $stmt = $pdo->prepare("SELECT er.*, s.subject_name, s.subject_code, es.full_marks
                              FROM exam_results er
                              JOIN subjects s ON er.subject_id = s.id
                              JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
                              WHERE er.exam_id = ? AND er.student_id = ?
                              ORDER BY s.subject_name");
        $stmt->execute([$exam_id, $student_info['id']]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($results)) {
            // Calculate overall statistics
            $total_marks = array_sum(array_column($results, 'full_marks'));
            $total_obtained = array_sum(array_column($results, 'marks_obtained'));
            $overall_percentage = ($total_obtained / $total_marks) * 100;
            $overall_grade = calculateGrade($overall_percentage);
            $overall_gpa = calculateGPA($overall_percentage);
            
            // Calculate position (rank)
            $stmt = $pdo->prepare("SELECT student_id, SUM(marks_obtained) as total_obtained
                                  FROM exam_results
                                  WHERE exam_id = ?
                                  GROUP BY student_id
                                  ORDER BY total_obtained DESC");
            $stmt->execute([$exam_id]);
            $all_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $position = 1;
            foreach ($all_results as $index => $result) {
                if ($result['student_id'] == $student_info['id']) {
                    $position = $index + 1;
                    break;
                }
            }
            
            $report_data = [
                'exam' => $selected_exam,
                'student' => $student_info,
                'results' => $results,
                'total_marks' => $total_marks,
                'total_obtained' => $total_obtained,
                'overall_percentage' => $overall_percentage,
                'overall_grade' => $overall_grade,
                'overall_gpa' => $overall_gpa,
                'position' => $position,
                'total_students' => count($all_results)
            ];
        }
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

function getGradeColor($grade) {
    switch ($grade) {
        case 'A+': return '#059669';
        case 'A': return '#0d9488';
        case 'A-': return '#0891b2';
        case 'B+': return '#0284c7';
        case 'B': return '#2563eb';
        case 'B-': return '#7c3aed';
        case 'C+': return '#c2410c';
        case 'C': return '#dc2626';
        case 'F': return '#991b1b';
        default: return '#6b7280';
    }
}
?>
