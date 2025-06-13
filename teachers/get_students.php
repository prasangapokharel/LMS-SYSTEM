<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

header('Content-Type: application/json');

if (!isset($_GET['class_id']) || !isset($_GET['date'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$class_id = $_GET['class_id'];
$date = $_GET['date'];

// Verify that this class is taught by the current teacher
$stmt = $pdo->prepare("SELECT COUNT(*) FROM class_subject_teachers 
                      WHERE class_id = ? AND teacher_id = ? AND is_active = 1");
$stmt->execute([$class_id, $_SESSION['user_id']]);
if ($stmt->fetchColumn() == 0) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get students for this class with existing attendance
$stmt = $pdo->prepare("SELECT s.id, u.first_name, u.last_name, s.student_id,
                      a.status, a.remarks
                      FROM students s
                      JOIN users u ON s.user_id = u.id
                      JOIN student_enrollments se ON s.id = se.student_id
                      LEFT JOIN attendance a ON s.id = a.student_id 
                          AND a.class_id = ? AND a.attendance_date = ? AND a.teacher_id = ?
                      WHERE se.class_id = ? AND se.status = 'enrolled'
                      ORDER BY s.student_id");
$stmt->execute([$class_id, $date, $_SESSION['user_id'], $class_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($students);
?>
