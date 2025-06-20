<?php
include '../include/connect.php';
include '../include/session.php';
requireRole('principal');

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// Initialize cache
$cache = new FilesystemAdapter('attendance_reports', 3600, __DIR__ . '/../cache');

$user = getCurrentUser($pdo);

// Sanitize filter parameters - Updated sanitization methods
$class_filter = isset($_GET['class_id']) ? (int)$_GET['class_id'] : '';
$date_from = isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : date('Y-m-d');
$student_filter = isset($_GET['student_id']) ? (int)$_GET['student_id'] : '';

// Validate date format and range
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
    $date_from = date('Y-m-01');
    $date_to = date('Y-m-d');
}

if (strtotime($date_from) > strtotime($date_to)) {
    $date_from = date('Y-m-01');
    $date_to = date('Y-m-d');
}

// Get all classes for filter (with caching)
$classesCache = $cache->getItem('active_classes_list');
if (!$classesCache->isHit()) {
    $stmt = $pdo->query("SELECT id, class_name, section FROM classes WHERE is_active = 1 ORDER BY class_level, section");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $classesCache->set($classes)->expiresAfter(86400); // Cache for 24 hours
    $cache->save($classesCache);
} else {
    $classes = $classesCache->get();
}

// Build cache key based on filters
$cacheKey = md5("attendance_report_{$class_filter}_{$date_from}_{$date_to}_{$student_filter}");

// Try to get cached data
$reportCache = $cache->getItem($cacheKey);

if (!$reportCache->isHit()) {
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

    // Get attendance data with improved percentage calculation
    $stmt = $pdo->prepare("SELECT s.id as student_db_id, s.student_id, u.first_name, u.last_name, 
                          c.id as class_id, c.class_name, c.section,
                          COUNT(*) as total_days,
                          SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                          SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                          SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                          SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_days,
                          ROUND((SUM(CASE WHEN a.status IN ('present', 'late', 'half_day') THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
                          FROM attendance a
                          JOIN students s ON a.student_id = s.id
                          JOIN users u ON s.user_id = u.id
                          JOIN classes c ON a.class_id = c.id
                          $where_clause
                          GROUP BY s.id, u.first_name, u.last_name, c.class_name, c.section
                          ORDER BY c.class_name, c.section, u.first_name, u.last_name");
    $stmt->execute($params);
    $attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get overall statistics with more detailed breakdown
    $stmt = $pdo->prepare("SELECT 
                          COUNT(DISTINCT s.id) as total_students,
                          COUNT(*) as total_records,
                          SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present,
                          SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as total_absent,
                          SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as total_late,
                          SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as total_half_day,
                          ROUND((SUM(CASE WHEN a.status IN ('present', 'late', 'half_day') THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as overall_percentage
                          FROM attendance a
                          JOIN students s ON a.student_id = s.id
                          JOIN classes c ON a.class_id = c.id
                          $where_clause");
    $stmt->execute($params);
    $overall_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get class-wise statistics with more details
    $stmt = $pdo->prepare("SELECT c.id as class_id, c.class_name, c.section,
                          COUNT(DISTINCT s.id) as class_students,
                          COUNT(*) as class_records,
                          SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as class_present,
                          SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as class_absent,
                          SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as class_late,
                          SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as class_half_day,
                          ROUND((SUM(CASE WHEN a.status IN ('present', 'late', 'half_day') THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as class_percentage
                          FROM attendance a
                          JOIN students s ON a.student_id = s.id
                          JOIN classes c ON a.class_id = c.id
                          $where_clause
                          GROUP BY c.id, c.class_name, c.section
                          ORDER BY c.class_name, c.section");
    $stmt->execute($params);
    $class_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cache the results for 1 hour
    $reportData = [
        'attendance_data' => $attendance_data,
        'overall_stats' => $overall_stats,
        'class_stats' => $class_stats
    ];
    $reportCache->set($reportData)->expiresAfter(3600);
    $cache->save($reportCache);
} else {
    $reportData = $reportCache->get();
    $attendance_data = $reportData['attendance_data'];
    $overall_stats = $reportData['overall_stats'];
    $class_stats = $reportData['class_stats'];
}

// Note: getAttendanceColor() function is intentionally not redeclared here
// as it's already available from include/loader.php
?>