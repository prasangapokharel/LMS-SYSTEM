<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);

// Get teacher's classes - Fixed query based on actual schema
$stmt = $pdo->prepare("SELECT DISTINCT c.*, s.subject_name 
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      JOIN subjects s ON cst.subject_id = s.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total students taught by this teacher - Fixed query
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT sc.student_id) as total_students
                      FROM student_classes sc
                      JOIN class_subject_teachers cst ON sc.class_id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$total_students = $stmt->fetch(PDO::FETCH_ASSOC)['total_students'];

// Get pending leave applications - Fixed query with proper joins
$stmt = $pdo->prepare("SELECT la.*, u.first_name, u.last_name, st.student_id, c.class_name
                      FROM leave_applications la
                      JOIN users u ON la.user_id = u.id
                      JOIN students st ON u.id = st.user_id
                      JOIN student_classes sc ON st.id = sc.student_id
                      JOIN class_subject_teachers cst ON sc.class_id = cst.class_id
                      JOIN classes c ON sc.class_id = c.id
                      WHERE cst.teacher_id = ? AND la.status = 'pending' AND la.user_type = 'student'
                      GROUP BY la.id
                      ORDER BY la.applied_date DESC");
$stmt->execute([$user['id']]);
$pending_leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - School LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        .welcome-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stats-icon.classes { background: var(--primary-gradient); }
        .stats-icon.students { background: var(--success-gradient); }
        .stats-icon.leaves { background: var(--warning-gradient); }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: #718096;
            font-weight: 500;
            font-size: 1rem;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-modern {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .list-group-item-modern {
            border: none;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .list-group-item-modern:hover {
            background-color: #f8fafc;
            transform: translateX(5px);
        }

        .list-group-item-modern:last-child {
            border-bottom: none;
        }

        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
            background: var(--info-gradient);
            color: white;
        }

        .quick-action-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 2rem;
            text-decoration: none;
            color: #2d3748;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 150px;
        }

        .quick-action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            color: #2d3748;
            border-color: #667eea;
        }

        .quick-action-btn i {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .quick-action-btn span {
            font-weight: 600;
            font-size: 1rem;
        }

        .leave-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .leave-item:hover {
            background-color: #f8fafc;
        }

        .leave-item:last-child {
            border-bottom: none;
        }

        .leave-meta {
            font-size: 0.875rem;
            color: #718096;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #718096;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .quick-action-btn {
                min-height: 120px;
                padding: 1.5rem;
            }
            
            .quick-action-btn i {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="main-content">
            <!-- Welcome Header -->
            <div class="welcome-header">
                <h1 class="welcome-title">
                    <i class="bi bi-person-workspace me-3"></i>
                    Welcome, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>!
                </h1>
                <p class="welcome-subtitle">Teacher Dashboard - Manage your classes, students, and assignments</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon classes">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                        <div class="stats-number"><?= count($classes) ?></div>
                        <div class="stats-label">My Classes</div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon students">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stats-number"><?= $total_students ?></div>
                        <div class="stats-label">My Students</div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon leaves">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div class="stats-number"><?= count($pending_leaves) ?></div>
                        <div class="stats-label">Pending Leaves</div>
                    </div>
                </div>
            </div>
            
            <!-- Content Cards -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5>
                                <i class="bi bi-journal-bookmark me-2"></i>
                                My Classes
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($classes)): ?>
                            <div class="empty-state">
                                <i class="bi bi-journal-x"></i>
                                <h6>No Classes Assigned</h6>
                                <p>You don't have any classes assigned yet. Contact the administration for class assignments.</p>
                            </div>
                            <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($classes as $class): ?>
                                <a href="class_details.php?id=<?= $class['id'] ?>" class="list-group-item list-group-item-action list-group-item-modern d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($class['class_name']) ?></h6>
                                        <small class="text-muted">Section: <?= htmlspecialchars($class['section']) ?></small>
                                    </div>
                                    <span class="badge-modern"><?= htmlspecialchars($class['subject_name']) ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5>
                                <i class="bi bi-calendar-x me-2"></i>
                                Pending Leave Applications
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($pending_leaves)): ?>
                            <div class="empty-state">
                                <i class="bi bi-calendar-check"></i>
                                <h6>No Pending Leaves</h6>
                                <p>All students are present. No pending leave applications to review.</p>
                            </div>
                            <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($pending_leaves as $leave): ?>
                                <a href="../headoffice/leave_details.php?id=<?= $leave['id'] ?>" class="list-group-item list-group-item-action leave-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h6>
                                            <p class="mb-1 leave-meta">
                                                <i class="bi bi-person-badge me-1"></i>
                                                Student ID: <?= htmlspecialchars($leave['student_id']) ?> | 
                                                <i class="bi bi-building me-1"></i>
                                                Class: <?= htmlspecialchars($leave['class_name']) ?>
                                            </p>
                                            <small class="leave-meta">
                                                <i class="bi bi-calendar-range me-1"></i>
                                                <?= htmlspecialchars(date('M d', strtotime($leave['from_date']))) ?> - 
                                                <?= htmlspecialchars(date('M d, Y', strtotime($leave['to_date']))) ?>
                                                (<?= $leave['total_days'] ?> days)
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(date('M d, Y', strtotime($leave['applied_date']))) ?>
                                        </small>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5>
                        <i class="bi bi-lightning me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="attendance.php" class="quick-action-btn">
                                <i class="bi bi-calendar-check"></i>
                                <span>Take Attendance</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="assignments.php" class="quick-action-btn">
                                <i class="bi bi-journal-text"></i>
                                <span>Manage Assignments</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="students.php" class="quick-action-btn">
                                <i class="bi bi-people"></i>
                                <span>View Students</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="teacher_log.php" class="quick-action-btn">
                                <i class="bi bi-journal-plus"></i>
                                <span>Daily Log</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
