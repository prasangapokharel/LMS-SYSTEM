<?php
requireRole('student');

$user = getCurrentUser($pdo);

// Initialize variables to prevent undefined errors
$resource_type = $_GET['type'] ?? 'all';
$subject_filter = $_GET['subject'] ?? 'all';
$search_query = $_GET['search'] ?? '';

class StudentResource {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getStudentResources($student_id, $filters = []) {
        // Get student's class_id
        $stmt = $this->pdo->prepare("SELECT sc.class_id FROM student_classes sc 
                                    JOIN students s ON sc.student_id = s.id 
                                    WHERE s.user_id = ? AND sc.status = 'enrolled'");
        $stmt->execute([$student_id]);
        $student_class = $stmt->fetch(PDO::FETCH_ASSOC);
        $class_id = $student_class ? $student_class['class_id'] : null;
        
        $sql = "SELECT lr.*, u.first_name, u.last_name, s.subject_name, c.class_name,
               CASE 
                  WHEN lr.file_size IS NOT NULL THEN CONCAT(ROUND(lr.file_size/1024/1024, 2), ' MB')
                  ELSE 'N/A'
               END as formatted_size
                FROM learning_resources lr
                LEFT JOIN users u ON lr.uploaded_by = u.id
                LEFT JOIN subjects s ON lr.subject_id = s.id
                LEFT JOIN classes c ON lr.class_id = c.id
                WHERE (lr.is_public = 1 OR lr.class_id = ?)";
        
        $params = [$class_id];
        
        if (!empty($filters['resource_type'])) {
            $sql .= " AND lr.resource_type = ?";
            $params[] = $filters['resource_type'];
        }
        
        if (!empty($filters['subject_id'])) {
            $sql .= " AND lr.subject_id = ?";
            $params[] = $filters['subject_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (lr.title LIKE ? OR lr.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY lr.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getResourceTypes() {
        $stmt = $this->pdo->prepare("SELECT DISTINCT resource_type FROM learning_resources ORDER BY resource_type");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getStudentSubjects($student_id) {
        $stmt = $this->pdo->prepare("SELECT DISTINCT s.id, s.subject_name 
                                    FROM subjects s
                                    JOIN class_subject_teachers cst ON s.id = cst.subject_id
                                    JOIN student_classes sc ON cst.class_id = sc.class_id
                                    JOIN students st ON sc.student_id = st.id
                                    WHERE st.user_id = ? AND sc.status = 'enrolled'
                                    ORDER BY s.subject_name");
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function logResourceAccess($resource_id, $user_id, $access_type = 'view') {
        $stmt = $this->pdo->prepare("INSERT INTO resource_access_log (resource_id, user_id, access_type, ip_address) 
                                    VALUES (?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt->execute([$resource_id, $user_id, $access_type, $ip]);
        
        // Update download count if it's a download
        if ($access_type === 'download') {
            $stmt = $this->pdo->prepare("UPDATE learning_resources SET download_count = download_count + 1 WHERE id = ?");
            $stmt->execute([$resource_id]);
        }
    }
}

// Get student data
$stmt = $pdo->prepare("SELECT s.*, sc.class_id FROM students s 
                      LEFT JOIN student_classes sc ON s.id = sc.student_id 
                      WHERE s.user_id = ? AND (sc.status = 'enrolled' OR sc.status IS NULL)
                      LIMIT 1");
$stmt->execute([$user['id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Default class_id if not found
$class_id = $student['class_id'] ?? 1;

// Get student's subjects
$stmt = $pdo->prepare("SELECT DISTINCT s.id, s.subject_name 
                    FROM subjects s
                    JOIN class_subject_teachers cst ON s.id = cst.subject_id
                    WHERE cst.class_id = ? AND cst.is_active = 1
                    ORDER BY s.subject_name");
$stmt->execute([$class_id]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build the resources query
$query = "SELECT lr.*, s.subject_name, u.first_name, u.last_name,
               CASE 
                  WHEN lr.file_size IS NOT NULL THEN CONCAT(ROUND(lr.file_size/1024/1024, 2), ' MB')
                  ELSE 'N/A'
               END as formatted_size
        FROM learning_resources lr
        LEFT JOIN subjects s ON lr.subject_id = s.id
        LEFT JOIN users u ON lr.uploaded_by = u.id
        WHERE (lr.is_public = 1 OR lr.class_id = ?)";

$params = [$class_id];

// Add type filter
if ($resource_type !== 'all') {
    $query .= " AND lr.resource_type = ?";
    $params[] = $resource_type;
}

// Add subject filter
if ($subject_filter !== 'all') {
    $query .= " AND lr.subject_id = ?";
    $params[] = $subject_filter;
}

// Add search filter
if (!empty($search_query)) {
    $query .= " AND (lr.title LIKE ? OR lr.description LIKE ?)";
    $search_term = '%' . $search_query . '%';
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY lr.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Handle resource download/access
if (isset($_GET['download']) && isset($_GET['resource_id'])) {
    $resource_id = (int)$_GET['resource_id'];
    
    // Log the access
    $stmt = $pdo->prepare("INSERT INTO resource_access_log (resource_id, user_id, access_type, ip_address) 
                          VALUES (?, ?, 'download', ?)");
    $stmt->execute([$resource_id, $user['id'], $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    
    // Update download count
    $stmt = $pdo->prepare("UPDATE learning_resources SET download_count = download_count + 1 WHERE id = ?");
    $stmt->execute([$resource_id]);
    
    // Get resource details for redirect
    $stmt = $pdo->prepare("SELECT file_url, external_url, title FROM learning_resources WHERE id = ?");
    $stmt->execute([$resource_id]);
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resource) {
        if ($resource['file_url']) {
            header("Location: ../" . $resource['file_url']);
        } elseif ($resource['external_url']) {
            header("Location: " . $resource['external_url']);
        }
        exit;
    }
}
?>
