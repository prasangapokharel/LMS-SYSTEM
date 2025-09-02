<?php
require_once __DIR__ . '/loader.php';

$current_role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
$user_name = getUserFullName();
$user_initials = getUserInitials();

// Define SVG icons (Heroicons)
$icons = [
    'dashboard' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
    'users' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
    'class' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
    'attendance' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>',
    'leave' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>',
    'teacher_log' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
    'analytics' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
    'logout' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>',
    'school' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>',
    'menu' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>',
    'close' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
];

// Principal navigation items only
$nav_items = [
    ['title' => 'Dashboard', 'url' => '/headoffice/index.php', 'icon' => $icons['dashboard'], 'page' => 'index.php'],
    ['title' => 'Manage Users', 'url' => '/headoffice/manage_users.php', 'icon' => $icons['users'], 'page' => 'manage_users.php'],
    ['title' => 'Class Management', 'url' => '/headoffice/createclass.php', 'icon' => $icons['class'], 'page' => 'createclass.php'],
    ['title' => 'Assign Teachers', 'url' => '/headoffice/assign_teachers.php', 'icon' => $icons['users'], 'page' => 'assign_teachers.php'],
    ['title' => 'Attendance Reports', 'url' => '/headoffice/attendance_reports.php', 'icon' => $icons['attendance'], 'page' => 'attendance_reports.php'],
    ['title' => 'Leave Management', 'url' => '/headoffice/leave_management.php', 'icon' => $icons['leave'], 'page' => 'leave_management.php'],
    ['title' => 'Teacher Logs', 'url' => '/headoffice/teacher_logs.php', 'icon' => $icons['teacher_log'], 'page' => 'teacher_logs.php'],
    ['title' => 'Academic Analytics', 'url' => '/headoffice/academic_analytics.php', 'icon' => $icons['analytics'], 'page' => 'academic_analytics.php'],
    ['title' => 'Create User', 'url' => '/headoffice/createusers.php', 'icon' => $icons['users'], 'page' => 'createusers.php'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/ui.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                        'primary-light': '#dbeafe',
                        gray: {
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-inter">

<!-- Desktop Sidebar -->
<div class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 lg:z-50">
    <div class="flex flex-col flex-grow bg-white border-r border-gray-200 h-full shadow-sm">
        
        <!-- Header -->
        <div class="px-6 py-6 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10  rounded-lg flex items-center justify-center">
                    <img  class="menu-icon h-8 w-8"  src="../assets/icons/principal/nepal.png">
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Prabidi  Solution</h1>
                    <p class="text-xs text-gray-500">Principal Portal</p>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10  rounded-full flex items-center justify-center text-white">
                    <span class="text-sm font-semibold"> <img  class="menu-icon h-8 w-8"  src="../assets/icons/principal/sadmin.png">
</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate"><?= htmlspecialchars($user_name) ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?= htmlspecialchars($current_role) ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <?php foreach ($nav_items as $item): ?>
                <?php $is_active = $current_page === $item['page']; ?>
                <a href="<?= $item['url'] ?>" 
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?= $is_active ? 'bg-primary text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' ?>">
                    <div class="mr-3 flex-shrink-0 <?= $is_active ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' ?>">
                        <?= $item['icon'] ?>
                    </div>
                    <span class="truncate"><?= $item['title'] ?></span>
                    <?php if ($is_active): ?>
                        <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Footer -->
        <div class="px-4 py-4 border-t border-gray-100">
            <a href="/include/logout.php" 
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
                <div class="mr-3 flex-shrink-0">
                    <?= $icons['logout'] ?>
                </div>
                <span class="truncate">Sign Out</span>
            </a>
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-400">© <?= date('Y') ?> School LMS</p>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header -->
<div class="lg:hidden bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-40 shadow-sm">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-3">
            <button id="mobile-menu-button" class="text-gray-600 hover:text-gray-900 focus:outline-none p-1">
                <?= $icons['menu'] ?>
            </button>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white">
                    <?= $icons['school'] ?>
                </div>
                <h1 class="text-lg font-bold text-gray-900">School LMS</h1>
            </div>
        </div>
        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white">
            <span class="text-xs font-semibold"><?= $user_initials ?></span>
        </div>
    </div>
</div>

<!-- Mobile Navigation Menu -->
<div id="mobile-menu" class="lg:hidden fixed inset-0 z-50 bg-gray-900 bg-opacity-50 hidden">
    <div class="absolute inset-y-0 left-0 w-80 bg-white transform transition-transform duration-300 ease-in-out shadow-xl">
        <div class="flex flex-col h-full">
            <!-- Mobile Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white">
                        <?= $icons['school'] ?>
                    </div>
                    <h1 class="text-lg font-bold text-gray-900">School LMS</h1>
                </div>
                <button id="close-mobile-menu" class="text-gray-600 hover:text-gray-900 focus:outline-none p-1">
                    <?= $icons['close'] ?>
                </button>
            </div>

            <!-- Mobile User Profile -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white">
                        <span class="text-sm font-semibold"><?= $user_initials ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($user_name) ?></p>
                        <p class="text-xs text-gray-500 capitalize"><?= htmlspecialchars($current_role) ?></p>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <?php foreach ($nav_items as $item): ?>
                    <?php $is_active = $current_page === $item['page']; ?>
                    <a href="<?= $item['url'] ?>" 
                       class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?= $is_active ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>">
                        <div class="mr-3 flex-shrink-0 <?= $is_active ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' ?>">
                            <?= $item['icon'] ?>
                        </div>
                        <span class="truncate"><?= $item['title'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Mobile Footer -->
            <div class="px-4 py-4 border-t border-gray-100">
                <a href="/include/logout.php" 
                   class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
                    <div class="mr-3 flex-shrink-0">
                        <?= $icons['logout'] ?>
                    </div>
                    <span class="truncate">Sign Out</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Wrapper -->
<div class="lg:pl-64 pt-16 lg:pt-0">
    <!-- Content will be inserted here by the including page -->
</div>

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

</body>
</html>