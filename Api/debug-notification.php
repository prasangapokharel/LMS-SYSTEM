<?php
session_start();
require_once '../include/connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Notification System Debug</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ Not logged in</p>";
    echo "<pre>Session data: " . print_r($_SESSION, true) . "</pre>";
    exit;
}

echo "<p style='color: green;'>✅ User logged in: " . $_SESSION['user_id'] . "</p>";

// Check database connection
try {
    $stmt = $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✅ Database connection working</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check if notifications table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Notifications table exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Notifications table does NOT exist</p>";
        echo "<p>Creating notifications table...</p>";
        
        // Create the table
        $createTable = "
        CREATE TABLE notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(50) DEFAULT 'info',
            data JSON NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            read_at TIMESTAMP NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at),
            INDEX idx_is_read (is_read)
        )";
        
        $pdo->exec($createTable);
        echo "<p style='color: green;'>✅ Notifications table created successfully</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Table check/creation failed: " . $e->getMessage() . "</p>";
}

// Check if user exists
try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color: green;'>✅ User found: " . $user['first_name'] . " " . $user['last_name'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ User not found with ID: " . $_SESSION['user_id'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ User check failed: " . $e->getMessage() . "</p>";
}

// Try to insert a test notification directly
try {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, created_at) VALUES (?, ?, ?, ?, NOW())");
    $result = $stmt->execute([
        $_SESSION['user_id'],
        "🔔 Direct Test Notification",
        "This is a direct database test notification!",
        'test'
    ]);
    
    if ($result) {
        $notification_id = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ Direct notification inserted successfully! ID: $notification_id</p>";
    } else {
        echo "<p style='color: red;'>❌ Direct notification insertion failed</p>";
        echo "<pre>Error info: " . print_r($stmt->errorInfo(), true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Direct insertion failed: " . $e->getMessage() . "</p>";
}

// Test the NotificationSystem class
try {
    require_once '../App/Models/Notification.php';
    $notification = new NotificationSystem($pdo);
    
    $result = $notification->createNotification(
        $_SESSION['user_id'],
        "🔔 Class Test Notification",
        "This is a test notification using the NotificationSystem class!",
        'test',
        ['url' => '/students/', 'timestamp' => time()]
    );
    
    if ($result) {
        echo "<p style='color: green;'>✅ NotificationSystem class working! Notification ID: $result</p>";
    } else {
        echo "<p style='color: red;'>❌ NotificationSystem class failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ NotificationSystem class error: " . $e->getMessage() . "</p>";
}

// Show recent notifications
try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Recent Notifications:</h3>";
    if (count($notifications) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Message</th><th>Type</th><th>Created</th><th>Read</th></tr>";
        foreach ($notifications as $notif) {
            echo "<tr>";
            echo "<td>" . $notif['id'] . "</td>";
            echo "<td>" . htmlspecialchars($notif['title']) . "</td>";
            echo "<td>" . htmlspecialchars($notif['message']) . "</td>";
            echo "<td>" . $notif['type'] . "</td>";
            echo "<td>" . $notif['created_at'] . "</td>";
            echo "<td>" . ($notif['is_read'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No notifications found.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Failed to fetch notifications: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='test-notification.php'>Try Test Notification Again</a></p>";
?>
