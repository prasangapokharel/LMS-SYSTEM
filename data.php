<?php
require_once 'include/connect.php'; // Ensure $pdo is set up

try {
    $pdo->beginTransaction();

    // Check for existing usernames/emails
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute(['prasanga741', 'prasanga741@gmail.com']);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Username 'prasanga741' or email 'prasanga741@gmail.com' already exists.");
    }
    $stmt->execute(['bhawana741', 'bhawana741@school.com']);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Username 'bhawana741' or email 'bhawana741@school.com' already exists.");
    }

    // Insert first principal into users
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password_hash, first_name, last_name, phone, address, profile_image, role_id, is_active, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([
        'prasanga741',
        'prasanga741@gmail.com',
        password_hash('prasanga741', PASSWORD_DEFAULT),
        'Mark', 'Taylor',
        '9841234582',
        '789 Oak St, City',
        NULL,
        1, // role_id for principal
        1
    ]);
    $user_id1 = $pdo->lastInsertId();

    // Insert second principal into users
    $stmt->execute([
        'bhawana741',
        'bhawana741@school.com',
        password_hash('bhawana741', PASSWORD_DEFAULT),
        'Lisa', 'Brown',
        '9841234583',
        '101 Pine St, City',
        NULL,
        1, // role_id for principal
        1
    ]);
    $user_id2 = $pdo->lastInsertId();

    $pdo->commit();
    echo "Two principals inserted successfully.\n";
    echo "Mark Taylor (prasanga741, prasanga741@gmail.com): prasanga741\n";
    echo "Lisa Brown (bhawana741, bhawana741@school.com): bhawana741\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error inserting principals: " . $e->getMessage();
}
?>