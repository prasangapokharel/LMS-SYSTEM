
<?php 
// Include necessary files
include_once '../App/Models/headoffice/Leave.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management - School LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
            max-width: calc(100vw - 250px);
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
                max-width: 100vw;
            }
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 1.25rem;
            color: white;
        }

        .stat-icon.total { background: var(--primary-gradient); }
        .stat-icon.pending { background: var(--warning-gradient); }
        .stat-icon.approved { background: var(--success-gradient); }
        .stat-icon.rejected { background: var(--danger-gradient); }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #718096;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header-modern {
            background: var(--primary-gradient);
            color: white;
            padding: 1.25rem;
            border: none;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-modern {
            border-radius: 10px;
            padding: 0.5rem 1rem;
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

        .btn-warning-modern {
            background: var(--warning-gradient);
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            color: white;
        }

        .badge-modern {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
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

        .badge-status-cancelled {
            background: #a0aec0;
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
            border-radius: 12px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .alert-icon {
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .form-control-modern {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 0.6rem 0.8rem;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modal-modern .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-modern .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .leave-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
        }

        .leave-card.pending { border-left-color: #f093fb; }
        .leave-card.approved { border-left-color: #11998e; }
        .leave-card.rejected { border-left-color: #ff6b6b; }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .leave-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.5);
            padding: 0.6rem;
            border-radius: 8px;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #718096;
            font-weight: 500;
        }

        .detail-value {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
            word-break: break-word;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Pagination styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }

        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .page-item {
            background: white;
        }

        .page-item.active .page-link {
            background: var(--primary-gradient);
            color: white;
            border: none;
        }

        .page-link {
            padding: 0.5rem 1rem;
            color: #4a5568;
            border: none;
            border-right: 1px solid #edf2f7;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .page-link:hover {
            background-color: #edf2f7;
            color: #4a5568;
        }

        .page-item:first-child .page-link {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .page-item:last-child .page-link {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-right: none;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 0.75rem;
            }
            
            .leave-details {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-modern {
                justify-content: center;
                width: 100%;
            }
            
            .user-info {
                flex-wrap: wrap;
            }
            
            .ms-auto {
                margin-left: 0 !important;
                margin-top: 0.5rem;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Leave Management</h1>
                <p class="page-subtitle">Review and manage leave applications</p>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-clipboard"></i>
                    </div>
                    <div class="stat-number"><?= $stats['total_applications'] ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?= $stats['pending_applications'] ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-number"><?= $stats['approved_applications'] ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rejected">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="stat-number"><?= $stats['rejected_applications'] ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <h5 class="mb-3">Filter Applications</h5>
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-control-modern">
                            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $status_filter == 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">User Type</label>
                        <select name="user_type" class="form-select form-control-modern">
                            <option value="all" <?= $user_type_filter == 'all' ? 'selected' : '' ?>>All Types</option>
                            <option value="student" <?= $user_type_filter == 'student' ? 'selected' : '' ?>>Students</option>
                            <option value="teacher" <?= $user_type_filter == 'teacher' ? 'selected' : '' ?>>Teachers</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-modern" value="<?= htmlspecialchars($date_from) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-modern" value="<?= htmlspecialchars($date_to) ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-modern btn-modern w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Leave Applications -->
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5>Leave Applications (<?= count($leave_applications) ?> of <?= $total_records ?> records)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($leave_applications)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No leave applications found</h5>
                        <p class="text-muted">No applications match your current filter criteria.</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($leave_applications as $leave): ?>
                    <div class="leave-card <?= $leave['status'] ?>">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?= strtoupper(substr($leave['first_name'], 0, 1) . substr($leave['last_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h6>
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="badge badge-modern badge-type-<?= $leave['user_type'] ?>">
                                        <?= htmlspecialchars(ucfirst($leave['user_type'])) ?>
                                    </span>
                                    <?php if ($leave['user_type'] == 'student'): ?>
                                    <small class="text-muted">ID: <?= htmlspecialchars($leave['identifier']) ?></small>
                                    <?php else: ?>
                                    <small class="text-muted"><?= htmlspecialchars($leave['email']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="ms-auto">
                                <span class="badge badge-modern badge-status-<?= $leave['status'] ?>">
                                    <?= htmlspecialchars(ucfirst($leave['status'])) ?>
                                </span>
                            </div>
                        </div>

                        <div class="leave-details">
                            <div class="detail-item">
                                <div class="detail-label">Leave Type</div>
                                <div class="detail-value"><?= htmlspecialchars(ucfirst($leave['leave_type'])) ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Duration</div>
                                <div class="detail-value">
                                    <?= htmlspecialchars(date('M d, Y', strtotime($leave['from_date']))) ?> to <?= htmlspecialchars(date('M d, Y', strtotime($leave['to_date']))) ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Total Days</div>
                                <div class="detail-value"><?= htmlspecialchars($leave['total_days']) ?> days</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Applied Date</div>
                                <div class="detail-value"><?= htmlspecialchars(date('M d, Y', strtotime($leave['applied_date']))) ?></div>
                            </div>
                        </div>

                        <?php if ($leave['reason']): ?>
                        <div class="detail-item mb-3">
                            <div class="detail-label">Reason</div>
                            <div class="detail-value"><?= htmlspecialchars($leave['reason']) ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($leave['status'] != 'pending' && $leave['approver_first_name']): ?>
                        <div class="detail-item mb-3">
                            <div class="detail-label">
                                <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?> by
                            </div>
                            <div class="detail-value">
                                <?= htmlspecialchars($leave['approver_first_name'] . ' ' . $leave['approver_last_name']) ?>
                                <?php if ($leave['approved_date']): ?>
                                <small class="text-muted">on <?= htmlspecialchars(date('M d, Y', strtotime($leave['approved_date']))) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($leave['rejection_reason']): ?>
                        <div class="detail-item mb-3">
                            <div class="detail-label">
                                <?= $leave['status'] == 'approved' ? 'Remarks' : 'Rejection Reason' ?>
                            </div>
                            <div class="detail-value"><?= htmlspecialchars($leave['rejection_reason']) ?></div>
                        </div>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <a href="leave_details.php?id=<?= $leave['id'] ?>" class="btn btn-sm btn-info-modern btn-modern">
                                View Details
                            </a>
                            
                            <?php if ($leave['status'] == 'pending'): ?>
                            <button type="button" class="btn btn-sm btn-success-modern btn-modern" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#approveModal<?= $leave['id'] ?>">
                                Approve
                            </button>
                            <button type="button" class="btn btn-sm btn-danger-modern btn-modern" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal<?= $leave['id'] ?>">
                                Reject
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Approve Modal -->
                    <div class="modal fade modal-modern" id="approveModal<?= $leave['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Approve Leave Application</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="leave_id" value="<?= $leave['id'] ?>">
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
                                            Approve Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade modal-modern" id="rejectModal<?= $leave['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Reject Leave Application</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="leave_id" value="<?= $leave['id'] ?>">
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
                                            Reject Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination-container">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1<?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>">First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>">Previous</a>
                            </li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>">Next</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $total_pages ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>">Last</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Add confirmation for form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert-modern');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    });
    </script>
</body>
</html>
