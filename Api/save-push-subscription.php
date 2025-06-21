<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['endpoint'])) {
        throw new Exception('Invalid subscription data');
    }
    
    $user_id = $_SESSION['user_id'];
    $endpoint = $input['endpoint'];
    $p256dh = $input['keys']['p256dh'] ?? '';
    $auth = $input['keys']['auth'] ?? '';
    
    // Save subscription to database
    $stmt = $pdo->prepare("INSERT INTO push_subscriptions 
                          (user_id, endpoint, p256dh_key, auth_key, created_at) 
                          VALUES (?, ?, ?, ?, NOW()) 
                          ON DUPLICATE KEY UPDATE 
                          p256dh_key = VALUES(p256dh_key), 
                          auth_key = VALUES(auth_key),
                          updated_at = NOW()");
    
    $result = $stmt->execute([$user_id, $endpoint, $p256dh, $auth]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Subscription saved successfully']);
    } else {
        throw new Exception('Failed to save subscription');
    }
    
} catch (Exception $e) {
    error_log("Save push subscription error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save subscription']);
}
?>
