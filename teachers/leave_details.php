<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$leave_id = $_GET['id'];

// Get leave application details
$stmt = $pdo->prepare("SELECT la.*, u.first_name, u.last_name, u.email, u.phone,
                      s.student_id, s.id as student_db_id
                      FROM leave_applications la
                      JOIN users u ON la.user_id = u.id
                      JOIN students s ON u.id = s.user_id
                      WHERE la.id = ? AND la.user_type = 'student'");
$stmt->execute([$leave_id]);
$leave = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leave) {
    header('Location: index.php');
    exit;
}

// Verify that this student is taught by the current teacher
$stmt = $pdo->prepare("SELECT COUNT(*) FROM students s
                      JOIN student_enrollments se ON s.id = se.student_id
                      JOIN class_subject_teachers cst ON se.class_id = cst.class_id
                      WHERE s.id = ? AND cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$leave['student_db_id'], $user['id']]);
if ($stmt->fetchColumn() == 0) {
    header('Location: index.php');
    exit;
}

$msg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $remarks = $_POST['remarks'] ?? '';
    
    if ($action == 'approve') {
        $stmt = $pdo->prepare("UPDATE leave_applications 
                              SET status = 'approved', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                              WHERE id = ?");
        $stmt->execute([$user['id'], $remarks, $leave_id]);
        $msg = "<div class='alert alert-success'>Leave application approved successfully.</div>";
    } elseif ($action == 'reject') {
        $stmt = $pdo->prepare("UPDATE leave_applications 
                              SET status = 'rejected', approved_by = ?, approved_date = NOW(), rejection_reason = ?
                              WHERE id = ?");
        $stmt->execute([$user['id'], $remarks, $leave_id]);
        $msg = "<div class='alert alert-success'>Leave application rejected successfully.</div>";
    }
    
    // Refresh leave data
    $stmt = $pdo->prepare("SELECT la.*, u.first_name, u.last_name, u.email, u.phone,
                          s.student_id, s.id as student_db_id
                          FROM leave_applications la
                          JOIN users u ON la.user_id = u.id
                          JOIN students s ON u.id = s.user_id
                          WHERE la.id = ?");
    $stmt->execute([$leave_id]);
    $leave = $stmt->fetch(PDO::FETCH_ASSOC);
}

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Application Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Leave Application Details</h2>
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <?= $msg ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Student Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></p>
                        <p><strong>Student ID:</strong> <?= htmlspecialchars($leave['student_id']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($leave['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($leave['phone'] ?? 'N/A') ?></p>
                        <a href="student_details.php?id=<?= $leave['student_db_id'] ?>" class="btn btn-primary">View Student Profile</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Leave Application</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Leave Type:</strong> <?= htmlspecialchars(ucfirst($leave['leave_type'])) ?></p>
                                <p><strong>From Date:</strong> <?= htmlspecialchars($leave['from_date']) ?></p>
                                <p><strong>To Date:</strong> <?= htmlspecialchars($leave['to_date']) ?></p>
                                <p><strong>Total Days:</strong> <?= htmlspecialchars($leave['total_days']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    <?php if ($leave['status'] == 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php elseif ($leave['status'] == 'rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php elseif ($leave['status'] == 'cancelled'): ?>
                                        <span class="badge bg-secondary">Cancelled</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Applied On:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($leave['applied_date']))) ?></p>
                                <?php if ($leave['approved_date']): ?>
                                <p><strong>Processed On:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($leave['approved_date']))) ?></p>
                                <?php endif; ?>
                                <?php if ($leave['emergency_contact']): ?>
                                <p><strong>Emergency Contact:</strong> <?= htmlspecialchars($leave['emergency_contact']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Reason:</h6>
                            <p><?= nl2br(htmlspecialchars($leave['reason'])) ?></p>
                        </div>
                        
                        <?php if ($leave['leave_details']): ?>
                        <div class="mb-3">
                            <h6>Additional Details:</h6>
                            <p><?= nl2br(htmlspecialchars($leave['leave_details'])) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($leave['attachment_url']): ?>
                        <div class="mb-3">
                            <h6>Attachment:</h6>
                            <a href="<?= htmlspecialchars($leave['attachment_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Attachment</a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($leave['rejection_reason']): ?>
                        <div class="mb-3">
                            <h6>Remarks/Reason:</h6>
                            <p><?= nl2br(htmlspecialchars($leave['rejection_reason'])) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($leave['status'] == 'pending'): ?>
                        <hr>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Remarks (Optional)</label>
                                <textarea name="remarks" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="approve" class="btn btn-success">Approve Leave</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject Leave</button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
