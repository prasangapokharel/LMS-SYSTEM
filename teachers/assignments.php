<?php
include '../App/Models/teacher/Assignment.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1a202c;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.4;
            min-height: 100vh;
        }

        .app-container {
            max-width: 480px;
            margin: 0 auto;
            background: #ffffff;
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Header Styles */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 8px;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }

        .page-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            flex: 1;
        }

        .page-subtitle {
            font-size: 12px;
            opacity: 0.9;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .header-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            backdrop-filter: blur(10px);
        }

        /* Message Styles */
        .message-container {
            padding: 12px 16px 0;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert-danger {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }

        /* Form Styles */
        .form-section {
            padding: 16px;
        }

        .form-toggle {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .form-toggle-header {
            padding: 12px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: between;
        }

        .form-toggle-title {
            font-size: 14px;
            font-weight: 600;
            flex: 1;
        }

        .form-toggle-icon {
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .form-toggle.collapsed .form-toggle-icon {
            transform: rotate(-90deg);
        }

        .form-toggle-body {
            padding: 16px;
            display: block;
        }

        .form-toggle.collapsed .form-toggle-body {
            display: none;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            background: white;
            transition: border-color 0.2s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* File Upload */
        .file-upload {
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .file-upload-icon {
            font-size: 24px;
            color: #a0aec0;
            margin-bottom: 8px;
        }

        .file-upload-text {
            font-size: 12px;
            color: #718096;
            margin-bottom: 4px;
        }

        .file-upload-hint {
            font-size: 10px;
            color: #a0aec0;
        }

        /* Button Styles */
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-full {
            width: 100%;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 11px;
        }

        /* Assignment Cards */
        .assignments-list {
            padding: 16px;
            padding-bottom: 80px;
        }

        .assignments-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 16px;
        }

        .assignments-title {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
        }

        .assignments-count {
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }

        .assignment-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid;
            position: relative;
        }

        .assignment-card.homework { border-left-color: #3b82f6; }
        .assignment-card.project { border-left-color: #8b5cf6; }
        .assignment-card.quiz { border-left-color: #22c55e; }
        .assignment-card.exam { border-left-color: #ef4444; }

        .assignment-header {
            display: flex;
            align-items: flex-start;
            justify-content: between;
            margin-bottom: 12px;
        }

        .assignment-info {
            flex: 1;
        }

        .assignment-title {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .assignment-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .assignment-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            color: white;
        }

        .badge-homework { background: #3b82f6; }
        .badge-project { background: #8b5cf6; }
        .badge-quiz { background: #22c55e; }
        .badge-exam { background: #ef4444; }

        .assignment-subject {
            font-size: 11px;
            color: #718096;
        }

        .assignment-description {
            font-size: 12px;
            color: #4a5568;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .assignment-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .stat-item {
            background: #f7fafc;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
        }

        .stat-value {
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
        }

        .stat-label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .progress-bar {
            background: #e2e8f0;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
            transition: width 0.3s ease;
        }

        .assignment-actions {
            display: flex;
            gap: 8px;
        }

        .action-menu {
            position: relative;
            margin-left: auto;
        }

        .action-menu-btn {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 12px;
            color: #718096;
            cursor: pointer;
        }

        .action-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            min-width: 150px;
            z-index: 10;
            display: none;
        }

        .action-menu.active .action-menu-dropdown {
            display: block;
        }

        .action-menu-item {
            padding: 8px 12px;
            font-size: 12px;
            color: #4a5568;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #f7fafc;
        }

        .action-menu-item:hover {
            background: #f7fafc;
        }

        .action-menu-item.danger {
            color: #e53e3e;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            font-size: 48px;
            color: #a0aec0;
            margin-bottom: 16px;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .empty-text {
            font-size: 12px;
            color: #718096;
            margin-bottom: 20px;
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 40px 20px;
        }

        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #e2e8f0;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .assignment-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Bottom padding for navigation */
        .assignments-list {
            padding-bottom: 100px;
        }
    </style>
</head>
<body>
    <?php include '../include/loader.php'; ?>

    <div class="app-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-top">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="page-title">
                    <i class="fas fa-tasks me-2"></i>
                    Assignments
                </h1>
            </div>
            <p class="page-subtitle">Create and manage assignments for your classes</p>
            <div class="header-actions">
                <button class="header-btn" onclick="toggleCreateForm()">
                    <i class="fas fa-plus"></i>
                    Create New
                </button>
                <a href="assignment_analytics.php" class="header-btn">
                    <i class="fas fa-chart-bar"></i>
                    Analytics
                </a>
            </div>
        </div>

        <!-- Message Display -->
        <?php if ($msg): ?>
        <div class="message-container">
            <?= $msg ?>
        </div>
        <?php endif; ?>

        <!-- Create Assignment Form -->
        <div class="form-section">
            <div class="form-toggle collapsed" id="createForm">
                <div class="form-toggle-header" onclick="toggleCreateForm()">
                    <h5 class="form-toggle-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Create New Assignment
                    </h5>
                    <i class="fas fa-chevron-down form-toggle-icon"></i>
                </div>
                <div class="form-toggle-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Subject & Class</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">-- Select Subject --</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>">
                                    <?= htmlspecialchars($subject['subject_name']) ?> 
                                    (<?= htmlspecialchars($subject['class_name']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Assignment Type</label>
                            <select name="assignment_type" class="form-select" required>
                                <option value="homework">📘 Homework</option>
                                <option value="project">🟣 Project</option>
                                <option value="quiz">🟢 Quiz</option>
                                <option value="exam">🔴 Exam</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Assignment Title</label>
                            <input type="text" name="title" class="form-input" required placeholder="Enter assignment title">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-textarea" required placeholder="Describe the assignment"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Instructions (Optional)</label>
                            <textarea name="instructions" class="form-textarea" placeholder="Additional instructions for students"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Max Marks</label>
                                <input type="number" name="max_marks" class="form-input" required min="1" max="1000" value="100">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Attachment (Optional)</label>
                            <div class="file-upload" onclick="document.getElementById('attachment').click()">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">Click to upload file</div>
                                <div class="file-upload-hint">PDF, DOC, Images (Max 10MB)</div>
                            </div>
                            <input type="file" id="attachment" name="attachment" style="display: none;" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-plus"></i>
                            Create Assignment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="assignments-list">
            <div class="assignments-header">
                <h2 class="assignments-title">My Assignments</h2>
                <span class="assignments-count"><?= count($assignments) ?></span>
            </div>

            <?php if (empty($assignments)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h6 class="empty-title">No Assignments Yet</h6>
                <p class="empty-text">Create your first assignment to get started with managing student work.</p>
                <button class="btn btn-primary" onclick="toggleCreateForm()">
                    <i class="fas fa-plus"></i>
                    Create Assignment
                </button>
            </div>
            <?php else: ?>
            <?php foreach ($assignments as $assignment): ?>
            <div class="assignment-card <?= $assignment['assignment_type'] ?>">
                <div class="assignment-header">
                    <div class="assignment-info">
                        <h6 class="assignment-title"><?= htmlspecialchars($assignment['title']) ?></h6>
                        <div class="assignment-meta">
                            <span class="assignment-badge badge-<?= $assignment['assignment_type'] ?>">
                                <?= htmlspecialchars(ucfirst($assignment['assignment_type'])) ?>
                            </span>
                            <span class="assignment-subject">
                                <?= htmlspecialchars($assignment['subject_name']) ?> - <?= htmlspecialchars($assignment['class_name']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="action-menu">
                        <button class="action-menu-btn" onclick="toggleActionMenu(this)">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="action-menu-dropdown">
                            <a href="view_submissions.php?id=<?= $assignment['id'] ?>" class="action-menu-item">
                                <i class="fas fa-eye"></i>View Submissions
                            </a>
                            <a href="edit_assignment.php?id=<?= $assignment['id'] ?>" class="action-menu-item">
                                <i class="fas fa-edit"></i>Edit Assignment
                            </a>
                            <form method="post" style="margin: 0;" onsubmit="return confirm('Delete this assignment?')">
                                <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                <button type="submit" name="delete_assignment" class="action-menu-item danger" style="width: 100%; text-align: left; background: none; border: none;">
                                    <i class="fas fa-trash"></i>Delete Assignment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <p class="assignment-description"><?= htmlspecialchars($assignment['description']) ?></p>

                <div class="assignment-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?= htmlspecialchars(date('M d', strtotime($assignment['due_date']))) ?></div>
                        <div class="stat-label">Due Date</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= htmlspecialchars($assignment['max_marks']) ?></div>
                        <div class="stat-label">Max Marks</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $assignment['submissions'] ?>/<?= $assignment['total_students'] ?></div>
                        <div class="stat-label">Submitted</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= htmlspecialchars(date('M d', strtotime($assignment['created_at']))) ?></div>
                        <div class="stat-label">Created</div>
                    </div>
                </div>

                <!-- Submission Progress -->
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $assignment['total_students'] > 0 ? ($assignment['submissions'] / $assignment['total_students']) * 100 : 0 ?>%"></div>
                </div>

                <?php if ($assignment['attachment_url']): ?>
                <div style="margin-bottom: 12px;">
                    <a href="../<?= htmlspecialchars($assignment['attachment_url']) ?>" target="_blank" style="font-size: 11px; color: #667eea; text-decoration: none;">
                        <i class="fas fa-paperclip me-1"></i>View Attachment
                    </a>
                </div>
                <?php endif; ?>

                <div class="assignment-actions">
                    <a href="view_submissions.php?id=<?= $assignment['id'] ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i>
                        Submissions (<?= $assignment['submissions'] ?>)
                    </a>
                    <a href="edit_assignment.php?id=<?= $assignment['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                        Edit
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        function toggleCreateForm() {
            const form = document.getElementById('createForm');
            form.classList.toggle('collapsed');
        }

        function toggleActionMenu(button) {
            const menu = button.parentElement;
            const isActive = menu.classList.contains('active');
            
            // Close all menus
            document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('active'));
            
            // Toggle current menu
            if (!isActive) {
                menu.classList.add('active');
            }
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-menu')) {
                document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('active'));
            }
        });

        // File upload handling
        document.getElementById('attachment').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                const uploadArea = document.querySelector('.file-upload');
                uploadArea.innerHTML = `
                    <div class="file-upload-icon" style="color: #22c55e;">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-upload-text" style="color: #22c55e;">${fileName}</div>
                    <div class="file-upload-hint">Click to change file</div>
                `;
            }
        });

        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
