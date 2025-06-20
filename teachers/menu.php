<?php
require_once '../include/session.php';
require_once '../include/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_role = $_SESSION['role'] ?? 'student';
$user_name = $_SESSION['name'] ?? 'User';
$user_avatar = $_SESSION['avatar'] ?? 'default-avatar.png';

// Define menu items that are NOT in bottom navigation
$menu_items = [];

if ($current_role == 'teacher') {
    // Bottom nav has: Dashboard, Gradebook, Courses, Resources, Messages
    // So menu will have other teacher-related items
    $menu_items = [
        [
            'page' => 'attendance',
            'icon' => 'check-circle',
            'title' => 'Attendance',
            'description' => 'Mark & View Student Attendance'
        ],
        [
            'page' => 'assignments',
            'icon' => 'file-text',
            'title' => 'Assignments',
            'description' => 'Create & Manage Assignments'
        ],
        [
            'page' => 'students',
            'icon' => 'users',
            'title' => 'Students',
            'description' => 'Manage Student Information'
        ],
        [
            'page' => 'teacher_log',
            'icon' => 'book-open',
            'title' => 'Teaching Logs',
            'description' => 'Daily Teaching Records'
        ],
        [
            'page' => 'reports',
            'icon' => 'bar-chart',
            'title' => 'Reports',
            'description' => 'Academic Performance Reports'
        ],
        [
            'page' => 'schedule',
            'icon' => 'calendar',
            'title' => 'Class Schedule',
            'description' => 'View Teaching Timetable'
        ],
        [
            'page' => 'exams',
            'icon' => 'clipboard',
            'title' => 'Examinations',
            'description' => 'Create & Manage Exams'
        ],
        [
            'page' => 'notifications',
            'icon' => 'bell',
            'title' => 'Notifications',
            'description' => 'School Announcements'
        ],
        [
            'page' => 'profile',
            'icon' => 'user',
            'title' => 'Profile Settings',
            'description' => 'Update Personal Information'
        ],
        [
            'page' => 'help',
            'icon' => 'help-circle',
            'title' => 'Help & Support',
            'description' => 'Get Help & Documentation'
        ]
    ];
} elseif ($current_role == 'student') {
    $menu_items = [
        [
            'page' => 'grades',
            'icon' => 'award',
            'title' => 'Grades',
            'description' => 'View Academic Results'
        ],
        [
            'page' => 'homework',
            'icon' => 'edit',
            'title' => 'Homework',
            'description' => 'Daily Homework Tasks'
        ],
        [
            'page' => 'schedule',
            'icon' => 'calendar',
            'title' => 'Class Schedule',
            'description' => 'View Daily Timetable'
        ],
        [
            'page' => 'library',
            'icon' => 'book',
            'title' => 'Digital Library',
            'description' => 'Access Learning Resources'
        ],
        [
            'page' => 'fees',
            'icon' => 'credit-card',
            'title' => 'Fee Payment',
            'description' => 'View & Pay School Fees'
        ],
        [
            'page' => 'transport',
            'icon' => 'truck',
            'title' => 'Transport',
            'description' => 'Bus Routes & Tracking'
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - School Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px 30px;
            text-align: center;
            position: relative;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 400px;
            margin: 0 auto;
        }

        .menu-title {
            font-size: 24px;
            font-weight: 600;
            flex-grow: 1;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
        }

        .menu-options {
            width: 24px;
            height: 24px;
            cursor: pointer;
            margin-left: 15px;
        }

        .search-container {
            background: white;
            margin: -20px 20px 0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            background: transparent;
            outline: none;
            color: #333;
        }

        .search-input::placeholder {
            color: #999;
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #999;
        }

        .menu-container {
            background: #f8f9fa;
            min-height: calc(100vh - 140px);
            padding: 30px 20px;
        }

        .menu-grid {
            max-width: 400px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .menu-item {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .menu-item:active {
            transform: translateY(0);
        }

        .menu-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .menu-icon svg {
            width: 24px;
            height: 24px;
            color: white;
        }

        .menu-content {
            flex: 1;
        }

        .menu-title-item {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .menu-description {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        .role-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .header {
                padding: 50px 15px 25px;
            }

            .search-container {
                margin: -15px 15px 0;
            }

            .menu-container {
                padding: 25px 15px;
            }

            .menu-item {
                padding: 16px;
            }

            .menu-icon {
                width: 44px;
                height: 44px;
                margin-right: 14px;
            }

            .menu-icon svg {
                width: 22px;
                height: 22px;
            }
        }

        /* Loading Animation */
        .menu-item {
            opacity: 0;
            animation: slideInUp 0.6s ease forwards;
        }

        .menu-item:nth-child(1) { animation-delay: 0.1s; }
        .menu-item:nth-child(2) { animation-delay: 0.2s; }
        .menu-item:nth-child(3) { animation-delay: 0.3s; }
        .menu-item:nth-child(4) { animation-delay: 0.4s; }
        .menu-item:nth-child(5) { animation-delay: 0.5s; }
        .menu-item:nth-child(6) { animation-delay: 0.6s; }
        .menu-item:nth-child(7) { animation-delay: 0.7s; }
        .menu-item:nth-child(8) { animation-delay: 0.8s; }
        .menu-item:nth-child(9) { animation-delay: 0.9s; }
        .menu-item:nth-child(10) { animation-delay: 1.0s; }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="role-badge"><?= $current_role ?></div>
        <div class="header-content">
            <h1 class="menu-title">Menu</h1>
            <img src="assets/images/<?= $user_avatar ?>" alt="Profile" class="user-avatar">
            <svg class="menu-options" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="1"/>
                <circle cx="12" cy="5" r="1"/>
                <circle cx="12" cy="19" r="1"/>
            </svg>
        </div>
    </div>

    <div class="search-container">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" class="search-input" placeholder="Search" id="searchInput">
    </div>

    <div class="menu-container">
        <div class="menu-grid" id="menuGrid">
            <?php foreach ($menu_items as $item): ?>
                <a href="<?= $item['page'] ?>.php" class="menu-item">
                    <div class="menu-icon">
                        <?php
                        $icon = $item['icon'];
                        switch($icon) {
                            case 'check-circle':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>';
                                break;
                            case 'file-text':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/></svg>';
                                break;
                            case 'users':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
                                break;
                            case 'book-open':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>';
                                break;
                            case 'bar-chart':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>';
                                break;
                            case 'calendar':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>';
                                break;
                            case 'clipboard':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>';
                                break;
                            case 'bell':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>';
                                break;
                            case 'user':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
                                break;
                            case 'help-circle':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>';
                                break;
                            case 'award':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>';
                                break;
                            case 'edit':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>';
                                break;
                            case 'book':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>';
                                break;
                            case 'credit-card':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>';
                                break;
                            case 'truck':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>';
                                break;
                            default:
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>';
                        }
                        ?>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title-item"><?= $item['title'] ?></div>
                        <div class="menu-description"><?= $item['description'] ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

     <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                const title = item.querySelector('.menu-title-item').textContent.toLowerCase();
                const description = item.querySelector('.menu-description').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Add touch feedback
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            item.addEventListener('touchend', function() {
                this.style.transform = 'scale(1)';
            });
            
            item.addEventListener('touchcancel', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>