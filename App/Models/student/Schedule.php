<?php
include '../include/loader.php';
requireRole('student');

$user = getCurrentUser($pdo);

// Get student's class information
$stmt = $pdo->prepare("SELECT sc.class_id, c.class_name, c.section
                      FROM students s
                      JOIN student_classes sc ON s.id = sc.student_id
                      JOIN classes c ON sc.class_id = c.id
                      WHERE s.user_id = ? AND sc.status = 'enrolled'
                      LIMIT 1");
$stmt->execute([$user['id']]);
$student_class = $stmt->fetch(PDO::FETCH_ASSOC);
$class_id = $student_class ? $student_class['class_id'] : null;

// Get exam schedules for student's class - prioritize nearest exams
$query = "SELECT e.*, 
                 COUNT(es.id) as subject_count,
                 MIN(es.exam_date) as first_exam_date,
                 MAX(es.exam_date) as last_exam_date,
                 CASE 
                     WHEN DATEDIFF(e.exam_date_start, CURDATE()) BETWEEN 0 AND 7 THEN 0
                     ELSE 1
                 END as priority_order
          FROM exams e
          LEFT JOIN exam_subjects es ON e.id = es.exam_id
          WHERE (e.status IN ('scheduled', 'completed') AND e.class_id = ?)
          GROUP BY e.id
          ORDER BY priority_order ASC, e.exam_date_start ASC";

$stmt = $pdo->prepare($query);
$stmt->execute([$class_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get detailed schedule for each exam
$exam_schedules = [];
foreach ($exams as $exam) {
    $stmt = $pdo->prepare("SELECT es.*, s.subject_name, s.subject_code
                          FROM exam_subjects es
                          JOIN subjects s ON es.subject_id = s.id
                          WHERE es.exam_id = ?
                          ORDER BY es.exam_date ASC, es.exam_time ASC");
    $stmt->execute([$exam['id']]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $exam_schedules[] = [
        'exam' => $exam,
        'subjects' => $subjects
    ];
}

// Function to check if exam is nearest (within next 7 days)
function isNearestExam($exam_date) {
    $today = new DateTime();
    $exam = new DateTime($exam_date);
    $diff = $today->diff($exam);
    
    return ($exam >= $today && $diff->days <= 7);
}

// Function to format date
function formatScheduleDate($date) {
    return date('M j, Y', strtotime($date));
}

// Function to format time
function formatScheduleTime($time) {
    return $time ? date('g:i A', strtotime($time)) : '';
}

// Function to get days until exam
function getDaysUntilExam($date) {
    $today = new DateTime();
    $exam_date = new DateTime($date);
    $diff = $today->diff($exam_date);
    
    if ($exam_date < $today) {
        return 'Completed';
    } elseif ($diff->days == 0) {
        return 'Today';
    } elseif ($diff->days == 1) {
        return 'Tomorrow';
    } else {
        return $diff->days . ' days left';
    }
}

// Function to get exam status color
function getExamStatusColor($date) {
    $today = new DateTime();
    $exam_date = new DateTime($date);
    $diff = $today->diff($exam_date);
    
    if ($exam_date < $today) {
        return '#6b7280'; // Grey for completed
    } elseif ($diff->days == 0) {
        return '#ef4444'; // Red for today
    } elseif ($diff->days <= 3) {
        return '#f59e0b'; // Orange for very soon
    } elseif ($diff->days <= 7) {
        return '#2563eb'; // Blue for upcoming
    } else {
        return '#10b981'; // Green for future
    }
}
?>
