<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $emergency_contact = $_POST['emergency_contact'] ?? '';
    $guardian_phone = $_POST['guardian_phone'] ?? '';
    
    try {
        // Update user table
        $stmt = $pdo->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$phone, $address, $user['id']]);
        
        // Update student table
        $stmt = $pdo->prepare("UPDATE students SET emergency_contact = ?, guardian_phone = ? WHERE id = ?");
        $stmt->execute([$emergency_contact, $guardian_phone, $student['id']]);
        
        $success_message = "Profile updated successfully!";
        
        // Refresh data
        $user = getCurrentUser($pdo);
        $student = getStudentData($pdo);
        
    } catch (Exception $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}

// Get academic performance
$stmt = $pdo->prepare("SELECT AVG(sub.grade) as avg_grade, COUNT(*) as total_assignments
                      FROM assignment_submissions sub
                      WHERE sub.student_id = ? AND sub.grade IS NOT NULL");
$stmt->execute([$student['id']]);
$performance = $stmt->fetch(PDO::FETCH_ASSOC);

// Get recent grades
$stmt = $pdo->prepare("SELECT a.title, a.max_marks, sub.grade, sub.graded_at, s.subject_name
                      FROM assignment_submissions sub
                      JOIN assignments a ON sub.assignment_id = a.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE sub.student_id = ? AND sub.grade IS NOT NULL
                      ORDER BY sub.graded_at DESC LIMIT 5");
$stmt->execute([$student['id']]);
$recent_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
