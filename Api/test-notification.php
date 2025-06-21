<?php
session_start();
require_once '../include/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

try {
    // Simple direct database insertion
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, created_at) VALUES (?, ?, ?, ?, NOW())");
    $result = $stmt->execute([
        $_SESSION['user_id'],
        "🔔 Simple Test",
        "This is a simple test notification!",
        'test'
    ]);
    
    if ($result) {
        $notification_id = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => 'Simple notification sent!',
            'notification_id' => $notification_id
        ]);
    } else {
        echo json_encode([
            'error' => 'Database insertion failed',
            'errorInfo' => $stmt->errorInfo()
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Exception: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
