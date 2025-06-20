<?php
include_once '../App/Models/student/ReportCard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <link rel="stylesheet" href="../assets/css/reportcard.css">
</head>
<body>
    <div class="report-container">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        
        <?php if (!$report_data): ?>
        <div class="exam-selector">
            <h3>Student Report Card</h3>
            <p class="exam-selector-subtitle">Select an exam to generate your report card</p>
            
            <?php if (!empty($available_exams)): ?>
            <div class="exam-list">
                <?php foreach ($available_exams as $exam): ?>
                <div class="exam-item">
                    <h4><?= htmlspecialchars($exam['exam_name']) ?></h4>
                    <div class="exam-meta">
                        <span><?= ucfirst($exam['exam_type']) ?></span>
                        <span><?= htmlspecialchars($exam['academic_year']) ?></span>
                        <span><?= date('M j, Y', strtotime($exam['exam_date_start'])) ?></span>
                        <span><?= $exam['has_results'] ?> subjects</span>
                    </div>
                    <a href="report-card.php?exam_id=<?= $exam['id'] ?>&action=generate_report" class="generate-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10,9 9,9 8,9"/>
                        </svg>
                        Generate Report Card
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                    </svg>
                </div>
                <h4>No Exam Results Available</h4>
                <p>Your teachers haven't published any exam results yet. Check back later for your report cards.</p>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        
        <!-- Report Card Display -->
        <div class="report-header">
            <h1>Student Report Card</h1>
            <h2><?= htmlspecialchars($report_data['exam']['exam_name']) ?> - <?= htmlspecialchars($report_data['exam']['academic_year']) ?></h2>
        </div>

        <div class="student-details">
            <div class="detail-item">
                <span class="detail-label">Student Name:</span>
                <span class="detail-value"><?= htmlspecialchars($report_data['student']['first_name'] . ' ' . $report_data['student']['last_name']) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Class:</span>
                <span class="detail-value"><?= htmlspecialchars($report_data['student']['class_name'] . ' ' . $report_data['student']['section']) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Student ID:</span>
                <span class="detail-value"><?= htmlspecialchars($report_data['student']['student_id']) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Exam Date:</span>
                <span class="detail-value"><?= date('M j, Y', strtotime($report_data['exam']['exam_date_start'])) ?></span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <td colspan="3">Subject</td>
                    <td rowspan="2">Full Marks</td>
                    <td rowspan="2">Marks Obtained</td>
                    <td colspan="2">Grade</td>
                </tr>
                <tr>
                    <td>S.N.</td>
                    <td colspan="2">Name</td>
                    <td>Letter</td>
                    <td>Points</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report_data['results'] as $index => $result): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td colspan="2"><?= htmlspecialchars($result['subject_name']) ?></td>
                    <td><?= number_format($result['full_marks']) ?></td>
                    <td><?= number_format($result['marks_obtained'], 1) ?></td>
                    <td class="grade-<?= strtolower(substr($result['grade'], 0, 1)) ?>"><?= htmlspecialchars($result['grade']) ?></td>
                    <td><?= number_format($result['gpa'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="footer-row">
                    <td colspan="4">Total</td>
                    <td><?= number_format($report_data['total_obtained'], 1) ?></td>
                    <td colspan="2"><?= number_format($report_data['total_marks']) ?></td>
                </tr>
                <tr class="footer-row">
                    <td colspan="4">Overall GPA</td>
                    <td colspan="3"><?= number_format($report_data['overall_gpa'], 2) ?> / 4.0</td>
                </tr>
                <tr class="footer-row">
                    <td colspan="4">Percentage</td>
                    <td colspan="3"><?= number_format($report_data['overall_percentage'], 1) ?>%</td>
                </tr>
                <tr class="footer-row">
                    <td colspan="4">Overall Grade</td>
                    <td colspan="3" class="grade-<?= strtolower(substr($report_data['overall_grade'], 0, 1)) ?>"><?= $report_data['overall_grade'] ?></td>
                </tr>
                <tr class="footer-row">
                    <td colspan="4">Class Position</td>
                    <td colspan="3"><?= $report_data['position'] ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="print-section">
            <button type="button" class="print-btn" onclick="window.print()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-right: 8px;">
                    <polyline points="6,9 6,2 18,2 18,9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print Report Card
            </button>
        </div>
        
        <?php endif; ?>
    </div>

    <?php if (!$report_data): ?>
    <?php include '../include/bootoomnav.php'; ?>
    <?php endif; ?>
</body>
</html>
