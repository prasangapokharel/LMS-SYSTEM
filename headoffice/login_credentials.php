<?php
// This file shows all the login credentials for testing
include 'include/connect.php';

echo "<h1>School LMS - Login Credentials</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .credential-box { 
        border: 1px solid #ddd; 
        padding: 15px; 
        margin: 10px 0; 
        border-radius: 5px; 
        background-color: #f9f9f9; 
    }
    .role-header { 
        color: #333; 
        border-bottom: 2px solid #007bff; 
        padding-bottom: 5px; 
    }
    .password { 
        color: #d63384; 
        font-weight: bold; 
    }
</style>";

// Get all users with their credentials
$stmt = $pdo->prepare("SELECT u.*, r.role_name, s.student_id 
                      FROM users u 
                      JOIN user_roles r ON u.role_id = r.id 
                      LEFT JOIN students s ON u.id = s.user_id 
                      ORDER BY r.role_name, u.id");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_role = '';
foreach ($users as $user) {
    if ($current_role != $user['role_name']) {
        $current_role = $user['role_name'];
        echo "<h2 class='role-header'>" . ucfirst($current_role) . " Accounts</h2>";
    }
    
    echo "<div class='credential-box'>";
    echo "<strong>Name:</strong> " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "<br>";
    if ($user['student_id']) {
        echo "<strong>Student ID:</strong> " . htmlspecialchars($user['student_id']) . "<br>";
    }
    echo "<strong>Username:</strong> " . htmlspecialchars($user['username']) . "<br>";
    echo "<strong>Email:</strong> " . htmlspecialchars($user['email']) . "<br>";
    
    // Show default passwords based on role
    if ($user['role_name'] == 'principal') {
        echo "<strong>Password:</strong> <span class='password'>password</span><br>";
    } elseif ($user['role_name'] == 'teacher') {
        echo "<strong>Password:</strong> <span class='password'>teacher123</span><br>";
    } elseif ($user['role_name'] == 'student') {
        echo "<strong>Password:</strong> <span class='password'>student123</span><br>";
    }
    echo "</div>";
}

echo "<hr>";
echo "<h3>Quick Login Links:</h3>";
echo "<p><a href='login.php' target='_blank'>Go to Login Page</a></p>";
echo "<p><strong>Note:</strong> This file should be deleted in production for security reasons.</p>";
?>
