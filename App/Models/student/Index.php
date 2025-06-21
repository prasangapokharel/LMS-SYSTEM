<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
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

// Get recent notices (limit 5)
$stmt = $pdo->prepare("
    SELECT n.*, u.first_name, u.last_name,
           CASE 
               WHEN TIMESTAMPDIFF(MINUTE, n.created_at, NOW()) < 60 THEN CONCAT(TIMESTAMPDIFF(MINUTE, n.created_at, NOW()), ' min ago')
               WHEN TIMESTAMPDIFF(HOUR, n.created_at, NOW()) < 24 THEN CONCAT(TIMESTAMPDIFF(HOUR, n.created_at, NOW()), ' hr ago')
               WHEN TIMESTAMPDIFF(DAY, n.created_at, NOW()) < 7 THEN CONCAT(TIMESTAMPDIFF(DAY, n.created_at, NOW()), ' days ago')
               ELSE DATE_FORMAT(n.created_at, '%M %d, %Y')
           END as time_ago,
           COALESCE(n.notice_image, '') as notice_image
    FROM notices n
    JOIN users u ON n.created_by = u.id
    WHERE n.is_active = 1
    ORDER BY n.created_at DESC
    LIMIT 5
");
$stmt->execute();
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get upcoming events (limit 5)
$stmt = $pdo->prepare("
    SELECT e.*, u.first_name, u.last_name, c.class_name, c.section, s.subject_name,
           CASE 
               WHEN e.start_date = CURDATE() THEN 'Today'
               WHEN e.start_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY) THEN 'Tomorrow'
               WHEN TIMESTAMPDIFF(DAY, CURDATE(), e.start_date) <= 7 THEN CONCAT(TIMESTAMPDIFF(DAY, CURDATE(), e.start_date), ' days')
               ELSE DATE_FORMAT(e.start_date, '%M %d')
           END as time_until
    FROM events e
    JOIN users u ON e.created_by = u.id
    LEFT JOIN classes c ON e.class_id = c.id
    LEFT JOIN subjects s ON e.subject_id = s.id
    WHERE (e.is_public = 1 OR e.class_id = ? OR e.class_id IS NULL)
    AND e.start_date >= CURDATE()
    ORDER BY e.start_date ASC, e.start_time ASC
    LIMIT 5
");
$stmt->execute([$class_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>
