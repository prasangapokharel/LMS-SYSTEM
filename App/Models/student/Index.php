<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Get student's attendance
$stmt = $pdo->prepare("SELECT COUNT(*) as total, 
                      SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present 
                      FROM attendance WHERE student_id = ?");
$stmt->execute([$student['id']]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

$attendance_percentage = $attendance['total'] > 0 ? 
    round(($attendance['present'] / $attendance['total']) * 100) : 0;

// Get student's upcoming assignments
$stmt = $pdo->prepare("SELECT a.*, s.subject_name FROM assignments a 
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN student_classes sc ON s.class_id = sc.class_id
                      WHERE sc.student_id = ? AND a.due_date >= CURDATE()
                      ORDER BY a.due_date ASC LIMIT 5");
$stmt->execute([$student['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent grades
$stmt = $pdo->prepare("SELECT ag.*, a.title as assignment_title, s.subject_name 
                      FROM assignment_grades ag
                      JOIN assignments a ON ag.assignment_id = a.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE ag.student_id = ?
                      ORDER BY ag.created_at DESC LIMIT 3");
$stmt->execute([$student['id']]);
$recent_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get today's schedule
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT s.subject_name, sch.start_time, sch.end_time, c.class_name, t.first_name, t.last_name
                      FROM schedules sch
                      JOIN subjects s ON sch.subject_id = s.id
                      JOIN classes c ON s.class_id = c.id
                      JOIN users t ON s.teacher_id = t.id
                      JOIN student_classes sc ON c.id = sc.class_id
                      WHERE sc.student_id = ? AND sch.day_of_week = DAYNAME(?)
                      ORDER BY sch.start_time ASC");
$stmt->execute([$student['id'], $today]);
$today_schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
