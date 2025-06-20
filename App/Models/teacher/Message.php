<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher's students for messaging
$stmt = $pdo->prepare("SELECT DISTINCT s.id, u.first_name, u.last_name, u.email, c.class_name, c.section
                      FROM students s
                      JOIN users u ON s.user_id = u.id
                      JOIN student_classes sc ON s.id = sc.student_id
                      JOIN classes c ON sc.class_id = c.id
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1 AND sc.status = 'enrolled'
                      ORDER BY c.class_name, u.first_name, u.last_name");
$stmt->execute([$user['id']]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $recipient_ids = $_POST['recipients'] ?? [];
    $subject = $_POST['subject'];
    $message_body = $_POST['message_body'];
    $message_type = $_POST['message_type'] ?? 'personal';
    $priority = $_POST['priority'] ?? 'normal';
    
    if (!empty($recipient_ids) && !empty($subject) && !empty($message_body)) {
        try {
            foreach ($recipient_ids as $recipient_id) {
                $stmt = $pdo->prepare("INSERT INTO messages 
                                      (sender_id, recipient_id, subject, message_body, message_type, priority)
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user['id'], $recipient_id, $subject, $message_body, $message_type, $priority]);
            }
            
            $msg = "<div class='alert alert-success'>Message sent successfully to " . count($recipient_ids) . " recipient(s)!</div>";
            
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Error sending message: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Please fill in all required fields and select at least one recipient.</div>";
    }
}

// Get sent messages
$stmt = $pdo->prepare("SELECT m.*, u.first_name, u.last_name, u.email
                      FROM messages m
                      JOIN users u ON m.recipient_id = u.id
                      WHERE m.sender_id = ?
                      ORDER BY m.sent_at DESC
                      LIMIT 50");
$stmt->execute([$user['id']]);
$sent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get received messages
$stmt = $pdo->prepare("SELECT m.*, u.first_name, u.last_name, u.email
                      FROM messages m
                      JOIN users u ON m.sender_id = u.id
                      WHERE m.recipient_id = ? AND m.is_deleted_by_recipient = 0
                      ORDER BY m.sent_at DESC
                      LIMIT 50");
$stmt->execute([$user['id']]);
$received_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get message statistics
$stmt = $pdo->prepare("SELECT 
                      COUNT(CASE WHEN sender_id = ? THEN 1 END) as sent_count,
                      COUNT(CASE WHEN recipient_id = ? AND is_deleted_by_recipient = 0 THEN 1 END) as received_count,
                      COUNT(CASE WHEN recipient_id = ? AND is_read = 0 AND is_deleted_by_recipient = 0 THEN 1 END) as unread_count
                      FROM messages 
                      WHERE sender_id = ? OR recipient_id = ?");
$stmt->execute([$user['id'], $user['id'], $user['id'], $user['id'], $user['id']]);
$message_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'mark_read') {
    $message_id = $_POST['message_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() 
                              WHERE id = ? AND recipient_id = ?");
        $stmt->execute([$message_id, $user['id']]);
        
        header("Location: messages.php");
        exit;
        
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Error updating message: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>
