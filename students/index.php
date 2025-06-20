<?php
include_once '../App/Models/student/Index.php';
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
    <meta name="description" content="Student Learning Management System Dashboard">
    <meta name="theme-color" content="#3b82f6">
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        .student-app {
            font-family: var(--font-inter);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .student-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .student-header::before {
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .student-info h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 4px 0;
            letter-spacing: -0.025em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .student-role {
            font-size: 15px;
            opacity: 0.9;
            margin: 0;
            font-weight: 400;
        }

        .attendance-widget {
            text-align: right;
        }

        .attendance-label {
            font-size: 12px;
            opacity: 0.8;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .attendance-percentage {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .student-content {
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .dashboard-section {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
            padding: 20px 24px;
            border-bottom: 1px solid var(--color-gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: var(--color-primary-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .section-icon svg {
            width: 16px;
            height: 16px;
            color: var(--color-primary);
        }

        .section-action {
            font-size: 14px;
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .section-action:hover {
            color: var(--color-primary-dark);
            text-decoration: none;
        }

        .section-body {
            padding: 24px;
        }

        .schedule-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: var(--color-gray-50);
            border-radius: 12px;
            border: 1px solid var(--color-gray-200);
        }

        .schedule-content {
            flex: 1;
            min-width: 0;
        }

        .schedule-subject {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0 0 4px 0;
        }

        .schedule-teacher {
            font-size: 14px;
            color: var(--color-gray-600);
            margin: 0;
        }

        .schedule-time {
            text-align: right;
            flex-shrink: 0;
            margin-left: 16px;
        }

        .schedule-time-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--color-primary);
            margin: 0 0 4px 0;
        }

        .schedule-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-success);
            margin: 0 auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .assignments-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .assignment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: var(--color-gray-50);
            border-radius: 12px;
            border: 1px solid var(--color-gray-200);
            transition: all 0.3s ease;
        }

        .assignment-item:hover {
            background: var(--color-primary-light);
            border-color: var(--color-primary);
        }

        .assignment-content {
            flex: 1;
            min-width: 0;
        }

        .assignment-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0 0 4px 0;
        }

        .assignment-subject {
            font-size: 14px;
            color: var(--color-gray-600);
            margin: 0;
        }

        .assignment-due {
            text-align: right;
            flex-shrink: 0;
            margin-left: 16px;
        }

        .assignment-due-text {
            font-size: 12px;
            color: var(--color-danger);
            font-weight: 600;
            margin: 0;
        }

        .grades-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .grade-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: var(--color-gray-50);
            border-radius: 12px;
            border: 1px solid var(--color-gray-200);
        }

        .grade-content {
            flex: 1;
            min-width: 0;
        }

        .grade-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0 0 4px 0;
        }

        .grade-subject {
            font-size: 14px;
            color: var(--color-gray-600);
            margin: 0;
        }

        .grade-score {
            text-align: right;
            flex-shrink: 0;
            margin-left: 16px;
        }

        .grade-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0;
            line-height: 1;
        }

        .grade-max {
            font-size: 12px;
            color: var(--color-gray-500);
            margin: 0;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: var(--color-white);
            border: 2px solid var(--color-gray-200);
            border-radius: 12px;
            text-decoration: none;
            color: var(--color-gray-700);
            transition: all 0.3s ease;
            min-height: 100px;
        }

        .quick-action:hover {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: var(--color-white);
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .quick-action-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--color-primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .quick-action:hover .quick-action-icon {
            background: rgba(255, 255, 255, 0.2);
        }

        .quick-action-icon svg {
            width: 20px;
            height: 20px;
            color: var(--color-primary);
        }

        .quick-action:hover .quick-action-icon svg {
            color: var(--color-white);
        }

        .quick-action-text {
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            line-height: 1.3;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .stat-card {
            background: var(--color-white);
            border: 1px solid var(--color-gray-200);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--color-primary);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0 0 4px 0;
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--color-gray-600);
            margin: 0;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--color-gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: var(--color-gray-400);
        }

        .empty-icon svg {
            width: 24px;
            height: 24px;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-gray-700);
            margin: 0 0 8px 0;
        }

        .empty-text {
            font-size: 14px;
            color: var(--color-gray-500);
            margin: 0;
            line-height: 1.5;
        }

        @media (min-width: 768px) {
            .student-content {
                padding: 0 32px;
            }

            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .quick-actions {
                grid-template-columns: repeat(4, 1fr);
            }

            .header-top {
                flex-direction: row;
            }
        }

        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 767px) {
            .student-content {
                padding: 0 16px;
            }

            .dashboard-section {
                border-radius: 12px;
            }

            .section-header,
            .section-body {
                padding: 16px;
            }

            .header-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .attendance-widget {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="student-app">
        <div class="student-header">
            <div class="header-content">
                <div class="header-top">
                    <div class="student-info">
                        <h1>Hello, <?= htmlspecialchars($user['first_name']) ?>!</h1>
                        <p class="student-role"><?= htmlspecialchars($student['class_name'] ?? 'Student') ?> - Student Dashboard</p>
                    </div>
                    <div class="attendance-widget">
                        <p class="attendance-label">Attendance Rate</p>
                        <p class="attendance-percentage"><?= $attendance_percentage ?>%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="student-content">
            <div class="dashboard-grid">
                <!-- Today's Schedule -->
                <!-- <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <div class="section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                    <line x1="16" x2="16" y1="2" y2="6"/>
                                    <line x1="8" x2="8" y1="2" y2="6"/>
                                    <line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                            </div>
                            Today's Classes
                        </h2>
                        <span style="font-size: 12px; color: var(--color-gray-500);"><?= date('l, M j') ?></span>
                    </div>
                    <div class="section-body">
                        <?php if (empty($today_schedule)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                    <line x1="16" x2="16" y1="2" y2="6"/>
                                    <line x1="8" x2="8" y1="2" y2="6"/>
                                    <line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                            </div>
                            <h4 class="empty-title">No Classes Today</h4>
                            <p class="empty-text">Enjoy your free day!</p>
                        </div>
                        <?php else: ?>
                        <div class="schedule-list">
                            <?php foreach ($today_schedule as $class): ?>
                            <div class="schedule-item">
                                <div class="schedule-content">
                                    <h3 class="schedule-subject"><?= htmlspecialchars($class['subject_name']) ?></h3>
                                    <p class="schedule-teacher"><?= htmlspecialchars($class['first_name'] . ' ' . $class['last_name']) ?></p>
                                </div>
                                <div class="schedule-time">
                                    <p class="schedule-time-text">
                                        <?= date('g:i A', strtotime($class['start_time'])) ?> - <?= date('g:i A', strtotime($class['end_time'])) ?>
                                    </p>
                                    <div class="schedule-status"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div> -->

                <!-- Upcoming Assignments -->
                <!-- <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <div class="section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                                </svg>
                            </div>
                            Upcoming Tasks
                        </h2>
                        <a href="assignments.php" class="section-action">View All</a>
                    </div>
                    <div class="section-body">
                        <?php if (empty($assignments)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4"/>
                                    <circle cx="12" cy="12" r="10"/>
                                </svg>
                            </div>
                            <h4 class="empty-title">All Caught Up!</h4>
                            <p class="empty-text">No pending assignments</p>
                        </div>
                        <?php else: ?>
                        <div class="assignments-list">
                            <?php foreach ($assignments as $assignment): ?>
                            <div class="assignment-item">
                                <div class="assignment-content">
                                    <h3 class="assignment-title"><?= htmlspecialchars($assignment['title']) ?></h3>
                                    <p class="assignment-subject"><?= htmlspecialchars($assignment['subject_name']) ?></p>
                                </div>
                                <div class="assignment-due">
                                    <p class="assignment-due-text">Due: <?= date('M j', strtotime($assignment['due_date'])) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div> -->

                <!-- Recent Grades -->
                <?php if (!empty($recent_grades)): ?>
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <div class="section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"/>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                                </svg>
                            </div>
                            Recent Grades
                        </h2>
                        <a href="grades.php" class="section-action">View All</a>
                    </div>
                    <div class="section-body">
                        <div class="grades-list">
                            <?php foreach ($recent_grades as $grade): ?>
                            <div class="grade-item">
                                <div class="grade-content">
                                    <h3 class="grade-title"><?= htmlspecialchars($grade['assignment_title']) ?></h3>
                                    <p class="grade-subject"><?= htmlspecialchars($grade['subject_name']) ?></p>
                                </div>
                                <div class="grade-score">
                                    <p class="grade-value"><?= number_format($grade['grade'], 1) ?></p>
                                    <p class="grade-max">/ <?= number_format($grade['max_grade'], 0) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section" style="margin-bottom: 24px;">
                <div class="section-header">
                    <h2 class="section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        Quick Actions
                    </h2>
                </div>
                <div class="section-body">
                    <div class="quick-actions">
                        <a href="leavenotice.php" class="quick-action">
                            <div class="quick-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/>
                                </svg>
                            </div>
                            <span class="quick-action-text">Apply Leave</span>
                        </a>
                        
                        <a href="attendance.php" class="quick-action">
                            <div class="quick-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" x2="12" y1="20" y2="10"/>
                                    <line x1="18" x2="18" y1="20" y2="4"/>
                                    <line x1="6" x2="6" y1="20" y2="16"/>
                                </svg>
                            </div>
                            <span class="quick-action-text">Attendance</span>
                        </a>

                        <a href="report-card.php" class="quick-action">
                            <div class="quick-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"/>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                                </svg>
                            </div>
                            <span class="quick-action-text">View Grades</span>
                        </a>

                        <a href="schedule.php" class="quick-action">
                            <div class="quick-action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                    <line x1="16" x2="16" y1="2" y2="6"/>
                                    <line x1="8" x2="8" y1="2" y2="6"/>
                                    <line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                            </div>
                            <span class="quick-action-text">Schedule</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" x2="18" y1="20" y2="4"/>
                                <line x1="12" x2="12" y1="20" y2="10"/>
                                <line x1="6" x2="6" y1="20" y2="16"/>
                            </svg>
                        </div>
                        Overview
                    </h2>
                </div>
                <div class="section-body">
                    <div class="stats-overview">
                        <div class="stat-card">
                            <div class="stat-value"><?= $attendance['present'] ?></div>
                            <div class="stat-label">Present Days</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?= count($assignments) ?></div>
                            <div class="stat-label">Pending Tasks</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?= count($recent_grades) ?></div>
                            <div class="stat-label">Recent Grades</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth animations to cards
            const cards = document.querySelectorAll('.dashboard-section, .quick-action, .assignment-item, .grade-item, .schedule-item');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Add click animations
            const clickableItems = document.querySelectorAll('.quick-action, .assignment-item, .grade-item, .schedule-item');
            clickableItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Update time display
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    hour12: true 
                });
                const timeElements = document.querySelectorAll('.current-time');
                timeElements.forEach(element => {
                    element.textContent = timeString;
                });
            }

            updateTime();
            setInterval(updateTime, 60000);
        });
    </script>
</body>
</html>
