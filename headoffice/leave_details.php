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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Details - School LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        a {
            text-decoration: none;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-content {
            padding: 2rem;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-modern {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 600;
        }

        .btn-modern {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-modern {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-success-modern {
            background: var(--success-gradient);
            color: white;
        }

        .btn-danger-modern {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-info-modern {
            background: var(--info-gradient);
            color: white;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            color: white;
        }

        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge-status-pending {
            background: var(--warning-gradient);
            color: white;
        }

        .badge-status-approved {
            background: var(--success-gradient);
            color: white;
        }

        .badge-status-rejected {
            background: var(--danger-gradient);
            color: white;
        }

        .badge-type-student {
            background: var(--info-gradient);
            color: white;
        }

        .badge-type-teacher {
            background: var(--success-gradient);
            color: white;
        }

        .alert-modern {
            border-radius: 16px;
            border: none;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .alert-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .user-info-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.7);
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid;
        }

        .detail-item.primary { border-left-color: #667eea; }
        .detail-item.success { border-left-color: #11998e; }
        .detail-item.warning { border-left-color: #f093fb; }
        .detail-item.info { border-left-color: #4facfe; }

        .detail-label {
            font-size: 0.875rem;
            color: #718096;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-gradient);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.25rem;
            top: 1.5rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-gradient);
        }

        .form-control-modern {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modal-modern .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-modern .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
        }

        .attachment-preview {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            border: 2px dashed #e2e8f0;
        }

        .attachment-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-grey-50">
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Leave Application Details</h1>
                    <p class="page-subtitle">Review detailed information about this leave request</p>
                </div>
                <a href="leave_management.php" class="btn btn-light btn-modern">
                    <i class="fas fa-arrow-left"></i>
                    Back to Leave Management
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        <?= $msg ?>

        <!-- User Information Card -->
        <div class="user-info-card">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <div class="user-avatar mx-auto">
                        <?= strtoupper(substr($leave['first_name'], 0, 1) . substr($leave['last_name'], 0, 1)) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="mb-2"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h4>
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge badge-modern badge-type-<?= $leave['user_type'] ?>">
                            <?= htmlspecialchars(ucfirst($leave['user_type'])) ?>
                        </span>
                        <span class="badge badge-modern badge-status-<?= $leave['status'] ?>">
                            <?= htmlspecialchars(ucfirst($leave['status'])) ?>
                        </span>
                    </div>
                    <p class="text-muted mb-1">
                        <i class="fas fa-envelope me-2"></i>
                        <?= htmlspecialchars($leave['email']) ?>
                    </p>
                    <?php if ($leave['phone']): ?>
                    <p class="text-muted mb-1">
                        <i class="fas fa-phone me-2"></i>
                        <?= htmlspecialchars($leave['phone']) ?>
                    </p>
                    <?php endif; ?>
                    <?php if ($leave['user_type'] == 'student'): ?>
                    <p class="text-muted mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        Student ID: <?= htmlspecialchars($leave['identifier']) ?>
                        <?php if ($leave['class_name']): ?>
                        | Class: <?= htmlspecialchars($leave['class_name']) ?>
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-end">
                    <?php if ($leave['status'] == 'pending'): ?>
                    <button type="button" class="btn btn-success-modern btn-modern me-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#approveModal">
                        <i class="fas fa-check"></i>
                        Approve
                    </button>
                    <button type="button" class="btn btn-danger-modern btn-modern" 
                            data-bs-toggle="modal" 
                            data-bs-target="#rejectModal">
                        <i class="fas fa-times"></i>
                        Reject
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Leave Details -->
        <div class="modern-card">
            <div class="card-header-modern">
                <h5>Leave Application Information</h5>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item primary">
                        <div class="detail-label">Leave Type</div>
                        <div class="detail-value"><?= htmlspecialchars(ucfirst($leave['leave_type'])) ?></div>
                    </div>
                    <div class="detail-item info">
                        <div class="detail-label">From Date</div>
                        <div class="detail-value"><?= htmlspecialchars(date('M d, Y', strtotime($leave['from_date']))) ?></div>
                    </div>
                    <div class="detail-item info">
                        <div class="detail-label">To Date</div>
                        <div class="detail-value"><?= htmlspecialchars(date('M d, Y', strtotime($leave['to_date']))) ?></div>
                    </div>
                    <div class="detail-item warning">
                        <div class="detail-label">Total Days</div>
                        <div class="detail-value"><?= $duration ?> days</div>
                    </div>
                    <div class="detail-item success">
                        <div class="detail-label">Applied Date</div>
                        <div class="detail-value"><?= htmlspecialchars(date('M d, Y g:i A', strtotime($leave['applied_date']))) ?></div>
                    </div>
                    <?php if ($leave['emergency_contact']): ?>
                    <div class="detail-item primary">
                        <div class="detail-label">Emergency Contact</div>
                        <div class="detail-value"><?= htmlspecialchars($leave['emergency_contact']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($leave['reason']): ?>
                <div class="detail-item primary mb-3">
                    <div class="detail-label">Reason for Leave</div>
                    <div class="detail-value"><?= nl2br(htmlspecialchars($leave['reason'])) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($leave['leave_details']): ?>
                <div class="detail-item info mb-3">
                    <div class="detail-label">Additional Details</div>
                    <div class="detail-value"><?= nl2br(htmlspecialchars($leave['leave_details'])) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($leave['attachment_url']): ?>
                <div class="detail-item warning">
                    <div class="detail-label">Attachment</div>
                    <div class="detail-value">
                        <div class="attachment-preview">
                            <?php 
                            $file_extension = strtolower(pathinfo($leave['attachment_url'], PATHINFO_EXTENSION));
                            if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): 
                            ?>
                            <img src="../<?= htmlspecialchars($leave['attachment_url']) ?>" alt="Leave attachment" class="mb-2">
                            <br>
                            <?php endif; ?>
                            <a href="../<?= htmlspecialchars($leave['attachment_url']) ?>" 
                               class="btn btn-info-modern btn-modern btn-sm" 
                               target="_blank">
                                <i class="fas fa-download"></i>
                                Download Attachment
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approval History -->
        <?php if ($leave['status'] != 'pending'): ?>
        <div class="modern-card">
            <div class="card-header-modern">
                <h5>Approval History</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <h6 class="mb-2">Application Submitted</h6>
                        <p class="text-muted mb-1">
                            <i class="fas fa-calendar me-2"></i>
                            <?= htmlspecialchars(date('M d, Y g:i A', strtotime($leave['applied_date']))) ?>
                        </p>
                        <p class="mb-0">Leave application submitted by <?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></p>
                    </div>
                    
                    <div class="timeline-item">
                        <h6 class="mb-2">
                            Application <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?>
                        </h6>
                        <?php if ($leave['approved_date']): ?>
                        <p class="text-muted mb-1">
                            <i class="fas fa-calendar me-2"></i>
                            <?= htmlspecialchars(date('M d, Y g:i A', strtotime($leave['approved_date']))) ?>
                        </p>
                        <?php endif; ?>
                        <?php if ($leave['approver_first_name']): ?>
                        <p class="mb-2">
                            <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?> by 
                            <strong><?= htmlspecialchars($leave['approver_first_name'] . ' ' . $leave['approver_last_name']) ?></strong>
                        </p>
                        <?php endif; ?>
                        <?php if ($leave['rejection_reason']): ?>
                        <div class="alert alert-light">
                            <strong><?= $leave['status'] == 'approved' ? 'Remarks:' : 'Rejection Reason:' ?></strong><br>
                            <?= nl2br(htmlspecialchars($leave['rejection_reason'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Approve Modal -->
    <div class="modal fade modal-modern" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>
                        Approve Leave Application
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="approve">
                        <div class="text-center mb-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p>Are you sure you want to approve this leave application for <strong><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></strong>?</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control form-control-modern" rows="3" placeholder="Add any remarks or conditions..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success-modern btn-modern">
                            <i class="fas fa-check"></i>
                            Approve Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade modal-modern" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i>
                        Reject Leave Application
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reject">
                        <div class="text-center mb-3">
                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                            <p>Are you sure you want to reject this leave application for <strong><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></strong>?</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="remarks" class="form-control form-control-modern" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger-modern btn-modern">
                            <i class="fas fa-times"></i>
                            Reject Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
</body>
</html>
