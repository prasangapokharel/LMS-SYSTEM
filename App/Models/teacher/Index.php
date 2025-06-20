<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);

// Get teacher's classes - Enhanced with more details
$stmt = $pdo->prepare("SELECT DISTINCT c.*, s.subject_name 
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      JOIN subjects s ON cst.subject_id = s.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total students taught by this teacher
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT sc.student_id) as total_students
                      FROM student_classes sc
                      JOIN class_subject_teachers cst ON sc.class_id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$total_students = $stmt->fetch(PDO::FETCH_ASSOC)['total_students'] ?? 0;

// Get pending leave applications - Enhanced with more details
$stmt = $pdo->prepare("SELECT la.*, u.first_name, u.last_name, st.student_id, c.class_name
                      FROM leave_applications la
                      JOIN users u ON la.user_id = u.id
                      JOIN students st ON u.id = st.user_id
                      JOIN student_classes sc ON st.id = sc.student_id
                      JOIN class_subject_teachers cst ON sc.class_id = cst.class_id
                      JOIN classes c ON sc.class_id = c.id
                      WHERE cst.teacher_id = ? AND la.status = 'pending' AND la.user_type = 'student'
                      GROUP BY la.id
                      ORDER BY la.applied_date DESC
                      LIMIT 10");
$stmt->execute([$user['id']]);
$pending_leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent assignments created by this teacher
$stmt = $pdo->prepare("SELECT a.*, c.class_name, s.subject_name
                      FROM assignments a
                      JOIN classes c ON a.class_id = c.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE a.teacher_id = ? AND a.is_active = 1
                      ORDER BY a.created_at DESC
                      LIMIT 5");
$stmt->execute([$user['id']]);
$recent_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get assignments count
$stmt = $pdo->prepare("SELECT COUNT(*) as assignment_count
                      FROM assignments a
                      WHERE a.teacher_id = ? AND a.is_active = 1");
$stmt->execute([$user['id']]);
$assignment_count = $stmt->fetch(PDO::FETCH_ASSOC)['assignment_count'] ?? 0;

// Get today's attendance summary for teacher's classes - FIXED
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT c.class_name, c.section, s.subject_name,
                      COUNT(CASE WHEN att.status = 'present' THEN 1 END) as present_count,
                      COUNT(CASE WHEN att.status = 'absent' THEN 1 END) as absent_count,
                      COUNT(CASE WHEN att.status = 'late' THEN 1 END) as late_count,
                      COUNT(att.id) as total_marked
                      FROM class_subject_teachers cst
                      JOIN classes c ON cst.class_id = c.id
                      JOIN subjects s ON cst.subject_id = s.id
                      LEFT JOIN attendance att ON c.id = att.class_id AND att.attendance_date = ? AND att.teacher_id = ?
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      GROUP BY c.id, s.id
                      ORDER BY c.class_name, c.section");
$stmt->execute([$today, $user['id'], $user['id']]);
$attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process attendance summary with proper keys
$attendance_summary = [];
foreach ($attendance_data as $row) {
    $attendance_summary[] = [
        'class_name' => $row['class_name'],
        'section' => $row['section'],
        'subject_name' => $row['subject_name'],
        'present' => $row['present_count'] ?? 0,
        'absent' => $row['absent_count'] ?? 0,
        'late' => $row['late_count'] ?? 0,
        'total' => $row['total_marked'] ?? 0
    ];
}

// Get current Nepali date if available - FIXED
$current_nepali_date = date('F d, Y'); // Default to English date
$nepali_date = null;
try {
    $stmt = $pdo->prepare("SELECT nepali_year, nepali_month, nepali_day, nepali_date_string,
                          GetNepaliMonthNameEn(nepali_month) as month_name_en,
                          GetNepaliMonthNameNp(nepali_month) as month_name_np,
                          GetNepaliDayOfWeek(day_of_week_en) as day_name_np
                          FROM nepali_date_mapping 
                          WHERE gregorian_date = CURDATE()
                          LIMIT 1");
    $stmt->execute();
    $nepali_date = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($nepali_date && !empty($nepali_date['nepali_date_string'])) {
        $current_nepali_date = $nepali_date['nepali_date_string'];
    }
} catch (PDOException $e) {
    // Nepali calendar not available, use English date
    error_log("Nepali calendar not available: " . $e->getMessage());
}

// Get teacher's monthly teaching logs count - FIXED
$current_month = date('Y-m');
$monthly_logs = [];
try {
    $stmt = $pdo->prepare("SELECT tl.*, c.class_name, s.subject_name
                          FROM teacher_logs tl
                          JOIN classes c ON tl.class_id = c.id
                          JOIN subjects s ON tl.subject_id = s.id
                          WHERE tl.teacher_id = ? 
                          AND DATE_FORMAT(tl.log_date, '%Y-%m') = ?
                          ORDER BY tl.log_date DESC
                          LIMIT 10");
    $stmt->execute([$user['id'], $current_month]);
    $monthly_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Teacher logs table might not exist
    error_log("Teacher logs not available: " . $e->getMessage());
    $monthly_logs = [];
}

// Calculate statistics for dashboard
$stats = [
    'classes_count' => count($classes),
    'students_count' => $total_students,
    'assignments_count' => $assignment_count,
    'pending_assignments' => 0, // Placeholder for pending assignments count
    'pending_leaves_count' => count($pending_leaves),
    'monthly_logs' => count($monthly_logs),
    'attendance_taken_today' => count(array_filter($attendance_summary, function($att) {
        return $att['total'] > 0;
    }))
];

// Get school settings
$school_settings = [];
try {
    $stmt = $pdo->prepare("SELECT setting_key, setting_value 
                          FROM school_settings 
                          WHERE setting_key IN ('school_name', 'use_nepali_calendar', 'show_both_calendars')");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $school_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    // Settings table might not exist
    $school_settings = ['school_name' => 'School LMS'];
}

?>
