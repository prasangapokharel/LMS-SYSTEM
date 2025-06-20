<?php
include_once '../App/Models/teacher/Course.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Courses - LMS</title>
<link rel="stylesheet" href="../assets/css/ui.css">
<style>
    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 1rem;
        background-color: var(--color-gray-50);
        min-height: 100vh;
        padding-bottom: 80px;
    }
    
    .course-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .course-card {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .course-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .course-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 0 0 0.25rem 0;
    }
    
    .course-subtitle {
        font-size: 0.875rem;
        color: var(--color-gray-600);
        margin: 0;
    }
    
    .course-badge {
        background: var(--color-primary-light);
        color: var(--color-primary);
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .course-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .stat-item {
        text-align: center;
        padding: 0.75rem;
        background: var(--color-gray-50);
        border-radius: 0.5rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--color-primary);
        margin: 0;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: var(--color-gray-600);
        margin: 0.25rem 0 0 0;
    }
    
    .course-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .btn-primary {
        background: var(--color-primary);
        color: var(--color-white);
    }
    
    .btn-secondary {
        background: var(--color-gray-500);
        color: var(--color-white);
    }
    
    .btn-success {
        background: var(--color-success);
        color: var(--color-white);
    }
    
    .btn-info {
        background: var(--color-primary-light);
        color: var(--color-primary);
        border: 1px solid var(--color-primary);
    }
    
    .quick-actions {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
        margin-bottom: 1.5rem;
    }
    
    .quick-actions-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 0 0 1rem 0;
    }
    
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1rem;
        background: var(--color-gray-50);
        border-radius: 0.5rem;
        text-decoration: none;
        color: var(--color-gray-700);
        border: 1px solid var(--color-gray-200);
        transition: all 0.2s;
    }
    
    .quick-action-btn:hover {
        background: var(--color-primary-light);
        color: var(--color-primary);
        border-color: var(--color-primary);
    }
    
    .quick-action-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .quick-action-text {
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
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
    
    @media (min-width: 768px) {
        .mobile-container {
            max-width: 1200px;
            padding: 2rem;
        }
        
        .course-grid {
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        }
        
        .course-actions {
            justify-content: flex-start;
        }
    }
</style>
</head>
<body>
<div class="mobile-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">My Courses</h1>
        <p class="page-subtitle">Manage your assigned courses and classes</p>
    </div>

    <!-- Alert Messages -->
    <?= $msg ?>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2 class="quick-actions-title">Quick Actions</h2>
        <div class="quick-actions-grid">
            <a href="assignments.php" class="quick-action-btn">
                <div class="quick-action-icon">📝</div>
                <div class="quick-action-text">Create Assignment</div>
            </a>
            <a href="attendance.php" class="quick-action-btn">
                <div class="quick-action-icon">✅</div>
                <div class="quick-action-text">Take Attendance</div>
            </a>
            <a href="gradebook.php" class="quick-action-btn">
                <div class="quick-action-icon">📊</div>
                <div class="quick-action-text">View Gradebook</div>
            </a>
            <a href="resources.php" class="quick-action-btn">
                <div class="quick-action-icon">📚</div>
                <div class="quick-action-text">Learning Resources</div>
            </a>
            <a href="teacher_log.php" class="quick-action-btn">
                <div class="quick-action-icon">📋</div>
                <div class="quick-action-text">Teaching Log</div>
            </a>
            <a href="messages.php" class="quick-action-btn">
                <div class="quick-action-icon">💬</div>
                <div class="quick-action-text">Messages</div>
            </a>
        </div>
    </div>

    <!-- Courses Grid -->
    <?php if (empty($courses)): ?>
    <div class="empty-state">
        <div class="empty-title">No Courses Assigned</div>
        <div class="empty-text">You don't have any courses assigned yet. Please contact the administrator.</div>
    </div>
    <?php else: ?>
    <div class="course-grid">
        <?php foreach ($courses as $course): ?>
        <?php 
        $stats_key = $course['id'] . '_' . $course['subject_id'];
        $stats = $course_stats[$stats_key] ?? ['students' => 0, 'assignments' => 0, 'recent_classes' => 0];
        ?>
        <div class="course-card">
            <div class="course-header">
                <div>
                    <h3 class="course-title"><?= htmlspecialchars($course['class_name'] . ' ' . $course['section']) ?></h3>
                    <p class="course-subtitle"><?= htmlspecialchars($course['subject_name']) ?></p>
                </div>
                <span class="course-badge"><?= htmlspecialchars($course['subject_code']) ?></span>
            </div>
            
            <div class="course-stats">
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['students'] ?></div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['assignments'] ?></div>
                    <div class="stat-label">Assignments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $stats['recent_classes'] ?></div>
                    <div class="stat-label">Classes (30d)</div>
                </div>
            </div>
            
            <div class="course-actions">
                <a href="students.php?class_id=<?= $course['id'] ?>" class="btn btn-primary">
                    View Students
                </a>
                <a href="assignments.php?class_id=<?= $course['id'] ?>&subject_id=<?= $course['subject_id'] ?>" class="btn btn-secondary">
                    Assignments
                </a>
                <a href="attendance.php?class_id=<?= $course['id'] ?>" class="btn btn-success">
                    Attendance
                </a>
                <a href="gradebook.php?class_id=<?= $course['id'] ?>&subject_id=<?= $course['subject_id'] ?>" class="btn btn-info">
                    Gradebook
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<!-- Include Bottom Navigation -->
<?php include '../include/bootoomnav.php'; ?>

</body>
</html>
