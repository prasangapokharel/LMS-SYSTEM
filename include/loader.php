<?php
// Database connection and session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/connect.php';

// Include session management functions
require_once __DIR__ . '/session.php';

// Get user's full name
function getUserFullName() {
    $first_name = $_SESSION['first_name'] ?? '';
    $last_name = $_SESSION['last_name'] ?? '';
    return trim("$first_name $last_name") ?: ($_SESSION['username'] ?? 'User');
}

// Get user's initials for avatar
function getUserInitials() {
    $first_name = $_SESSION['first_name'] ?? '';
    $last_name = $_SESSION['last_name'] ?? '';
    $username = $_SESSION['username'] ?? 'U';
    
    if ($first_name && $last_name) {
        return strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
    } elseif ($first_name) {
        return strtoupper(substr($first_name, 0, 2));
    } else {
        return strtoupper(substr($username, 0, 2));
    }
}

// Get formatted date
function getFormattedDate($date = null) {
    if (!$date) $date = date('Y-m-d');
    return date('M j, Y', strtotime($date));
}

// Get time ago format
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' min ago';
    if ($time < 86400) return floor($time/3600) . ' hr ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}

// Format number with suffix
function formatNumber($num) {
    if ($num >= 1000000) {
        return round($num/1000000, 1) . 'M';
    } elseif ($num >= 1000) {
        return round($num/1000, 1) . 'K';
    }
    return number_format($num);
}

// Get attendance percentage color
function getAttendanceColor($percentage) {
    if ($percentage >= 90) return 'success';
    if ($percentage >= 75) return 'warning';
    return 'danger';
}

// Get grade color
function getGradeColor($grade) {
    switch (strtoupper($grade)) {
        case 'A+':
        case 'A': return 'success';
        case 'B+':
        case 'B': return 'info';
        case 'C+':
        case 'C': return 'warning';
        default: return 'danger';
    }
}
?>
