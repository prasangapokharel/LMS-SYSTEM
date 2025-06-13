<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";

// Get leave application ID
$leave_id = $_GET['id'] ?? 0;

if (!$leave_id) {
    header('Location: leave_management.php');
    exit;
}

// Handle leave approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $remarks = $_POST['remarks'] ?? '';
    
    try {
        if ($action == 'approve') {
            $stmt = $pdo->prepare("UPDATE leave_applications 
                                  SET status = 'approved', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                                  WHERE id = ? AND status = 'pending'");
            $stmt->execute([$user['id'], $remarks, $leave_id]);
            
            if ($stmt->rowCount() > 0) {
                $msg = "<div class='alert alert-success alert-modern'>
                        <div class='alert-icon'>✅</div>
                        <div><strong>Leave application approved successfully!</strong></div>
                       </div>";
                logActivity($pdo, 'leave_approved', 'leave_applications', $leave_id);
            } else {
                $msg = "<div class='alert alert-warning alert-modern'>
                        <div class='alert-icon'>⚠️</div>
                        <div><strong>Leave application could not be approved. It may have already been processed.</strong></div>
                       </div>";
            }
        } elseif ($action == 'reject') {
            if (empty($remarks)) {
                $msg = "<div class='alert alert-danger alert-modern'>
                        <div class='alert-icon'>❌</div>
                        <div><strong>Rejection reason is required!</strong></div>
                       </div>";
            } else {
                $stmt = $pdo->prepare("UPDATE leave_applications 
                                      SET status = 'rejected', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                                      WHERE id = ? AND status = 'pending'");
                $stmt->execute([$user['id'], $remarks, $leave_id]);
                
                if ($stmt->rowCount() > 0) {
                    $msg = "<div class='alert alert-success alert-modern'>
                            <div class='alert-icon'>✅</div>
                            <div><strong>Leave application rejected successfully!</strong></div>
                           </div>";
                    logActivity($pdo, 'leave_rejected', 'leave_applications', $leave_id);
                } else {
                    $msg = "<div class='alert alert-warning alert-modern'>
                            <div class='alert-icon'>⚠️</div>
                            <div><strong>Leave application could not be rejected. It may have already been processed.</strong></div>
                           </div>";
                }
            }
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>❌</div>
                <div><strong>Error processing request. Please try again.</strong></div>
               </div>";
    }
}

// Fetch leave application details
$stmt = $pdo->prepare("SELECT la.*, u.first_name, u.last_name, u.email, u.phone,
                      CASE 
                          WHEN la.user_type = 'student' THEN (SELECT student_id FROM students WHERE user_id = la.user_id)
                          ELSE 'N/A'
                      END as identifier,
                      CASE 
                          WHEN la.user_type = 'student' THEN (SELECT c.class_name FROM students s 
                                                             JOIN student_classes sc ON s.id = sc.student_id 
                                                             JOIN classes c ON sc.class_id = c.id 
                                                             WHERE s.user_id = la.user_id LIMIT 1)
                          ELSE 'N/A'
                      END as class_name,
                      approver.first_name as approver_first_name,
                      approver.last_name as approver_last_name
                      FROM leave_applications la
                      JOIN users u ON la.user_id = u.id
                      LEFT JOIN users approver ON la.approved_by = approver.id
                      WHERE la.id = ?");
$stmt->execute([$leave_id]);
$leave = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leave) {
    header('Location: leave_management.php');
    exit;
}

// Calculate leave duration details
$from_date = new DateTime($leave['from_date']);
$to_date = new DateTime($leave['to_date']);
$duration = $from_date->diff($to_date)->days + 1;

include '../include/sidebar.php';
?>