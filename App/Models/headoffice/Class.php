<?php
include '../include/connect.php';
include '../include/session.php';
requireRole('principal');

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// Initialize cache
$cache = new FilesystemAdapter('class_management', 3600, __DIR__ . '/../cache');

$user = getCurrentUser($pdo);
$msg = "";

// Handle form submission for new class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_name'])) {
    // Sanitize inputs
    $class_name = trim(filter_var($_POST['class_name'], FILTER_SANITIZE_STRING));
    $class_level = (int)$_POST['class_level'];
    $section = trim(filter_var($_POST['section'], FILTER_SANITIZE_STRING));
    $capacity = (int)$_POST['capacity'];
    $academic_year_id = (int)$_POST['academic_year_id'];
    $description = trim(filter_var($_POST['description'], FILTER_SANITIZE_STRING));
    
    try {
        // Validate inputs
        if (empty($class_name) || empty($section)) {
            throw new Exception("Class name and section are required.");
        }
        
        if ($capacity < 1) {
            throw new Exception("Capacity must be at least 1.");
        }

        // Check if class already exists for this academic year
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE class_name = ? AND section = ? AND academic_year_id = ?");
        $stmt->execute([$class_name, $section, $academic_year_id]);
        
        if ($stmt->fetch()) {
            throw new Exception("Class with this name and section already exists for the selected academic year.");
        }
        
        // Insert new class
        $stmt = $pdo->prepare("INSERT INTO classes 
                              (class_name, class_level, section, academic_year_id, capacity, description, is_active, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$class_name, $class_level, $section, $academic_year_id, $capacity, $description]);
        
        $class_id = $pdo->lastInsertId();
        
        // Clear relevant caches
        $cache->deleteItems(['classes_list', 'academic_years_list', 'teachers_list']);
        
        logActivity($pdo, 'class_created', 'classes', $class_id);
        
        $msg = successMessage("Class created successfully! Class ID: $class_id");
        
        // Clear form data
        $_POST = array();
        
    } catch (Exception $e) {
        $msg = errorMessage("Error: " . $e->getMessage());
    }
}

// Handle class deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_class'])) {
    $class_id = (int)$_POST['class_id'];
    
    try {
        // Check if class has students
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_classes WHERE class_id = ? AND status = 'enrolled'");
        $stmt->execute([$class_id]);
        $student_count = $stmt->fetchColumn();
        
        if ($student_count > 0) {
            throw new Exception("Cannot delete class with enrolled students. Please transfer students first.");
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        // First deactivate all related records
        $stmt = $pdo->prepare("UPDATE class_subject_teachers SET is_active = 0 WHERE class_id = ?");
        $stmt->execute([$class_id]);
        
        // Then delete the class
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->execute([$class_id]);
        
        $pdo->commit();
        
        // Clear relevant caches
        $cache->deleteItems(['classes_list', 'academic_years_list', 'teachers_list']);
        
        logActivity($pdo, 'class_deleted', 'classes', $class_id);
        
        $msg = successMessage("Class deleted successfully!");
               
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = errorMessage("Error: " . $e->getMessage());
    }
}

// Get academic years with caching
$academicYearsCache = $cache->getItem('academic_years_list');
if (!$academicYearsCache->isHit()) {
    $stmt = $pdo->prepare("SELECT * FROM academic_years ORDER BY start_date DESC");
    $stmt->execute();
    $academic_years = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $academicYearsCache->set($academic_years)->expiresAfter(86400); // 24 hours
    $cache->save($academicYearsCache);
} else {
    $academic_years = $academicYearsCache->get();
}

// Get available teachers with caching
$teachersCache = $cache->getItem('teachers_list');
if (!$teachersCache->isHit()) {
    $stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name, u.email 
                          FROM users u 
                          WHERE u.role_id = 2 AND u.is_active = 1 
                          ORDER BY u.first_name, u.last_name");
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $teachersCache->set($teachers)->expiresAfter(3600); // 1 hour
    $cache->save($teachersCache);
} else {
    $teachers = $teachersCache->get();
}

// Get existing classes with details and caching
$classesCache = $cache->getItem('classes_list');
if (!$classesCache->isHit()) {
    $stmt = $pdo->prepare("SELECT c.*, ay.year_name,
                          (SELECT COUNT(*) FROM student_classes sc WHERE sc.class_id = c.id AND sc.status = 'enrolled') as student_count,
                          (SELECT COUNT(*) FROM class_subject_teachers cst WHERE cst.class_id = c.id AND cst.is_active = 1) as subject_count
                          FROM classes c
                          JOIN academic_years ay ON c.academic_year_id = ay.id
                          ORDER BY c.class_level, c.class_name, c.section");
    $stmt->execute();
    $existing_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $classesCache->set($existing_classes)->expiresAfter(3600); // 1 hour
    $cache->save($classesCache);
} else {
    $existing_classes = $classesCache->get();
}

// Get class teachers (from class_subject_teachers table) with caching
$classTeachersCache = $cache->getItem('class_teachers_mapping');
if (!$classTeachersCache->isHit() && !empty($existing_classes)) {
    $class_ids = array_column($existing_classes, 'id');
    $placeholders = str_repeat('?,', count($class_ids) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT cst.class_id, u.id as teacher_id, u.first_name, u.last_name, u.email, s.id as subject_id, s.subject_name
                          FROM class_subject_teachers cst
                          JOIN users u ON cst.teacher_id = u.id
                          JOIN subjects s ON cst.subject_id = s.id
                          WHERE cst.class_id IN ($placeholders) AND cst.is_active = 1
                          ORDER BY cst.class_id, s.subject_name");
    $stmt->execute($class_ids);
    $teachers_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $class_teachers = [];
    foreach ($teachers_data as $teacher) {
        $class_teachers[$teacher['class_id']][] = $teacher;
    }
    
    $classTeachersCache->set($class_teachers)->expiresAfter(3600); // 1 hour
    $cache->save($classTeachersCache);
} else {
    $class_teachers = $classTeachersCache->get() ?: [];
}

// Helper functions
function successMessage($message) {
    return <<<HTML
    <div class='alert alert-success alert-modern'>
        <div class='alert-icon'>✅</div>
        <div><strong>$message</strong></div>
    </div>
HTML;
}

function errorMessage($message) {
    return <<<HTML
    <div class='alert alert-danger alert-modern'>
        <div class='alert-icon'>❌</div>
        <div><strong>$message</strong></div>
    </div>
HTML;
}
?>