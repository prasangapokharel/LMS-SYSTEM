<?php
include '../App/Models/teacher/Attendance.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance - LMS</title>
    <meta name="description" content="Mark student attendance for your classes">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        /* Premium Attendance System Styles */
        .attendance-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Enhanced Header */
        .attendance-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .attendance-header::before {
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
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
            color: white;
            text-decoration: none;
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

        /* Content Container */
        .attendance-content {
            padding: 0 20px;
            max-width: 100%;
        }

        /* Selection Card */
        .selection-card {
            background: white;
            border-radius: 20px;
            padding: 28px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .selection-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        }

        .selection-header {
            margin-bottom: 24px;
        }

        .selection-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .selection-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .selection-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .selection-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-label-icon {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .form-select, .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #111827;
            transition: all 0.3s ease;
        }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .load-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .load-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -2px rgba(16, 185, 129, 0.4);
        }

        .load-btn:active {
            transform: translateY(0);
        }

        .load-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Attendance Section */
        .attendance-section {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .section-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 24px 28px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .section-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .section-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        /* Quick Actions */
        .quick-actions {
            padding: 24px 28px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(135deg, #fafbfc, #f8fafc);
        }

        .quick-actions-title {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quick-actions-title svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .select-all-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
        }

        .select-all-option {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            background: white;
            color: #6b7280;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .select-all-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .select-all-option:active {
            transform: translateY(0);
        }

        .select-all-option svg {
            width: 16px;
            height: 16px;
        }

        .select-all-option.present {
            border-color: #22c55e;
            color: #22c55e;
            background: #f0fdf4;
        }

        .select-all-option.absent {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        .select-all-option.late {
            border-color: #f59e0b;
            color: #f59e0b;
            background: #fffbeb;
        }

        .select-all-option.half-day {
            border-color: #8b5cf6;
            color: #8b5cf6;
            background: #faf5ff;
        }

        .select-all-option.clear {
            border-color: #6b7280;
            color: #6b7280;
            background: #f9fafb;
        }

        /* Students Container */
        .students-container {
            max-height: 60vh;
            overflow-y: auto;
        }

        .student-counter {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #059669;
            text-align: center;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .student-counter svg {
            width: 16px;
            height: 16px;
        }

        /* Student Item */
        .student-item {
            padding: 24px 28px;
            border-bottom: 1px solid #f1f5f9;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .student-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .student-info {
            flex: 1;
        }

        .student-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .student-id {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
            font-weight: 500;
        }

        /* Attendance Grid */
        .attendance-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .attendance-option {
            position: relative;
            cursor: pointer;
        }

        .attendance-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            cursor: pointer;
        }

        .option-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 16px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: white;
            text-align: center;
            transition: all 0.3s ease;
        }

        .option-content:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .option-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #6b7280;
            transition: all 0.3s ease;
        }

        .option-icon svg {
            width: 16px;
            height: 16px;
        }

        .option-text {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            transition: color 0.3s ease;
        }

        /* Present Option */
        .attendance-option.present input:checked + .option-content {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .attendance-option.present input:checked + .option-content .option-icon {
            background: #22c55e;
            color: white;
        }

        .attendance-option.present input:checked + .option-content .option-text {
            color: #22c55e;
        }

        /* Absent Option */
        .attendance-option.absent input:checked + .option-content {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .attendance-option.absent input:checked + .option-content .option-icon {
            background: #ef4444;
            color: white;
        }

        .attendance-option.absent input:checked + .option-content .option-text {
            color: #ef4444;
        }

        /* Late Option */
        .attendance-option.late input:checked + .option-content {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .attendance-option.late input:checked + .option-content .option-icon {
            background: #f59e0b;
            color: white;
        }

        .attendance-option.late input:checked + .option-content .option-text {
            color: #f59e0b;
        }

        /* Half Day Option */
        .attendance-option.half-day input:checked + .option-content {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .attendance-option.half-day input:checked + .option-content .option-icon {
            background: #8b5cf6;
            color: white;
        }

        .attendance-option.half-day input:checked + .option-content .option-text {
            color: #8b5cf6;
        }

        /* Remarks Input */
        .remarks-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 13px;
            background: #f8fafc;
            color: #374151;
            transition: all 0.3s ease;
        }

        .remarks-input:focus {
            outline: none;
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .remarks-input::placeholder {
            color: #9ca3af;
            font-size: 12px;
        }

        /* Save Section */
        .save-section {
            padding: 24px 28px;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-top: 1px solid #e5e7eb;
        }

        .save-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
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
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3);
        }

        .save-btn:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -2px rgba(34, 197, 94, 0.4);
        }

        .save-btn:active {
            transform: translateY(0);
        }

        .save-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Loading State */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .loading-content {
            background: white;
            padding: 32px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 300px;
            width: 90%;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f1f5f9;
            border-top: 3px solid #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        /* Empty State */
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

        /* Alert Messages */
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

        .alert-warning {
            background: #fffbeb;
            color: #f59e0b;
            border: 2px solid #fed7aa;
        }

        .alert-info {
            background: #eff6ff;
            color: #3b82f6;
            border: 2px solid #bfdbfe;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .attendance-content {
                padding: 0 16px;
            }
            
            .selection-card, .attendance-section {
                border-radius: 16px;
                padding: 20px 16px;
            }
            
            .section-header, .save-section {
                padding: 20px 16px;
            }
            
            .quick-actions {
                padding: 20px 16px;
            }
            
            .student-item {
                padding: 20px 16px;
            }
            
            .attendance-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            .select-all-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .student-avatar {
                width: 40px;
                height: 40px;
                font-size: 12px;
            }

            .option-content {
                padding: 12px 8px;
            }

            .option-icon {
                width: 28px;
                height: 28px;
            }
        }

        /* Focus States */
        .select-all-option:focus,
        .attendance-option:focus-within {
            outline: 2px solid #10b981;
            outline-offset: 2px;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <div class="attendance-app">
        <!-- Header -->
        <div class="attendance-header">
            <div class="header-content">
                <div class="header-top">
                    <a href="index.php" class="back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="header-title">Take Attendance</h1>
                </div>
                <p class="header-subtitle">
                    Mark student attendance for today: <?= date('M d, Y') ?>
                </p>
            </div>
        </div>

        <div class="attendance-content">
            <!-- Message Display -->
            <?php if ($msg): ?>
            <div id="messageContainer">
                <?= $msg ?>
            </div>
            <?php endif; ?>

            <!-- Class Selection -->
            <div class="selection-card">
                <div class="selection-header">
                    <h2 class="selection-title">
                        <div class="selection-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                        </div>
                        Select Class & Date
                    </h2>
                    <p class="selection-subtitle">Choose class and date to load students</p>
                </div>
                
                <form id="classForm">
                    <div class="form-grid">
                        <div class="input-group">
                            <label class="input-label">
                                <svg class="input-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                Select Class
                            </label>
                            <select id="class_id" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>">
                                    <?= htmlspecialchars($class['class_name'] . ' - Section ' . $class['section']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label class="input-label">
                                <svg class="input-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                Date
                            </label>
                            <input type="date" id="date" class="form-input" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="load-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                        Load Students
                    </button>
                </form>
            </div>

            <!-- Attendance Form -->
            <div id="attendanceForm" class="attendance-section" style="display: none;">
                <div class="section-header">
                    <h2 class="section-title">
                        <div class="section-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4"/>
                                <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                                <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                                <path d="M12 3v6"/>
                                <path d="M12 15v6"/>
                            </svg>
                        </div>
                        Mark Attendance
                    </h2>
                    <p class="section-subtitle">Select attendance status for each student</p>
                </div>

                <form method="post" id="markAttendanceForm">
                    <input type="hidden" name="class_id" id="hidden_class_id">
                    <input type="hidden" name="date" id="hidden_date">
                    
                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <div class="quick-actions-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                            Quick Select All
                        </div>
                        <div class="select-all-grid">
                            <div class="select-all-option present" onclick="markAll('present')" tabindex="0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                                All Present
                            </div>
                            <div class="select-all-option absent" onclick="markAll('absent')" tabindex="0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                                All Absent
                            </div>
                            <div class="select-all-option late" onclick="markAll('late')" tabindex="0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                                All Late
                            </div>
                            <div class="select-all-option half-day" onclick="markAll('half_day')" tabindex="0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 2a10 10 0 0 0 0 20"/>
                                </svg>
                                All Half Day
                            </div>
                            <div class="select-all-option clear" onclick="clearAll()" tabindex="0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"/>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                </svg>
                                Clear All
                            </div>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="students-container">
                        <div id="studentCounter" class="student-counter" style="display: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span id="studentCount">0</span> students loaded
                        </div>
                        <div id="studentsList">
                            <!-- Students will be loaded here -->
                        </div>
                    </div>
                    
                    <div class="save-section">
                        <button type="submit" class="save-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17,21 17,13 7,13 7,21"/>
                                <polyline points="7,3 7,8 15,8"/>
                            </svg>
                            Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="loading-overlay" style="display: none;">
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <p class="loading-text">Loading students...</p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="empty-state" style="display: none;">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                </svg>
            </div>
            <h3 class="empty-title">No Students Found</h3>
            <p class="empty-text">No students are enrolled in the selected class.</p>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
    // Form submission handler
    document.getElementById('classForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const classId = document.getElementById('class_id').value;
        const date = document.getElementById('date').value;
        
        if (!classId || !date) {
            showAlert('Please select class and date', 'warning');
            return;
        }
        
        loadStudents(classId, date);
    });

    // Load students function
    function loadStudents(classId, date) {
        document.getElementById('loadingState').style.display = 'flex';
        document.getElementById('attendanceForm').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
        
        document.getElementById('hidden_class_id').value = classId;
        document.getElementById('hidden_date').value = date;
        
        fetch(`get_students.php?class_id=${classId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingState').style.display = 'none';
                
                if (data.error) {
                    showAlert(data.error, 'danger');
                    return;
                }
                
                if (data.length === 0) {
                    document.getElementById('emptyState').style.display = 'block';
                    return;
                }
                
                renderStudentsList(data);
                document.getElementById('attendanceForm').style.display = 'block';
                
                // Show student counter
                document.getElementById('studentCounter').style.display = 'block';
                document.getElementById('studentCount').textContent = data.length;
            })
            .catch(error => {
                document.getElementById('loadingState').style.display = 'none';
                console.error('Error:', error);
                showAlert('Failed to load students', 'danger');
            });
    }

    // Render students list
    function renderStudentsList(students) {
        const studentsList = document.getElementById('studentsList');
        studentsList.innerHTML = '';
        
        students.forEach((student, index) => {
            const studentItem = document.createElement('div');
            studentItem.className = 'student-item';
            
            studentItem.innerHTML = `
                <div class="student-header">
                    <div class="student-avatar">
                        ${index + 1}
                    </div>
                    <div class="student-info">
                        <h3 class="student-name">${student.first_name} ${student.last_name}</h3>
                        <p class="student-id">ID: ${student.student_id}</p>
                    </div>
                </div>
                
                <div class="attendance-grid">
                    <label class="attendance-option present">
                        <input type="radio" name="attendance[${student.id}]" value="present" 
                            ${student.status === 'present' ? 'checked' : ''} required>
                        <div class="option-content">
                            <div class="option-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                            </div>
                            <div class="option-text">Present</div>
                        </div>
                    </label>
                    
                    <label class="attendance-option absent">
                        <input type="radio" name="attendance[${student.id}]" value="absent"
                            ${student.status === 'absent' ? 'checked' : ''}>
                        <div class="option-content">
                            <div class="option-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </div>
                            <div class="option-text">Absent</div>
                        </div>
                    </label>
                    
                    <label class="attendance-option late">
                        <input type="radio" name="attendance[${student.id}]" value="late"
                            ${student.status === 'late' ? 'checked' : ''}>
                        <div class="option-content">
                            <div class="option-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                            </div>
                            <div class="option-text">Late</div>
                        </div>
                    </label>
                    
                    <label class="attendance-option half-day">
                        <input type="radio" name="attendance[${student.id}]" value="half_day"
                            ${student.status === 'half_day' ? 'checked' : ''}>
                        <div class="option-content">
                            <div class="option-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 2a10 10 0 0 0 0 20"/>
                                </svg>
                            </div>
                            <div class="option-text">Half Day</div>
                        </div>
                    </label>
                </div>
                
                <input type="text" class="remarks-input" name="remarks[${student.id}]" 
                    placeholder="Add remarks (optional)" value="${student.remarks || ''}">
            `;
            
            studentsList.appendChild(studentItem);
        });
    }

    // Quick action functions
    function markAll(status) {
        const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
        radios.forEach(radio => {
            radio.checked = true;
        });
        
        const statusText = {
            'present': 'Present',
            'absent': 'Absent', 
            'late': 'Late',
            'half_day': 'Half Day'
        };
        
        showAlert(`All students marked as ${statusText[status]}`, 'success');
    }

    function clearAll() {
        const radios = document.querySelectorAll('input[type="radio"]');
        radios.forEach(radio => {
            radio.checked = false;
        });
        showAlert('All selections cleared', 'info');
    }

    // Alert function
    function showAlert(message, type) {
        const icons = {
            success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>',
            danger: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>'
        };
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `${icons[type]}${message}`;
        
        const container = document.getElementById('messageContainer') || document.querySelector('.attendance-content');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 4000);
    }

    // Form submission
    document.getElementById('markAttendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('attendance.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            showAlert('Attendance saved successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to save attendance', 'danger');
        });
    });

    // Keyboard navigation for quick actions
    document.addEventListener('DOMContentLoaded', function() {
        const quickOptions = document.querySelectorAll('.select-all-option');
        quickOptions.forEach(option => {
            option.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    });
    </script>
</body>
</html>