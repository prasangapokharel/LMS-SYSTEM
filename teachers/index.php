<?php 
// Include necessary files
include_once '../App/Models/teacher/Index.php';

// Set default values to prevent undefined variable warnings
$current_nepali_date = $current_nepali_date ?? date('F d, Y');
$classes = $classes ?? [];
$total_students = $total_students ?? 0;
$assignment_count = $assignment_count ?? 0;
$pending_leaves = $pending_leaves ?? [];
$attendance_summary = $attendance_summary ?? [];
$recent_assignments = $recent_assignments ?? [];
$monthly_logs = $monthly_logs ?? [];

// Ensure attendance summary has proper structure
if (!empty($attendance_summary)) {
    foreach ($attendance_summary as &$summary) {
        $summary['present'] = $summary['present'] ?? 0;
        $summary['absent'] = $summary['absent'] ?? 0;
        $summary['total'] = $summary['total'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Mobile App Pattern Styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-weight: 500;
            background: #f8fafc;
            margin: 0;
            padding: 0;
            padding-bottom: 80px;
        }

        .app-container {
            max-width: 100%;
            margin: 0 auto;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Header Section */
        .app-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 16px 24px;
            border-radius: 0 0 24px 24px;
            margin-bottom: 16px;
        }

        .header-title {
            display: flex;
            align-items: center;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header-title i {
            margin-right: 12px;
            font-size: 32px;
        }

        .header-subtitle {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
        }

        .header-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        .header-btn-primary {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
        }

        /* Stats Grid */
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 0 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            flex: 1;
            min-width: calc(50% - 6px);
            background: white;
            border-radius: 16px;
            padding: 20px 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #f1f5f9;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 8px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: #64748b;
        }

        /* Stat Card Variants */
        .stat-card-primary .stat-icon {
            background: #dbeafe;
            color: #3b82f6;
        }
        .stat-card-primary .stat-number {
            color: #3b82f6;
        }

        .stat-card-success .stat-icon {
            background: #dcfce7;
            color: #22c55e;
        }
        .stat-card-success .stat-number {
            color: #22c55e;
        }

        .stat-card-warning .stat-icon {
            background: #fef3c7;
            color: #f59e0b;
        }
        .stat-card-warning .stat-number {
            color: #f59e0b;
        }

        .stat-card-danger .stat-icon {
            background: #fee2e2;
            color: #ef4444;
        }
        .stat-card-danger .stat-number {
            color: #ef4444;
        }

        /* Section Cards */
        .section-card {
            background: white;
            border-radius: 16px;
            margin: 0 16px 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .section-header {
            padding: 20px 20px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-title {
            display: flex;
            align-items: center;
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .section-title i {
            margin-right: 8px;
            color: #3b82f6;
        }

        .section-subtitle {
            font-size: 14px;
            color: #64748b;
            font-weight: 400;
        }

        .section-body {
            padding: 20px;
        }

        /* Attendance Summary */
        .attendance-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .attendance-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e2e8f0;
        }

        .attendance-class {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .attendance-stats {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .attendance-stat {
            text-align: center;
            flex: 1;
        }

        .attendance-number {
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }

        .attendance-label {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
        }

        .text-success { color: #22c55e; }
        .text-danger { color: #ef4444; }
        .text-primary { color: #3b82f6; }

        /* Quick Actions Grid */
        .actions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .action-item {
            flex: 1;
            min-width: calc(33.333% - 8px);
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 12px;
            text-decoration: none;
            text-align: center;
            color: inherit;
        }

        .action-item:hover {
            background: #f1f5f9;
            text-decoration: none;
            color: inherit;
        }

        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 18px;
        }

        .action-text {
            font-size: 12px;
            font-weight: 500;
            color: #1e293b;
            line-height: 1.3;
        }

        /* Action Variants */
        .action-primary .action-icon {
            background: #dbeafe;
            color: #3b82f6;
        }

        .action-info .action-icon {
            background: #e0f2fe;
            color: #0ea5e9;
        }

        .action-success .action-icon {
            background: #dcfce7;
            color: #22c55e;
        }

        .action-warning .action-icon {
            background: #fef3c7;
            color: #f59e0b;
        }

        .action-purple .action-icon {
            background: #f3e8ff;
            color: #8b5cf6;
        }

        .action-dark .action-icon {
            background: #f1f5f9;
            color: #475569;
        }

        /* Data Lists */
        .data-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .data-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .data-item:last-child {
            border-bottom: none;
        }

        .data-content {
            flex: 1;
        }

        .data-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .data-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .data-details {
            font-size: 12px;
            color: #64748b;
        }

        .data-actions {
            display: flex;
            gap: 8px;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-primary {
            background: #dbeafe;
            color: #3b82f6;
        }

        .badge-success {
            background: #dcfce7;
            color: #22c55e;
        }

        .badge-warning {
            background: #fef3c7;
            color: #f59e0b;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 11px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 24px;
            color: #94a3b8;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .empty-text {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* Responsive Design */
        @media (min-width: 768px) {
            .app-container {
                max-width: 768px;
            }
            
            .stats-grid {
                gap: 16px;
            }
            
            .stat-card {
                min-width: calc(25% - 12px);
            }
            
            .actions-grid {
                gap: 16px;
            }
            
            .action-item {
                min-width: calc(16.666% - 14px);
            }
        }

        @media (min-width: 1024px) {
            .app-container {
                max-width: 1024px;
                padding: 0 20px;
            }
        }
    </style>
</head>
<body>
    <?php include '../include/loader.php'; ?>

    <div class="app-container">
        <!-- App Header -->
        <div class="app-header">
            <h1 class="header-title">
                <i class="fas fa-chalkboard-teacher"></i>
                Teacher Dashboard
            </h1>
            <p class="header-subtitle">
                स्वागतम्, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>!<br>
                आजको मिति: <?= htmlspecialchars($current_nepali_date) ?>
            </p>
            <div class="header-actions">
                <a href="attendance.php" class="header-btn header-btn-primary">
                    <i class="fas fa-calendar-check"></i>
                    Take Attendance
                </a>
                <a href="assignments.php" class="header-btn">
                    <i class="fas fa-tasks"></i>
                    Assignments
                </a>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stat-number"><?= count($classes) ?></div>
                <div class="stat-label">My Classes</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>Active Classes</span>
                </div>
            </div>
            
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?= $total_students ?></div>
                <div class="stat-label">Total Students</div>
                <div class="stat-trend">
                    <i class="fas fa-user-graduate"></i>
                    <span>Enrolled Students</span>
                </div>
            </div>
            
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-number"><?= $assignment_count ?></div>
                <div class="stat-label">Assignments</div>
                <div class="stat-trend">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Total Created</span>
                </div>
            </div>
            
            <div class="stat-card stat-card-danger">
                <div class="stat-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-number"><?= count($pending_leaves) ?></div>
                <div class="stat-label">Pending Leaves</div>
                <div class="stat-trend">
                    <i class="fas fa-clock"></i>
                    <span>Awaiting Review</span>
                </div>
            </div>
        </div>

        <!-- Today's Attendance Summary -->
        <?php if (!empty($attendance_summary)): ?>
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-calendar-day"></i>
                    Today's Attendance Summary
                </h2>
                <p class="section-subtitle">Attendance status for <?= date('F d, Y') ?></p>
            </div>
            <div class="section-body">
                <div class="attendance-grid">
                    <?php foreach ($attendance_summary as $summary): ?>
                    <div class="attendance-item">
                        <div class="attendance-class">
                            <?= htmlspecialchars($summary['class_name']) ?> - <?= htmlspecialchars($summary['subject_name']) ?>
                        </div>
                        <div class="attendance-stats">
                            <div class="attendance-stat">
                                <div class="attendance-number text-success"><?= intval($summary['present']) ?></div>
                                <div class="attendance-label">Present</div>
                            </div>
                            <div class="attendance-stat">
                                <div class="attendance-number text-danger"><?= intval($summary['absent']) ?></div>
                                <div class="attendance-label">Absent</div>
                            </div>
                            <div class="attendance-stat">
                                <div class="attendance-number text-primary"><?= intval($summary['total']) ?></div>
                                <div class="attendance-label">Total</div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h2>
                <p class="section-subtitle">Frequently used teacher functions</p>
            </div>
            <div class="section-body">
                <div class="actions-grid">
                    <a href="attendance.php" class="action-item action-primary">
                        <div class="action-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="action-text">Take Attendance</div>
                    </a>
                    
                    <a href="assignments.php" class="action-item action-info">
                        <div class="action-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="action-text">Manage Assignments</div>
                    </a>
                    
                    <a href="students.php" class="action-item action-success">
                        <div class="action-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="action-text">View Students</div>
                    </a>
                    
                    <a href="teacher_log.php" class="action-item action-warning">
                        <div class="action-icon">
                            <i class="fas fa-journal-whills"></i>
                        </div>
                        <div class="action-text">Daily Log</div>
                    </a>
                    
                    <a href="grades.php" class="action-item action-purple">
                        <div class="action-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="action-text">Grade Management</div>
                    </a>
                    
                    <a href="schedule.php" class="action-item action-dark">
                        <div class="action-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="action-text">Class Schedule</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- My Classes -->
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-chalkboard"></i>
                    My Classes & Subjects
                </h2>
                <p class="section-subtitle">Classes assigned to you</p>
            </div>
            <div class="section-body">
                <?php if (empty($classes)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <h3 class="empty-title">No Classes Assigned</h3>
                    <p class="empty-text">You don't have any classes assigned yet. Contact the administration for class assignments.</p>
                    <a href="../headoffice/manage_users.php" class="btn btn-secondary">Contact Admin</a>
                </div>
                <?php else: ?>
                <div class="data-list">
                    <?php foreach ($classes as $class): ?>
                    <div class="data-item">
                        <div class="data-content">
                            <div class="data-title"><?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?></div>
                            <div class="data-meta">
                                <span class="badge badge-primary"><?= htmlspecialchars($class['subject_name']) ?></span>
                            </div>
                        </div>
                        <div class="data-actions">
                            <a href="class_details.php?class_id=<?= $class['id'] ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="attendance.php?class_id=<?= $class['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-check"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Assignments -->
        <?php if (!empty($recent_assignments)): ?>
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tasks"></i>
                    Recent Assignments
                </h2>
                <p class="section-subtitle">Latest assignments you've created</p>
            </div>
            <div class="section-body">
                <div class="data-list">
                    <?php foreach ($recent_assignments as $assignment): ?>
                    <div class="data-item">
                        <div class="data-content">
                            <div class="data-title"><?= htmlspecialchars($assignment['title']) ?></div>
                            <div class="data-meta">
                                <span><?= htmlspecialchars($assignment['class_name']) ?> - <?= htmlspecialchars($assignment['subject_name']) ?></span>
                            </div>
                            <div class="data-details">
                                <i class="fas fa-calendar"></i>
                                Due: <?= date('M d, Y', strtotime($assignment['due_date'])) ?>
                            </div>
                        </div>
                        <div class="data-actions">
                            <a href="assignment_details.php?id=<?= $assignment['id'] ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Leave Applications -->
        <?php if (!empty($pending_leaves)): ?>
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-calendar-times"></i>
                    Pending Leave Applications
                </h2>
                <p class="section-subtitle">Student leave requests awaiting review</p>
            </div>
            <div class="section-body">
                <div class="data-list">
                    <?php foreach ($pending_leaves as $leave): ?>
                    <div class="data-item">
                        <div class="data-content">
                            <div class="data-title"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></div>
                            <div class="data-meta">
                                <span>ID: <?= htmlspecialchars($leave['student_id']) ?></span>
                                <span class="badge badge-warning"><?= ucfirst(htmlspecialchars($leave['leave_type'])) ?></span>
                            </div>
                            <div class="data-details">
                                <i class="fas fa-calendar-range"></i>
                                <?= date('M d', strtotime($leave['from_date'])) ?> - <?= date('M d, Y', strtotime($leave['to_date'])) ?>
                                (<?= intval($leave['total_days']) ?> days)
                            </div>
                        </div>
                        <div class="data-actions">
                            <a href="../headoffice/leave_details.php?id=<?= $leave['id'] ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i> Review
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>
</body>
</html>
