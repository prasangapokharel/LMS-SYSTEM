<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get current date
$current_date = date('Y-m-d');
$current_nepali_date = date('F d, Y');

// Get teacher's classes and subjects
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section, s.subject_name, s.id as subject_id
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      JOIN subjects s ON cst.subject_id = s.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total students count
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT sc.student_id) as total
                      FROM student_classes sc
                      JOIN class_subject_teachers cst ON sc.class_id = cst.class_id
                      WHERE cst.teacher_id = ? AND sc.status = 'enrolled' AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$total_students = $stmt->fetchColumn();

// Get assignments count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments WHERE teacher_id = ? AND is_active = 1");
$stmt->execute([$user['id']]);
$assignment_count = $stmt->fetchColumn();

// Get teaching logs count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM teacher_logs WHERE teacher_id = ?");
$stmt->execute([$user['id']]);
$logs_count = $stmt->fetchColumn();

// Get today's attendance summary
$attendance_summary = [];
$stmt = $pdo->prepare("SELECT c.class_name, c.section, s.subject_name,
                      COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present,
                      COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent,
                      COUNT(*) as total
                      FROM attendance a
                      JOIN classes c ON a.class_id = c.id
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      JOIN subjects s ON cst.subject_id = s.id
                      WHERE a.teacher_id = ? AND a.attendance_date = ? AND cst.teacher_id = ?
                      GROUP BY c.id, s.id");
$stmt->execute([$user['id'], $current_date, $user['id']]);
$attendance_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent assignments
$stmt = $pdo->prepare("SELECT a.id, a.title, a.due_date, c.class_name, s.subject_name
                      FROM assignments a
                      JOIN classes c ON a.class_id = c.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE a.teacher_id = ? AND a.is_active = 1
                      ORDER BY a.created_at DESC
                      LIMIT 5");
$stmt->execute([$user['id']]);
$recent_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get pending leave applications
$stmt = $pdo->prepare("SELECT la.*, u.first_name, u.last_name, s.student_id
                      FROM leave_applications la
                      JOIN users u ON la.user_id = u.id
                      JOIN students s ON u.id = s.user_id
                      JOIN student_classes sc ON s.id = sc.student_id
                      JOIN class_subject_teachers cst ON sc.class_id = cst.class_id
                      WHERE cst.teacher_id = ? AND la.status = 'pending' AND la.user_type = 'student'
                      ORDER BY la.applied_date DESC
                      LIMIT 5");
$stmt->execute([$user['id']]);
$pending_leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>