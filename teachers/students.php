<?php
include_once '../App/Models/teacher/Student.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students - LMS</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        /* Additional mobile-specific styles */
        .mobile-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 1rem;
            background-color: var(--color-gray-50);
            min-height: 100vh;
            padding-bottom: 80px; /* Space for bottom nav */
        }
        
        .filter-section {
            background: var(--color-white);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }
        
        .students-table-container {
            background: var(--color-white);
            border-radius: 0.75rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
            overflow: hidden;
        }
        
        .table-header {
            background: var(--color-gray-50);
            padding: 1.5rem;
            border-bottom: 1px solid var(--color-gray-200);
        }
        
        .table-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .students-table th {
            background: var(--color-gray-50);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--color-gray-700);
            border-bottom: 1px solid var(--color-gray-200);
            font-size: 0.875rem;
        }
        
        .students-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--color-gray-200);
            color: var(--color-gray-700);
            font-size: 0.875rem;
        }
        
        .students-table tr:last-child td {
            border-bottom: none;
        }
        
        .student-name {
            font-weight: 500;
            color: var(--color-gray-900);
        }
        
        .student-id {
            font-family: monospace;
            color: var(--color-gray-600);
        }
        
        .attendance-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
        }
        
        .attendance-high {
            background: var(--color-success-light);
            color: var(--color-success);
        }
        
        .attendance-medium {
            background: var(--color-warning-light);
            color: var(--color-warning);
        }
        
        .attendance-low {
            background: var(--color-danger-light);
            color: var(--color-danger);
        }
        
        .btn-view {
            background: var(--color-primary);
            color: var(--color-white);
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        
        .btn-clear {
            background: var(--color-gray-500);
            color: var(--color-white);
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--color-gray-500);
        }
        
        .filter-row {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .filter-col {
            flex: 1;
        }
        
        .filter-actions {
            display: flex;
            align-items: end;
            gap: 0.5rem;
        }
        
        /* Mobile responsive table */
        @media (max-width: 768px) {
            .students-table {
                font-size: 0.75rem;
            }
            
            .students-table th,
            .students-table td {
                padding: 0.75rem 0.5rem;
            }
            
            .mobile-container {
                padding: 0.5rem;
            }
            
            .filter-section {
                padding: 1rem;
            }
            
            .table-header {
                padding: 1rem;
            }
            
            /* Hide less important columns on mobile */
            .hide-mobile {
                display: none;
            }
        }
        
        @media (min-width: 769px) {
            .filter-row {
                flex-direction: row;
                align-items: end;
            }
            
            .mobile-container {
                max-width: 1200px;
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">My Students</h1>
            <p class="page-subtitle">Manage and view your student information</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="get" class="filter-row">
                <div class="filter-col">
                    <label class="form-label">Filter by Class</label>
                    <select name="class_id" class="form-input" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-actions">
                    <?php if ($class_id): ?>
                    <a href="students.php" class="btn-clear">Clear Filter</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="students-table-container">
            <div class="table-header">
                <h2 class="table-title">Student List</h2>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
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
                                <span class="student-id"><?= htmlspecialchars($student['student_id']) ?></span>
                            </td>
                            <td>
                                <span class="student-name"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></span>
                            </td>
                            <?php if (!$class_id): ?>
                            <td class="hide-mobile">
                                <?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?>
                            </td>
                            <?php endif; ?>
                            <td class="hide-mobile">
                                <?= htmlspecialchars($student['email']) ?>
                            </td>
                            <td class="hide-mobile">
                                <?= htmlspecialchars($student['phone'] ?? 'N/A') ?>
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
                                <span class="attendance-badge <?= $badge_class ?>">
                                    <?= $attendance_percentage ?>%
                                </span>
                            </td>
                            <td>
                                <a href="student_details.php?id=<?= $student['id'] ?>" class="btn-view">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="<?= $class_id ? '6' : '7' ?>" class="empty-state">
                                <div class="empty-icon">👥</div>
                                <div class="empty-title">No Students Found</div>
                                <div class="empty-text">
                                    <?php if ($class_id): ?>
                                        No students found in the selected class.
                                    <?php else: ?>
                                        You don't have any students assigned yet.
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>
</body>
</html>