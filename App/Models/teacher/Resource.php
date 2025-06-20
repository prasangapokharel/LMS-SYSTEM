<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher's classes and subjects
$stmt = $pdo->prepare("SELECT DISTINCT c.id as class_id, c.class_name, c.section, s.id as subject_id, s.subject_name
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      JOIN subjects s ON cst.subject_id = s.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$teacher_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get filter parameters
$class_filter = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$subject_filter = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
$type_filter = $_GET['type'] ?? '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload_resource') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $resource_type = $_POST['resource_type'];
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $external_url = $_POST['external_url'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    
    $file_url = '';
    $file_size = 0;
    $file_format = '';
    
    // Handle file upload
    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] == 0) {
        $upload_dir = '../uploads/resources/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['resource_file']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['resource_file']['tmp_name'], $file_path)) {
            $file_url = 'uploads/resources/' . $file_name;
            $file_size = $_FILES['resource_file']['size'];
            $file_format = $file_extension;
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO learning_resources 
                              (title, description, resource_type, file_url, external_url, file_size, file_format,
                               class_id, subject_id, uploaded_by, is_public, tags)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $title, $description, $resource_type, $file_url, $external_url, $file_size, $file_format,
            $class_id, $subject_id, $user['id'], $is_public, $tags
        ]);
        
        $msg = "<div class='alert alert-success'>Resource uploaded successfully!</div>";
        
        // Log activity
        if (function_exists('logActivity')) {
            logActivity($pdo, 'resource_uploaded', 'learning_resources', $pdo->lastInsertId());
        }
        
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Error uploading resource: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Handle resource deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_resource') {
    $resource_id = $_POST['resource_id'];
    
    try {
        // Get resource info first
        $stmt = $pdo->prepare("SELECT file_url FROM learning_resources WHERE id = ? AND uploaded_by = ?");
        $stmt->execute([$resource_id, $user['id']]);
        $resource = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resource) {
            // Delete file if exists
            if ($resource['file_url'] && file_exists('../' . $resource['file_url'])) {
                unlink('../' . $resource['file_url']);
            }
            
            // Delete database record
            $stmt = $pdo->prepare("DELETE FROM learning_resources WHERE id = ? AND uploaded_by = ?");
            $stmt->execute([$resource_id, $user['id']]);
            
            $msg = "<div class='alert alert-success'>Resource deleted successfully!</div>";
        }
        
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Error deleting resource: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Get resources
$where_conditions = ["lr.uploaded_by = ?"];
$params = [$user['id']];

if ($class_filter > 0) {
    $where_conditions[] = "lr.class_id = ?";
    $params[] = $class_filter;
}

if ($subject_filter > 0) {
    $where_conditions[] = "lr.subject_id = ?";
    $params[] = $subject_filter;
}

if ($type_filter) {
    $where_conditions[] = "lr.resource_type = ?";
    $params[] = $type_filter;
}

$where_clause = implode(' AND ', $where_conditions);

$stmt = $pdo->prepare("SELECT lr.*, c.class_name, c.section, s.subject_name
                      FROM learning_resources lr
                      LEFT JOIN classes c ON lr.class_id = c.id
                      LEFT JOIN subjects s ON lr.subject_id = s.id
                      WHERE $where_clause
                      ORDER BY lr.created_at DESC");
$stmt->execute($params);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get resource statistics
$stmt = $pdo->prepare("SELECT 
                      COUNT(*) as total_resources,
                      COUNT(CASE WHEN resource_type = 'document' THEN 1 END) as documents,
                      COUNT(CASE WHEN resource_type = 'video' THEN 1 END) as videos,
                      COUNT(CASE WHEN resource_type = 'link' THEN 1 END) as links,
                      SUM(download_count) as total_downloads
                      FROM learning_resources 
                      WHERE uploaded_by = ?");
$stmt->execute([$user['id']]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
