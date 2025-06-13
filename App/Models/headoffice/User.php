<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'create_teacher') {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'] ?? '';
            
            // Generate teacher username and password
            $teacher_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn();
            $username = 'teacher' . str_pad($teacher_count + 1, 3, '0', STR_PAD_LEFT);
            $password = 'teacher' . rand(100, 999);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO users 
                                      (username, email, password_hash, first_name, last_name, phone, address, role_id) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 2)");
                $stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $phone, $address]);
                
                logActivity($pdo, 'teacher_created', 'users', $pdo->lastInsertId());
                $msg = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-green-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>Teacher created successfully!</p>
                                <div class='mt-2 p-3 bg-white rounded border border-green-200'>
                                    <div class='flex justify-between mb-1'>
                                        <span class='text-sm font-medium'>Username:</span>
                                        <span class='text-sm font-mono bg-gray-100 px-2 py-1 rounded'>{$username}</span>
                                    </div>
                                    <div class='flex justify-between'>
                                        <span class='text-sm font-medium'>Password:</span>
                                        <span class='text-sm font-mono bg-gray-100 px-2 py-1 rounded'>{$password}</span>
                                    </div>
                                </div>
                                <p class='text-xs mt-1'>Please save these credentials and share with the teacher.</p>
                            </div>
                        </div>
                       </div>";
            } catch (PDOException $e) {
                $msg = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-red-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                            </div>
                        </div>
                       </div>";
            }
        }
        
        elseif ($action == 'create_student') {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'] ?? '';
            $class_id = $_POST['class_id'];
            $date_of_birth = $_POST['date_of_birth'];
            $blood_group = $_POST['blood_group'] ?? '';
            $guardian_name = $_POST['guardian_name'];
            $guardian_phone = $_POST['guardian_phone'];
            $guardian_email = $_POST['guardian_email'] ?? '';
            
            // Generate student ID and credentials
            $current_year = date('Y');
            $student_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
            $student_id = 'STU' . $current_year . str_pad($student_count + 1, 3, '0', STR_PAD_LEFT);
            $username = 'student' . str_pad($student_count + 1, 3, '0', STR_PAD_LEFT);
            $password = 'student' . rand(100, 999);
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
                
                logActivity($pdo, 'student_created', 'students', $student_db_id);
                $msg = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-green-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>Student created successfully!</p>
                                <div class='mt-2 p-3 bg-white rounded border border-green-200'>
                                    <div class='flex justify-between mb-1'>
                                        <span class='text-sm font-medium'>Student ID:</span>
                                        <span class='text-sm font-mono bg-gray-100 px-2 py-1 rounded'>{$student_id}</span>
                                    </div>
                                    <div class='flex justify-between mb-1'>
                                        <span class='text-sm font-medium'>Username:</span>
                                        <span class='text-sm font-mono bg-gray-100 px-2 py-1 rounded'>{$username}</span>
                                    </div>
                                    <div class='flex justify-between'>
                                        <span class='text-sm font-medium'>Password:</span>
                                        <span class='text-sm font-mono bg-gray-100 px-2 py-1 rounded'>{$password}</span>
                                    </div>
                                </div>
                                <p class='text-xs mt-1'>Please save these credentials and share with the student/guardian.</p>
                            </div>
                        </div>
                       </div>";
            } catch (PDOException $e) {
                $pdo->rollBack();
                $msg = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-red-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                            </div>
                        </div>
                       </div>";
            }
        }
        
        elseif ($action == 'reset_password') {
            $user_id = $_POST['user_id'];
            $new_password = 'reset' . rand(1000, 9999);
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$password_hash, $user_id]);
                
                logActivity($pdo, 'password_reset', 'users', $user_id);
                $msg = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-green-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>Password reset successfully!</p>
                                <div class='mt-2 p-3 bg-white rounded border border-green-200'>
                                    <div class='flex justify-between'>
                                        <span class='text-sm font-medium'>New Password:</span>
                                        <span class='text-sm font-mono bg-gray-100 px-2 py-1 rounded'>{$new_password}</span>
                                    </div>
                                </div>
                                <p class='text-xs mt-1'>Please share this new password with the user.</p>
                            </div>
                        </div>
                       </div>";
            } catch (PDOException $e) {
                $msg = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-red-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                            </div>
                        </div>
                       </div>";
            }
        }
        
        elseif ($action == 'toggle_status') {
            $user_id = $_POST['user_id'];
            $new_status = $_POST['new_status'];
            
            try {
                $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
                $stmt->execute([$new_status, $user_id]);
                
                $status_text = $new_status ? 'activated' : 'deactivated';
                logActivity($pdo, 'user_' . $status_text, 'users', $user_id);
                $msg = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-green-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>User account has been {$status_text} successfully!</p>
                            </div>
                        </div>
                       </div>";
            } catch (PDOException $e) {
                $msg = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <svg class='h-5 w-5 text-red-500' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'/>
                                </svg>
                            </div>
                            <div class='ml-3'>
                                <p class='text-sm font-medium'>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                            </div>
                        </div>
                       </div>";
            }
        }
    }
}

// Get filter parameters
$role_filter = $_GET['role'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

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

// Get classes for student creation
$stmt = $pdo->query("SELECT id, class_name, section FROM classes WHERE is_active = 1 ORDER BY class_level, section");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats = [
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_students' => $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 3")->fetchColumn(),
    'total_teachers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn(),
    'active_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn()
];

include '../include/sidebar.php';
?>