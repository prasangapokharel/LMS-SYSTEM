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
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        .assignments-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .assignments-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .assignments-header::before {
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
            margin: 0 0 16px 0;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
        }

        .header-btn svg {
            width: 16px;
            height: 16px;
        }

        .assignments-content {
            padding: 0 20px;
            max-width: 100%;
        }

        .create-form-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .form-toggle-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-toggle-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-toggle-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-toggle-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .form-chevron {
            width: 20px;
            height: 20px;
            color: #6b7280;
            transition: transform 0.3s ease;
        }

        .create-form-card.collapsed .form-chevron {
            transform: rotate(-90deg);
        }

        .form-toggle-body {
            padding: 28px 24px;
            display: block;
        }

        .create-form-card.collapsed .form-toggle-body {
            display: none;
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label-icon {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #111827;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .file-upload {
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            padding: 32px 20px;
            text-align: center;
            cursor: pointer;
            background: #f8fafc;
        }

        .file-upload-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            color: #9ca3af;
        }

        .file-upload-text {
            font-size: 14px;
            color: #374151;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .file-upload-hint {
            font-size: 12px;
            color: #6b7280;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .submit-btn svg {
            width: 16px;
            height: 16px;
        }

        .assignments-list-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .list-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 24px 28px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .list-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .list-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .list-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .assignments-count {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .assignments-container {
            padding: 24px 28px;
        }

        .assignment-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
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
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .assignment-info {
            flex: 1;
            min-width: 0;
        }

        .assignment-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
        }

        .assignment-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .assignment-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-homework { background: #3b82f6; }
        .badge-project { background: #8b5cf6; }
        .badge-quiz { background: #22c55e; }
        .badge-exam { background: #ef4444; }

        .assignment-subject {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
        }

        .assignment-description {
            font-size: 14px;
            color: #374151;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .assignment-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-item {
            background: white;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .stat-value {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin: 0;
        }

        .progress-bar {
            background: #e5e7eb;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
            border-radius: 3px;
        }

        .assignment-actions {
            display: flex;
            gap: 12px;
        }

        .action-btn {
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn svg {
            width: 14px;
            height: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }

        .action-menu {
            position: relative;
        }

        .action-menu-btn {
            background: white;
            border: 2px solid #e5e7eb;
            padding: 8px;
            border-radius: 8px;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-menu-btn svg {
            width: 16px;
            height: 16px;
        }

        .action-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            min-width: 180px;
            z-index: 10;
            display: none;
            overflow: hidden;
        }

        .action-menu.active .action-menu-dropdown {
            display: block;
        }

        .action-menu-item {
            padding: 12px 16px;
            font-size: 13px;
            color: #374151;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 500;
        }

        .action-menu-item:last-child {
            border-bottom: none;
        }

        .action-menu-item svg {
            width: 14px;
            height: 14px;
        }

        .action-menu-item.danger {
            color: #dc2626;
        }

        .attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #059669;
            text-decoration: none;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .attachment-link svg {
            width: 14px;
            height: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f1f5f9, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: #9ca3af;
        }

        .empty-icon svg {
            width: 32px;
            height: 32px;
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

        .empty-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .empty-btn svg {
            width: 16px;
            height: 16px;
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
            background: #f0fdf4;
            color: #22c55e;
            border: 2px solid #bbf7d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #ef4444;
            border: 2px solid #fecaca;
        }

        @media (max-width: 767px) {
            .assignments-content {
                padding: 0 16px;
            }

            .create-form-card, .assignments-list-card {
                border-radius: 16px;
            }

            .form-toggle-header, .list-header, .assignments-container {
                padding: 20px 16px;
            }

            .form-toggle-body {
                padding: 20px 16px;
            }

            .assignment-card {
                padding: 20px 16px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .assignment-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .header-actions {
                flex-direction: column;
                gap: 8px;
            }

            .header-btn {
                padding: 10px 14px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="assignments-app">
        <div class="assignments-header">
            <div class="header-content">
                <div class="header-top">
                    <a href="index.php" class="back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="header-title">Assignments</h1>
                </div>
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

        <div class="assignments-content">
            <?php if ($msg): ?>
            <div id="messageContainer">
                <?= $msg ?>
            </div>
            <?php endif; ?>

            <div class="create-form-card collapsed" id="createForm">
                <div class="form-toggle-header" onclick="toggleCreateForm()">
                    <h2 class="form-toggle-title">
                        <div class="form-toggle-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </div>
                        Create New Assignment
                    </h2>
                    <svg class="form-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>
                </div>
                <div class="form-toggle-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                                Subject & Class
                            </label>
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
                            <label class="form-label">
                                <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4"/>
                                    <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                                    <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                                    <path d="M12 3v6"/>
                                    <path d="M12 15v6"/>
                                </svg>
                                Assignment Type
                            </label>
                            <select name="assignment_type" class="form-select" required>
                                <option value="homework">📘 Homework</option>
                                <option value="project">🟣 Project</option>
                                <option value="quiz">🟢 Quiz</option>
                                <option value="exam">🔴 Exam</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10,9 9,9 8,9"/>
                                </svg>
                                Assignment Title
                            </label>
                            <input type="text" name="title" class="form-input" required placeholder="Enter assignment title">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                                Description
                            </label>
                            <textarea name="description" class="form-textarea" required placeholder="Describe the assignment"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Instructions (Optional)
                            </label>
                            <textarea name="instructions" class="form-textarea" placeholder="Additional instructions for students"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    Due Date
                                </label>
                                <input type="date" name="due_date" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    Max Marks
                                </label>
                                <input type="number" name="max_marks" class="form-input" required min="1" max="1000" value="100">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                </svg>
                                Attachment (Optional)
                            </label>
                            <div class="file-upload" onclick="document.getElementById('attachment').click()">
                                <svg class="file-upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <div class="file-upload-text">Click to upload file</div>
                                <div class="file-upload-hint">PDF, DOC, Images (Max 10MB)</div>
                            </div>
                            <input type="file" id="attachment" name="attachment" style="display: none;" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        </div>

                        <button type="submit" class="submit-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Create Assignment
                        </button>
                    </form>
                </div>
            </div>

            <div class="assignments-list-card">
                <div class="list-header">
                    <h2 class="list-title">
                        <div class="list-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4"/>
                                <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                                <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                                <path d="M12 3v6"/>
                                <path d="M12 15v6"/>
                            </svg>
                        </div>
                        My Assignments
                    </h2>
                    <span class="assignments-count"><?= count($assignments) ?></span>
                </div>

                <div class="assignments-container">
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
                        <h3 class="empty-title">No Assignments Yet</h3>
                        <p class="empty-text">Create your first assignment to get started with managing student work.</p>
                        <button class="empty-btn" onclick="toggleCreateForm()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Create Assignment
                        </button>
                    </div>
                    <?php else: ?>
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
                                        <button type="submit" name="delete_assignment" class="action-menu-item danger" style="width: 100%; text-align: left; background: none; border: none;">
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
                            <a href="view_submissions.php?id=<?= $assignment['id'] ?>" class="action-btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Submissions (<?= $assignment['submissions'] ?>)
                            </a>
                            <a href="edit_assignment.php?id=<?= $assignment['id'] ?>" class="action-btn btn-warning">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

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
                    <svg class="file-upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #22c55e;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <div class="file-upload-text" style="color: #22c55e;">${fileName}</div>
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