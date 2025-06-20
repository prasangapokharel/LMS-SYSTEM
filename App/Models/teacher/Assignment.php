<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Handle form submission for new assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $subject_id = $_POST['subject_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $max_marks = $_POST['max_marks'];
    $assignment_type = $_POST['assignment_type'];
    $instructions = $_POST['instructions'];
    
    // Validate due date is in the future
    if (strtotime($due_date) <= time()) {
        $msg = "<div class='alert alert-danger'>Due date must be in the future.</div>";
    } else {
        try {
            // Handle file upload
            $attachment_url = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $upload_dir = '../uploads/assignments/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                        $attachment_url = 'uploads/assignments/' . $filename;
                    }
                }
            }
            
            // Get class_id from subject
            $stmt = $pdo->prepare("SELECT cst.class_id FROM class_subject_teachers cst WHERE cst.subject_id = ? AND cst.teacher_id = ?");
            $stmt->execute([$subject_id, $user['id']]);
            $class_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$class_info) {
                throw new Exception("Invalid subject selection.");
            }
            
            $stmt = $pdo->prepare("INSERT INTO assignments 
                                  (title, description, class_id, subject_id, teacher_id, due_date, max_marks, assignment_type, instructions, attachment_url, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $description, $class_info['class_id'], $subject_id, $user['id'], $due_date, $max_marks, $assignment_type, $instructions, $attachment_url]);
            
            $assignment_id = $pdo->lastInsertId();
            logActivity($pdo, 'assignment_created', 'assignments', $assignment_id);
            
            $msg = "<div class='alert alert-success alert-modern'>
                    <div class='alert-icon'>✅</div>
                    <div><strong>Assignment created successfully!</strong></div>
                   </div>";
        } catch (Exception $e) {
            $msg = "<div class='alert alert-danger alert-modern'>
                    <div class='alert-icon'>❌</div>
                    <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
                   </div>";
        }
    }
}

// Handle assignment deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_assignment'])) {
    $assignment_id = $_POST['assignment_id'];
    
    try {
        // Check if assignment belongs to this teacher
        $stmt = $pdo->prepare("SELECT id FROM assignments WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$assignment_id, $user['id']]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM assignments WHERE id = ? AND teacher_id = ?");
            $stmt->execute([$assignment_id, $user['id']]);
            
            logActivity($pdo, 'assignment_deleted', 'assignments', $assignment_id);
            $msg = "<div class='alert alert-success alert-modern'>
                    <div class='alert-icon'>✅</div>
                    <div><strong>Assignment deleted successfully!</strong></div>
                   </div>";
        } else {
            $msg = "<div class='alert alert-danger alert-modern'>
                    <div class='alert-icon'>❌</div>
                    <div><strong>Error:</strong> Assignment not found or access denied.</div>
                   </div>";
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>❌</div>
                <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
               </div>";
    }
}

// Get teacher's subjects with class information
$stmt = $pdo->prepare("SELECT s.id, s.subject_name, c.class_name, cst.class_id
                      FROM class_subject_teachers cst
                      JOIN subjects s ON cst.subject_id = s.id
                      JOIN classes c ON cst.class_id = c.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get teacher's assignments with submission counts
$stmt = $pdo->prepare("SELECT a.*, s.subject_name, c.class_name,
                      (SELECT COUNT(*) FROM assignment_submissions asub WHERE asub.assignment_id = a.id) as submissions,
                      (SELECT COUNT(DISTINCT sc.student_id) FROM student_classes sc WHERE sc.class_id = a.class_id AND sc.status = 'enrolled') as total_students
                      FROM assignments a
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN classes c ON a.class_id = c.id
                      WHERE a.teacher_id = ?
                      ORDER BY a.created_at DESC");
$stmt->execute([$user['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>