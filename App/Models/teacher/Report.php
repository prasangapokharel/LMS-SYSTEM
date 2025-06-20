<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get date range filters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$report_type = $_GET['report_type'] ?? 'overview';

// Get teacher's classes
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section, c.class_level
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_level, c.class_name, c.section");
$stmt->execute([$user['id']]);
$teacher_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get teacher's subjects
$stmt = $pdo->prepare("SELECT DISTINCT s.id, s.subject_name, s.subject_code
                      FROM subjects s
                      JOIN class_subject_teachers cst ON s.id = cst.subject_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY s.subject_name");
$stmt->execute([$user['id']]);
$teacher_subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Overview Statistics
$overview_stats = [];

// Total students taught
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT sc.student_id) as total_students
                      FROM student_classes sc
                      JOIN class_subject_teachers cst ON sc.class_id = cst.class_id
                      WHERE cst.teacher_id = ? AND sc.status = 'enrolled' AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$overview_stats['total_students'] = $stmt->fetchColumn();

// Total classes conducted
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT DATE(created_at)) as classes_conducted
                      FROM teacher_logs
                      WHERE teacher_id = ? AND created_at BETWEEN ? AND ?");
$stmt->execute([$user['id'], $start_date, $end_date]);
$overview_stats['classes_conducted'] = $stmt->fetchColumn();

// Total assignments created
$stmt = $pdo->prepare("SELECT COUNT(*) as total_assignments
                      FROM assignments
                      WHERE teacher_id = ? AND created_at BETWEEN ? AND ? AND is_active = 1");
$stmt->execute([$user['id'], $start_date, $end_date]);
$overview_stats['total_assignments'] = $stmt->fetchColumn();

// Average attendance rate
$stmt = $pdo->prepare("SELECT 
                      COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count,
                      COUNT(*) as total_attendance
                      FROM attendance
                      WHERE teacher_id = ? AND attendance_date BETWEEN ? AND ?");
$stmt->execute([$user['id'], $start_date, $end_date]);
$attendance_data = $stmt->fetch(PDO::FETCH_ASSOC);
$overview_stats['attendance_rate'] = $attendance_data['total_attendance'] > 0 ? 
    round(($attendance_data['present_count'] / $attendance_data['total_attendance']) * 100, 1) : 0;

// Class-wise performance
$class_performance = [];
foreach ($teacher_classes as $class) {
    $stmt = $pdo->prepare("SELECT 
                          COUNT(DISTINCT tl.id) as logs_count,
                          COUNT(DISTINCT a.id) as assignments_count,
                          AVG(CASE WHEN att.status = 'present' THEN 1 ELSE 0 END) * 100 as avg_attendance
                          FROM classes c
                          LEFT JOIN teacher_logs tl ON c.id = tl.class_id AND tl.teacher_id = ? AND tl.created_at BETWEEN ? AND ?
                          LEFT JOIN assignments a ON c.id = a.class_id AND a.teacher_id = ? AND a.created_at BETWEEN ? AND ?
                          LEFT JOIN attendance att ON c.id = att.class_id AND att.teacher_id = ? AND att.attendance_date BETWEEN ? AND ?
                          WHERE c.id = ?");
    $stmt->execute([$user['id'], $start_date, $end_date, $user['id'], $start_date, $end_date, $user['id'], $start_date, $end_date, $class['id']]);
    $performance = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $class_performance[] = [
        'class_info' => $class,
        'performance' => $performance
    ];
}

// Subject-wise statistics
$subject_stats = [];
foreach ($teacher_subjects as $subject) {
    $stmt = $pdo->prepare("SELECT 
                          COUNT(DISTINCT tl.id) as logs_count,
                          COUNT(DISTINCT a.id) as assignments_count,
                          AVG(asub.grade) as avg_grade
                          FROM subjects s
                          LEFT JOIN teacher_logs tl ON s.id = tl.subject_id AND tl.teacher_id = ? AND tl.created_at BETWEEN ? AND ?
                          LEFT JOIN assignments a ON s.id = a.subject_id AND a.teacher_id = ? AND a.created_at BETWEEN ? AND ?
                          LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.grade IS NOT NULL
                          WHERE s.id = ?");
    $stmt->execute([$user['id'], $start_date, $end_date, $user['id'], $start_date, $end_date, $subject['id']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $subject_stats[] = [
        'subject_info' => $subject,
        'stats' => $stats
    ];
}

// Recent activities
$recent_activities = [];
$stmt = $pdo->prepare("SELECT 'log' as type, tl.created_at, c.class_name, c.section, s.subject_name, tl.chapter_title as title
                      FROM teacher_logs tl
                      JOIN classes c ON tl.class_id = c.id
                      JOIN subjects s ON tl.subject_id = s.id
                      WHERE tl.teacher_id = ?
                      UNION ALL
                      SELECT 'assignment' as type, a.created_at, c.class_name, c.section, s.subject_name, a.title
                      FROM assignments a
                      JOIN classes c ON a.class_id = c.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE a.teacher_id = ? AND a.is_active = 1
                      ORDER BY created_at DESC
                      LIMIT 10");
$stmt->execute([$user['id'], $user['id']]);
$recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly teaching hours
$monthly_hours = [];
$stmt = $pdo->prepare("SELECT 
                      DATE_FORMAT(created_at, '%Y-%m') as month,
                      SUM(lesson_duration) as total_minutes
                      FROM teacher_logs
                      WHERE teacher_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                      GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                      ORDER BY month DESC");
$stmt->execute([$user['id']]);
$monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($monthly_data as $data) {
    $monthly_hours[] = [
        'month' => date('M Y', strtotime($data['month'] . '-01')),
        'hours' => round($data['total_minutes'] / 60, 1)
    ];
}

// Assignment submission rates
$assignment_stats = [];
$stmt = $pdo->prepare("SELECT 
                      a.title,
                      a.due_date,
                      COUNT(DISTINCT sc.student_id) as total_students,
                      COUNT(DISTINCT asub.student_id) as submitted_count,
                      AVG(asub.grade) as avg_grade
                      FROM assignments a
                      JOIN student_classes sc ON a.class_id = sc.class_id AND sc.status = 'enrolled'
                      LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id
                      WHERE a.teacher_id = ? AND a.created_at BETWEEN ? AND ? AND a.is_active = 1
                      GROUP BY a.id
                      ORDER BY a.due_date DESC
                      LIMIT 10");
$stmt->execute([$user['id'], $start_date, $end_date]);
$assignment_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate report data based on type
$report_data = [
    'overview_stats' => $overview_stats,
    'class_performance' => $class_performance,
    'subject_stats' => $subject_stats,
    'recent_activities' => $recent_activities,
    'monthly_hours' => $monthly_hours,
    'assignment_stats' => $assignment_stats,
    'date_range' => ['start' => $start_date, 'end' => $end_date]
];

// Handle report export
if (isset($_POST['action']) && $_POST['action'] == 'export_report') {
    $export_format = $_POST['export_format'];
    
    if ($export_format == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="teacher_report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Overview stats
        fputcsv($output, ['Teacher Report - Overview Statistics']);
        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Total Students', $overview_stats['total_students']]);
        fputcsv($output, ['Classes Conducted', $overview_stats['classes_conducted']]);
        fputcsv($output, ['Total Assignments', $overview_stats['total_assignments']]);
        fputcsv($output, ['Attendance Rate', $overview_stats['attendance_rate'] . '%']);
        fputcsv($output, []);
        
        // Class performance
        fputcsv($output, ['Class Performance']);
        fputcsv($output, ['Class', 'Section', 'Logs', 'Assignments', 'Avg Attendance']);
        foreach ($class_performance as $cp) {
            fputcsv($output, [
                $cp['class_info']['class_name'],
                $cp['class_info']['section'],
                $cp['performance']['logs_count'],
                $cp['performance']['assignments_count'],
                round($cp['performance']['avg_attendance'], 1) . '%'
            ]);
        }
        
        fclose($output);
        exit;
    }
}
?>