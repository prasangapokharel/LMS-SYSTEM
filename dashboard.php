<?php
session_start();
include 'include/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];

// Redirect to appropriate dashboard based on role
switch ($role) {
    case 'principal':
        header('Location: headoffice/index.php');
        break;
    case 'teacher':
        header('Location: teachers/index.php');
        break;
    case 'student':
        header('Location: students/index.php');
        break;
    default:
        // If role is not recognized, logout and redirect to login
        session_destroy();
        header('Location: login.php');
        break;
}
exit;
?>
