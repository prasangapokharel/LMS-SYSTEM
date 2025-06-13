<?php
// Initialize the application
require_once __DIR__ . '/../app/Bootstrap.php';
App\Bootstrap::init();

// Database configuration
$host = 'localhost';
$dbname = 'lms_school';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Cache the database connection status
    App\Bootstrap::cache()->set('db_status', 'connected', 300);
    
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    App\Bootstrap::cache()->set('db_status', 'failed', 60);
    die("Database connection failed. Please try again later.");
}
?>
