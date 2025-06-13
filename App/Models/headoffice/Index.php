<?php
include '../include/loader.php';
requireRole('principal');

$user = getCurrentUser($pdo);

// Get comprehensive statistics
try {
    // Basic counts
    $stats = [];
    $stats['students'] = $pdo->query("SELECT COUNT(*) FROM students WHERE is_active = 1")->fetchColumn();
    $stats['teachers'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2 AND is_active = 1")->fetchColumn();
    $stats['classes'] = $pdo->query("SELECT COUNT(*) FROM classes WHERE is_active = 1")->fetchColumn();
    $stats['subjects'] = $pdo->query("SELECT COUNT(*) FROM subjects WHERE is_active = 1")->fetchColumn();
    
    // Leave applications
    $stats['pending_leaves'] = $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'pending'")->fetchColumn();
    $stats['approved_leaves'] = $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'approved'")->fetchColumn();
    
    // Attendance today
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT 
                          COUNT(*) as total_marked,
                          SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                          SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                          SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late
                          FROM attendance WHERE attendance_date = ?");
    $stmt->execute([$today]);
    $attendance_today = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Recent leave applications
    $stmt = $pdo->prepare("SELECT la.*, u.first_name, u.last_name, u.email,
                          CASE 
                              WHEN la.user_type = 'student' THEN (SELECT student_id FROM students WHERE user_id = la.user_id)
                              ELSE 'Teacher'
                          END as identifier
                          FROM leave_applications la
                          JOIN users u ON la.user_id = u.id
                          WHERE la.status = 'pending'
                          ORDER BY la.applied_date DESC
                          LIMIT 5");
    $stmt->execute();
    $recent_leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent assignments
    $stmt = $pdo->prepare("SELECT a.*, s.subject_name, c.class_name, u.first_name, u.last_name,
                          (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id) as submissions
                          FROM assignments a
                          JOIN subjects s ON a.subject_id = s.id
                          JOIN classes c ON a.class_id = c.id
                          JOIN users u ON a.teacher_id = u.id
                          WHERE a.is_active = 1
                          ORDER BY a.assigned_date DESC
                          LIMIT 5");
    $stmt->execute();
    $recent_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Monthly statistics
    $current_month = date('Y-m');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$current_month]);
    $new_students_month = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments WHERE DATE_FORMAT(assigned_date, '%Y-%m') = ?");
    $stmt->execute([$current_month]);
    $assignments_month = $stmt->fetchColumn();
    
    // Teacher activity
    $stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name, 
                          COUNT(DISTINCT a.id) as assignments_count,
                          COUNT(DISTINCT att.id) as attendance_records,
                          COUNT(DISTINCT tl.id) as log_entries
                          FROM users u
                          LEFT JOIN assignments a ON u.id = a.teacher_id AND a.assigned_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          LEFT JOIN attendance att ON u.id = att.teacher_id AND att.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          LEFT JOIN teacher_logs tl ON u.id = tl.teacher_id AND tl.log_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          WHERE u.role_id = 2 AND u.is_active = 1
                          GROUP BY u.id
                          ORDER BY assignments_count DESC, attendance_records DESC
                          LIMIT 5");
    $stmt->execute();
    $active_teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $stats = ['students' => 0, 'teachers' => 0, 'classes' => 0, 'subjects' => 0, 'pending_leaves' => 0, 'approved_leaves' => 0];
    $attendance_today = ['total_marked' => 0, 'present' => 0, 'absent' => 0, 'late' => 0];
    $recent_leaves = [];
    $recent_assignments = [];
    $new_students_month = 0;
    $assignments_month = 0;
    $active_teachers = [];
}

include '../include/sidebar.php';
?>