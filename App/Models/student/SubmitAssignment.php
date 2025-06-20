<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$assignment_id = $_GET['id'] ?? 0;

// Get assignment details
$stmt = $pdo->prepare("SELECT a.*, s.subject_name, u.first_name, u.last_name
                      FROM assignments a 
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN users u ON a.teacher_id = u.id
                      WHERE a.id = ?");
$stmt->execute([$assignment_id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    header('Location: assignments.php');
    exit;
}

// Check if already submitted
$stmt = $pdo->prepare("SELECT * FROM assignment_submissions 
                      WHERE assignment_id = ? AND student_id = ?");
$stmt->execute([$assignment_id, $student['id']]);
$existing_submission = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_submission) {
    header('Location: view_assignment.php?id=' . $assignment_id);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_text = $_POST['submission_text'] ?? '';
    $file_path = null;
    
    // Handle file upload
    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/assignments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['submission_file']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $file_name = $student['id'] . '_' . $assignment_id . '_' . time() . '.' . $file_extension;
            $file_path_full = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $file_path_full)) {
                $file_path = 'uploads/assignments/' . $file_name;
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Invalid file type. Only PDF, DOC, DOCX, TXT, JPG, PNG files are allowed.";
        }
    }
    
    if (!isset($error_message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO assignment_submissions 
                                  (assignment_id, student_id, submission_text, attachment_url, submission_date, status) 
                                  VALUES (?, ?, ?, ?, NOW(), 'submitted')");
            $stmt->execute([$assignment_id, $student['id'], $submission_text, $file_path]);
            
            logActivity($pdo, 'assignment_submitted', 'assignment_submissions', $pdo->lastInsertId());
            
            $success_message = "Assignment submitted successfully!";
            
            // Redirect after 2 seconds
            header("refresh:2;url=view_assignment.php?id=" . $assignment_id);
        } catch (Exception $e) {
            $error_message = "Error submitting assignment. Please try again.";
        }
    }
}
?>
