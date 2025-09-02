<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Get student's class_id safely
$class_id = null;
if (isset($student['class_id'])) {
    $class_id = $student['class_id'];
} else {
    // Get class_id from student_classes table
    $stmt = $pdo->prepare("SELECT sc.class_id FROM student_classes sc 
                          JOIN students s ON sc.student_id = s.id 
                          WHERE s.user_id = ? AND sc.status = 'enrolled'");
    $stmt->execute([$user['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $class_id = $result ? $result['class_id'] : null;
}

// Get unread message count
$stmt = $pdo->prepare("SELECT COUNT(*) as unread_count FROM messages WHERE recipient_id = ? AND is_read = 0 AND is_deleted_by_recipient = 0");
$stmt->execute([$user['id']]);
$unread_messages = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

// Get pending assignments count
$stmt = $pdo->prepare("SELECT COUNT(*) as pending_count 
                      FROM assignments a
                      JOIN student_classes sc ON a.class_id = sc.class_id
                      LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND sc.student_id = asub.student_id
                      WHERE sc.student_id = (SELECT id FROM students WHERE user_id = ?) 
                      AND a.due_date >= CURDATE() AND asub.id IS NULL");
$stmt->execute([$user['id']]);
$pending_assignments = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];

// Get new resources count (last 7 days)
$new_resources = 0;
if ($class_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as new_count 
                          FROM learning_resources lr
                          WHERE (lr.is_public = 1 OR lr.class_id = ?) 
                          AND lr.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stmt->execute([$class_id]);
    $new_resources = $stmt->fetch(PDO::FETCH_ASSOC)['new_count'];
}
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Student Portal</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding-bottom: 80px;
        }

        .mobile-header {
            background: #3b82f6;
            color: white;
            padding: 30px 10px 20px;
            text-align: center;
            position: relative;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 400px;
            margin: 0 auto;
        }

        .menu-title {
            font-size: 20px;
            font-weight: 600;
            flex: 1;
            text-align: center;
        }

        .profile-avatar {
            text-decoration: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .search-container {
            padding: 20px;
            background: white;
            margin: -15px 20px 0;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            background: #f9fafb;
        }

        .search-box::placeholder {
            color: #9ca3af;
        }

        .menu-container {
            padding: 30px 20px 20px;
        }

        .menu-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f3f4f6;
            position: relative;
        }

        .menu-item:last-child {
            border-bottom: none;
        }

        .menu-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 24px;
        }

        .menu-content {
            flex: 1;
        }

        .menu-item-title {
            font-size: 17px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .menu-item-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.4;
        }

        .menu-badge {
            background: #ef4444;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
            position: absolute;
            top: 15px;
            right: 20px;
        }

        .menu-badge.blue {
            background: #3b82f6;
        }

        .menu-badge.green {
            background: #10b981;
        }

        /* Icon colors */
        .icon-blue { background: #dbeafe; }
        .icon-green { background: #d1fae5; }
        .icon-purple { background: #ede9fe; }
        .icon-orange { background: #fed7aa; }
        .icon-red { background: #fecaca; }
        .icon-yellow { background: #fef3c7; }
        .icon-pink { background: #fce7f3; }
        .icon-indigo { background: #e0e7ff; }

        @media (max-width: 480px) {
            .mobile-header {
                padding: 50px 16px 25px;
            }
            
            .search-container {
                margin: -15px 16px 0;
                padding: 16px;
            }
            
            .menu-container {
                padding: 25px 16px 16px;
            }
            
            .menu-item {
                padding: 16px;
            }
            
            .menu-icon {
                width: 45px;
                height: 45px;
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-header">
        <div class="header-content">
            <div></div>
            <h1 class="menu-title">Menu</h1>
            <a href="profile.php" class="profile-avatar">
                <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
            </a>
            <!-- <div class="profile-avatar">
                <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
            </div> -->
        </div>
    </div>


    <div class="menu-container">
        <div class="menu-list">
            <!-- <a href="index.php" class="menu-item">
                <div class="menu-icon icon-blue">🏠</div>
                <div class="menu-content">
                    <div class="menu-item-title">Dashboard</div>
                    <div class="menu-item-desc">View your daily overview</div>
                </div>
            </a> -->

            <a href="assignments.php" class="menu-item">
                <?php if ($pending_assignments > 0): ?>
                    <div class="menu-badge"><?= $pending_assignments ?></div>
                <?php endif; ?>
                <div class="menu-icon icon-yellow"><img class="h-8 w-8"  src="../assets/icons/assingn.png"></div>
                <div class="menu-content">
                    <div class="menu-item-title">Assignments</div>
                    <div class="menu-item-desc">View & Submit Assignment</div>
                </div>
            </a>

            <a href="attendance.php" class="menu-item">
                <div class="menu-icon icon-yellow"><img class="h-8 w-8"  src="../assets/icons/attendance.png"></div>
                <div class="menu-content">
                    <div class="menu-item-title">Attendance</div>
                    <div class="menu-item-desc">Monthly & Aggregate Report</div>
                </div>
            </a>

            <a href="resources.php" class="menu-item">
                <?php if ($new_resources > 0): ?>
                    <div class="menu-badge green"><?= $new_resources ?></div>
                <?php endif; ?>
                <div class="menu-icon icon-yellow"><img class="h-8 w-8"  src="../assets/icons/reading.png"></div>
                <div class="menu-content">
                    <div class="menu-item-title">Reading Materials</div>
                    <div class="menu-item-desc">View Reading Material & Resources</div>
                </div>
            </a>

            <a href="messages.php" class="menu-item">
                <?php if ($unread_messages > 0): ?>
                    <div class="menu-badge"><?= $unread_messages ?></div>
                <?php endif; ?>
                <div class="menu-icon icon-yellow"><img class="h-8 w-8"  src="../assets/icons/message.png"></div>
                <div class="menu-content">
                    <div class="menu-item-title">Messages</div>
                    <div class="menu-item-desc">View messages from teachers</div>
                </div>
            </a>

            <a href="report-card.php" class="menu-item">
                <div class="menu-icon icon-yellow"><img class="h-8 w-8"  src="../assets/icons/setting.png"></div>
                <div class="menu-content">
                    <div class="menu-item-title">Report Card</div>
                    <div class="menu-item-desc">View Exam Results</div>
                </div>
            </a>

            <a href="events.php" class="menu-item">
                <div class="menu-icon icon-yellow"><img class="h-8 w-8"  src="../assets/icons/events.png"></div>
                <div class="menu-content">
                    <div class="menu-item-title">Events</div>
                    <div class="menu-item-desc">Events & Class Routine</div>
                </div>
            </a>

            <a href="leavenotice.php" class="menu-item">
                <div class="menu-icon icon-yellow"><img class="h-8 w-8"  src="../assets/icons/leave.png"></div>
                <div class="menu-content">
                    <div class="menu-item-title">Leave Application</div>
                    <div class="menu-item-desc">Apply for leave requests</div>
                </div>
            </a>

            <!-- <a href="profile.php" class="menu-item">
                <div class="menu-icon icon-pink">👤</div>
                <div class="menu-content">
                    <div class="menu-item-title">My Profile</div>
                    <div class="menu-item-desc">Manage personal information</div>
                </div>
            </a> -->
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>

    <script>
        // Simple search functionality
        document.getElementById('searchBox').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                const title = item.querySelector('.menu-item-title').textContent.toLowerCase();
                const desc = item.querySelector('.menu-item-desc').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || desc.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
