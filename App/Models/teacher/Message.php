<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher's students for messaging
try {
    $stmt = $pdo->prepare("SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, c.class_name, c.section
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          JOIN student_classes sc ON s.id = sc.student_id
                          JOIN classes c ON sc.class_id = c.id
                          JOIN class_subject_teachers cst ON c.id = cst.class_id
                          WHERE cst.teacher_id = ? AND cst.is_active = 1 AND sc.status = 'enrolled'
                          ORDER BY c.class_name, u.first_name, u.last_name");
    $stmt->execute([$user['id']]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching students: " . $e->getMessage());
    $students = [];
    $msg = "<div class='alert alert-danger'>Error loading students. Please try again.</div>";
}

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $recipient_ids = $_POST['recipients'] ?? [];
    $subject = trim($_POST['subject'] ?? '');
    $message_body = trim($_POST['message_body'] ?? '');
    $message_type = $_POST['message_type'] ?? 'personal';
    $priority = $_POST['priority'] ?? 'normal';
    
    if (!empty($recipient_ids) && !empty($subject) && !empty($message_body)) {
        try {
            $success_count = 0;
            foreach ($recipient_ids as $recipient_id) {
                // Validate recipient exists and is a student of this teacher
                $validate_stmt = $pdo->prepare("SELECT COUNT(*) FROM students s
                                               JOIN users u ON s.user_id = u.id
                                               JOIN student_classes sc ON s.id = sc.student_id
                                               JOIN classes c ON sc.class_id = c.id
                                               JOIN class_subject_teachers cst ON c.id = cst.class_id
                                               WHERE u.id = ? AND cst.teacher_id = ? AND cst.is_active = 1");
                $validate_stmt->execute([$recipient_id, $user['id']]);
                
                if ($validate_stmt->fetchColumn() > 0) {
                    $stmt = $pdo->prepare("INSERT INTO messages 
                                          (sender_id, recipient_id, subject, message_body, message_type, priority)
                                          VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user['id'], $recipient_id, $subject, $message_body, $message_type, $priority]);
                    $success_count++;
                }
            }
            
            if ($success_count > 0) {
                $msg = "<div class='alert alert-success'>Message sent successfully to {$success_count} recipient(s)!</div>";
            } else {
                $msg = "<div class='alert alert-warning'>No valid recipients found.</div>";
            }
            
        } catch (PDOException $e) {
            error_log("Error sending message: " . $e->getMessage());
            $msg = "<div class='alert alert-danger'>Error sending message. Please try again.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Please fill in all required fields and select at least one recipient.</div>";
    }
}

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'mark_read') {
    $message_id = intval($_POST['message_id'] ?? 0);
    
    if ($message_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() 
                                  WHERE id = ? AND recipient_id = ?");
            $stmt->execute([$message_id, $user['id']]);
            
            header("Location: messages.php");
            exit;
            
        } catch (PDOException $e) {
            error_log("Error marking message as read: " . $e->getMessage());
            $msg = "<div class='alert alert-danger'>Error updating message.</div>";
        }
    }
}

// Handle message deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_message') {
    $message_id = intval($_POST['message_id'] ?? 0);
    $delete_type = $_POST['delete_type'] ?? 'sender';
    
    if ($message_id > 0) {
        try {
            if ($delete_type === 'sender') {
                $stmt = $pdo->prepare("UPDATE messages SET is_deleted_by_sender = 1 
                                      WHERE id = ? AND sender_id = ?");
                $stmt->execute([$message_id, $user['id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE messages SET is_deleted_by_recipient = 1 
                                      WHERE id = ? AND recipient_id = ?");
                $stmt->execute([$message_id, $user['id']]);
            }
            
            $msg = "<div class='alert alert-success'>Message deleted successfully.</div>";
            
        } catch (PDOException $e) {
            error_log("Error deleting message: " . $e->getMessage());
            $msg = "<div class='alert alert-danger'>Error deleting message.</div>";
        }
    }
}

// Get sent messages
try {
    $stmt = $pdo->prepare("SELECT m.*, u.first_name, u.last_name, u.email
                          FROM messages m
                          JOIN users u ON m.recipient_id = u.id
                          WHERE m.sender_id = ? AND m.is_deleted_by_sender = 0
                          ORDER BY m.sent_at DESC
                          LIMIT 50");
    $stmt->execute([$user['id']]);
    $sent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching sent messages: " . $e->getMessage());
    $sent_messages = [];
}

// Get received messages
try {
    $stmt = $pdo->prepare("SELECT m.*, u.first_name, u.last_name, u.email
                          FROM messages m
                          JOIN users u ON m.sender_id = u.id
                          WHERE m.recipient_id = ? AND m.is_deleted_by_recipient = 0
                          ORDER BY m.sent_at DESC
                          LIMIT 50");
    $stmt->execute([$user['id']]);
    $received_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching received messages: " . $e->getMessage());
    $received_messages = [];
}

// Get message statistics
try {
    $stmt = $pdo->prepare("SELECT 
                          COUNT(CASE WHEN sender_id = ? AND is_deleted_by_sender = 0 THEN 1 END) as sent_count,
                          COUNT(CASE WHEN recipient_id = ? AND is_deleted_by_recipient = 0 THEN 1 END) as received_count,
                          COUNT(CASE WHEN recipient_id = ? AND is_read = 0 AND is_deleted_by_recipient = 0 THEN 1 END) as unread_count
                          FROM messages 
                          WHERE sender_id = ? OR recipient_id = ?");
    $stmt->execute([$user['id'], $user['id'], $user['id'], $user['id'], $user['id']]);
    $message_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ensure all stats have default values
    $message_stats = array_merge([
        'sent_count' => 0,
        'received_count' => 0,
        'unread_count' => 0
    ], $message_stats ?: []);
    
} catch (PDOException $e) {
    error_log("Error fetching message statistics: " . $e->getMessage());
    $message_stats = [
        'sent_count' => 0,
        'received_count' => 0,
        'unread_count' => 0
    ];
}

// Get classes for bulk messaging
try {
    $stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section,
                          COUNT(DISTINCT sc.student_id) as student_count
                          FROM classes c
                          JOIN class_subject_teachers cst ON c.id = cst.class_id
                          JOIN student_classes sc ON c.id = sc.class_id
                          WHERE cst.teacher_id = ? AND cst.is_active = 1 AND sc.status = 'enrolled'
                          GROUP BY c.id, c.class_name, c.section
                          ORDER BY c.class_name, c.section");
    $stmt->execute([$user['id']]);
    $teacher_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching teacher classes: " . $e->getMessage());
    $teacher_classes = [];
}
?>
