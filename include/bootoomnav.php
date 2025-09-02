<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_role = $_SESSION['role'] ?? 'student';

// Define navigation items based on role
$nav_items = [];

if ($current_role == 'student') {
    $nav_items = [
        ['page' => 'index', 'icon' => 'home', 'label' => 'Home'],
        ['page' => 'assignments', 'icon' => 'book', 'label' => 'Tasks'],
        ['page' => 'attendance', 'icon' => 'calendar', 'label' => 'Attendance'],
        ['page' => 'leavenotice', 'icon' => 'calendar-days', 'label' => 'Leave'],
        ['page' => 'menu', 'icon' => 'menu', 'label' => 'Menu']
    ];
} elseif ($current_role == 'teacher') {
    $nav_items = [
        ['page' => 'index', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['page' => 'gradebook', 'icon' => 'book-open', 'label' => 'Gradebook'],
        ['page' => 'notices', 'icon' => 'graduation-cap', 'label' => 'Notices'],
        ['page' => 'resources', 'icon' => 'folder', 'label' => 'Resources'],
        ['page' => 'menu', 'icon' => 'message-circle', 'label' => 'Menu']
    ];
} elseif ($current_role == 'principal') {
    $nav_items = [
        ['page' => 'index', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['page' => 'manage_users', 'icon' => 'settings', 'label' => 'Users'],
        ['page' => 'attendance_reports', 'icon' => 'chart', 'label' => 'Reports'],
        ['page' => 'leave_management', 'icon' => 'clipboard', 'label' => 'Leaves'],
        ['page' => 'createclass', 'icon' => 'school', 'label' => 'Classes']
    ];
}

function isNavActive($page) {
    global $current_page;
    return $current_page === $page;
}
?>

<div class="bottom-nav ">
    <div class="nav-container">
        <?php foreach ($nav_items as $item): ?>
            <a href="<?= $item['page'] ?>.php" class="nav-item <?= isNavActive($item['page']) ? 'active' : '' ?>">
                <div class="nav-icon">
                    <?php if ($item['icon'] === 'home'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'dashboard'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="9" rx="1"/>
                            <rect x="14" y="3" width="7" height="5" rx="1"/>
                            <rect x="14" y="12" width="7" height="9" rx="1"/>
                            <rect x="3" y="16" width="7" height="5" rx="1"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'check'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'file'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" x2="8" y1="13" y2="13"/>
                            <line x1="16" x2="8" y1="17" y2="17"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'menu'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
</svg>

                    <?php elseif ($item['icon'] === 'book'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'book-open'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'graduation-cap'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'folder'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'message-circle'): ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M4.499 8.248h15m-15 7.501h15" />
</svg>

                    <?php elseif ($item['icon'] === 'calendar'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'calendar-days'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                            <path d="M8 14h.01"/>
                            <path d="M12 14h.01"/>
                            <path d="M16 14h.01"/>
                            <path d="M8 18h.01"/>
                            <path d="M12 18h.01"/>
                            <path d="M16 18h.01"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'user'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'settings'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'chart'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" x2="12" y1="20" y2="10"/>
                            <line x1="18" x2="18" y1="20" y2="4"/>
                            <line x1="6" x2="6" y1="20" y2="16"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'clipboard'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                        </svg>
                    <?php elseif ($item['icon'] === 'school'): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <span class="nav-label"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-top: 1px solid #e5e7eb;
    z-index: 50;
    padding-bottom: env(safe-area-inset-bottom);
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.nav-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    max-width: 100%;
    padding: 8px 16px;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #6b7280;
    padding: 8px 12px;
    border-radius: 8px;
    min-width: 60px;
    flex: 1;
    max-width: 80px;
    transition: all 0.2s ease;
}

.nav-item.active {
    color: #3b82f6;
    background-color: #eff6ff;
}

.nav-item:hover {
    color: #3b82f6;
    background-color: #f8fafc;
}

.nav-icon {
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-label {
    font-size: 11px;
    font-weight: 500;
    text-align: center;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

/* Body padding for bottom nav */
body {
    padding-bottom: 70px;
}

/* Hide on desktop */
@media (min-width: 1024px) {
    .bottom-nav {
        display: none;
    }
    body {
        padding-bottom: 0;
    }
}

/* Responsive adjustments for smaller screens */
@media (max-width: 480px) {
    .nav-container {
        padding: 6px 8px;
    }
    
    .nav-item {
        padding: 6px 4px;
        min-width: 45px;
        max-width: 70px;
    }
    
    .nav-label {
        font-size: 9px;
    }
    
    .nav-icon svg {
        width: 18px;
        height: 18px;
    }
}

/* Extra small screens - adjust for 5 items */
@media (max-width: 360px) {
    .nav-container {
        padding: 4px 2px;
    }
    
    .nav-item {
        padding: 4px 2px;
        min-width: 40px;
        max-width: 60px;
    }
    
    .nav-label {
        font-size: 8px;
    }
    
    .nav-icon {
        margin-bottom: 2px;
    }
    
    .nav-icon svg {
        width: 16px;
        height: 16px;
    }
}

/* Safe area for devices with home indicator */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
    .bottom-nav {
        padding-bottom: calc(8px + env(safe-area-inset-bottom));
    }
    
    body {
        padding-bottom: calc(70px + env(safe-area-inset-bottom)) !important;
    }
}

/* Prevent text selection on nav items */
.nav-item {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}

/* Smooth transitions */
.nav-item svg {
    transition: transform 0.2s ease;
}

.nav-item:active svg {
    transform: scale(0.95);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent double-tap zoom on nav items
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('touchend', function(e) {
            e.preventDefault();
            
            // Don't navigate if already on the same page
            const targetPage = this.getAttribute('href').replace('.php', '');
            const currentPage = '<?= $current_page ?>';
            
            if (targetPage.includes(currentPage)) {
                return false;
            }
            
            // Add a small delay to show the active state
            setTimeout(() => {
                window.location.href = this.getAttribute('href');
            }, 100);
        });
        
        // Handle regular clicks for desktop
        item.addEventListener('click', function(e) {
            const targetPage = this.getAttribute('href').replace('.php', '');
            const currentPage = '<?= $current_page ?>';
            
            if (targetPage.includes(currentPage)) {
                e.preventDefault();
                return false;
            }
        });
        
        // Add touch feedback
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
});
</script>