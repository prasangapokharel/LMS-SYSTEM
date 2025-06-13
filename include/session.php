<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] == $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

// Redirect if not authorized for role
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: /dashboard.php');
        exit;
    }
}

// Get current user data
function getCurrentUser($pdo) {
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u 
                          JOIN user_roles r ON u.role_id = r.id 
                          WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get student data if current user is a student
function getStudentData($pdo) {
    if (!isLoggedIn() || !hasRole('student')) return null;
    
    $stmt = $pdo->prepare("SELECT s.*, c.class_name, c.section, c.class_level
                          FROM students s 
                          LEFT JOIN student_enrollments se ON s.id = se.student_id AND se.status = 'enrolled'
                          LEFT JOIN classes c ON se.class_id = c.id
                          WHERE s.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Log system activity
function logActivity($pdo, $action, $table_name = null, $record_id = null, $old_values = null, $new_values = null) {
    if (!isLoggedIn()) return;
    
    $stmt = $pdo->prepare("INSERT INTO system_logs 
                          (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $action,
        $table_name,
        $record_id,
        $old_values ? json_encode($old_values) : null,
        $new_values ? json_encode($new_values) : null,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}
?>
