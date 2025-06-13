<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";

// Handle leave approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $leave_id = (int)$_POST['leave_id'];
    $action = $_POST['action'];
    $remarks = $_POST['remarks'] ?? '';
    
    try {
        if ($action == 'approve') {
            $stmt = $pdo->prepare("UPDATE leave_applications 
                                  SET status = 'approved', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                                  WHERE id = ?");
            $stmt->execute([$user['id'], $remarks, $leave_id]);
            $msg = "<div class='alert alert-success alert-modern'>
                    <div class='alert-icon'>✓</div>
                    <div><strong>Leave application approved successfully!</strong></div>
                   </div>";
                   
            // Get user email for notification
            $stmt = $pdo->prepare("SELECT u.email FROM leave_applications la 
                                  JOIN users u ON la.user_id = u.id 
                                  WHERE la.id = ?");
            $stmt->execute([$leave_id]);
            $user_email = $stmt->fetchColumn();
            
            // Log the activity
            logActivity($pdo, 'leave_approve', 'leave_applications', $leave_id);
            
        } elseif ($action == 'reject') {
            $stmt = $pdo->prepare("UPDATE leave_applications 
                                  SET status = 'rejected', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                                  WHERE id = ?");
            $stmt->execute([$user['id'], $remarks, $leave_id]);
            $msg = "<div class='alert alert-success alert-modern'>
                    <div class='alert-icon'>✓</div>
                    <div><strong>Leave application rejected successfully!</strong></div>
                   </div>";
                   
            // Get user email for notification
            $stmt = $pdo->prepare("SELECT u.email FROM leave_applications la 
                                  JOIN users u ON la.user_id = u.id 
                                  WHERE la.id = ?");
            $stmt->execute([$leave_id]);
            $user_email = $stmt->fetchColumn();
            
            // Log the activity
            logActivity($pdo, 'leave_reject', 'leave_applications', $leave_id);
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>✗</div>
                <div><strong>Error: " . htmlspecialchars($e->getMessage()) . "</strong></div>
               </div>";
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$user_type_filter = $_GET['user_type'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query conditions
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

// Get statistics
$stats = [
    'total_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications")->fetchColumn(),
    'pending_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'pending'")->fetchColumn(),
    'approved_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'approved'")->fetchColumn(),
    'rejected_applications' => $pdo->query("SELECT COUNT(*) FROM leave_applications WHERE status = 'rejected'")->fetchColumn()
];

include '../include/sidebar.php';
?>
