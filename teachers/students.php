<?php
include_once '../App/Models/teacher/Student.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students - LMS</title>
    <meta name="description" content="Manage and view your student information">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <style>
        .students-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .students-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .students-header::before {
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

        .students-content {
            padding: 0 20px;
            max-width: 100%;
        }

        .filter-card {
            background: white;
            border-radius: 20px;
            padding: 28px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .filter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        }

        .filter-header {
            margin-bottom: 24px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .filter-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        .filter-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-label-icon {
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

        .filter-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .clear-btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.3);
        }

        .clear-btn svg {
            width: 16px;
            height: 16px;
        }

        .students-table-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .table-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 24px 28px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-title-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-title-icon svg {
            width: 16px;
            height: 16px;
            color: #059669;
        }

        .table-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        .table-container {
            overflow-x: auto;
            max-height: 70vh;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .students-table th {
            background: #f8fafc;
            padding: 16px 20px;
            text-align: left;
            font-weight: 700;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .students-table td {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #374151;
            font-size: 14px;
            vertical-align: middle;
        }

        .students-table tr:last-child td {
            border-bottom: none;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .student-details {
            flex: 1;
            min-width: 0;
        }

        .student-name {
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px 0;
            font-size: 15px;
        }

        .student-id {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            color: #6b7280;
            font-size: 12px;
            margin: 0;
            font-weight: 500;
        }

        .class-info {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .contact-info {
            color: #6b7280;
            font-size: 13px;
        }

        .attendance-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            min-width: 70px;
            justify-content: center;
        }

        .attendance-high {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
            border: 1px solid #bbf7d0;
        }

        .attendance-medium {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            color: #d97706;
            border: 1px solid #fed7aa;
        }

        .attendance-low {
            background: linear-gradient(135deg, #fef2f2, #fecaca);
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .view-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        .view-btn svg {
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
            margin: 0;
            line-height: 1.5;
        }

        @media (min-width: 768px) {
            .filter-form {
                flex-direction: row;
                align-items: end;
            }

            .filter-group {
                flex: 1;
            }

            .students-content {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 32px;
            }
        }

        @media (max-width: 767px) {
            .students-content {
                padding: 0 16px;
            }

            .filter-card, .students-table-card {
                border-radius: 16px;
                padding: 20px 16px;
            }

            .table-header {
                padding: 20px 16px;
            }

            .students-table {
                min-width: 600px;
            }

            .students-table th,
            .students-table td {
                padding: 12px 8px;
                font-size: 12px;
            }

            .student-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .student-name {
                font-size: 13px;
            }

            .hide-mobile {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="students-app">
        <div class="students-header">
            <div class="header-content">
                <div class="header-top">
                    <a href="index.php" class="back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="header-title">My Students</h1>
                </div>
                <p class="header-subtitle">Manage and view your student information</p>
            </div>
        </div>

        <div class="students-content">
            <div class="filter-card">
                <div class="filter-header">
                    <h2 class="filter-title">
                        <div class="filter-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 3h-6l-2 3h-4l-2-3H2v18h20V3z"/>
                                <path d="M8 21l-2-8h12l-2 8"/>
                            </svg>
                        </div>
                        Filter Students
                    </h2>
                    <p class="filter-subtitle">Filter students by class to view specific groups</p>
                </div>
                
                <form method="get" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">
                            <svg class="filter-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Filter by Class
                        </label>
                        <select name="class_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if ($class_id): ?>
                    <div class="filter-actions">
                        <a href="students.php" class="clear-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"/>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            </svg>
                            Clear Filter
                        </a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            <div class="students-table-card">
                <div class="table-header">
                    <h2 class="table-title">
                        <div class="table-title-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                        </div>
                        Student List
                    </h2>
                    <p class="table-subtitle"><?= count($students) ?> students found</p>
                </div>
                
                <div class="table-container">
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
                        <h3 class="empty-title">No Students Found</h3>
                        <p class="empty-text">
                            <?php if ($class_id): ?>
                                No students found in the selected class.
                            <?php else: ?>
                                You don't have any students assigned yet.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php else: ?>
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <?php if (!$class_id): ?>
                                <th class="hide-mobile">Class</th>
                                <?php endif; ?>
                                <th class="hide-mobile">Email</th>
                                <th class="hide-mobile">Phone</th>
                                <th>Attendance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                        </div>
                                        <div class="student-details">
                                            <div class="student-name"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
                                            <div class="student-id">ID: <?= htmlspecialchars($student['student_id']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <?php if (!$class_id): ?>
                                <td class="hide-mobile">
                                    <div class="class-info"><?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?></div>
                                </td>
                                <?php endif; ?>
                                <td class="hide-mobile">
                                    <div class="contact-info"><?= htmlspecialchars($student['email']) ?></div>
                                </td>
                                <td class="hide-mobile">
                                    <div class="contact-info"><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></div>
                                </td>
                                <td>
                                    <?php 
                                    $attendance_percentage = $student['total_attendance'] > 0 ? 
                                        round(($student['present_count'] / $student['total_attendance']) * 100) : 0;
                                    
                                    $badge_class = 'attendance-low';
                                    if ($attendance_percentage >= 75) {
                                        $badge_class = 'attendance-high';
                                    } elseif ($attendance_percentage >= 50) {
                                        $badge_class = 'attendance-medium';
                                    }
                                    ?>
                                    <div class="attendance-badge <?= $badge_class ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px;">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22,4 12,14.01 9,11.01"/>
                                        </svg>
                                        <?= $attendance_percentage ?>%
                                    </div>
                                </td>
                                <td>
                                    <a href="student_details.php?id=<?= $student['id'] ?>" class="view-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>
</body>
</html>