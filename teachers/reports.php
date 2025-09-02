<?php
include_once '../App/Models/teacher/Report.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - LMS</title>
    <meta name="description" content="View comprehensive teaching reports and analytics">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <link rel="stylesheet" href="../assets/css/teacher/reports.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>
<body>
    <div class="container ">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="header-title">Reports & Analytics</h1>
                <p class="header-subtitle">View comprehensive teaching reports and performance metrics</p>
            </div>
        </div>

        <!-- Report Filters -->
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon">
                <h2 class="card-title">Report Filters</h2>
            </div>
            
            <form method="get" class="filters-form">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-input" value="<?= $start_date ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-input" value="<?= $end_date ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Report Type</label>
                        <select name="report_type" class="form-select">
                            <option value="overview" <?= $report_type == 'overview' ? 'selected' : '' ?>>Overview</option>
                            <option value="detailed" <?= $report_type == 'detailed' ? 'selected' : '' ?>>Detailed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon students">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $overview_stats['total_students'] ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon classes">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $overview_stats['classes_conducted'] ?></div>
                    <div class="stat-label">Classes Conducted</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon assignments">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $overview_stats['total_assignments'] ?></div>
                    <div class="stat-label">Assignments Created</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon attendance">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4"/>
                        <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                        <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                        <path d="M12 3v6"/>
                        <path d="M12 15v6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $overview_stats['attendance_rate'] ?>%</div>
                    <div class="stat-label">Attendance Rate</div>
                </div>
            </div>
        </div>

        <!-- Class Performance -->
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3 class="card-title">Class Performance</h3>
            </div>
            
            <div class="card-content">
                <?php if (empty($class_performance)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="empty-title">No Class Data</div>
                    <div class="empty-text">No class performance data available for the selected period.</div>
                </div>
                <?php else: ?>
                <div class="performance-list">
                    <?php foreach ($class_performance as $cp): ?>
                    <div class="performance-item">
                        <div class="performance-header">
                            <div class="class-info">
                                <div class="class-name"><?= htmlspecialchars($cp['class_info']['class_name']) ?></div>
                                <div class="class-section">Section <?= htmlspecialchars($cp['class_info']['section']) ?></div>
                            </div>
                        </div>
                        <div class="performance-metrics">
                            <div class="metric">
                                <div class="metric-value"><?= $cp['performance']['logs_count'] ?></div>
                                <div class="metric-label">Logs</div>
                            </div>
                            <div class="metric">
                                <div class="metric-value"><?= $cp['performance']['assignments_count'] ?></div>
                                <div class="metric-label">Assignments</div>
                            </div>
                            <div class="metric">
                                <div class="metric-value"><?= round($cp['performance']['avg_attendance'] ?? 0, 1) ?>%</div>
                                <div class="metric-label">Attendance</div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <h3 class="card-title">Recent Activities</h3>
            </div>
            
            <div class="card-content">
                <?php if (empty($recent_activities)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div class="empty-title">No Recent Activities</div>
                    <div class="empty-text">No recent teaching activities found.</div>
                </div>
                <?php else: ?>
                <div class="activities-list">
                    <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon <?= $activity['type'] ?>">
                            <?php if ($activity['type'] == 'log'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                            <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                            <?php endif; ?>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title"><?= htmlspecialchars($activity['title']) ?></div>
                            <div class="activity-meta">
                                <?= htmlspecialchars($activity['class_name'] . ' ' . $activity['section']) ?> - 
                                <?= htmlspecialchars($activity['subject_name']) ?>
                            </div>
                        </div>
                        <div class="activity-date">
                            <?= date('M j, Y', strtotime($activity['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <div class="export-header">
                <div class="export-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </div>
                <h3 class="export-title">Export Report</h3>
            </div>
            
            <form method="post" class="export-form">
                <input type="hidden" name="action" value="export_report">
                <div class="form-group">
                    <label class="form-label">Export Format</label>
                    <select name="export_format" class="form-select">
                        <option value="csv">CSV File</option>
                        <option value="pdf">PDF Report</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export Report
                </button>
            </form>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>
</body>
</html>