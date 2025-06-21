<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

include '../include/loader.php';

if (!isLoggedIn()) {
    echo "data: " . json_encode(['error' => 'Not authenticated']) . "\n\n";
    exit;
}

$user = getCurrentUser($pdo);
$user_id = $user['id'];

// Check for pending notifications
if (isset($_SESSION['pending_notifications'][$user_id])) {
    $notifications = $_SESSION['pending_notifications'][$user_id];
    
    foreach ($notifications as $notification) {
        echo "data: " . json_encode($notification) . "\n\n";
        flush();
    }
    
    // Clear sent notifications
    unset($_SESSION['pending_notifications'][$user_id]);
}

// Keep connection alive
echo "data: " . json_encode(['type' => 'heartbeat', 'timestamp' => time()]) . "\n\n";
flush();
?>
