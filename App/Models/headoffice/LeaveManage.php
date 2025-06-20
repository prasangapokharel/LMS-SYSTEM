<?php
include '../include/connect.php';
include '../include/session.php';
requireRole('principal');

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

// Initialize cache
$cache = new FilesystemAdapter('leave_management', 3600, __DIR__ . '/../cache');

$user = getCurrentUser($pdo);
$msg = "";

// Initialize HTTP clients
$symfonyClient = HttpClient::create();
$guzzleClient = new Client(['timeout' => 3.0]);

// Handle leave approval/rejection with async notifications
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $leave_id = (int)$_POST['leave_id'];
    $action = $_POST['action'];
    $remarks = $_POST['remarks'] ?? '';
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        if ($action == 'approve') {
            $stmt = $pdo->prepare("UPDATE leave_applications 
                                  SET status = 'approved', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                                  WHERE id = ?");
            $stmt->execute([$user['id'], $remarks, $leave_id]);
            $msg = successMessage("Leave application approved successfully!");
        } elseif ($action == 'reject') {
            $stmt = $pdo->prepare("UPDATE leave_applications 
                                  SET status = 'rejected', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                                  WHERE id = ?");
            $stmt->execute([$user['id'], $remarks, $leave_id]);
            $msg = successMessage("Leave application rejected successfully!");
        }
        
        // Get user details for notification
        $stmt = $pdo->prepare("SELECT u.email, u.first_name, u.last_name, la.leave_type, la.from_date, la.to_date 
                              FROM leave_applications la 
                              JOIN users u ON la.user_id = u.id 
                              WHERE la.id = ?");
        $stmt->execute([$leave_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Commit transaction
        $pdo->commit();
        
        // Log the activity
        logActivity($pdo, 'leave_' . $action, 'leave_applications', $leave_id);
        
        // Send async notification using both clients (fallback mechanism)
        try {
            // Using Symfony HTTP Client (faster)
            $symfonyClient->request('POST', 'https://your-api-endpoint.com/notify', [
                'json' => [
                    'email' => $user_data['email'],
                    'action' => $action,
                    'leave_details' => $user_data
                ],
                'timeout' => 2
            ]);
        } catch (Exception $e) {
            // Fallback to Guzzle if Symfony client fails
            $guzzleClient->postAsync('https://your-api-endpoint.com/notify', [
                'json' => [
                    'email' => $user_data['email'],
                    'action' => $action,
                    'leave_details' => $user_data
                ]
            ]);
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = errorMessage("Error: " . $e->getMessage());
        error_log("Leave management error: " . $e->getMessage());
    }
}

// Get filter parameters with sanitization
$status_filter = isset($_GET['status']) ? preg_replace('/[^a-z]/', '', $_GET['status']) : 'all';
$user_type_filter = isset($_GET['user_type']) ? preg_replace('/[^a-z]/', '', $_GET['user_type']) : 'all';
$date_from = isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '';

// Validate date format
if ($date_from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) {
    $date_from = '';
}
if ($date_to && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
    $date_to = '';
}

// Build query conditions with caching
$cacheKey = 'leave_apps_' . md5(serialize([$status_filter, $user_type_filter, $date_from, $date_to]));
$cachedData = $cache->getItem($cacheKey);

if (!$cachedData->isHit()) {
    $where_conditions = [];
    $params = [];

    if ($status_filter != 'all') {
        $where_conditions[] = "la.status = ?";
        $params[] = $status_filter;
    }

    if ($user_type_filter != 'all') {
        $where_conditions[] = "la.user_type = ?";
        $params[] = $user_type_filter;
    }

    if ($date_from) {
        $where_conditions[] = "la.from_date >= ?";
        $params[] = $date_from;
    }

    if ($date_to) {
        $where_conditions[] = "la.to_date <= ?";
        $params[] = $date_to;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Pagination setup
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    // Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM leave_applications la
                    JOIN users u ON la.user_id = u.id
                    LEFT JOIN users approver ON la.approved_by = approver.id
                    $where_clause";
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $per_page);

    // Get leave applications with pagination
    $main_query = "SELECT la.*, u.first_name, u.last_name, u.email,
                   CASE 
                       WHEN la.user_type = 'student' THEN (SELECT student_id FROM students WHERE user_id = la.user_id)
                       ELSE 'N/A'
                   END as identifier,
                   approver.first_name as approver_first_name,
                   approver.last_name as approver_last_name
                   FROM leave_applications la
                   JOIN users u ON la.user_id = u.id
                   LEFT JOIN users approver ON la.approved_by = approver.id
                   $where_clause
                   ORDER BY la.applied_date DESC
                   LIMIT $offset, $per_page";

    $stmt = $pdo->prepare($main_query);
    $stmt->execute($params);
    $leave_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cache the results for 5 minutes
    $cachedData->set([
        'leave_applications' => $leave_applications,
        'total_records' => $total_records,
        'total_pages' => $total_pages
    ])->expiresAfter(300);
    $cache->save($cachedData);
} else {
    $cachedResult = $cachedData->get();
    $leave_applications = $cachedResult['leave_applications'];
    $total_records = $cachedResult['total_records'];
    $total_pages = $cachedResult['total_pages'];
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per_page = 10;
}

// Get statistics with caching
$statsCache = $cache->getItem('leave_stats');
if (!$statsCache->isHit()) {
    $stats = [
        'total_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications")->fetchColumn(),
        'pending_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'pending'")->fetchColumn(),
        'approved_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'approved'")->fetchColumn(),
        'rejected_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'rejected'")->fetchColumn()
    ];
    $statsCache->set($stats)->expiresAfter(3600);
    $cache->save($statsCache);
} else {
    $stats = $statsCache->get();
}

// Helper functions
function successMessage($message) {
    return <<<HTML
    <div class='alert alert-success alert-modern'>
        <div class='alert-icon'>✓</div>
        <div><strong>$message</strong></div>
    </div>
HTML;
}

function errorMessage($message) {
    return <<<HTML
    <div class='alert alert-danger alert-modern'>
        <div class='alert-icon'>✗</div>
        <div><strong>$message</strong></div>
    </div>
HTML;
}
?>