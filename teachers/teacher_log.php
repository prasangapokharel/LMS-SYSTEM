<?php
include_once '../App/Models/teacher/Log.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Log<?= $class_details ? " - $class_details" : "" ?></title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        /* Additional mobile-specific styles */
        .mobile-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 1rem;
            background-color: var(--color-gray-50);
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .page-header {
            background: var(--color-white);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0 0 0.5rem 0;
        }
        
        .page-subtitle {
            font-size: 0.875rem;
            color: var(--color-gray-500);
            margin: 0;
        }
        
        .class-selector {
            background: var(--color-white);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }
        
        .class-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .class-card {
            background: var(--color-white);
            border: 2px solid var(--color-gray-200);
            border-radius: 0.75rem;
            padding: 1.25rem;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .class-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--color-gray-900);
            margin-bottom: 0.25rem;
        }
        
        .class-section {
            font-size: 0.875rem;
            color: var(--color-gray-600);
            margin-bottom: 0.75rem;
        }
        
        .class-action {
            font-size: 0.75rem;
            color: var(--color-primary);
            font-weight: 500;
        }
        
        .log-form-container {
            background: var(--color-white);
            border-radius: 0.75rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
            margin-bottom: 1.5rem;
        }
        
        .form-header {
            background: var(--color-gray-50);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-gray-200);
            border-radius: 0.75rem 0.75rem 0 0;
        }
        
        .form-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0;
        }
        
        .form-body {
            padding: 1.5rem;
        }
        
        .form-row {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-gray-700);
            margin-bottom: 0.5rem;
        }
        
        .required {
            color: var(--color-danger);
        }
        
        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: var(--color-gray-700);
            background-color: var(--color-white);
            border: 2px solid var(--color-gray-200);
            border-radius: 0.5rem;
            box-shadow: var(--shadow-sm);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-start;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary {
            background: var(--color-primary);
            color: var(--color-white);
        }
        
        .btn-secondary {
            background: var(--color-gray-500);
            color: var(--color-white);
        }
        
        .btn-danger {
            background: var(--color-danger);
            color: var(--color-white);
        }
        
        .btn-info {
            background: var(--color-primary-light);
            color: var(--color-primary);
            border: 1px solid var(--color-primary);
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }
        
        .logs-container {
            background: var(--color-white);
            border-radius: 0.75rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }
        
        .logs-header {
            background: var(--color-gray-50);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-gray-200);
            border-radius: 0.75rem 0.75rem 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .logs-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0;
        }
        
        .filter-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .logs-body {
            padding: 1.5rem;
        }
        
        .logs-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .logs-table th {
            background: var(--color-gray-50);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--color-gray-700);
            border-bottom: 1px solid var(--color-gray-200);
            font-size: 0.875rem;
        }
        
        .logs-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--color-gray-200);
            color: var(--color-gray-700);
            font-size: 0.875rem;
            vertical-align: top;
        }
        
        .logs-table tr:last-child td {
            border-bottom: none;
        }
        
        .log-date {
            font-weight: 500;
            color: var(--color-gray-900);
        }
        
        .subject-badge {
            background: var(--color-primary-light);
            color: var(--color-primary);
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .chapter-title {
            font-weight: 500;
            color: var(--color-gray-900);
            margin-bottom: 0.25rem;
        }
        
        .log-meta {
            font-size: 0.75rem;
            color: var(--color-gray-500);
        }
        
        .log-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--color-gray-500);
        }
        
        .empty-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--color-gray-700);
            margin-bottom: 0.5rem;
        }
        
        .empty-text {
            font-size: 0.875rem;
            color: var(--color-gray-500);
        }
        
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .alert-success {
            background: var(--color-success-light);
            color: var(--color-success);
            border: 1px solid var(--color-success);
        }
        
        .alert-danger {
            background: var(--color-danger-light);
            color: var(--color-danger);
            border: 1px solid var(--color-danger);
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
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: var(--color-white);
            border-radius: 0.75rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--color-gray-500);
            cursor: pointer;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--color-gray-200);
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
        
        /* Mobile responsive */
        @media (min-width: 768px) {
            .mobile-container {
                max-width: 1200px;
                padding: 2rem;
            }
            
            .class-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1rem;
            }
            
            .form-row {
                flex-direction: row;
            }
            
            .form-group {
                flex: 1;
            }
            
            .logs-table th,
            .logs-table td {
                padding: 1.25rem 1rem;
            }
        }
        
        @media (max-width: 767px) {
            .logs-table {
                font-size: 0.75rem;
            }
            
            .logs-table th,
            .logs-table td {
                padding: 0.75rem 0.5rem;
            }
            
            .hide-mobile {
                display: none;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .filter-form {
                flex-direction: column;
                width: 100%;
            }
            
            .filter-form select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Teacher Log</h1>
            <?php if ($class_details): ?>
            <p class="page-subtitle"><?= $class_details ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Alert Messages -->
        <?= $msg ?>
        
        <?php if ($class_id == 0): ?>
        <!-- Class Selection -->
        <div class="class-selector">
            <h2 class="form-title" style="margin-bottom: 1rem;">Select a Class to Manage Logs</h2>
            <div class="class-grid">
                <?php foreach ($classes as $cls): ?>
                <a href="teacher_log.php?class_id=<?= $cls['id'] ?>" class="class-card">
                    <div class="class-name"><?= htmlspecialchars($cls['class_name']) ?></div>
                    <div class="class-section">Section <?= htmlspecialchars($cls['section']) ?></div>
                    <div class="class-action">Click to manage logs</div>
                </a>
                <?php endforeach; ?>
                
                <?php if (empty($classes)): ?>
                <div class="empty-state">
                    <div class="empty-title">No Classes Assigned</div>
                    <div class="empty-text">You don't have any classes assigned yet.</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        
        <!-- Log Form -->
        <div class="log-form-container">
            <div class="form-header">
                <h2 class="form-title"><?= $edit_log ? 'Edit Log' : 'Add New Log' ?></h2>
            </div>
            <div class="form-body">
                <form method="post">
                    <input type="hidden" name="action" value="save_log">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Subject <span class="required">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select" required>
                                <option value="">-- Select Subject --</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= ($subject_id == $subject['id'] || ($edit_log && $edit_log['subject_id'] == $subject['id'])) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($subject['subject_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Date <span class="required">*</span></label>
                            <input type="date" name="log_date" class="form-input" value="<?= $edit_log ? $edit_log['log_date'] : date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Chapter Title <span class="required">*</span></label>
                            <input type="text" name="chapter_title" class="form-input" value="<?= $edit_log ? htmlspecialchars($edit_log['chapter_title']) : '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Chapter Content</label>
                            <textarea name="chapter_content" class="form-textarea"><?= $edit_log ? htmlspecialchars($edit_log['chapter_content']) : '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Topics Covered <span class="required">*</span></label>
                            <textarea name="topics_covered" class="form-textarea" required><?= $edit_log ? htmlspecialchars($edit_log['topics_covered']) : '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Teaching Method</label>
                            <select name="teaching_method" class="form-select">
                                <option value="Lecture" <?= ($edit_log && $edit_log['teaching_method'] == 'Lecture') ? 'selected' : '' ?>>Lecture</option>
                                <option value="Discussion" <?= ($edit_log && $edit_log['teaching_method'] == 'Discussion') ? 'selected' : '' ?>>Discussion</option>
                                <option value="Group Work" <?= ($edit_log && $edit_log['teaching_method'] == 'Group Work') ? 'selected' : '' ?>>Group Work</option>
                                <option value="Practical" <?= ($edit_log && $edit_log['teaching_method'] == 'Practical') ? 'selected' : '' ?>>Practical</option>
                                <option value="Demonstration" <?= ($edit_log && $edit_log['teaching_method'] == 'Demonstration') ? 'selected' : '' ?>>Demonstration</option>
                                <option value="Project" <?= ($edit_log && $edit_log['teaching_method'] == 'Project') ? 'selected' : '' ?>>Project</option>
                                <option value="Other" <?= ($edit_log && $edit_log['teaching_method'] == 'Other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Lesson Duration (minutes)</label>
                            <input type="number" name="lesson_duration" class="form-input" value="<?= $edit_log ? $edit_log['lesson_duration'] : '45' ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Homework Assigned</label>
                            <textarea name="homework_assigned" class="form-textarea"><?= $edit_log ? htmlspecialchars($edit_log['homework_assigned']) : '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="notes" class="form-textarea"><?= $edit_log ? htmlspecialchars($edit_log['notes']) : '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?= $edit_log ? 'Update Log' : 'Save Log' ?>
                        </button>
                        
                        <?php if ($edit_log): ?>
                        <a href="teacher_log.php?class_id=<?= $class_id ?>" class="btn btn-secondary">
                            Cancel
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Recent Logs -->
        <div class="logs-container">
            <div class="logs-header">
                <h2 class="logs-title">Recent Logs</h2>
                
                <?php if (count($subjects) > 1): ?>
                <form method="get" class="filter-form">
                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                    <select name="subject_id" class="form-select">
                        <option value="0">All Subjects</option>
                        <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>" <?= ($subject_id == $subject['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subject['subject_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                </form>
                <?php endif; ?>
            </div>
            <div class="logs-body">
                <?php if (empty($recent_logs)): ?>
                <div class="empty-state">
                    <div class="empty-title">No Logs Found</div>
                    <div class="empty-text">
                        <?php if ($subject_id > 0): ?>
                            No logs found for the selected subject.
                        <?php else: ?>
                            No logs found for this class. Start by adding a new log using the form above.
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="logs-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Chapter</th>
                                <th class="hide-mobile">Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_logs as $log): ?>
                            <tr>
                                <td>
                                    <span class="log-date">
                                        <?= date('M d, Y', strtotime($log['log_date'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="subject-badge">
                                        <?= htmlspecialchars($log['subject_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="chapter-title">
                                        <?= htmlspecialchars($log['chapter_title']) ?>
                                    </div>
                                    <div class="log-meta">
                                        <?= $log['lesson_duration'] ?> minutes
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <span class="log-meta">
                                        <?= $log['teaching_method'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="log-actions">
                                        <a href="view_log.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-info">
                                            View
                                        </a>
                                        <a href="teacher_log.php?class_id=<?= $class_id ?>&edit_id=<?= $log['id'] ?>" class="btn btn-sm btn-primary">
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(<?= $log['id'] ?>, '<?= addslashes($log['chapter_title']) ?>', '<?= date('M d, Y', strtotime($log['log_date'])) ?>')">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Delete</h3>
                <button type="button" class="modal-close" onclick="hideDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this log entry?</p>
                <p><strong>Date:</strong> <span id="deleteDate"></span></p>
                <p><strong>Chapter:</strong> <span id="deleteChapter"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">Cancel</button>
                <a id="deleteLink" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>

        <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>
    <script>
        function showDeleteModal(logId, chapterTitle, logDate) {
            document.getElementById('deleteDate').textContent = logDate;
            document.getElementById('deleteChapter').textContent = chapterTitle;
            document.getElementById('deleteLink').href = 'delete_log.php?id=' + logId + '&class_id=<?= $class_id ?>';
            document.getElementById('deleteModal').classList.add('show');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.display = 'none';
                });
            }, 5000);
        });
    </script>
</body>
</html>