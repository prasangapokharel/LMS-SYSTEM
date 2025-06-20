<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);

// Get teacher's classes
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get class filter
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : '';

// Get students based on filter
if ($class_id) {
    $stmt = $pdo->prepare("SELECT s.*, u.first_name, u.last_name, u.email, u.phone,
                          se.status as enrollment_status,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ? AND a.status = 'present') as present_count,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ?) as total_attendance
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          JOIN student_enrollments se ON s.id = se.student_id
                          WHERE se.class_id = ? AND se.status = 'enrolled'
                          ORDER BY u.first_name, u.last_name");
    $stmt->execute([$user['id'], $user['id'], $class_id]);
} else {
    $stmt = $pdo->prepare("SELECT s.*, u.first_name, u.last_name, u.email, u.phone,
                          c.class_name, c.section,
                          se.status as enrollment_status,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ? AND a.status = 'present') as present_count,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ?) as total_attendance
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          JOIN student_enrollments se ON s.id = se.student_id
                          JOIN classes c ON se.class_id = c.id
                          JOIN class_subject_teachers cst ON c.id = cst.class_id
                          WHERE cst.teacher_id = ? AND cst.is_active = 1 AND se.status = 'enrolled'
                          GROUP BY s.id
                          ORDER BY c.class_name, c.section, u.first_name, u.last_name");
    $stmt->execute([$user['id'], $user['id'], $user['id']]);
}

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>