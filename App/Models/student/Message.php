<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Get filter parameters
$message_type = $_GET['type'] ?? 'all';
$read_status = $_GET['status'] ?? 'all';

// Build the messages query
$query = "SELECT m.*, 
                 sender.first_name as sender_first_name, 
                 sender.last_name as sender_last_name,
                 sender.role_id as sender_role
          FROM messages m
          JOIN users sender ON m.sender_id = sender.id
          WHERE m.recipient_id = ? AND m.is_deleted_by_recipient = 0";

$params = [$user['id']];

// Add type filter
if ($message_type !== 'all') {
    $query .= " AND m.message_type = ?";
    $params[] = $message_type;
}

// Add read status filter
if ($read_status === 'unread') {
    $query .= " AND m.is_read = 0";
} elseif ($read_status === 'read') {
    $query .= " AND m.is_read = 1";
}

$query .= " ORDER BY m.sent_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get message counts for stats
$stmt = $pdo->prepare("SELECT 
                        COUNT(*) as total_messages,
                        SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count,
                        SUM(CASE WHEN message_type = 'announcement' THEN 1 ELSE 0 END) as announcements,
                        SUM(CASE WHEN priority = 'high' OR priority = 'urgent' THEN 1 ELSE 0 END) as important
                       FROM messages 
                       WHERE recipient_id = ? AND is_deleted_by_recipient = 0");
$stmt->execute([$user['id']]);
$message_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Mark message as read if viewing
if (isset($_GET['read']) && isset($_GET['message_id'])) {
    $message_id = (int)$_GET['message_id'];
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() WHERE id = ? AND recipient_id = ?");
    $stmt->execute([$message_id, $user['id']]);
    
    // Redirect to avoid resubmission
    header("Location: messages.php");
    exit;
}

// Function to get priority color
function getPriorityColor($priority) {
    switch ($priority) {
        case 'urgent': return '#ef4444';
        case 'high': return '#f59e0b';
        case 'normal': return '#3b82f6';
        case 'low': return '#6b7280';
        default: return '#3b82f6';
    }
}

// Function to get message type icon
function getMessageTypeIcon($type) {
    $icons = [
        'personal' => '💬',
        'announcement' => '📢',
        'system' => '⚙️',
        'assignment' => '📝'
    ];
    return $icons[$type] ?? '💬';
}

// Function to get sender role badge
function getSenderRoleBadge($role_id) {
    switch ($role_id) {
        case 1: return 'Principal';
        case 2: return 'Teacher';
        case 3: return 'Student';
        default: return 'User';
    }
}
?>
