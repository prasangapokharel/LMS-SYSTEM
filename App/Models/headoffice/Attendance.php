<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);

// Get filter parameters
$class_filter = $_GET['class_id'] ?? '';
$date_from = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
$date_to = $_GET['date_to'] ?? date('Y-m-d'); // Today
$student_filter = $_GET['student_id'] ?? '';

// Get all classes for filter
$stmt = $pdo->query("SELECT id, class_name, section FROM classes WHERE is_active = 1 ORDER BY class_level, section");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build attendance query
$where_conditions = ["a.attendance_date BETWEEN ? AND ?"];
$params = [$date_from, $date_to];

if ($class_filter) {
    $where_conditions[] = "a.class_id = ?";
    $params[] = $class_filter;
}

if ($student_filter) {
    $where_conditions[] = "s.id = ?";
    $params[] = $student_filter;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get attendance data
$stmt = $pdo->prepare("SELECT s.student_id, u.first_name, u.last_name, c.class_name, c.section,
                      COUNT(*) as total_days,
                      SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                      SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                      SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                      SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_days,
                      ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
                      FROM attendance a
                      JOIN students s ON a.student_id = s.id
                      JOIN users u ON s.user_id = u.id
                      JOIN classes c ON a.class_id = c.id
                      $where_clause
                      GROUP BY s.id, u.first_name, u.last_name, c.class_name, c.section
                      ORDER BY c.class_name, c.section, u.first_name, u.last_name");
$stmt->execute($params);
$attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get overall statistics
$stmt = $pdo->prepare("SELECT 
                      COUNT(DISTINCT s.id) as total_students,
                      COUNT(*) as total_records,
                      SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present,
                      SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as total_absent,
                      SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as total_late,
                      ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as overall_percentage
                      FROM attendance a
                      JOIN students s ON a.student_id = s.id
                      JOIN classes c ON a.class_id = c.id
                      $where_clause");
$stmt->execute($params);
$overall_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get class-wise statistics
$stmt = $pdo->prepare("SELECT c.class_name, c.section,
                      COUNT(DISTINCT s.id) as class_students,
                      COUNT(*) as class_records,
                      SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as class_present,
                      ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as class_percentage
                      FROM attendance a
                      JOIN students s ON a.student_id = s.id
                      JOIN classes c ON a.class_id = c.id
                      $where_clause
                      GROUP BY c.id, c.class_name, c.section
                      ORDER BY c.class_name, c.section");
$stmt->execute($params);
$class_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../include/sidebar.php';
?>
