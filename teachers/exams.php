<?php
include_once '../App/Models/teacher/Exam.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Management</title>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        .exam-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .exam-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
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
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .exam-content {
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .exam-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            font-size: 14px;
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
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .exam-list {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .exam-list-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .exam-list-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .exam-item {
            padding: 20px 24px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .exam-item:last-child {
            border-bottom: none;
        }

        .exam-info h4 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .exam-meta {
            font-size: 13px;
            color: #6b7280;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .exam-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-scheduled {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-ongoing {
            background: #fef3c7;
            color: #d97706;
        }

        .status-completed {
            background: #d1fae5;
            color: #059669;
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
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
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
        }

        .modal-body {
            padding: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            background: white;
            color: #111827;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .subject-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #f9fafb;
        }

        .subject-card.selected {
            border-color: #10b981;
            background: #ecfdf5;
        }

        .subject-checkbox {
            margin-bottom: 12px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .results-table th,
        .results-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .results-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
        }

        .results-input {
            width: 80px;
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            text-align: center;
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

        @media (max-width: 768px) {
            .exam-content {
                padding: 0 16px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .exam-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .exam-actions {
                width: 100%;
                justify-content: stretch;
            }

            .exam-actions .btn {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <div class="exam-app">
        <div class="exam-header">
            <div class="header-content">
                <a href="index.php" class="back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5"/>
                        <path d="M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="header-title">Exam Management</h1>
            </div>
        </div>

        <div class="exam-content">
            <?php if ($msg): ?>
            <?= $msg ?>
            <?php endif; ?>

            <div class="exam-stats">
                <div class="stat-card">
                    <div class="stat-value"><?= count($exams) ?></div>
                    <div class="stat-label">Total Exams</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= count(array_filter($exams, fn($e) => $e['status'] == 'scheduled')) ?></div>
                    <div class="stat-label">Scheduled</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= count(array_filter($exams, fn($e) => $e['status'] == 'completed')) ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= array_sum(array_column($exams, 'students_with_results')) ?></div>
                    <div class="stat-label">Results Entered</div>
                </div>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-primary" onclick="showCreateExamModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Create New Exam
                </button>
            </div>

            <?php if (!empty($exams)): ?>
            <div class="exam-list">
                <div class="exam-list-header">
                    <h3 class="exam-list-title">Your Exams</h3>
                </div>
                <?php foreach ($exams as $exam): ?>
                <div class="exam-item">
                    <div class="exam-info">
                        <h4><?= htmlspecialchars($exam['exam_name']) ?></h4>
                        <div class="exam-meta">
                            <span><?= htmlspecialchars($exam['class_name'] . ' ' . $exam['section']) ?></span>
                            <span><?= ucfirst($exam['exam_type']) ?></span>
                            <span><?= date('M j, Y', strtotime($exam['exam_date_start'])) ?> - <?= date('M j, Y', strtotime($exam['exam_date_end'])) ?></span>
                            <span class="status-badge status-<?= $exam['status'] ?>"><?= ucfirst($exam['status']) ?></span>
                            <span><?= $exam['subject_count'] ?> Subjects</span>
                        </div>
                    </div>
                    <div class="exam-actions">
                        <a href="exams.php?exam_id=<?= $exam['id'] ?>&action=enter_results" class="btn btn-primary btn-sm">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Enter Results
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="viewExamDetails(<?= $exam['id'] ?>)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            View Details
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="exam-list">
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #9ca3af;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        </svg>
                    </div>
                    <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin: 0 0 8px 0;">No Exams Created</h4>
                    <p style="font-size: 14px; color: #6b7280; margin: 0 0 24px 0;">Create your first exam to start managing student assessments.</p>
                    <button type="button" class="btn btn-primary" onclick="showCreateExamModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Create Your First Exam
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($exam_details): ?>
            <div class="exam-list" style="margin-top: 24px;">
                <div class="exam-list-header">
                    <h3 class="exam-list-title">Enter Results - <?= htmlspecialchars($exam_details['exam_name']) ?></h3>
                </div>
                <div style="padding: 24px;">
                    <form method="post">
                        <input type="hidden" name="action" value="enter_results">
                        <input type="hidden" name="exam_id" value="<?= $exam_details['id'] ?>">
                        
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <?php foreach ($exam_subjects_list as $subject): ?>
                                    <th><?= htmlspecialchars($subject['subject_name']) ?><br>
                                        <small>(<?= $subject['full_marks'] ?> marks)</small>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exam_students as $student): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong><br>
                                        <small><?= htmlspecialchars($student['student_id']) ?></small>
                                    </td>
                                    <?php foreach ($exam_subjects_list as $subject): ?>
                                    <td>
                                        <input type="number" 
                                               name="results[<?= $student['id'] ?>][<?= $subject['subject_id'] ?>]"
                                               class="results-input"
                                               min="0" 
                                               max="<?= $subject['full_marks'] ?>"
                                               step="0.5"
                                               placeholder="0"
                                               value="<?= $existing_results[$student['id']][$subject['subject_id']] ?? '' ?>">
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end;">
                            <a href="exams.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                </svg>
                                Save Results
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Exam Modal -->
    <div id="createExamModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create New Exam</h3>
                <button type="button" class="modal-close" onclick="hideCreateExamModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="post" id="createExamForm">
                    <input type="hidden" name="action" value="create_exam">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Exam Name</label>
                            <input type="text" name="exam_name" class="form-input" required placeholder="e.g., First Unit Test">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Exam Type</label>
                            <select name="exam_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="unit_test">Unit Test</option>
                                <option value="mid_term">Mid Term</option>
                                <option value="final">Final Exam</option>
                                <option value="annual">Annual Exam</option>
                                <option value="monthly">Monthly Test</option>
                                <option value="weekly">Weekly Test</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-select" required onchange="loadSubjects(this.value)">
                                <option value="">Select Class</option>
                                <?php foreach ($teacher_classes as $class): ?>
                                <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Academic Year</label>
                            <input type="text" name="academic_year" class="form-input" required value="2024-2025">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="exam_date_start" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">End Date</label>
                            <input type="date" name="exam_date_end" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Total Marks (Default)</label>
                            <input type="number" name="total_marks" class="form-input" required value="100" min="1">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Pass Marks (Default)</label>
                            <input type="number" name="pass_marks" class="form-input" required value="40" min="1">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" class="form-textarea" rows="3" placeholder="Enter exam instructions..."></textarea>
                    </div>
                    
                    <div id="subjectsContainer">
                        <h4 style="margin: 24px 0 16px 0; font-size: 16px; font-weight: 600;">Select Subjects</h4>
                        <div id="subjectsGrid" class="subjects-grid">
                            <!-- Subjects will be loaded here -->
                        </div>
                    </div>
                    
                    <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" onclick="hideCreateExamModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                            </svg>
                            Create Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>

    <script>
        function showCreateExamModal() {
            document.getElementById('createExamModal').classList.add('show');
        }

        function hideCreateExamModal() {
            document.getElementById('createExamModal').classList.remove('show');
        }

        function loadSubjects(classId) {
            if (!classId) {
                document.getElementById('subjectsGrid').innerHTML = '';
                return;
            }

            fetch(`../api/get_subjects.php?class_id=${classId}`)
                .then(response => response.json())
                .then(subjects => {
                    const grid = document.getElementById('subjectsGrid');
                    grid.innerHTML = '';
                    
                    subjects.forEach(subject => {
                        const card = document.createElement('div');
                        card.className = 'subject-card';
                        card.innerHTML = `
                            <div class="subject-checkbox">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="subjects[]" value="${subject.id}" onchange="toggleSubjectCard(this)">
                                    <strong>${subject.subject_name}</strong>
                                </label>
                            </div>
                            <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                                <div>
                                    <label class="form-label">Full Marks</label>
                                    <input type="number" name="subject_marks_${subject.id}" class="form-input" value="100" min="1">
                                </div>
                                <div>
                                    <label class="form-label">Pass Marks</label>
                                    <input type="number" name="subject_pass_${subject.id}" class="form-input" value="40" min="1">
                                </div>
                            </div>
                            <div class="form-grid" style="grid-template-columns: 1fr 1fr; margin-top: 12px;">
                                <div>
                                    <label class="form-label">Exam Date</label>
                                    <input type="date" name="subject_date_${subject.id}" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Exam Time</label>
                                    <input type="time" name="subject_time_${subject.id}" class="form-input" value="10:00">
                                </div>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                });
        }

        function toggleSubjectCard(checkbox) {
            const card = checkbox.closest('.subject-card');
            if (checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
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

        // Close modal when clicking outside
        document.getElementById('createExamModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCreateExamModal();
            }
        });
    </script>
</body>
</html>
