<?php
include_once '../App/Models/student/Schedule.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Schedule</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        .schedule-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .schedule-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
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
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .schedule-content {
            padding: 0 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .schedule-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .schedule-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .nearest-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nearest-exam {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-left: 4px solid #2563eb;
            position: relative;
        }

        .exam-header {
            padding: 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .exam-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
        }

        .exam-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
        }

        .meta-icon {
            width: 16px;
            height: 16px;
            color: #9ca3af;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .subjects-section {
            padding: 0 24px 24px;
        }

        .subjects-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .subject-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .subject-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .subject-name {
            font-weight: 600;
            color: #111827;
            font-size: 14px;
        }

        .subject-code {
            font-size: 12px;
            color: #6b7280;
        }

        .subject-schedule {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }

        .subject-date {
            font-size: 13px;
            color: #374151;
            font-weight: 500;
        }

        .subject-time {
            font-size: 12px;
            color: #6b7280;
        }

        .duration-badge {
            background: #e5e7eb;
            color: #374151;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f1f5f9, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #9ca3af;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 700;
            color: #374151;
            margin: 0 0 8px 0;
        }

        .empty-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .schedule-content {
                padding: 0 16px;
            }

            .exam-meta {
                flex-direction: column;
                gap: 12px;
            }

            .subject-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .subject-schedule {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="schedule-app">
        <div class="schedule-header">
            <div class="header-content">
                <a href="index.php" class="back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <path d="M19 12H5"/>
                        <path d="M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="header-title">Exam Schedule</h1>
            </div>
        </div>

        <div class="schedule-content">
            <?php if (!empty($exam_schedules)): ?>
                <?php foreach ($exam_schedules as $index => $schedule): ?>
                    <?php 
                    $exam = $schedule['exam'];
                    $subjects = $schedule['subjects'];
                    $isNearest = $index === 0 && isNearestExam($exam['exam_date_start']);
                    $statusColor = getExamStatusColor($exam['exam_date_start']);
                    $daysUntil = getDaysUntilExam($exam['exam_date_start']);
                    ?>
                    
                    <div class="schedule-card <?= $isNearest ? 'nearest-exam' : '' ?>">
                        <?php if ($isNearest): ?>
                            <div class="nearest-badge">Nearest</div>
                        <?php endif; ?>
                        
                        <div class="exam-header">
                            <h3 class="exam-title"><?= htmlspecialchars($exam['exam_name']) ?></h3>
                            
                            <div class="exam-meta">
                                <div class="meta-item">
                                    <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <?= formatScheduleDate($exam['exam_date_start']) ?>
                                    <?php if ($exam['exam_date_start'] != $exam['exam_date_end']): ?>
                                        - <?= formatScheduleDate($exam['exam_date_end']) ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="meta-item">
                                    <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                    </svg>
                                    <?= ucfirst(str_replace('_', ' ', $exam['exam_type'])) ?>
                                </div>
                                
                                <div class="meta-item">
                                    <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <?= $exam['subject_count'] ?> subjects
                                </div>
                            </div>
                            
                            <div class="status-badge" style="background-color: <?= $statusColor ?>; color: white;">
                                <svg style="width: 12px; height: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                                <?= $daysUntil ?>
                            </div>
                        </div>

                        <?php if (!empty($subjects)): ?>
                        <div class="subjects-section">
                            <h4 class="subjects-title">
                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                </svg>
                                Subject Schedule
                            </h4>
                            
                            <?php foreach ($subjects as $subject): ?>
                            <div class="subject-item">
                                <div class="subject-info">
                                    <div class="subject-name"><?= htmlspecialchars($subject['subject_name']) ?></div>
                                    <div class="subject-code"><?= htmlspecialchars($subject['subject_code']) ?></div>
                                </div>
                                
                                <div class="subject-schedule">
                                    <div class="subject-date"><?= formatScheduleDate($subject['exam_date']) ?></div>
                                    <div class="subject-time">
                                        <?= formatScheduleTime($subject['exam_time']) ?>
                                        <?php if ($subject['duration_minutes']): ?>
                                            <span class="duration-badge"><?= $subject['duration_minutes'] ?>min</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <h4 class="empty-title">No Exam Schedules Available</h4>
                    <p class="empty-text">There are no scheduled exams for your class at the moment. Check back later for updates.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>
</body>
</html>
