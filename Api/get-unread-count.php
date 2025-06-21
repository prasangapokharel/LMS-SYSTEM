<?php
session_start();
require_once '../include/connect.php';
require_once '../App/Models/Notification.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

try {
    $notification = new NotificationSystem($pdo);
    $count = $notification->getUnreadCount($_SESSION['user_id']);
    echo json_encode(['count' => $count]);
} catch (Exception $e) {
    echo json_encode(['count' => 0, 'error' => $e->getMessage()]);
}
?>
