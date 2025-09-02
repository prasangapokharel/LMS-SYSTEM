<?php
include_once '../App/Models/teacher/Gradebook.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradebook<?php if($course_info): ?> - <?= htmlspecialchars($course_info['class_name'] . ' ' . $course_info['section'] . ' - ' . $course_info['subject_name']) ?><?php endif; ?></title>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        .gradebook-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .gradebook-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .gradebook-header::before {
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
            margin: 0 0 4px 0;
            letter-spacing: -0.025em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            font-size: 15px;
            opacity: 0.9;
            margin: 0;
            font-weight: 400;
        }

        .gradebook-content {
            padding: 0 20px;
            max-width: 100%;
        }

        .course-selector {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .course-selector h3 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .course-selector-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .course-selector-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #111827;
        }

        .form-select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .gradebook-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .gradebook-header-section {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .gradebook-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .gradebook-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gradebook-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .gradebook-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -2px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background: #f9fafb;
            color: #374151;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .gradebook-table-container {
            overflow-x: auto;
            max-height: 70vh;
        }

        .gradebook-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .gradebook-table th {
            background: #f8fafc;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            font-size: 13px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .gradebook-table th:first-child {
            position: sticky;
            left: 0;
            z-index: 11;
            min-width: 180px;
            text-align: left;
            padding-left: 16px;
            background: #f8fafc;
        }

        .gradebook-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            text-align: center;
            font-size: 13px;
            vertical-align: middle;
        }

        .gradebook-table td:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 9;
            text-align: left;
            padding-left: 16px;
            font-weight: 500;
            min-width: 180px;
        }

        .student-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 2px;
        }

        .student-id {
            font-size: 11px;
            color: #6b7280;
        }

        .grade-display {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
            min-width: 60px;
        }

        .grade-display:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .grade-excellent {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .grade-good {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #2563eb;
            border: 1px solid #93c5fd;
        }

        .grade-average {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #d97706;
            border: 1px solid #fcd34d;
        }

        .grade-poor {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid #f87171;
        }

        .grade-missing {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .assignment-header {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            min-width: 100px;
            max-width: 100px;
            padding: 16px 8px !important;
        }

        .assignment-info {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            font-weight: 400;
        }

        .average-column {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
            color: #059669 !important;
            font-weight: 700 !important;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header {
            padding: 24px 24px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #111827;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
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

        .empty-icon svg {
            width: 24px;
            height: 24px;
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
            margin: 0 0 24px 0;
            line-height: 1.5;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .alert svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid #f87171;
        }

        @media (min-width: 768px) {
            .gradebook-content {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 32px;
            }

            .assignment-header {
                writing-mode: horizontal-tb;
                text-orientation: initial;
                min-width: 140px;
                max-width: 140px;
                padding: 12px 8px !important;
            }

            .gradebook-table th,
            .gradebook-table td {
                padding: 16px 12px;
                font-size: 14px;
            }
        }

        @media (max-width: 767px) {
            .gradebook-content {
                padding: 0 16px;
            }

            .gradebook-container {
                border-radius: 16px;
            }

            .gradebook-header-section {
                padding: 16px;
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .gradebook-actions {
                width: 100%;
                justify-content: stretch;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="gradebook-app fixed">
        <div class="gradebook-header">
            <div class="header-content">
                <div class="header-top">
                    <a href="index.php" class="back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="header-title">Gradebook</h1>
                        <?php if($course_info): ?>
                        <p class="header-subtitle"><?= htmlspecialchars($course_info['class_name'] . ' ' . $course_info['section'] . ' - ' . $course_info['subject_name']) ?></p>
                        <?php else: ?>
                        <p class="header-subtitle">Select a course to view grades</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="gradebook-content">
            <?php if ($msg): ?>
            <div id="messageContainer">
                <?= $msg ?>
            </div>
            <?php endif; ?>

            <div class="course-selector">
                <h3>
                    <div class="course-selector-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    Select Course
                </h3>
                <form method="get">
                    <select name="course" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Select Course --</option>
                        <?php foreach ($teacher_courses as $course): ?>
                        <option value="<?= $course['id'] ?>_<?= $course['subject_id'] ?>" 
                                <?= ($course['id'] == $class_id && $course['subject_id'] == $subject_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['class_name'] . ' ' . $course['section'] . ' - ' . $course['subject_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if ($class_id && $subject_id && $course_info): ?>
            <div class="gradebook-container">
                <div class="gradebook-header-section">
                    <h2 class="gradebook-title">
                        <div class="gradebook-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4"/>
                                <circle cx="12" cy="12" r="10"/>
                            </svg>
                        </div>
                        Student Grades
                    </h2>
                    <div class="gradebook-actions">
                        <a href="assignments.php?class_id=<?= $class_id ?>&subject_id=<?= $subject_id ?>" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                            Manage Assignments
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="exportGrades()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7,10 12,15 17,10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Export Grades
                        </button>
                    </div>
                </div>
                
                <div class="gradebook-table-container">
                    <?php if (empty($students)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h4 class="empty-title">No Students Found</h4>
                        <p class="empty-text">No students are enrolled in this class.</p>
                    </div>
                    <?php elseif (empty($assignments)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                        </div>
                        <h4 class="empty-title">No Assignments Found</h4>
                        <p class="empty-text">Create assignments to start grading students.</p>
                        <a href="assignments.php?class_id=<?= $class_id ?>&subject_id=<?= $subject_id ?>" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Create Assignment
                        </a>
                    </div>
                    <?php else: ?>
                    <table class="gradebook-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <?php foreach ($assignments as $assignment): ?>
                                <th class="assignment-header">
                                    <div><?= htmlspecialchars($assignment['title']) ?></div>
                                    <div class="assignment-info">
                                        <?= $assignment['max_marks'] ?>pts<br>
                                        <?= date('M j', strtotime($assignment['due_date'])) ?>
                                    </div>
                                </th>
                                <?php endforeach; ?>
                                <th class="average-column">Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <div class="student-name"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
                                    <div class="student-id"><?= htmlspecialchars($student['student_id']) ?></div>
                                </td>
                                <?php foreach ($assignments as $assignment): ?>
                                <td>
                                    <?php 
                                    $grade_data = $grades[$student['id']][$assignment['id']] ?? null;
                                    $grade = $grade_data['grade'] ?? null;
                                    $status = $grade_data['status'] ?? 'not_submitted';
                                    
                                    if ($grade !== null) {
                                        $percentage = ($grade / $assignment['max_marks']) * 100;
                                        $grade_class = 'grade-missing';
                                        if ($percentage >= 90) $grade_class = 'grade-excellent';
                                        elseif ($percentage >= 80) $grade_class = 'grade-good';
                                        elseif ($percentage >= 70) $grade_class = 'grade-average';
                                        elseif ($percentage >= 60) $grade_class = 'grade-poor';
                                        
                                        echo "<div class='grade-display $grade_class' onclick='editGrade({$assignment['id']}, {$student['id']}, \"$grade\", \"" . addslashes($assignment['title']) . "\", \"" . addslashes($student['first_name'] . ' ' . $student['last_name']) . "\")'>";
                                        echo number_format($grade, 1) . '/' . $assignment['max_marks'];
                                        echo "</div>";
                                    } elseif ($status == 'submitted') {
                                        echo "<button class='btn btn-sm btn-primary' onclick='editGrade({$assignment['id']}, {$student['id']}, \"\", \"" . addslashes($assignment['title']) . "\", \"" . addslashes($student['first_name'] . ' ' . $student['last_name']) . "\")'>Grade</button>";
                                    } else {
                                        echo "<span class='grade-display grade-missing'>-</span>";
                                    }
                                    ?>
                                </td>
                                <?php endforeach; ?>
                                <td class="average-column">
                                    <?php 
                                    $avg_data = $student_averages[$student['id']];
                                    if ($avg_data['graded_assignments'] > 0) {
                                        echo number_format($avg_data['average'], 1) . '%';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grade Edit Modal -->
    <div id="gradeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Grade</h3>
                <button type="button" class="modal-close" onclick="closeGradeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="post" id="gradeForm">
                    <input type="hidden" name="action" value="update_grade">
                    <input type="hidden" name="assignment_id" id="modalAssignmentId">
                    <input type="hidden" name="student_id" id="modalStudentId">
                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                    <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Assignment</label>
                        <div id="modalAssignmentTitle" style="font-weight: 500; color: #374151;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Student</label>
                        <div id="modalStudentName" style="font-weight: 500; color: #374151;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Grade</label>
                        <input type="number" name="grade" id="modalGrade" class="form-input" step="0.1" min="0" placeholder="Enter grade">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Feedback (Optional)</label>
                        <textarea name="feedback" id="modalFeedback" class="form-textarea" placeholder="Provide feedback to the student..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeGradeModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                            </svg>
                            Save Grade
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        function editGrade(assignmentId, studentId, currentGrade, assignmentTitle, studentName) {
            document.getElementById('modalAssignmentId').value = assignmentId;
            document.getElementById('modalStudentId').value = studentId;
            document.getElementById('modalGrade').value = currentGrade;
            document.getElementById('modalAssignmentTitle').textContent = assignmentTitle;
            document.getElementById('modalStudentName').textContent = studentName;
            document.getElementById('modalFeedback').value = '';
            document.getElementById('gradeModal').classList.add('show');
        }

        function closeGradeModal() {
            document.getElementById('gradeModal').classList.remove('show');
        }

        function exportGrades() {
            const table = document.querySelector('.gradebook-table');
            if (!table) {
                alert('No grades to export');
                return;
            }
            
            let csv = '';
            
            // Get headers
            const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim().replace(/\n/g, ' '));
            csv += headers.join(',') + '\n';
            
            // Get data rows
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = Array.from(row.querySelectorAll('td')).map(td => {
                    return '"' + td.textContent.trim().replace(/"/g, '""').replace(/\n/g, ' ') + '"';
                });
                csv += cells.join(',') + '\n';
            });
            
            // Download CSV
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'gradebook_<?= $course_info ? $course_info['class_name'] . '_' . $course_info['subject_name'] : 'export' ?>.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Handle course selection
        document.addEventListener('DOMContentLoaded', function() {
            const courseSelect = document.querySelector('select[name="course"]');
            if (courseSelect) {
                courseSelect.addEventListener('change', function() {
                    if (this.value) {
                        const [classId, subjectId] = this.value.split('_');
                        window.location.href = `gradebook.php?class_id=${classId}&subject_id=${subjectId}`;
                    }
                });
            }

            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });

        // Close modal when clicking outside
        document.getElementById('gradeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGradeModal();
            }
        });
    </script>
</body>
</html>
