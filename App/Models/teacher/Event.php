<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher's classes and subjects
$stmt = $pdo->prepare("SELECT DISTINCT c.id as class_id, c.class_name, c.section, s.id as subject_id, s.subject_name
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      LEFT JOIN subjects s ON cst.subject_id = s.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$teacher_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_event') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_type = $_POST['event_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'] ?: $start_date;
    $start_time = $_POST['start_time'] ?: null;
    $end_time = $_POST['end_time'] ?: null;
    $location = trim($_POST['location']) ?: null;
    $class_id = !empty($_POST['class_id']) ? intval($_POST['class_id']) : null;
    $subject_id = !empty($_POST['subject_id']) ? intval($_POST['subject_id']) : null;
    $is_all_day = isset($_POST['is_all_day']) ? 1 : 0;
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $color = $_POST['color'] ?: '#3498db';
    $reminder_minutes = !empty($_POST['reminder_minutes']) ? intval($_POST['reminder_minutes']) : null;
    
    $event_image = null;
    
    // Handle image upload
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $upload_dir = '../uploads/events/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $msg = "<div class='alert alert-danger'>Invalid image format. Please upload JPG, PNG, GIF, or WebP images.</div>";
        } elseif ($_FILES['event_image']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $msg = "<div class='alert alert-danger'>Image size too large. Maximum size is 5MB.</div>";
        } else {
            $file_name = 'event_' . uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $file_path)) {
                $event_image = 'uploads/events/' . $file_name;
            } else {
                $msg = "<div class='alert alert-danger'>Failed to upload image. Please try again.</div>";
            }
        }
    }
    
    if (empty($msg)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO events 
                                  (title, description, event_type, start_date, end_date, start_time, end_time, 
                                   location, class_id, subject_id, created_by, is_all_day, is_public, color, 
                                   reminder_minutes, event_image)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $title, $description, $event_type, $start_date, $end_date, $start_time, $end_time,
                $location, $class_id, $subject_id, $user['id'], $is_all_day, $is_public, $color,
                $reminder_minutes, $event_image
            ]);
            
            $msg = "<div class='alert alert-success'>Event created successfully!</div>";
            
        } catch (PDOException $e) {
            error_log("Event creation error: " . $e->getMessage());
            $msg = "<div class='alert alert-danger'>Error creating event. Please try again.</div>";
        }
    }
}

// Handle event deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_event') {
    $event_id = intval($_POST['event_id']);
    
    try {
        // Get event info first to delete image
        $stmt = $pdo->prepare("SELECT event_image FROM events WHERE id = ? AND created_by = ?");
        $stmt->execute([$event_id, $user['id']]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event) {
            // Delete image file if exists
            if ($event['event_image'] && file_exists('../' . $event['event_image'])) {
                unlink('../' . $event['event_image']);
            }
            
            // Delete database record
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
            $stmt->execute([$event_id, $user['id']]);
            
            $msg = "<div class='alert alert-success'>Event deleted successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Event not found or you don't have permission to delete it.</div>";
        }
        
    } catch (PDOException $e) {
        error_log("Event deletion error: " . $e->getMessage());
        $msg = "<div class='alert alert-danger'>Error deleting event. Please try again.</div>";
    }
}

// Get events created by this teacher
$stmt = $pdo->prepare("SELECT e.*, c.class_name, c.section, s.subject_name
                      FROM events e
                      LEFT JOIN classes c ON e.class_id = c.id
                      LEFT JOIN subjects s ON e.subject_id = s.id
                      WHERE e.created_by = ?
                      ORDER BY e.start_date DESC, e.start_time DESC");
$stmt->execute([$user['id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get event statistics
$stmt = $pdo->prepare("SELECT 
                      COUNT(*) as total_events,
                      COUNT(CASE WHEN event_type = 'exam' THEN 1 END) as exams,
                      COUNT(CASE WHEN event_type = 'assignment' THEN 1 END) as assignments,
                      COUNT(CASE WHEN event_type = 'meeting' THEN 1 END) as meetings,
                      COUNT(CASE WHEN start_date >= CURDATE() THEN 1 END) as upcoming_events
                      FROM events 
                      WHERE created_by = ?");
$stmt->execute([$user['id']]);
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
?>
