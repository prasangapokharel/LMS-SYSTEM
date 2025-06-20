<?php
include '../include/loader.php';
requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";

// Handle export requests
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    $filters = $_GET;
    
    // Build the same query as below but for export
    $where_conditions = ["1=1"];
    $params = [];
    
    if (!empty($filters['teacher_id'])) {
        $where_conditions[] = "tl.teacher_id = ?";
        $params[] = $filters['teacher_id'];
    }
    
    if (!empty($filters['class_id'])) {
        $where_conditions[] = "tl.class_id = ?";
        $params[] = $filters['class_id'];
    }
    
    if (!empty($filters['subject_id'])) {
        $where_conditions[] = "tl.subject_id = ?";
        $params[] = $filters['subject_id'];
    }
    
    if (!empty($filters['date_from'])) {
        $where_conditions[] = "tl.log_date >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $where_conditions[] = "tl.log_date <= ?";
        $params[] = $filters['date_to'];
    }
    
    if (!empty($filters['search'])) {
        $where_conditions[] = "(tl.chapter_title LIKE ? OR tl.topics_covered LIKE ? OR tl.notes LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $search_param = "%{$filters['search']}%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $stmt = $pdo->prepare("SELECT tl.*, u.first_name, u.last_name, c.class_name, c.section, s.subject_name
                          FROM teacher_logs tl
                          JOIN users u ON tl.teacher_id = u.id
                          JOIN classes c ON tl.class_id = c.id
                          JOIN subjects s ON tl.subject_id = s.id
                          WHERE $where_clause
                          ORDER BY tl.log_date DESC, u.last_name, u.first_name");
    $stmt->execute($params);
    $export_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($export_type == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="teacher_logs_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Teacher', 'Class', 'Subject', 'Chapter Title', 'Topics Covered', 'Teaching Method', 'Students Present', 'Duration (min)', 'Homework', 'Notes']);
        
        foreach ($export_data as $row) {
            fputcsv($output, [
                $row['log_date'],
                $row['first_name'] . ' ' . $row['last_name'],
                $row['class_name'] . ' ' . $row['section'],
                $row['subject_name'],
                $row['chapter_title'],
                $row['topics_covered'],
                $row['teaching_method'],
                $row['students_present'],
                $row['lesson_duration'],
                $row['homework_assigned'],
                $row['notes']
            ]);
        }
        fclose($output);
        exit;
    }
}

// Get filter parameters
$teacher_id = $_GET['teacher_id'] ?? '';
$class_id = $_GET['class_id'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query conditions
$where_conditions = ["1=1"];
$params = [];

if ($teacher_id) {
    $where_conditions[] = "tl.teacher_id = ?";
    $params[] = $teacher_id;
}

if ($class_id) {
    $where_conditions[] = "tl.class_id = ?";
    $params[] = $class_id;
}

if ($subject_id) {
    $where_conditions[] = "tl.subject_id = ?";
    $params[] = $subject_id;
}

if ($date_from) {
    $where_conditions[] = "tl.log_date >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where_conditions[] = "tl.log_date <= ?";
    $params[] = $date_to;
}

if ($search) {
    $where_conditions[] = "(tl.chapter_title LIKE ? OR tl.topics_covered LIKE ? OR tl.notes LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM teacher_logs tl
                            JOIN users u ON tl.teacher_id = u.id
                            JOIN classes c ON tl.class_id = c.id
                            JOIN subjects s ON tl.subject_id = s.id
                            WHERE $where_clause");
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Get teacher logs with pagination
$stmt = $pdo->prepare("SELECT tl.*, u.first_name, u.last_name, c.class_name, c.section, s.subject_name
                      FROM teacher_logs tl
                      JOIN users u ON tl.teacher_id = u.id
                      JOIN classes c ON tl.class_id = c.id
                      JOIN subjects s ON tl.subject_id = s.id
                      WHERE $where_clause
                      ORDER BY tl.log_date DESC, u.last_name, u.first_name
                      LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$teacher_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all teachers for filter dropdown
$stmt = $pdo->query("SELECT u.id, u.first_name, u.last_name 
                    FROM users u 
                    WHERE u.role_id = 2 AND u.is_active = 1 
                    ORDER BY u.first_name, u.last_name");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all classes for filter dropdown
$stmt = $pdo->query("SELECT id, class_name, section FROM classes WHERE is_active = 1 ORDER BY class_level, section");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all subjects for filter dropdown
$stmt = $pdo->query("SELECT id, subject_name FROM subjects WHERE is_active = 1 ORDER BY subject_name");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get summary statistics
$stats = [];

// Total logs
$stats['total_logs'] = $pdo->query("SELECT COUNT(*) FROM teacher_logs")->fetchColumn();

// Logs this month
$current_month = date('Y-m');
$stmt = $pdo->prepare("SELECT COUNT(*) FROM teacher_logs WHERE DATE_FORMAT(log_date, '%Y-%m') = ?");
$stmt->execute([$current_month]);
$stats['logs_this_month'] = $stmt->fetchColumn();

// Active teachers with logs
$stats['active_teachers'] = $pdo->query("SELECT COUNT(DISTINCT teacher_id) FROM teacher_logs")->fetchColumn();

// Average logs per teacher this month
$stmt = $pdo->prepare("SELECT AVG(log_count) FROM (
                      SELECT COUNT(*) as log_count 
                      FROM teacher_logs 
                      WHERE DATE_FORMAT(log_date, '%Y-%m') = ?
                      GROUP BY teacher_id
                      ) as teacher_counts");
$stmt->execute([$current_month]);
$stats['avg_logs_per_teacher'] = round($stmt->fetchColumn() ?: 0, 1);

// Teaching method distribution
$stmt = $pdo->query("SELECT teaching_method, COUNT(*) as count 
                    FROM teacher_logs 
                    WHERE teaching_method IS NOT NULL 
                    GROUP BY teaching_method 
                    ORDER BY count DESC");
$teaching_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly log trends (last 6 months)
$stmt = $pdo->query("SELECT DATE_FORMAT(log_date, '%Y-%m') as month, COUNT(*) as count
                    FROM teacher_logs 
                    WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(log_date, '%Y-%m')
                    ORDER BY month DESC");
$monthly_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
