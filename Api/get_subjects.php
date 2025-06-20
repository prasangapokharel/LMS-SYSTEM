<?php
header('Content-Type: application/json');
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

if (!$class_id) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT s.id, s.subject_name, s.subject_code
                          FROM subjects s
                          JOIN class_subject_teachers cst ON s.id = cst.subject_id
                          WHERE cst.class_id = ? AND cst.teacher_id = ? AND cst.is_active = 1
                          ORDER BY s.subject_name");
    $stmt->execute([$class_id, $user['id']]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($subjects);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
