<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_role = $_SESSION['role'] ?? 'student';

// Define navigation items based on role
$nav_items = [];

if ($current_role == 'student') {
    $nav_items = [
        ['page' => 'index', 'icon' => 'home', 'label' => 'Home'],
        ['page' => 'assignments', 'icon' => 'book-open', 'label' => 'Tasks'],
        ['page' => 'attendance', 'icon' => 'calendar', 'label' => 'Attend'],
        ['page' => 'leavenotice', 'icon' => 'calendar-days', 'label' => 'Leave'],
        ['page' => 'profile', 'icon' => 'user', 'label' => 'Profile']
    ];
} elseif ($current_role == 'teacher') {
    $nav_items = [
        ['page' => 'index', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
        ['page' => 'students', 'icon' => 'users', 'label' => 'Students'],
        ['page' => 'attendance', 'icon' => 'check-square', 'label' => 'Attendance'],
        ['page' => 'assignments', 'icon' => 'file-text', 'label' => 'Assignments'],
        ['page' => 'teacher_log', 'icon' => 'book', 'label' => 'Logs']
    ];
} elseif ($current_role == 'principal') {
    $nav_items = [
        ['page' => 'index', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
        ['page' => 'manage_users', 'icon' => 'settings', 'label' => 'Users'],
        ['page' => 'attendance_reports', 'icon' => 'bar-chart', 'label' => 'Reports'],
        ['page' => 'leave_management', 'icon' => 'clipboard', 'label' => 'Leaves'],
        ['page' => 'settings', 'icon' => 'settings-2', 'label' => 'Settings']
    ];
}

function isNavActive($page) {
    global $current_page;
    return $current_page === $page;
}
?>

<div class="bottom-nav fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
    <div class="nav-container flex justify-around items-center max-w-md mx-auto px-4 py-2">
        <?php foreach ($nav_items as $item): ?>
            <a href="<?= $item['page'] ?>.php" 
               class="nav-item flex flex-col items-center text-gray-500 hover:text-blue-800 px-3 py-2 rounded-lg transition-all <?= isNavActive($item['page']) ? 'active text-blue-800 bg-blue-50' : '' ?>"
               data-page="<?= $item['page'] ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-1">
                    <?php if ($item['icon'] === 'home'): ?>
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    <?php elseif ($item['icon'] === 'book-open'): ?>
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    <?php elseif ($item['icon'] === 'calendar'): ?>
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                        <line x1="16" x2="16" y1="2" y2="6"></line>
                        <line x1="8" x2="8" y1="2" y2="6"></line>
                        <line x1="3" x2="21" y1="10" y2="10"></line>
                    <?php elseif ($item['icon'] === 'calendar-days'): ?>
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                        <line x1="16" x2="16" y1="2" y2="6"></line>
                        <line x1="8" x2="8" y1="2" y2="6"></line>
                        <line x1="3" x2="21" y1="10" y2="10"></line>
                        <path d="M8 14h.01"></path>
                        <path d="M12 14h.01"></path>
                        <path d="M16 14h.01"></path>
                        <path d="M8 18h.01"></path>
                        <path d="M12 18h.01"></path>
                        <path d="M16 18h.01"></path>
                    <?php elseif ($item['icon'] === 'user'): ?>
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    <?php elseif ($item['icon'] === 'layout-dashboard'): ?>
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    <?php elseif ($item['icon'] === 'users'): ?>
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    <?php elseif ($item['icon'] === 'check-square'): ?>
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    <?php elseif ($item['icon'] === 'file-text'): ?>
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" x2="8" y1="13" y2="13"></line>
                        <line x1="16" x2="8" y1="17" y2="17"></line>
                        <line x1="10" x2="8" y1="9" y2="9"></line>
                    <?php elseif ($item['icon'] === 'book'): ?>
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    <?php elseif ($item['icon'] === 'settings'): ?>
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    <?php elseif ($item['icon'] === 'bar-chart'): ?>
                        <line x1="12" x2="12" y1="20" y2="10"></line>
                        <line x1="18" x2="18" y1="20" y2="4"></line>
                        <line x1="6" x2="6" y1="20" y2="16"></line>
                    <?php elseif ($item['icon'] === 'clipboard'): ?>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                    <?php elseif ($item['icon'] === 'settings-2'): ?>
                        <path d="M20 7h-9"></path>
                        <path d="M14 17H5"></path>
                        <circle cx="17" cy="17" r="3"></circle>
                        <circle cx="7" cy="7" r="3"></circle>
                    <?php endif; ?>
                </svg>
                <span class="text-xs font-medium"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
    /* Safe area for devices with home indicator */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .bottom-nav {
            padding-bottom: calc(0.5rem + env(safe-area-inset-bottom));
        }
        
        body {
            padding-bottom: calc(60px + env(safe-area-inset-bottom)) !important;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .desktop-sidebar { 
            display: none !important; 
        }
        
        body {
            padding-bottom: 60px !important;
        }
    }

    /* Animation for active item */
    .nav-item.active svg {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }

    /* Ripple effect */
    .nav-item {
        position: relative;
        overflow: hidden;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click feedback and smooth transitions
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't navigate if already on the same page
            const targetPage = this.getAttribute('data-page');
            const currentPage = '<?= $current_page ?>';
            
            if (targetPage === currentPage) {
                e.preventDefault();
                return;
            }
            
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            
            ripple.style.position = 'absolute';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            ripple.style.background = 'rgba(0, 0, 0, 0.1)';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'scale(0)';
            ripple.style.left = e.clientX - rect.left + 'px';
            ripple.style.top = e.clientY - rect.top + 'px';
            ripple.style.animation = 'ripple 0.6s linear';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});
</script>
