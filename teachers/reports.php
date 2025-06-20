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
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        .reports-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .reports-header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .reports-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            pointer-events: none;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 8px;
        }

        .back-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .back-btn svg {
            width: 20px;
            height: 20px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            flex: 1;
            letter-spacing: -0.025em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            font-size: 15px;
            opacity: 0.9;
            margin: 0;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        .reports-content {
            padding: 0 20px;
            max-width: 100%;
        }

        .filters-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .filters-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .filters-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filters-icon svg {
            width: 16px;
            height: 16px;
            color: #6366f1;
        }

        .filters-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .filters-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input, .form-select {
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #111827;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #6366f1, #4f46e5);
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon.students {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
        }

        .stat-icon.classes {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #10b981;
        }

        .stat-icon.assignments {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #f59e0b;
        }

        .stat-icon.attendance {
            background: linear-gradient(135deg, #fce7f3, #fbcfe8);
            color: #ec4899;
        }

        .stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 4px 0;
            line-height: 1;
        }

        .stat-label {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
            font-weight: 500;
        }

        .report-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-title-icon svg {
            width: 16px;
            height: 16px;
            color: #6366f1;
        }

        .card-body {
            padding: 24px;
        }

        .performance-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .performance-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e5e7eb;
        }

        .performance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .class-info {
            display: flex;
            flex-direction: column;
        }

        .class-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .class-section {
            font-size: 12px;
            color: #6b7280;
            margin: 2px 0 0 0;
        }

        .performance-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .metric {
            text-align: center;
        }

        .metric-value {
            font-size: 18px;
            font-weight: 700;
            color: #6366f1;
            margin: 0;
        }

        .metric-label {
            font-size: 11px;
            color: #6b7280;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .activities-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon.log {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #10b981;
        }

        .activity-icon.assignment {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #f59e0b;
        }

        .activity-icon svg {
            width: 16px;
            height: 16px;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 2px 0;
        }

        .activity-meta {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }

        .activity-date {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
        }

        .export-section {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #bae6fd;
        }

        .export-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .export-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .export-icon svg {
            width: 16px;
            height: 16px;
            color: #3b82f6;
        }

        .export-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .export-form {
            display: flex;
            gap: 12px;
            align-items: end;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f1f5f9, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: #9ca3af;
        }

        .empty-icon svg {
            width: 24px;
            height: 24px;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
            margin: 0 0 6px 0;
        }

        .empty-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        @media (min-width: 768px) {
            .reports-content {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 32px;
            }

            .filters-form {
                grid-template-columns: 1fr 1fr 1fr auto;
                align-items: end;
            }

            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .performance-metrics {
                grid-template-columns: repeat(3, 1fr);
            }

            .export-form {
                flex-direction: row;
            }
        }

        @media (max-width: 767px) {
            .reports-content {
                padding: 0 16px;
            }

            .filters-card, .report-card {
                border-radius: 16px;
            }

            .card-header, .card-body {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .performance-metrics {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .export-form {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="reports-app">
        <div class="reports-header">
            <div class="header-content">
                <div class="header-top">
                    <a href="index.php" class="back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="header-title">Reports & Analytics</h1>
                </div>
                <p class="header-subtitle">View comprehensive teaching reports and performance metrics</p>
            </div>
        </div>

        <div class="reports-content">
            <div class="filters-card">
                <div class="filters-header">
                    <div class="filters-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 3h-6l-2 3h-4l-2-3H2v18h20V3z"/>
                            <path d="M8 21l-2-8h12l-2 8"/>
                        </svg>
                    </div>
                    <h2 class="filters-title">Report Filters</h2>
                </div>
                
                <form method="get" class="filters-form">
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
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Generate Report
                    </button>
                </form>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon students">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $overview_stats['total_students'] ?></div>
                            <div class="stat-label">Total Students</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon classes">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $overview_stats['classes_conducted'] ?></div>
                            <div class="stat-label">Classes Conducted</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon assignments">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $overview_stats['total_assignments'] ?></div>
                            <div class="stat-label">Assignments Created</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
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
                            <div class="stat-value"><?= $overview_stats['attendance_rate'] ?>%</div>
                            <div class="stat-label">Attendance Rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <div class="card-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        Class Performance
                    </h3>
                </div>
                <div class="card-body">
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
                        <h4 class="empty-title">No Class Data</h4>
                        <p class="empty-text">No class performance data available for the selected period.</p>
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

            <div class="report-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <div class="card-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        Recent Activities
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_activities)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h4 class="empty-title">No Recent Activities</h4>
                        <p class="empty-text">No recent teaching activities found.</p>
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
                    <button type="submit" class="btn btn-secondary">
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
    </div>

    <?php include '../include/bootoomnav.php'; ?>
</body>
</html>