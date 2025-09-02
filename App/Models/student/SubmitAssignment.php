<?php
// Prevent any output before JSON
ob_start();

// Set error reporting to prevent HTML error output
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Include the loader file with correct path
    $loader_path = __DIR__ . '/../../include/loader.php';
    if (!file_exists($loader_path)) {
        throw new Exception('Loader file not found');
    }
    include $loader_path;
    
    // Clean any output buffer before sending JSON
    ob_clean();
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Check if user is logged in and is a student
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }
    
    $student = getStudentData($pdo);
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student data not found']);
        exit;
    }
    
    $response = ['success' => false, 'message' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $assignment_id = $_POST['assignment_id'] ?? 0;
        $submission_text = trim($_POST['submission_text'] ?? '');
        
        // Validate input
        if (empty($assignment_id) || empty($submission_text)) {
            $response['message'] = 'Assignment ID and submission text are required.';
            echo json_encode($response);
            exit;
        }
        
        // Get assignment details and verify it exists
        $stmt = $pdo->prepare("SELECT a.*, s.subject_name, u.first_name, u.last_name
                              FROM assignments a 
                              JOIN subjects s ON a.subject_id = s.id
                              JOIN users u ON a.teacher_id = u.id
                              WHERE a.id = ? AND a.is_active = 1");
        $stmt->execute([$assignment_id]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$assignment) {
            $response['message'] = 'Assignment not found.';
            echo json_encode($response);
            exit;
        }
        
        // Check if already submitted
        $stmt = $pdo->prepare("SELECT id FROM assignment_submissions 
                              WHERE assignment_id = ? AND student_id = ?");
        $stmt->execute([$assignment_id, $student['id']]);
        $existing_submission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_submission) {
            $response['message'] = 'You have already submitted this assignment.';
            echo json_encode($response);
            exit;
        }
        
        $file_path = null;
        
        // Handle file upload if present
        if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/assignments/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['submission_file']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
            
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                $response['message'] = 'Invalid file type. Only PDF, DOC, DOCX, TXT, JPG, PNG files are allowed.';
                echo json_encode($response);
                exit;
            }
            
            // Check file size (10MB limit)
            if ($_FILES['submission_file']['size'] > 10 * 1024 * 1024) {
                $response['message'] = 'File size must be less than 10MB.';
                echo json_encode($response);
                exit;
            }
            
            $file_name = $student['id'] . '_' . $assignment_id . '_' . time() . '.' . $file_extension;
            $file_path_full = $upload_dir . $file_name;
            
            if (!move_uploaded_file($_FILES['submission_file']['tmp_name'], $file_path_full)) {
                $response['message'] = 'Error uploading file. Please try again.';
                echo json_encode($response);
                exit;
            }
            
            $file_path = 'uploads/assignments/' . $file_name;
        }
        
        // Insert submission
        $stmt = $pdo->prepare("INSERT INTO assignment_submissions 
                              (assignment_id, student_id, submission_text, attachment_url, submission_date, status) 
                              VALUES (?, ?, ?, ?, NOW(), 'submitted')");
        $stmt->execute([$assignment_id, $student['id'], $submission_text, $file_path]);
        
        // Log activity if function exists
        if (function_exists('logActivity')) {
            logActivity($pdo, 'assignment_submitted', 'assignment_submissions', $pdo->lastInsertId());
        }
        
        $response['success'] = true;
        $response['message'] = 'Assignment submitted successfully!';
        $response['data'] = [
            'assignment_id' => $assignment_id,
            'submission_date' => date('M j, Y g:i A'),
            'status' => 'submitted'
        ];
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_assignment') {
        // Get assignment details for modal
        $assignment_id = $_GET['id'] ?? 0;
        
        if (empty($assignment_id)) {
            $response['message'] = 'Assignment ID is required.';
            echo json_encode($response);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT a.*, s.subject_name, u.first_name, u.last_name
                              FROM assignments a 
                              JOIN subjects s ON a.subject_id = s.id
                              JOIN users u ON a.teacher_id = u.id
                              WHERE a.id = ? AND a.is_active = 1");
        $stmt->execute([$assignment_id]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($assignment) {
            // Check if already submitted
            $stmt = $pdo->prepare("SELECT id FROM assignment_submissions 
                                  WHERE assignment_id = ? AND student_id = ?");
            $stmt->execute([$assignment_id, $student['id']]);
            $existing_submission = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $assignment['already_submitted'] = !empty($existing_submission);
            $assignment['days_left'] = ceil((strtotime($assignment['due_date']) - time()) / 86400);
            
            $response['success'] = true;
            $response['data'] = $assignment;
        } else {
            $response['message'] = 'Assignment not found.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ];
}

// Clean output buffer and send JSON
ob_clean();
echo json_encode($response);
exit;
?>
