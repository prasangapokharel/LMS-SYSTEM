<?php
session_start();
require_once 'include/connect.php';
require_once 'App/Models/Notification.php';

// Test notification endpoint
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$notification = new NotificationSystem($pdo);

// Send test notification
$result = $notification->createNotification(
    $_SESSION['user_id'],
    "🔔 Test Notification",
    "This is a test notification from your LMS system!",
    'test',
    ['url' => '/students/']
);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Test notification sent!']);
} else {
    echo json_encode(['error' => 'Failed to send notification']);
}
?>
