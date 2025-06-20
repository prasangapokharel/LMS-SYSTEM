<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Get date range (default to current month)
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get attendance records with correct column names
$stmt = $pdo->prepare("SELECT a.*, DATE_FORMAT(a.attendance_date, '%Y-%m-%d') as formatted_date,
                      DAYNAME(a.attendance_date) as day_name, s.subject_name
                      FROM attendance a 
                      LEFT JOIN subjects s ON a.class_id = s.class_id
                      WHERE a.student_id = ? AND a.attendance_date BETWEEN ? AND ?
                      ORDER BY a.attendance_date DESC");
$stmt->execute([$student['id'], $start_date, $end_date]);
$attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$total_days = count($attendance_records);
$present_days = count(array_filter($attendance_records, fn($r) => $r['status'] === 'present'));
$absent_days = count(array_filter($attendance_records, fn($r) => $r['status'] === 'absent'));
$late_days = count(array_filter($attendance_records, fn($r) => $r['status'] === 'late'));

$attendance_percentage = $total_days > 0 ? round(($present_days / $total_days) * 100) : 0;

// Group by month for calendar view
$calendar_data = [];
foreach ($attendance_records as $record) {
    $month = date('Y-m', strtotime($record['attendance_date']));
    $day = date('j', strtotime($record['attendance_date']));
    $calendar_data[$month][$day] = $record['status'];
}
?>