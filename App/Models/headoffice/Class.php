<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";

// Handle form submission for new class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_name'])) {
    $class_name = trim($_POST['class_name']);
    $class_level = $_POST['class_level'];
    $section = trim($_POST['section']);
    $capacity = $_POST['capacity'];
    $academic_year_id = $_POST['academic_year_id'];
    $description = trim($_POST['description']);
    
    try {
        // Check if class already exists for this academic year
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE class_name = ? AND section = ? AND academic_year_id = ?");
        $stmt->execute([$class_name, $section, $academic_year_id]);
        
        if ($stmt->fetch()) {
            throw new Exception("Class with this name and section already exists for the selected academic year.");
        }
        
        // Insert new class
        $stmt = $pdo->prepare("INSERT INTO classes 
                              (class_name, class_level, section, academic_year_id, capacity, is_active, created_at) 
                              VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$class_name, $class_level, $section, $academic_year_id, $capacity]);
        
        $class_id = $pdo->lastInsertId();
        
        logActivity($pdo, 'class_created', 'classes', $class_id);
        
        $msg = "<div class='alert alert-success alert-modern'>
                <div class='alert-icon'>✅</div>
                <div><strong>Class created successfully!</strong> Class ID: $class_id</div>
               </div>";
               
        // Clear form data
        $_POST = array();
        
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>❌</div>
                <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
               </div>";
    }
}

// Handle class deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_class'])) {
    $class_id = $_POST['class_id'];
    
    try {
        // Check if class has students
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_classes WHERE class_id = ? AND status = 'enrolled'");
        $stmt->execute([$class_id]);
        $student_count = $stmt->fetchColumn();
        
        if ($student_count > 0) {
            throw new Exception("Cannot delete class with enrolled students. Please transfer students first.");
        }
        
        // Delete class
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->execute([$class_id]);
        
        logActivity($pdo, 'class_deleted', 'classes', $class_id);
        
        $msg = "<div class='alert alert-success alert-modern'>
                <div class='alert-icon'>✅</div>
                <div><strong>Class deleted successfully!</strong></div>
               </div>";
               
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>❌</div>
                <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
               </div>";
    }
}

// Get academic years
$stmt = $pdo->prepare("SELECT * FROM academic_years ORDER BY start_date DESC");
$stmt->execute();
$academic_years = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available teachers
$stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name, u.email 
                      FROM users u 
                      WHERE u.role_id = 2 AND u.is_active = 1 
                      ORDER BY u.first_name, u.last_name");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get existing classes with details
$stmt = $pdo->prepare("SELECT c.*, ay.year_name,
                      (SELECT COUNT(*) FROM student_classes sc WHERE sc.class_id = c.id AND sc.status = 'enrolled') as student_count,
                      (SELECT COUNT(*) FROM class_subject_teachers cst WHERE cst.class_id = c.id AND cst.is_active = 1) as subject_count
                      FROM classes c
                      JOIN academic_years ay ON c.academic_year_id = ay.id
                      ORDER BY c.class_level, c.class_name, c.section");
$stmt->execute();
$existing_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get class teachers (from class_subject_teachers table)
$class_teachers = [];
if (!empty($existing_classes)) {
    $class_ids = array_column($existing_classes, 'id');
    $placeholders = str_repeat('?,', count($class_ids) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT cst.class_id, u.first_name, u.last_name, u.email, s.subject_name
                          FROM class_subject_teachers cst
                          JOIN users u ON cst.teacher_id = u.id
                          JOIN subjects s ON cst.subject_id = s.id
                          WHERE cst.class_id IN ($placeholders) AND cst.is_active = 1
                          ORDER BY cst.class_id, s.subject_name");
    $stmt->execute($class_ids);
    $teachers_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($teachers_data as $teacher) {
        $class_teachers[$teacher['class_id']][] = $teacher;
    }
}

include '../include/sidebar.php';
?>