<?php 
// Include necessary files
include_once '../App/Models/headoffice/User.php';
include_once '../include/connect.php';
include_once '../include/session.php';

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
    }
}

// Get classes for student creation
$stmt = $pdo->query("SELECT id, class_name, section FROM classes WHERE is_active = 1 ORDER BY class_level, section");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Users - School LMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/ui.css">
</head>
<body class="bg-grey-50">
    <!-- Main Content -->
    <main class="main-content">
        <div class="p-4 lg:p-6">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl p-6 text-white shadow-lg mb-6">
                <h1 class="text-2xl md:text-3xl font-bold mb-2">
                    <i class="fas fa-user-plus mr-3"></i>
                    Create Users
                </h1>
                <p class="text-blue-100">Add new teachers and students to the system</p>
                
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="manage_users.php" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-all">
                        <i class="fas fa-users mr-2"></i>
                        Manage Users
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Create Teacher Form -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-5">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            Create New Teacher
                        </h2>
                    </div>
                    <div class="p-6">
                        <form method="post" id="createTeacherForm">
                            <input type="hidden" name="action" value="create_teacher">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">First Name *</label>
                                    <input type="text" name="first_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Last Name *</label>
                                    <input type="text" name="last_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Email *</label>
                                    <input type="email" name="email" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Phone *</label>
                                    <input type="tel" name="phone" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-grey-700 mb-1">Address</label>
                                <textarea name="address" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="3"></textarea>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A username and password will be automatically generated for the teacher.
                                </p>
                            </div>
                            
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-plus mr-2"></i>
                                Create Teacher
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Create Student Form -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Create New Student
                        </h2>
                    </div>
                    <div class="p-6">
                        <form method="post" id="createStudentForm">
                            <input type="hidden" name="action" value="create_student">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">First Name *</label>
                                    <input type="text" name="first_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Last Name *</label>
                                    <input type="text" name="last_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Email *</label>
                                    <input type="email" name="email" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Phone *</label>
                                    <input type="tel" name="phone" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Date of Birth *</label>
                                    <input type="date" name="date_of_birth" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Blood Group</label>
                                    <select name="blood_group" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-grey-700 mb-1">Class *</label>
                                <select name="class_id" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-grey-700 mb-1">Address</label>
                                <textarea name="address" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="2"></textarea>
                            </div>
                            
                            <div class="border-t border-grey-200 pt-4 mb-4">
                                <h6 class="text-sm font-semibold text-grey-700 mb-3">Guardian Information</h6>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Guardian Name *</label>
                                        <input type="text" name="guardian_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Guardian Phone *</label>
                                        <input type="tel" name="guardian_phone" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Guardian Email</label>
                                    <input type="email" name="guardian_email" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A student ID, username, and password will be automatically generated.
                                </p>
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-graduate mr-2"></i>
                                Create Student
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
</body>
</html>
