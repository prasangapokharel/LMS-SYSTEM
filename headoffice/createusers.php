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
                $msg = "<div class='alert alert-success'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <i class='fas fa-check-circle text-green-500'></i>
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
                $msg = "<div class='alert alert-error'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <i class='fas fa-times-circle text-red-500'></i>
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
                $msg = "<div class='alert alert-success'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <i class='fas fa-check-circle text-green-500'></i>
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
                $msg = "<div class='alert alert-error'>
                        <div class='flex'>
                            <div class='flex-shrink-0'>
                                <i class='fas fa-times-circle text-red-500'></i>
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8 ml-0 lg:ml-64">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">
                            <i class="fas fa-user-plus mr-3"></i>
                            Create Users
                        </h1>
                        <p class="text-blue-100">Add new teachers and students to the system</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="manage_users.php" class="btn btn2">
                            <i class="fas fa-users mr-2"></i>
                            Manage Users
                        </a>
                    </div>
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
                                    <label class="form-label">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" class="form-input" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Phone <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" class="form-input" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-input" rows="3" placeholder="Enter teacher's address"></textarea>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <p class="text-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A username and password will be automatically generated for the teacher.
                                </p>
                            </div>
                            
                            <button type="submit" class="btn btn3 w-full">
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
                                    <label class="form-label">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" class="form-input" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Phone <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" class="form-input" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="form-label">Date of Birth <span class="text-red-500">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Blood Group</label>
                                    <select name="blood_group" class="form-input">
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
                                <label class="form-label">Class <span class="text-red-500">*</span></label>
                                <select name="class_id" class="form-input" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-input" rows="2" placeholder="Enter student's address"></textarea>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 mb-4">
                                <h6 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-user-friends mr-2 text-blue-600"></i>
                                    Guardian Information
                                </h6>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="form-label">Guardian Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="guardian_name" class="form-input" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Guardian Phone <span class="text-red-500">*</span></label>
                                        <input type="tel" name="guardian_phone" class="form-input" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Guardian Email</label>
                                    <input type="email" name="guardian_email" class="form-input" placeholder="guardian@example.com">
                                </div>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <p class="text-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A student ID, username, and password will be automatically generated.
                                </p>
                            </div>
                            
                            <button type="submit" class="btn btn1 w-full">
                                <i class="fas fa-user-graduate mr-2"></i>
                                Create Student
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-white rounded-xl p-5 shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mr-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php 
                                $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id IN (2,3)")->fetchColumn();
                                echo $total_users;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mr-4">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Teachers</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php 
                                $total_teachers = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn();
                                echo $total_teachers;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 mr-4">
                            <i class="fas fa-user-graduate text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Students</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php 
                                $total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 3")->fetchColumn();
                                echo $total_students;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <script>
        // Form validation
        document.getElementById('createTeacherForm').addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            if (phone.length < 10) {
                e.preventDefault();
                alert('Please enter a valid phone number.');
                return false;
            }
        });

        document.getElementById('createStudentForm').addEventListener('submit', function(e) {
            const email = document.querySelector('#createStudentForm input[name="email"]').value;
            const phone = document.querySelector('#createStudentForm input[name="phone"]').value;
            const dateOfBirth = document.querySelector('input[name="date_of_birth"]').value;
            const classId = document.querySelector('select[name="class_id"]').value;
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            if (phone.length < 10) {
                e.preventDefault();
                alert('Please enter a valid phone number.');
                return false;
            }
            
            if (!dateOfBirth) {
                e.preventDefault();
                alert('Please select a date of birth.');
                return false;
            }
            
            if (!classId) {
                e.preventDefault();
                alert('Please select a class.');
                return false;
            }
            
            // Check if student is not too young or too old
            const birthDate = new Date(dateOfBirth);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            
            if (age < 3 || age > 25) {
                e.preventDefault();
                alert('Please check the date of birth. Student age should be between 3 and 25 years.');
                return false;
            }
        });

        // Auto-dismiss alerts after 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 10000);
            });
        });
    </script>
</body>
</html>
