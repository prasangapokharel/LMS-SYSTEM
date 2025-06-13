<?php
require_once __DIR__ . '/loader.php';

$current_role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
$user_name = getUserFullName();
$user_initials = getUserInitials();

// Define SVG icons
$icons = [
    'dashboard' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
    'users' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
    'calendar' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
    'attendance' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>',
    'chart' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
    'class' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
    'leave' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>',
    'teacher_log' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
    'settings' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
    'reports' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
    'logout' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>',
    'notification' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>',
    'school' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>',
    'analytics' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
    'academic' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
];

// Define navigation items for each role
$nav_items = [];

if ($current_role == 'principal') {
    $nav_items = [
        ['title' => 'Dashboard', 'url' => '/headoffice/index.php', 'icon' => $icons['dashboard'], 'page' => 'index.php'],
        ['title' => 'Manage Users', 'url' => '/headoffice/manage_users.php', 'icon' => $icons['users'], 'page' => 'manage_users.php'],
        ['title' => 'Class Management', 'url' => '/headoffice/createclass.php', 'icon' => $icons['class'], 'page' => 'createclass.php'],
        ['title' => 'Assign Teachers', 'url' => '/headoffice/assign_teachers.php', 'icon' => $icons['users'], 'page' => 'assign_teachers.php'],
        ['title' => 'Attendance Reports', 'url' => '/headoffice/attendance_reports.php', 'icon' => $icons['attendance'], 'page' => 'attendance_reports.php'],
        ['title' => 'Leave Management', 'url' => '/headoffice/leave_management.php', 'icon' => $icons['leave'], 'page' => 'leave_management.php'],
        ['title' => 'Teacher Logs', 'url' => '/headoffice/teacher_logs.php', 'icon' => $icons['teacher_log'], 'page' => 'teacher_logs.php'],
        ['title' => 'Academic Analytics', 'url' => '/headoffice/academic_analytics.php', 'icon' => $icons['analytics'], 'page' => 'academic_analytics.php'],
        ['title' => 'System Settings', 'url' => '/headoffice/settings.php', 'icon' => $icons['settings'], 'page' => 'settings.php'],
    ];
} elseif ($current_role == 'teacher') {
    $nav_items = [
        ['title' => 'Dashboard', 'url' => '/teachers/index.php', 'icon' => $icons['dashboard'], 'page' => 'index.php'],
        ['title' => 'Take Attendance', 'url' => '/teachers/attendance.php', 'icon' => $icons['attendance'], 'page' => 'attendance.php'],
        ['title' => 'Assignments', 'url' => '/teachers/assignments.php', 'icon' => $icons['academic'], 'page' => 'assignments.php'],
        ['title' => 'My Students', 'url' => '/teachers/students.php', 'icon' => $icons['users'], 'page' => 'students.php'],
        ['title' => 'Teacher Log', 'url' => '/teachers/teacher_log.php', 'icon' => $icons['teacher_log'], 'page' => 'teacher_log.php'],
    ];
} elseif ($current_role == 'student') {
    $nav_items = [
        ['title' => 'Dashboard', 'url' => '/students/index.php', 'icon' => $icons['dashboard'], 'page' => 'index.php'],
        ['title' => 'My Assignments', 'url' => '/students/assignments.php', 'icon' => $icons['academic'], 'page' => 'assignments.php'],
        ['title' => 'My Attendance', 'url' => '/students/attendance.php', 'icon' => $icons['attendance'], 'page' => 'attendance.php'],
        ['title' => 'Leave Application', 'url' => '/students/leavenotice.php', 'icon' => $icons['leave'], 'page' => 'leavenotice.php'],
        ['title' => 'My Profile', 'url' => '/students/profile.php', 'icon' => $icons['users'], 'page' => 'profile.php'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        sidebar: {
                            bg: '#1e293b',
                            hover: '#334155',
                            active: '#0ea5e9',
                            text: '#f8fafc',
                            muted: '#94a3b8',
                            border: '#475569'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">

<!-- Desktop Sidebar -->
<div class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 lg:z-50">
    <div class="flex flex-col flex-grow bg-sidebar-bg overflow-y-auto border-r border-sidebar-border h-full">
        <!-- Logo/Brand -->
        <div class="flex items-center flex-shrink-0 px-6 py-6 border-b border-sidebar-border">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                    <?= $icons['school'] ?>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">School LMS</h1>
                    <p class="text-xs text-sidebar-muted">Learning Management System</p>
                </div>
            </div>
        </div>

        <!-- User Profile Card -->
        <div class="px-6 py-4 border-b border-sidebar-border">
            <div class="flex items-center space-x-3 p-3 bg-sidebar-hover rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                        <span class="text-sm font-bold"><?= $user_initials ?></span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($user_name) ?></p>
                    <p class="text-xs text-sidebar-muted capitalize flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                        <?= htmlspecialchars($current_role) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1">
            <div class="mb-2">
                <p class="px-3 text-xs font-semibold text-sidebar-muted uppercase tracking-wider">Main Menu</p>
            </div>
            
            <?php foreach ($nav_items as $item): ?>
                <?php $is_active = $current_page === $item['page']; ?>
                <a href="<?= $item['url'] ?>" 
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg <?= $is_active ? 'bg-blue-600 text-white' : 'text-sidebar-text hover:bg-sidebar-hover' ?>">
                    <div class="mr-3 flex-shrink-0 <?= $is_active ? 'text-white' : 'text-sidebar-muted group-hover:text-white' ?>">
                        <?= $item['icon'] ?>
                    </div>
                    <span class="truncate"><?= $item['title'] ?></span>
                    <?php if ($is_active): ?>
                        <div class="ml-auto w-1.5 h-1.5 bg-white rounded-full"></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
            
            <!-- Quick Actions Section -->
            <div class="mt-6 pt-4 border-t border-sidebar-border">
                <p class="px-3 text-xs font-semibold text-sidebar-muted uppercase tracking-wider mb-2">Quick Actions</p>
                
                <a href="/include/logout.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300">
                    <div class="mr-3 flex-shrink-0">
                        <?= $icons['logout'] ?>
                    </div>
                    <span class="truncate">Sign Out</span>
                </a>
            </div>
        </nav>

        <!-- Footer -->
        <div class="flex-shrink-0 border-t border-sidebar-border p-4">
            <div class="text-center">
                <p class="text-xs text-sidebar-muted">© <?= date('Y') ?> School LMS</p>
                <p class="text-xs text-sidebar-muted">Version 2.0</p>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header -->
<div class="lg:hidden bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-3">
            <button id="mobile-menu-button" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white mr-2">
                    <?= $icons['school'] ?>
                </div>
                <h1 class="text-lg font-bold text-gray-900">School LMS</h1>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button class="p-1 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
                <?= $icons['notification'] ?>
            </button>
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                <span class="text-xs font-bold"><?= $user_initials ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Navigation Menu -->
<div id="mobile-menu" class="lg:hidden fixed inset-0 z-50 bg-gray-900 bg-opacity-50 hidden">
    <div class="absolute inset-y-0 left-0 w-64 bg-sidebar-bg transform transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-6 py-4 border-b border-sidebar-border">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                        <?= $icons['school'] ?>
                    </div>
                    <h1 class="text-lg font-bold text-white">School LMS</h1>
                </div>
                <button id="close-mobile-menu" class="text-white hover:text-gray-300 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- User Profile -->
            <div class="px-6 py-4 border-b border-sidebar-border">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                        <span class="text-sm font-bold"><?= $user_initials ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white"><?= htmlspecialchars($user_name) ?></p>
                        <p class="text-xs text-sidebar-muted capitalize"><?= htmlspecialchars($current_role) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-4 overflow-y-auto">
                <?php foreach ($nav_items as $item): ?>
                    <?php $is_active = $current_page === $item['page']; ?>
                    <a href="<?= $item['url'] ?>" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg mb-1 <?= $is_active ? 'bg-blue-600 text-white' : 'text-sidebar-text hover:bg-sidebar-hover' ?>">
                        <div class="mr-3 flex-shrink-0 <?= $is_active ? 'text-white' : 'text-sidebar-muted group-hover:text-white' ?>">
                            <?= $item['icon'] ?>
                        </div>
                        <span class="truncate"><?= $item['title'] ?></span>
                    </a>
                <?php endforeach; ?>
                
                <div class="mt-6 pt-4 border-t border-sidebar-border">
                    <a href="/include/logout.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300">
                        <div class="mr-3 flex-shrink-0">
                            <?= $icons['logout'] ?>
                        </div>
                        <span class="truncate">Sign Out</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content Wrapper -->
<div class="lg:pl-64 pt-16 lg:pt-0">
    <!-- Content will be inserted here by the including page -->

<script>
    // Mobile menu functionality
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMobileMenuButton = document.getElementById('close-mobile-menu');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && closeMobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
        
        closeMobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
        
        // Close menu when clicking outside
        mobileMenu.addEventListener('click', (e) => {
            if (e.target === mobileMenu) {
                mobileMenu.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    }
</script>
