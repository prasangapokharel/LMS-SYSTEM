<?php
// Include necessary files
require_once '../App/Models/headoffice/User.php';
require_once '../include/connect.php';
require_once '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);
$userModel = new HeadOfficeUser($pdo);
$msg = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'create_teacher') {
            // Prepare teacher data
            $teacherData = [
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name']),
                'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'phone' => preg_replace('/[^0-9]/', '', $_POST['phone']),
                'address' => $_POST['address'] ?? '',
                'qualification' => $_POST['qualification'] ?? '',
                'experience_years' => (int)($_POST['experience_years'] ?? 0),
                'specialization' => $_POST['specialization'] ?? '',
                'salary' => (float)($_POST['salary'] ?? 0),
                'subjects' => $_POST['subjects'] ?? [],
                'classes' => []
            ];
            
            // Process class assignments
            if (!empty($_POST['class_assignments'])) {
                foreach ($_POST['class_assignments'] as $assignment) {
                    if (!empty($assignment['class_id']) && !empty($assignment['subject_id'])) {
                        $teacherData['classes'][] = [
                            'class_id' => $assignment['class_id'],
                            'subject_id' => $assignment['subject_id'],
                            'academic_year_id' => 1 // Current academic year
                        ];
                    }
                }
            }
            
            $result = $userModel->createTeacher($teacherData);
            
            if ($result['success']) {
                $credentials = $result['credentials'];
                $msg = successMessageWithCredentials(
                    $result['message'],
                    $credentials['username'],
                    $credentials['password'],
                    "Please save these credentials and share with the teacher.",
                    $credentials['employee_id']
                );
            } else {
                $msg = errorMessage($result['message']);
            }
        }
        
        elseif ($action == 'create_student') {
            // Prepare student data
            $studentData = [
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name']),
                'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'phone' => preg_replace('/[^0-9]/', '', $_POST['phone']),
                'address' => $_POST['address'] ?? '',
                'class_id' => (int)$_POST['class_id'],
                'date_of_birth' => $_POST['date_of_birth'],
                'blood_group' => $_POST['blood_group'] ?? '',
                'guardian_name' => trim($_POST['guardian_name']),
                'guardian_phone' => preg_replace('/[^0-9]/', '', $_POST['guardian_phone']),
                'guardian_email' => $_POST['guardian_email'] ?? ''
            ];
            
            $result = $userModel->createStudent($studentData);
            
            if ($result['success']) {
                $credentials = $result['credentials'];
                $msg = successMessageWithCredentials(
                    $result['message'],
                    $credentials['username'],
                    $credentials['password'],
                    "Please save these credentials and share with the student/guardian.",
                    $credentials['student_id']
                );
            } else {
                $msg = errorMessage($result['message']);
            }
        }
    }
}

// Get data for dropdowns
$classes = $userModel->getAllClasses();
$subjects = $userModel->getAllSubjects();
$stats = $userModel->getUserStats();

// Helper functions
function successMessageWithCredentials($message, $username, $password, $note, $id = null) {
    $id_html = $id ? <<<HTML
        <div class='flex justify-between mb-2'>
            <span class='font-semibold text-gray-700'>ID:</span>
            <span class='font-mono bg-gray-100 px-2 py-1 rounded text-sm'>$id</span>
        </div>
HTML : '';

    return <<<HTML
    <div class='bg-green-50 border border-green-200 rounded-lg p-4 mb-6'>
        <div class='flex items-start'>
            <div class='flex-shrink-0'>
                <svg class='h-5 w-5 text-green-400' fill='currentColor' viewBox='0 0 20 20'>
                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'/>
                </svg>
            </div>
            <div class='ml-3 w-full'>
                <p class='text-sm font-medium text-green-800 mb-3'>$message</p>
                <div class='bg-white border border-green-200 rounded-lg p-3 mb-3'>
                    $id_html
                    <div class='flex justify-between mb-2'>
                        <span class='font-semibold text-gray-700'>Username:</span>
                        <span class='font-mono bg-gray-100 px-2 py-1 rounded text-sm'>$username</span>
                    </div>
                    <div class='flex justify-between'>
                        <span class='font-semibold text-gray-700'>Password:</span>
                        <span class='font-mono bg-gray-100 px-2 py-1 rounded text-sm'>$password</span>
                    </div>
                </div>
                <p class='text-xs text-green-700'>$note</p>
            </div>
        </div>
    </div>
HTML;
}

function errorMessage($message) {
    return <<<HTML
    <div class='bg-red-50 border border-red-200 rounded-lg p-4 mb-6'>
        <div class='flex items-start'>
            <div class='flex-shrink-0'>
                <svg class='h-5 w-5 text-red-400' fill='currentColor' viewBox='0 0 20 20'>
                    <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'/>
                </svg>
            </div>
            <div class='ml-3'>
                <p class='text-sm font-medium text-red-800'>$message</p>
            </div>
        </div>
    </div>
HTML;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Users - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-input {
            @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors;
        }
        .form-label {
            @apply block text-sm font-medium text-gray-700 mb-1;
        }
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors;
        }
        .btn-success {
            @apply bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 shadow-lg">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold flex items-center">
                            <i class="fas fa-user-plus mr-3"></i>
                            Create Users
                        </h1>
                        <p class="text-blue-100 mt-1">Add new teachers and students to the system</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-blue-100">Welcome, <?= htmlspecialchars($user['first_name'] ?? 'Admin') ?></p>
                        <p class="text-xs text-blue-200"><?= date('F j, Y') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto p-6">
            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_users'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Teachers</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_teachers'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_students'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['active_users'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <!-- Create Teacher Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6 rounded-t-xl">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-chalkboard-teacher mr-3"></i>
                            Create New Teacher
                        </h2>
                        <p class="text-green-100 text-sm mt-1">Add a new teacher with subject assignments</p>
                    </div>
                    
                    <div class="p-6">
                        <form method="post" id="createTeacherForm">
                            <input type="hidden" name="action" value="create_teacher">
                            
                            <!-- Basic Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-user mr-2 text-green-600"></i>
                                    Basic Information
                                </h3>
                                
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
                                    <textarea name="address" class="form-input" rows="2" placeholder="Enter teacher's address"></textarea>
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-graduation-cap mr-2 text-green-600"></i>
                                    Professional Information
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="form-label">Qualification</label>
                                        <input type="text" name="qualification" class="form-input" placeholder="e.g., M.Ed, B.Ed">
                                    </div>
                                    <div>
                                        <label class="form-label">Experience (Years)</label>
                                        <input type="number" name="experience_years" class="form-input" min="0" max="50">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="form-label">Specialization</label>
                                        <input type="text" name="specialization" class="form-input" placeholder="e.g., Mathematics, Science">
                                    </div>
                                    <div>
                                        <label class="form-label">Salary</label>
                                        <input type="number" name="salary" class="form-input" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <!-- Subject Assignments -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-book mr-2 text-green-600"></i>
                                    Subject Assignments
                                </h3>
                                
                                <div class="mb-4">
                                    <label class="form-label">Subjects</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-3">
                                        <?php foreach ($subjects as $subject): ?>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="subjects[]" value="<?= $subject['id'] ?>" class="mr-2">
                                            <span class="text-sm"><?= htmlspecialchars($subject['subject_name']) ?></span>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Class Assignments -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-school mr-2 text-green-600"></i>
                                    Class Assignments
                                </h3>
                                
                                <div id="classAssignments">
                                    <div class="class-assignment-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                        <div>
                                            <label class="form-label">Class</label>
                                            <select name="class_assignments[0][class_id]" class="form-input">
                                                <option value="">Select Class</option>
                                                <?php foreach ($classes as $class): ?>
                                                <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Subject</label>
                                            <select name="class_assignments[0][subject_id]" class="form-input">
                                                <option value="">Select Subject</option>
                                                <?php foreach ($subjects as $subject): ?>
                                                <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" onclick="removeClassAssignment(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" onclick="addClassAssignment()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-plus mr-2"></i>Add Class Assignment
                                </button>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A secure username and password will be automatically generated for the teacher.
                                </p>
                            </div>
                            
                            <button type="submit" class="btn-success w-full">
                                <i class="fas fa-user-plus mr-2"></i>
                                Create Teacher
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Create Student Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-xl">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-user-graduate mr-3"></i>
                            Create New Student
                        </h2>
                        <p class="text-blue-100 text-sm mt-1">Add a new student with class enrollment</p>
                    </div>
                    
                    <div class="p-6">
                        <form method="post" id="createStudentForm">
                            <input type="hidden" name="action" value="create_student">
                            
                            <!-- Student Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Student Information
                                </h3>
                                
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
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="form-label">Class <span class="text-red-500">*</span></label>
                                        <select name="class_id" class="form-input" required>
                                            <option value="">Select Class</option>
                                            <?php foreach ($classes as $class): ?>
                                            <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div></div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-input" rows="2" placeholder="Enter student's address"></textarea>
                                </div>
                            </div>

                            <!-- Guardian Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-user-friends mr-2 text-blue-600"></i>
                                    Guardian Information
                                </h3>
                                
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
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A student ID, username, and password will be automatically generated.
                                </p>
                            </div>
                            
                            <button type="submit" class="btn-primary w-full">
                                <i class="fas fa-user-graduate mr-2"></i>
                                Create Student
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let classAssignmentIndex = 1;

        function addClassAssignment() {
            const container = document.getElementById('classAssignments');
            const newRow = document.createElement('div');
            newRow.className = 'class-assignment-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-3';
            newRow.innerHTML = `
                <div>
                    <label class="form-label">Class</label>
                    <select name="class_assignments[${classAssignmentIndex}][class_id]" class="form-input">
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Subject</label>
                    <select name="class_assignments[${classAssignmentIndex}][subject_id]" class="form-input">
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="removeClassAssignment(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            classAssignmentIndex++;
        }

        function removeClassAssignment(button) {
            const row = button.closest('.class-assignment-row');
            if (document.querySelectorAll('.class-assignment-row').length > 1) {
                row.remove();
            } else {
                alert('At least one class assignment row must remain.');
            }
        }

        // Form validation
        document.getElementById('createTeacherForm').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value;
            const phone = this.querySelector('input[name="phone"]').value;
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            if (phone.replace(/[^0-9]/g, '').length < 10) {
                e.preventDefault();
                alert('Please enter a valid phone number (minimum 10 digits).');
                return false;
            }
        });

        document.getElementById('createStudentForm').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value;
            const phone = this.querySelector('input[name="phone"]').value;
            const dateOfBirth = this.querySelector('input[name="date_of_birth"]').value;
            const classId = this.querySelector('select[name="class_id"]').value;
            const guardianPhone = this.querySelector('input[name="guardian_phone"]').value;
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            if (phone.replace(/[^0-9]/g, '').length < 10) {
                e.preventDefault();
                alert('Please enter a valid phone number (minimum 10 digits).');
                return false;
            }
            
            if (guardianPhone.replace(/[^0-9]/g, '').length < 10) {
                e.preventDefault();
                alert('Please enter a valid guardian phone number (minimum 10 digits).');
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
            
            // Check age
            const birthDate = new Date(dateOfBirth);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            
            if (age < 3 || age > 25) {
                e.preventDefault();
                alert('Please check the date of birth. Student age should be between 3 and 25 years.');
                return false;
            }
        });

        // Auto-dismiss alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 15000);
            });
        });
    </script>
</body>
</html>
