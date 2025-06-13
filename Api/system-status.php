<?php
header('Content-Type: application/json');
require_once '../include/connect.php';

use App\Bootstrap;

try {
    // Check database connection
    $stmt = $pdo->query("SELECT 1");
    
    // Check cache service
    $cache = Bootstrap::cache();
    $cache->set('health_check', time(), 60);
    $cacheStatus = $cache->get('health_check') !== null;
    
    echo json_encode([
        'status' => 'connected',
        'database' => true,
        'cache' => $cacheStatus,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'failed',
        'database' => false,
        'cache' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
