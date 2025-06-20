<?php
include_once '../App/Models/teacher/Log.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Log<?= $class_details ? " - $class_details" : "" ?></title>
    <meta name="description" content="Manage your daily teaching logs and track class progress">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        .teacher-log-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .log-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .log-header::before {
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

        .log-content {
            padding: 0 20px;
            max-width: 100%;
        }

        .class-selector-card {
            background: white;
            border-radius: 20px;
            padding: 28px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .class-selector-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        }

        .selector-header {
            margin-bottom: 24px;
        }

        .selector-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .selector-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .selector-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .selector-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        .class-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .class-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .class-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .class-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .class-icon svg {
            width: 24px;
            height: 24px;
            color: #059669;
        }

        .class-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .class-section {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 12px 0;
        }

        .class-action {
            font-size: 12px;
            color: #059669;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .class-action svg {
            width: 14px;
            height: 14px;
        }

        .log-form-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 24px 28px;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .form-body {
            padding: 28px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
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

        .required {
            color: #ef4444;
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

        .form-actions {
            display: flex;
            gap: 12px;
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }

        .btn-info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 12px;
        }

        .btn-sm svg {
            width: 14px;
            height: 14px;
        }

        .logs-list-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .logs-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 24px 28px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .logs-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logs-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logs-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .logs-body {
            padding: 28px;
        }

        .logs-table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .logs-table th {
            background: #f8fafc;
            padding: 16px 20px;
            text-align: left;
            font-weight: 700;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .logs-table td {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #374151;
            font-size: 14px;
            vertical-align: top;
        }

        .logs-table tr:last-child td {
            border-bottom: none;
        }

        .log-date {
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .log-date svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .subject-badge {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #bbf7d0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .subject-badge svg {
            width: 12px;
            height: 12px;
        }

        .chapter-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .chapter-title {
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .chapter-meta {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chapter-meta svg {
            width: 12px;
            height: 12px;
        }

        .method-badge {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #d97706;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid #fed7aa;
        }

        .log-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
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
            margin: 0;
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
            background: #f0fdf4;
            color: #22c55e;
            border: 2px solid #bbf7d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #ef4444;
            border: 2px solid #fecaca;
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
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            padding: 24px 28px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #fef2f2, #fecaca);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-title-icon svg {
            width: 16px;
            height: 16px;
            color: #ef4444;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-body {
            padding: 28px;
        }

        .modal-body p {
            margin: 0 0 16px 0;
            color: #374151;
            line-height: 1.6;
        }

        .modal-body strong {
            color: #111827;
            font-weight: 600;
        }

        .modal-footer {
            padding: 20px 28px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        @media (min-width: 768px) {
            .log-content {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 32px;
            }

            .class-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }

            .form-row {
                grid-template-columns: 1fr 1fr;
            }

            .form-row.full {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: row;
            }
        }

        @media (max-width: 767px) {
            .log-content {
                padding: 0 16px;
            }

            .log-form-card, .logs-list-card, .class-selector-card {
                border-radius: 16px;
            }

            .form-header, .logs-header, .form-body, .logs-body {
                padding: 20px 16px;
            }

            .logs-table {
                min-width: 600px;
            }

            .logs-table th, .logs-table td {
                padding: 12px 8px;
                font-size: 12px;
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

            .modal-content {
                margin: 20px;
                width: calc(100% - 40px);
            }
        }
    </style>
</head>
<body>
    <div class="teacher-log-app">
        <div class="log-header">
            <div class="header-content">
                <div class="header-top">
                    <a href="index.php" class="back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="header-title">Teacher Log</h1>
                </div>
                <?php if ($class_details): ?>
                <p class="header-subtitle"><?= $class_details ?></p>
                <?php else: ?>
                <p class="header-subtitle">Manage your daily teaching logs and track class progress</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="log-content">
            <?php if ($msg): ?>
            <div id="messageContainer">
                <?= $msg ?>
            </div>
            <?php endif; ?>

            <?php if ($class_id == 0): ?>
            <div class="class-selector-card">
                <div class="selector-header">
                    <h2 class="selector-title">
                        <div class="selector-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        Select a Class to Manage Logs
                    </h2>
                    <p class="selector-subtitle">Choose a class to start creating and managing your teaching logs</p>
                </div>
                
                <div class="class-grid">
                    <?php foreach ($classes as $cls): ?>
                    <a href="teacher_log.php?class_id=<?= $cls['id'] ?>" class="class-card">
                        <div class="class-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div class="class-name"><?= htmlspecialchars($cls['class_name']) ?></div>
                        <div class="class-section">Section <?= htmlspecialchars($cls['section']) ?></div>
                        <div class="class-action">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                            Click to manage logs
                        </div>
                    </a>
                    <?php endforeach; ?>
                    
                    <?php if (empty($classes)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h3 class="empty-title">No Classes Assigned</h3>
                        <p class="empty-text">You don't have any classes assigned yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>

            <div class="log-form-card">
                <div class="form-header">
                    <h2 class="form-title">
                        <div class="form-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <?php if ($edit_log): ?>
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                <?php else: ?>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <?php endif; ?>
                            </svg>
                        </div>
                        <?= $edit_log ? 'Edit Log' : 'Add New Log' ?>
                    </h2>
                </div>
                <div class="form-body">
                    <form method="post">
                        <input type="hidden" name="action" value="save_log">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    </svg>
                                    Subject <span class="required">*</span>
                                </label>
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
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    Date <span class="required">*</span>
                                </label>
                                <input type="date" name="log_date" class="form-input" value="<?= $edit_log ? $edit_log['log_date'] : date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14,2 14,8 20,8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                    Chapter Title <span class="required">*</span>
                                </label>
                                <input type="text" name="chapter_title" class="form-input" value="<?= $edit_log ? htmlspecialchars($edit_log['chapter_title']) : '' ?>" required placeholder="Enter chapter title">
                            </div>
                        </div>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14,2 14,8 20,8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                    Chapter Content
                                </label>
                                <textarea name="chapter_content" class="form-textarea" placeholder="Describe the chapter content"><?= $edit_log ? htmlspecialchars($edit_log['chapter_content']) : '' ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 12l2 2 4-4"/>
                                        <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                                        <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                                        <path d="M12 3v6"/>
                                        <path d="M12 15v6"/>
                                    </svg>
                                    Topics Covered <span class="required">*</span>
                                </label>
                                <textarea name="topics_covered" class="form-textarea" required placeholder="List the topics covered in this lesson"><?= $edit_log ? htmlspecialchars($edit_log['topics_covered']) : '' ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    Teaching Method
                                </label>
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
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12,6 12,12 16,14"/>
                                    </svg>
                                    Lesson Duration (minutes)
                                </label>
                                <input type="number" name="lesson_duration" class="form-input" value="<?= $edit_log ? $edit_log['lesson_duration'] : '45' ?>" required min="1" max="300">
                            </div>
                        </div>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                    </svg>
                                    Homework Assigned
                                </label>
                                <textarea name="homework_assigned" class="form-textarea" placeholder="Describe any homework assigned"><?= $edit_log ? htmlspecialchars($edit_log['homework_assigned']) : '' ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14,2 14,8 20,8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                    Additional Notes
                                </label>
                                <textarea name="notes" class="form-textarea" placeholder="Any additional notes or observations"><?= $edit_log ? htmlspecialchars($edit_log['notes']) : '' ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                </svg>
                                <?= $edit_log ? 'Update Log' : 'Save Log' ?>
                            </button>
                            
                            <?php if ($edit_log): ?>
                            <a href="teacher_log.php?class_id=<?= $class_id ?>" class="btn btn-secondary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6L6 18"/>
                                    <path d="M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="logs-list-card">
                <div class="logs-header">
                    <h2 class="logs-title">
                        <div class="logs-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                        </div>
                        Recent Logs
                    </h2>
                    
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
                        <button type="submit" class="btn btn-sm btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 3h-6l-2 3h-4l-2-3H2v18h20V3z"/>
                                <path d="M8 21l-2-8h12l-2 8"/>
                            </svg>
                            Filter
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                <div class="logs-body">
                    <?php if (empty($recent_logs)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                        </div>
                        <h3 class="empty-title">No Logs Found</h3>
                        <p class="empty-text">
                            <?php if ($subject_id > 0): ?>
                                No logs found for the selected subject.
                            <?php else: ?>
                                No logs found for this class. Start by adding a new log using the form above.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php else: ?>
                    <div class="logs-table-container">
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
                                        <div class="log-date">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            <?= date('M d, Y', strtotime($log['log_date'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="subject-badge">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                            </svg>
                                            <?= htmlspecialchars($log['subject_name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="chapter-info">
                                            <div class="chapter-title">
                                                <?= htmlspecialchars($log['chapter_title']) ?>
                                            </div>
                                            <div class="chapter-meta">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <polyline points="12,6 12,12 16,14"/>
                                                </svg>
                                                <?= $log['lesson_duration'] ?> minutes
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hide-mobile">
                                        <div class="method-badge">
                                            <?= $log['teaching_method'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="log-actions">
                                            <a href="view_log.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-info">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View
                                            </a>
                                            <a href="teacher_log.php?class_id=<?= $class_id ?>&edit_id=<?= $log['id'] ?>" class="btn btn-sm btn-primary">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(<?= $log['id'] ?>, '<?= addslashes($log['chapter_title']) ?>', '<?= date('M d, Y', strtotime($log['log_date'])) ?>')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                </svg>
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
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <div class="modal-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                        </svg>
                    </div>
                    Confirm Delete
                </h3>
                <button type="button" class="modal-close" onclick="hideDeleteModal()">×</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this log entry? This action cannot be undone.</p>
                <p><strong>Date:</strong> <span id="deleteDate"></span></p>
                <p><strong>Chapter:</strong> <span id="deleteChapter"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18"/>
                        <path d="M6 6l12 12"/>
                    </svg>
                    Cancel
                </button>
                <a id="deleteLink" href="#" class="btn btn-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    </svg>
                    Delete
                </a>
            </div>
        </div>
    </div>

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

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>