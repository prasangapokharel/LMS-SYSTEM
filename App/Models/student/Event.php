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

// Get filter parameters
$event_type = $_GET['type'] ?? 'all';
$date_filter = $_GET['date'] ?? 'all';

// Build events query
$query = "SELECT e.*, u.first_name, u.last_name, c.class_name, c.section, s.subject_name
          FROM events e
          JOIN users u ON e.created_by = u.id
          LEFT JOIN classes c ON e.class_id = c.id
          LEFT JOIN subjects s ON e.subject_id = s.id
          WHERE (e.is_public = 1 OR e.class_id = ? OR e.class_id IS NULL)";

$params = [$class_id];

// Add type filter
if ($event_type !== 'all') {
    $query .= " AND e.event_type = ?";
    $params[] = $event_type;
}

// Add date filter
if ($date_filter === 'upcoming') {
    $query .= " AND e.start_date >= CURDATE()";
} elseif ($date_filter === 'today') {
    $query .= " AND e.start_date = CURDATE()";
} elseif ($date_filter === 'this_week') {
    $query .= " AND e.start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
}

$query .= " ORDER BY e.start_date ASC, e.start_time ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get event statistics for student
$stmt = $pdo->prepare("SELECT 
                      COUNT(*) as total_events,
                      COUNT(CASE WHEN event_type = 'exam' THEN 1 END) as exams,
                      COUNT(CASE WHEN event_type = 'assignment' THEN 1 END) as assignments,
                      COUNT(CASE WHEN start_date >= CURDATE() THEN 1 END) as upcoming_events,
                      COUNT(CASE WHEN start_date = CURDATE() THEN 1 END) as today_events
                      FROM events 
                      WHERE (is_public = 1 OR class_id = ? OR class_id IS NULL)");
$stmt->execute([$class_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Function to get event type icon
function getEventTypeIcon($type) {
    $icons = [
        'class' => '📚',
        'exam' => '📝',
        'assignment' => '📋',
        'holiday' => '🏖️',
        'meeting' => '👥',
        'announcement' => '📢',
        'other' => '📅'
    ];
    return $icons[$type] ?? '📅';
}

// Function to format date
function formatEventDate($date) {
    return date('M j, Y', strtotime($date));
}

// Function to format time
function formatEventTime($time) {
    return $time ? date('g:i A', strtotime($time)) : '';
}

// Function to get days until event
function getDaysUntilEvent($date) {
    $today = new DateTime();
    $event_date = new DateTime($date);
    $diff = $today->diff($event_date);
    
    if ($event_date < $today) {
        return 'Past';
    } elseif ($diff->days == 0) {
        return 'Today';
    } elseif ($diff->days == 1) {
        return 'Tomorrow';
    } else {
        return $diff->days . ' days';
    }
}
?>
