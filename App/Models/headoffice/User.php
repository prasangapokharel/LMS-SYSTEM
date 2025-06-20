<?php
include '../include/connect.php';
include '../include/session.php';
requireRole('principal');

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use GuzzleHttp\Client;

// Initialize cache
$cache = new FilesystemAdapter('user_management', 3600, __DIR__ . '/../cache');

$user = getCurrentUser($pdo);
$msg = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'create_teacher') {
            // Validate inputs
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
            $address = $_POST['address'] ?? '';
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $msg = errorMessage("Invalid email address");
            } elseif (strlen($phone) < 10) {
                $msg = errorMessage("Phone number must be at least 10 digits");
            } else {
                // Generate teacher username and password
                $teacher_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn();
                $username = 'teacher' . str_pad($teacher_count + 1, 3, '0', STR_PAD_LEFT);
                $password = generateStrongPassword(10);
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO users 
                                          (username, email, password_hash, first_name, last_name, phone, address, role_id) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, 2)");
                    $stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $phone, $address]);
                    
                    // Clear relevant caches
                    $cache->deleteItem('user_stats');
                    $cache->deleteItem('user_list_all');
                    
                    logActivity($pdo, 'teacher_created', 'users', $pdo->lastInsertId());
                    $msg = successMessageWithCredentials(
                        "Teacher created successfully!", 
                        $username, 
                        $password,
                        "Please save these credentials and share with the teacher."
                    );
                } catch (PDOException $e) {
                    $msg = errorMessage("Error creating teacher: " . $e->getMessage());
                }
            }
        }
        
        elseif ($action == 'create_student') {
            // Validate inputs
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
            $address = $_POST['address'] ?? '';
            $class_id = (int)$_POST['class_id'];
            $date_of_birth = $_POST['date_of_birth'];
            $blood_group = $_POST['blood_group'] ?? '';
            $guardian_name = trim($_POST['guardian_name']);
            $guardian_phone = preg_replace('/[^0-9]/', '', $_POST['guardian_phone']);
            $guardian_email = $_POST['guardian_email'] ?? '';
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $msg = errorMessage("Invalid student email address");
            } elseif (strlen($phone) < 10) {
                $msg = errorMessage("Student phone number must be at least 10 digits");
            } elseif (strlen($guardian_phone) < 10) {
                $msg = errorMessage("Guardian phone number must be at least 10 digits");
            } else {
                // Generate student ID and credentials
                $current_year = date('Y');
                $student_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
                $student_id = 'STU' . $current_year . str_pad($student_count + 1, 3, '0', STR_PAD_LEFT);
                $username = 'student' . str_pad($student_count + 1, 3, '0', STR_PAD_LEFT);
                $password = generateStrongPassword(10);
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                try {
                    $pdo->beginTransaction();
                    
                    // Create user account
                    $stmt = $pdo->prepare("INSERT INTO users 
                                          (username, email, password_hash, first_name, last_name, phone, address, role_id) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, 3)");
                    $stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $phone, $address]);
                    $user_id = $pdo->lastInsertId();
                    
                    // Create student record
                    $stmt = $pdo->prepare("INSERT INTO students 
                                          (user_id, student_id, admission_date, date_of_birth, blood_group, 
                                           guardian_name, guardian_phone, guardian_email) 
                                          VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $student_id, $date_of_birth, $blood_group, $guardian_name, $guardian_phone, $guardian_email]);
                    $student_db_id = $pdo->lastInsertId();
                    
                    // Get current academic year
                    $stmt = $pdo->query("SELECT id FROM academic_years WHERE is_current = 1");
                    $academic_year_id = $stmt->fetchColumn() ?: 1;
                    
                    // Enroll in class
                    $stmt = $pdo->prepare("INSERT INTO student_enrollments 
                                          (student_id, class_id, academic_year_id, enrollment_date, status) 
                                          VALUES (?, ?, ?, CURDATE(), 'enrolled')");
                    $stmt->execute([$student_db_id, $class_id, $academic_year_id]);
                    
                    // Also add to student_classes for compatibility
                    $stmt = $pdo->prepare("INSERT INTO student_classes 
                                          (student_id, class_id, academic_year_id, enrollment_date, status) 
                                          VALUES (?, ?, ?, CURDATE(), 'enrolled')");
                    $stmt->execute([$student_db_id, $class_id, $academic_year_id]);
                    
                    $pdo->commit();
                    
                    // Clear relevant caches
                    $cache->deleteItem('user_stats');
                    $cache->deleteItem('user_list_all');
                    $cache->deleteItem('class_list');
                    
                    logActivity($pdo, 'student_created', 'students', $student_db_id);
                    $msg = successMessageWithCredentials(
                        "Student created successfully!", 
                        $username, 
                        $password,
                        "Please save these credentials and share with the student/guardian.",
                        $student_id
                    );
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $msg = errorMessage("Error creating student: " . $e->getMessage());
                }
            }
        }
        
        elseif ($action == 'reset_password') {
            $user_id = (int)$_POST['user_id'];
            $new_password = generateStrongPassword(10);
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$password_hash, $user_id]);
                
                logActivity($pdo, 'password_reset', 'users', $user_id);
                $msg = successMessageWithCredentials(
                    "Password reset successfully!", 
                    '', 
                    $new_password,
                    "Please share this new password with the user."
                );
            } catch (PDOException $e) {
                $msg = errorMessage("Error resetting password: " . $e->getMessage());
            }
        }
        
        elseif ($action == 'toggle_status') {
            $user_id = (int)$_POST['user_id'];
            $new_status = (int)$_POST['new_status'];
            
            try {
                $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
                $stmt->execute([$new_status, $user_id]);
                
                // Clear user list cache
                $cache->deleteItem('user_list_all');
                
                $status_text = $new_status ? 'activated' : 'deactivated';
                logActivity($pdo, 'user_' . $status_text, 'users', $user_id);
                $msg = successMessage("User account has been {$status_text} successfully!");
            } catch (PDOException $e) {
                $msg = errorMessage("Error updating user status: " . $e->getMessage());
            }
        }
    }
}

// Get filter parameters with sanitization
$role_filter = isset($_GET['role']) ? preg_replace('/[^a-z]/', '', $_GET['role']) : 'all';
$status_filter = isset($_GET['status']) ? preg_replace('/[^a-z]/', '', $_GET['status']) : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Try to get users from cache first
$cacheKey = 'user_list_' . md5($role_filter . $status_filter . $search);
$cachedUsers = $cache->getItem($cacheKey);

if (!$cachedUsers->isHit()) {
    // Build query
    $where_conditions = [];
    $params = [];

    if ($role_filter != 'all') {
        $where_conditions[] = "r.role_name = ?";
        $params[] = $role_filter;
    }

    if ($status_filter != 'all') {
        $where_conditions[] = "u.is_active = ?";
        $params[] = $status_filter == 'active' ? 1 : 0;
    }

    if ($search) {
        $where_conditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.username LIKE ? OR u.email LIKE ? OR s.student_id LIKE ?)";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Get all users with their roles
    $stmt = $pdo->prepare("SELECT u.*, r.role_name,
                          CASE 
                              WHEN r.role_name = 'student' THEN s.student_id
                              ELSE NULL
                          END as student_id,
                          CASE 
                              WHEN r.role_name = 'student' THEN c.class_name
                              ELSE NULL
                          END as class_name,
                          CASE 
                              WHEN r.role_name = 'student' THEN c.section
                              ELSE NULL
                          END as section
                          FROM users u
                          JOIN user_roles r ON u.role_id = r.id
                          LEFT JOIN students s ON u.id = s.user_id
                          LEFT JOIN student_enrollments se ON s.id = se.student_id AND se.status = 'enrolled'
                          LEFT JOIN classes c ON se.class_id = c.id
                          $where_clause
                          ORDER BY r.role_name, u.first_name, u.last_name");
    $stmt->execute($params);
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cache for 5 minutes
    $cachedUsers->set($all_users)->expiresAfter(300);
    $cache->save($cachedUsers);
} else {
    $all_users = $cachedUsers->get();
}

// Get classes for student creation (with caching)
$cachedClasses = $cache->getItem('class_list');
if (!$cachedClasses->isHit()) {
    $stmt = $pdo->query("SELECT id, class_name, section FROM classes WHERE is_active = 1 ORDER BY class_level, section");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cachedClasses->set($classes)->expiresAfter(3600); // Cache for 1 hour
    $cache->save($cachedClasses);
} else {
    $classes = $cachedClasses->get();
}

// Get statistics (with caching)
$cachedStats = $cache->getItem('user_stats');
if (!$cachedStats->isHit()) {
    $stats = [
        'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'total_students' => $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 3")->fetchColumn(),
        'total_teachers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn(),
        'active_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn()
    ];
    $cachedStats->set($stats)->expiresAfter(3600); // Cache for 1 hour
    $cache->save($cachedStats);
} else {
    $stats = $cachedStats->get();
}

// Helper functions
function generateStrongPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

function successMessage($message) {
    return <<<HTML
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
HTML;
}

function successMessageWithCredentials($message, $username, $password, $note, $student_id = null) {
    $student_id_html = $student_id ? <<<HTML
        <div class='d-flex justify-content-between mb-2'>
            <span class='fw-bold'>Student ID:</span>
            <span class='font-monospace bg-light px-2 py-1 rounded'>$student_id</span>
        </div>
HTML : '';

    return <<<HTML
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <div class='d-flex'>
            <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
            <div>
                <p>$message</p>
                <div class='p-3 bg-white rounded border border-success mb-2'>
                    $student_id_html
                    <div class='d-flex justify-content-between mb-2'>
                        <span class='fw-bold'>Username:</span>
                        <span class='font-monospace bg-light px-2 py-1 rounded'>$username</span>
                    </div>
                    <div class='d-flex justify-content-between'>
                        <span class='fw-bold'>Password:</span>
                        <span class='font-monospace bg-light px-2 py-1 rounded'>$password</span>
                    </div>
                </div>
                <p class='small'>$note</p>
            </div>
        </div>
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
HTML;
}

function errorMessage($message) {
    return <<<HTML
    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
        <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Danger:'><use xlink:href='#exclamation-triangle-fill'/></svg>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
HTML;
}
?>
