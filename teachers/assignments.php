<?php
include '../App/Models/teacher/Assignment.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - LMS</title>
    <meta name="description" content="Create and manage assignments for your classes">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <link rel="stylesheet" href="../assets/css/teacher/assignments.css">
</head>
<body>
    <div class="container ">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="header-title">Assignments</h1>
                <p class="header-subtitle">Create and manage assignments for your classes</p>
                <div class="header-actions">
                    <button class="header-btn" onclick="toggleCreateForm()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Create New
                    </button>
                    <a href="assignment_analytics.php" class="header-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        Analytics
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($msg): ?>
        <div id="messageContainer">
            <?= $msg ?>
        </div>
        <?php endif; ?>

        <!-- Create Assignment Form -->
        <div class="card create-form-card collapsed" id="createForm">
            <div class="form-toggle-header" onclick="toggleCreateForm()">
                <div class="form-toggle-title">
                    <div class="form-toggle-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <h2 class="toggle-title-text">Create New Assignment</h2>
                </div>
                <svg class="form-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6,9 12,15 18,9"/>
                </svg>
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
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                            </div>
                            <div class="file-upload-text">Click to upload file</div>
                            <div class="file-upload-hint">PDF, DOC, Images (Max 10MB)</div>
                        </div>
                        <input type="file" id="attachment" name="attachment" style="display: none;" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                    </div>

                    <button type="submit" class="btn btn-primary full-width">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Create Assignment
                    </button>
                </form>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4"/>
                        <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                        <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                        <path d="M12 3v6"/>
                        <path d="M12 15v6"/>
                    </svg>
                </div>
                <h2 class="card-title">My Assignments</h2>
                <span class="assignments-count"><?= count($assignments) ?></span>
            </div>

            <div class="card-content">
                <?php if (empty($assignments)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12l2 2 4-4"/>
                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                            <path d="M12 3v6"/>
                            <path d="M12 15v6"/>
                        </svg>
                    </div>
                    <div class="empty-title">No Assignments Yet</div>
                    <div class="empty-text">Create your first assignment to get started with managing student work.</div>
                    <button class="btn btn-primary" onclick="toggleCreateForm()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Create Assignment
                    </button>
                </div>
                <?php else: ?>
                <div class="assignments-list">
                    <?php foreach ($assignments as $assignment): ?>
                    <div class="assignment-card <?= $assignment['assignment_type'] ?>">
                        <div class="assignment-header">
                            <div class="assignment-info">
                                <h3 class="assignment-title"><?= htmlspecialchars($assignment['title']) ?></h3>
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
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"/>
                                        <circle cx="12" cy="5" r="1"/>
                                        <circle cx="12" cy="19" r="1"/>
                                    </svg>
                                </button>
                                <div class="action-menu-dropdown">
                                    <a href="view_submissions.php?id=<?= $assignment['id'] ?>" class="action-menu-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        View Submissions
                                    </a>
                                    <a href="edit_assignment.php?id=<?= $assignment['id'] ?>" class="action-menu-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit Assignment
                                    </a>
                                    <form method="post" style="margin: 0;" onsubmit="return confirm('Delete this assignment?')">
                                        <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                        <button type="submit" name="delete_assignment" class="action-menu-item danger">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"/>
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                            </svg>
                                            Delete Assignment
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

                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $assignment['total_students'] > 0 ? ($assignment['submissions'] / $assignment['total_students']) * 100 : 0 ?>%"></div>
                        </div>

                        <?php if ($assignment['attachment_url']): ?>
                        <a href="../<?= htmlspecialchars($assignment['attachment_url']) ?>" target="_blank" class="attachment-link">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                            </svg>
                            View Attachment
                        </a>
                        <?php endif; ?>

                        <div class="assignment-actions">
                            <a href="view_submissions.php?id=<?= $assignment['id'] ?>" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Submissions (<?= $assignment['submissions'] ?>)
                            </a>
                            <a href="edit_assignment.php?id=<?= $assignment['id'] ?>" class="btn btn-secondary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
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
            
            document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('active'));
            
            if (!isActive) {
                menu.classList.add('active');
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-menu')) {
                document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('active'));
            }
        });

        document.getElementById('attachment').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                const uploadArea = document.querySelector('.file-upload');
                uploadArea.innerHTML = `
                    <div class="file-upload-icon" style="color: #10b981;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div class="file-upload-text" style="color: #10b981;">${fileName}</div>
                    <div class="file-upload-hint">Click to change file</div>
                `;
            }
        });

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