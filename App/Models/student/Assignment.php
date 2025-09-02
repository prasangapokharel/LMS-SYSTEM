<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Get student's class
$stmt = $pdo->prepare("SELECT class_id FROM student_classes WHERE student_id = ? AND status = 'enrolled' LIMIT 1");
$stmt->execute([$student['id']]);
$student_class = $stmt->fetch(PDO::FETCH_ASSOC);

// Filter parameters
$status_filter = $_GET['status'] ?? 'all';
$subject_filter = $_GET['subject'] ?? 'all';

// Build query based on filters
$where_conditions = ["a.class_id = ?"];
$params = [$student_class['class_id'] ?? 0];

if ($status_filter === 'pending') {
    $where_conditions[] = "sub.id IS NULL AND a.due_date >= CURDATE()";
} elseif ($status_filter === 'overdue') {
    $where_conditions[] = "sub.id IS NULL AND a.due_date < CURDATE()";
} elseif ($status_filter === 'submitted') {
    $where_conditions[] = "sub.id IS NOT NULL";
} elseif ($status_filter === 'graded') {
    $where_conditions[] = "sub.grade IS NOT NULL";
}

if ($subject_filter !== 'all') {
    $where_conditions[] = "a.subject_id = ?";
    $params[] = $subject_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get assignments
$stmt = $pdo->prepare("SELECT a.*, s.subject_name, u.first_name, u.last_name,
                      sub.id as submission_id, sub.grade, sub.feedback, sub.submission_date,
                      CASE 
                          WHEN sub.grade IS NOT NULL THEN 'graded'
                          WHEN sub.id IS NOT NULL THEN 'submitted'
                          WHEN a.due_date < CURDATE() THEN 'overdue'
                          ELSE 'pending'
                      END as assignment_status
                      FROM assignments a
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN users u ON a.teacher_id = u.id
                      LEFT JOIN assignment_submissions sub ON a.id = sub.assignment_id AND sub.student_id = ?
                      WHERE $where_clause AND a.is_active = 1
                      ORDER BY a.due_date ASC");

array_unshift($params, $student['id']);
$stmt->execute($params);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get subjects for filter
$stmt = $pdo->prepare("SELECT DISTINCT s.id, s.subject_name 
                      FROM subjects s 
                      JOIN assignments a ON s.id = a.subject_id 
                      WHERE a.class_id = ? AND a.is_active = 1");
$stmt->execute([$student_class['class_id'] ?? 0]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get assignment statistics
$stats = [
    'total' => count($assignments),
    'pending' => count(array_filter($assignments, fn($a) => $a['assignment_status'] === 'pending')),
    'submitted' => count(array_filter($assignments, fn($a) => $a['assignment_status'] === 'submitted')),
    'graded' => count(array_filter($assignments, fn($a) => $a['assignment_status'] === 'graded')),
    'overdue' => count(array_filter($assignments, fn($a) => $a['assignment_status'] === 'overdue'))
];
?>
