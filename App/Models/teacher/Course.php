<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher's assigned courses
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section, s.id as subject_id, s.subject_name, s.subject_code,
                      cst.assigned_date, ay.year_name
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      JOIN subjects s ON cst.subject_id = s.id
                      JOIN academic_years ay ON c.academic_year_id = ay.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get course statistics
$course_stats = [];
foreach ($courses as $course) {
    $class_id = $course['id'];
    $subject_id = $course['subject_id'];
    
    // Get student count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_classes sc 
                          JOIN students s ON sc.student_id = s.id 
                          WHERE sc.class_id = ? AND sc.status = 'enrolled'");
    $stmt->execute([$class_id]);
    $student_count = $stmt->fetchColumn();
    
    // Get assignment count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments 
                          WHERE class_id = ? AND subject_id = ? AND teacher_id = ? AND is_active = 1");
    $stmt->execute([$class_id, $subject_id, $user['id']]);
    $assignment_count = $stmt->fetchColumn();
    
    // Get recent attendance
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT attendance_date) FROM attendance 
                          WHERE class_id = ? AND teacher_id = ? 
                          AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute([$class_id, $user['id']]);
    $recent_classes = $stmt->fetchColumn();
    
    $course_stats[$class_id . '_' . $subject_id] = [
        'students' => $student_count,
        'assignments' => $assignment_count,
        'recent_classes' => $recent_classes
    ];
}

// Handle course actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create_announcement') {
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        $title = $_POST['title'];
        $message = $_POST['message'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, title, message_body, message_type, created_at)
                                  SELECT ?, ?, ?, 'announcement', NOW()");
            $stmt->execute([$user['id'], $title, $message]);
            
            $msg = "<div class='alert alert-success'>Announcement created successfully!</div>";
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Error creating announcement: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>
