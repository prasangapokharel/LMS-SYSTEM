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

// Get user profile image from database
$stmt = $pdo->prepare("SELECT profile_image, first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
$user_avatar = $user_data['profile_image'] ?? 'default-avatar.png';
$user_full_name = ($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? '');

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
            'page' => '../include/logout',
            'icon' => 'help-circle',
            'title' => 'Logout',
            'description' => 'Get logout & Documentation'
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 32px 20px 36px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
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

        .menu-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .user-role {
            font-size: 12px;
            opacity: 0.8;
            text-transform: capitalize;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
        }

        .search-container {
            background: white;
            margin: -20px 20px 0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .search-input {
            width: 100%;
            padding: 16px 20px 16px 56px;
            border: none;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 500;
            background: transparent;
            outline: none;
            color: #111827;
        }

        .search-input::placeholder {
            color: #6b7280;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #059669;
        }

        .menu-container {
            background: transparent;
            min-height: calc(100vh - 140px);
            padding: 24px 20px;
        }

        .menu-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .menu-item {
            background: white;
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #111827;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        }

        .menu-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .menu-icon svg {
            width: 24px;
            height: 24px;
            color: #059669;
        }

        .menu-content {
            flex: 1;
        }

        .menu-title-item {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }

        .menu-description {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            font-weight: 400;
        }

        .menu-arrow {
            width: 20px;
            height: 20px;
            color: #9ca3af;
            margin-left: 12px;
        }

        /* Responsive Design */
        @media (min-width: 768px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .header {
                padding: 40px 24px 44px;
            }

            .search-container {
                margin: -24px 24px 0;
            }

            .menu-container {
                padding: 32px 24px;
            }
        }

        @media (min-width: 1024px) {
            .menu-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 767px) {
            .header {
                padding: 28px 16px 32px;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .header-left {
                width: 100%;
                justify-content: space-between;
            }

            .header-right {
                width: 100%;
                justify-content: flex-end;
            }

            .user-info {
                display: none;
            }

            .search-container {
                margin: -16px 16px 0;
            }

            .menu-container {
                padding: 20px 16px;
            }

            .menu-item {
                padding: 20px;
            }

            .menu-icon {
                width: 48px;
                height: 48px;
                margin-right: 16px;
            }

            .menu-icon svg {
                width: 20px;
                height: 20px;
            }

            .menu-title-item {
                font-size: 15px;
            }

            .menu-description {
                font-size: 13px;
            }
        }

        /* No animations, transforms, or hover effects as requested */
        .menu-item,
        .back-btn,
        .search-input {
            transition: none;
        }

        .menu-item:hover,
        .menu-item:active,
        .menu-item:focus {
            transform: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .back-btn:hover,
        .back-btn:active,
        .back-btn:focus {
            transform: none;
            background: rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <a href="index.php" class="back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5"/>
                        <path d="M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="menu-title">Menu</h1>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars(trim($user_full_name)) ?: $user_name ?></div>
                    <div class="user-role"><?= htmlspecialchars($current_role) ?></div>
                </div>
                <img src="../assets/images/<?= htmlspecialchars($profile_image) ?>" alt="Profile" class="user-avatar" onerror="this.src='../assets/images/default-avatar.png'">
            </div>
        </div>
    </div>

    <div class="search-container">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" class="search-input" placeholder="Search menu items..." id="searchInput">
    </div>

    <div class="menu-container">
        <div class="menu-grid" id="menuGrid">
            <?php foreach ($menu_items as $item): ?>
                <a href="<?= htmlspecialchars($item['page']) ?>.php" class="menu-item">
                    <div class="menu-icon">
                        <?php
                        $icon = $item['icon'];
                        switch($icon) {
                            case 'check-circle':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>';
                                break;
                            case 'file-text':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/></svg>';
                                break;
                            case 'users':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
                                break;
                            case 'book-open':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>';
                                break;
                            case 'bar-chart':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>';
                                break;
                            case 'calendar':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>';
                                break;
                            case 'clipboard':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>';
                                break;
                            case 'bell':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>';
                                break;
                            case 'user':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
                                break;
                            case 'logout-circle':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>';
                                break;
                            case 'award':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>';
                                break;
                            case 'edit':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>';
                                break;
                            case 'book':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>';
                                break;
                            case 'credit-card':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>';
                                break;
                            case 'truck':
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>';
                                break;
                            default:
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>';
                        }
                        ?>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title-item"><?= htmlspecialchars($item['title']) ?></div>
                        <div class="menu-description"><?= htmlspecialchars($item['description']) ?></div>
                    </div>
                    <svg class="menu-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
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

        // Simple click feedback without animations
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                // Simple visual feedback without transforms
                this.style.opacity = '0.8';
                setTimeout(() => {
                    this.style.opacity = '1';
                }, 100);
            });
        });
    </script>
</body>
</html>
