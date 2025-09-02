<?php
include_once '../include/connect.php';
include_once '../include/session.php';

requireRole('principal');
$user = getCurrentUser($pdo);

// Get class ID from URL
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

if (!$class_id) {
    header("Location: createclass.php");
    exit;
}

// Get current academic year
$stmt = $pdo->prepare("SELECT * FROM academic_years WHERE is_current = 1");
$stmt->execute();
$current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current_academic_year) {
    // Get the most recent academic year if no current one is set
    $stmt = $pdo->prepare("SELECT * FROM academic_years ORDER BY start_date DESC LIMIT 1");
    $stmt->execute();
    $current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get class details
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    header("Location: createclass.php");
    exit;
}

// Get students in this class
$stmt = $pdo->prepare("
    SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.address,
           sc.enrollment_date, sc.status as enrollment_status,
           COALESCE(att_stats.total_days, 0) as total_attendance_days,
           COALESCE(att_stats.present_days, 0) as present_days,
           CASE 
               WHEN COALESCE(att_stats.total_days, 0) > 0 
               THEN ROUND((COALESCE(att_stats.present_days, 0) / att_stats.total_days) * 100, 1)
               ELSE 0 
           END as attendance_percentage
    FROM student_classes sc
    JOIN students s ON sc.student_id = s.id
    JOIN users u ON s.user_id = u.id
    LEFT JOIN (
        SELECT student_id,
               COUNT(*) as total_days,
               SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days
        FROM attendance 
        WHERE class_id = ?
        GROUP BY student_id
    ) att_stats ON s.id = att_stats.student_id
    WHERE sc.class_id = ? AND sc.academic_year_id = ? AND sc.status = 'enrolled' AND u.is_active = 1
    ORDER BY u.first_name, u.last_name
");
$stmt->execute([$class_id, $class_id, $current_academic_year['id']]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$student_count = count($students);

// Get available students for adding to class
$stmt = $pdo->prepare("
    SELECT s.*, u.first_name, u.last_name, u.email
    FROM students s
    JOIN users u ON s.user_id = u.id
    WHERE s.id NOT IN (
        SELECT student_id FROM student_classes 
        WHERE class_id = ? AND academic_year_id = ? AND status = 'enrolled'
    )
    AND u.is_active = 1
    ORDER BY u.first_name, u.last_name
");
$stmt->execute([$class_id, $current_academic_year['id']]);
$available_students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form submissions
$msg = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_student'])) {
            $student_id = intval($_POST['student_id']);
            
            // Check if student is already in this class
            $stmt = $pdo->prepare("SELECT id FROM student_classes WHERE student_id = ? AND class_id = ? AND academic_year_id = ?");
            $stmt->execute([$student_id, $class_id, $current_academic_year['id']]);
            
            if ($stmt->fetch()) {
                $error = "Student is already enrolled in this class.";
            } else {
                // Add student to class
                $stmt = $pdo->prepare("INSERT INTO student_classes (student_id, class_id, academic_year_id, enrollment_date, status) VALUES (?, ?, ?, CURDATE(), 'enrolled')");
                $stmt->execute([$student_id, $class_id, $current_academic_year['id']]);
                
                $msg = "Student added to class successfully!";
                
                // Refresh data
                $stmt = $pdo->prepare("
                    SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.address,
                           sc.enrollment_date, sc.status as enrollment_status,
                           COALESCE(att_stats.total_days, 0) as total_attendance_days,
                           COALESCE(att_stats.present_days, 0) as present_days,
                           CASE 
                               WHEN COALESCE(att_stats.total_days, 0) > 0 
                               THEN ROUND((COALESCE(att_stats.present_days, 0) / att_stats.total_days) * 100, 1)
                               ELSE 0 
                           END as attendance_percentage
                    FROM student_classes sc
                    JOIN students s ON sc.student_id = s.id
                    JOIN users u ON s.user_id = u.id
                    LEFT JOIN (
                        SELECT student_id,
                               COUNT(*) as total_days,
                               SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days
                        FROM attendance 
                        WHERE class_id = ?
                        GROUP BY student_id
                    ) att_stats ON s.id = att_stats.student_id
                    WHERE sc.class_id = ? AND sc.academic_year_id = ? AND sc.status = 'enrolled' AND u.is_active = 1
                    ORDER BY u.first_name, u.last_name
                ");
                $stmt->execute([$class_id, $class_id, $current_academic_year['id']]);
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $student_count = count($students);
                
                // Refresh available students
                $stmt = $pdo->prepare("
                    SELECT s.*, u.first_name, u.last_name, u.email
                    FROM students s
                    JOIN users u ON s.user_id = u.id
                    WHERE s.id NOT IN (
                        SELECT student_id FROM student_classes 
                        WHERE class_id = ? AND academic_year_id = ? AND status = 'enrolled'
                    )
                    AND u.is_active = 1
                    ORDER BY u.first_name, u.last_name
                ");
                $stmt->execute([$class_id, $current_academic_year['id']]);
                $available_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } elseif (isset($_POST['remove_student'])) {
            $student_id = intval($_POST['student_id']);
            
            // Remove student from class
            $stmt = $pdo->prepare("UPDATE student_classes SET status = 'withdrawn' WHERE student_id = ? AND class_id = ? AND academic_year_id = ?");
            $stmt->execute([$student_id, $class_id, $current_academic_year['id']]);
            
            $msg = "Student removed from class successfully!";
            
            // Refresh data
            $stmt = $pdo->prepare("
                SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.address,
                       sc.enrollment_date, sc.status as enrollment_status,
                       COALESCE(att_stats.total_days, 0) as total_attendance_days,
                       COALESCE(att_stats.present_days, 0) as present_days,
                       CASE 
                           WHEN COALESCE(att_stats.total_days, 0) > 0 
                           THEN ROUND((COALESCE(att_stats.present_days, 0) / att_stats.total_days) * 100, 1)
                           ELSE 0 
                       END as attendance_percentage
                FROM student_classes sc
                JOIN students s ON sc.student_id = s.id
                JOIN users u ON s.user_id = u.id
                LEFT JOIN (
                    SELECT student_id,
                           COUNT(*) as total_days,
                           SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days
                    FROM attendance 
                    WHERE class_id = ?
                    GROUP BY student_id
                ) att_stats ON s.id = att_stats.student_id
                WHERE sc.class_id = ? AND sc.academic_year_id = ? AND sc.status = 'enrolled' AND u.is_active = 1
                ORDER BY u.first_name, u.last_name
            ");
            $stmt->execute([$class_id, $class_id, $current_academic_year['id']]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $student_count = count($students);
            
            // Refresh available students
            $stmt = $pdo->prepare("
                SELECT s.*, u.first_name, u.last_name, u.email
                FROM students s
                JOIN users u ON s.user_id = u.id
                WHERE s.id NOT IN (
                    SELECT student_id FROM student_classes 
                    WHERE class_id = ? AND academic_year_id = ? AND status = 'enrolled'
                )
                AND u.is_active = 1
                ORDER BY u.first_name, u.last_name
            ");
            $stmt->execute([$class_id, $current_academic_year['id']]);
            $available_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                        'primary-light': '#dbeafe',
                        sidebar: {
                            bg: '#1e293b',
                            hover: '#334155',
                            active: '#3b82f6',
                            text: '#f8fafc',
                            muted: '#94a3b8',
                            border: '#475569'
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-inter">
    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <div class="p-6">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-primary to-primary-dark rounded-xl p-6 text-white shadow-lg mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2 flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Manage Students: <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                        </h1>
                        <p class="text-primary-light">Add, remove, or update students in this class</p>
                        
                        <nav class="mt-4">
                            <ol class="flex space-x-2 text-sm">
                                <li><a href="index.php" class="text-white hover:text-primary-light">Dashboard</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li><a href="createclass.php" class="text-white hover:text-primary-light">Classes</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li><a href="class_details.php?id=<?= $class['id'] ?>" class="text-white hover:text-primary-light">Class Details</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li class="text-white opacity-90">Manage Students</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="mt-4 lg:mt-0 flex flex-wrap gap-2">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors" data-modal-target="addStudentModal">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Add Student
                        </button>
                        <a href="class_details.php?id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Class Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($msg): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg flex items-start">
                <div class="text-green-500 mr-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-green-800 font-medium">Success!</h3>
                    <p class="text-green-700"><?= $msg ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg flex items-start">
                <div class="text-red-500 mr-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-red-800 font-medium">Error!</h3>
                    <p class="text-red-700"><?= $error ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Class Information -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Class Information
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary">
                        <div class="text-sm text-gray-500 font-medium">Class Name</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($class['class_name']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <div class="text-sm text-gray-500 font-medium">Section</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($class['section']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-green-500">
                        <div class="text-sm text-gray-500 font-medium">Academic Year</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($current_academic_year['year_name']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary">
                        <div class="text-sm text-gray-500 font-medium">Students Enrolled</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= $student_count ?> / <?= htmlspecialchars($class['capacity']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-primary to-primary-dark p-5 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Students in this Class
                    </h2>
                    <div class="flex space-x-2">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search students..." class="px-3 py-1.5 bg-white bg-opacity-20 rounded-lg text-white placeholder-white placeholder-opacity-70 border border-transparent focus:outline-none focus:border-white focus:ring-1 focus:ring-white">
                            <svg class="w-4 h-4 absolute right-3 top-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="studentsTable">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guardian</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                        </svg>
                                    </div>
                                    <h5 class="text-gray-500 text-lg font-medium mb-1">No Students Enrolled</h5>
                                    <p class="text-gray-400">There are no students enrolled in this class yet.</p>
                                    <button type="button" class="mt-4 inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors" data-modal-target="addStudentModal">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        Add Student
                                    </button>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-medium text-sm mr-3">
                                                <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($student['email']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($student['student_id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= isset($student['gender']) ? htmlspecialchars($student['gender']) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= isset($student['date_of_birth']) ? date('M d, Y', strtotime($student['date_of_birth'])) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= isset($student['guardian_name']) ? htmlspecialchars($student['guardian_name']) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $attendance_percentage = $student['attendance_percentage'] ?? 0;
                                        $status_class = 'bg-red-100 text-red-800';
                                        
                                        if ($attendance_percentage >= 90) {
                                            $status_class = 'bg-green-100 text-green-800';
                                        } elseif ($attendance_percentage >= 80) {
                                            $status_class = 'bg-blue-100 text-blue-800';
                                        } elseif ($attendance_percentage >= 70) {
                                            $status_class = 'bg-yellow-100 text-yellow-800';
                                        }
                                        ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class ?>">
                                            <?= $attendance_percentage ?>%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="student_details.php?id=<?= $student['id'] ?>" class="text-primary hover:text-primary-dark mr-3">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <button type="button" class="text-red-600 hover:text-red-900" data-modal-target="removeStudentModal<?= $student['id'] ?>">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>

                                        <!-- Remove Student Modal -->
                                        <div id="removeStudentModal<?= $student['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                            <div class="flex items-center justify-center min-h-screen p-4">
                                                <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="removeStudentModal<?= $student['id'] ?>"></div>
                                                <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-red-600 to-red-700 p-5">
                                                        <div class="flex justify-between items-center">
                                                            <h3 class="text-lg font-bold text-white flex items-center">
                                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                Remove Student
                                                            </h3>
                                                            <button type="button" class="text-white hover:text-gray-200" data-modal-close="removeStudentModal<?= $student['id'] ?>">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="p-6">
                                                        <div class="text-center mb-6">
                                                            <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                                                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                                </svg>
                                                            </div>
                                                            <p class="text-gray-700 mb-2">
                                                                Are you sure you want to remove <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong> from this class?
                                                            </p>
                                                            <p class="text-red-600 text-sm">This action cannot be undone.</p>
                                                        </div>
                                                        <form method="post" class="flex justify-end gap-2">
                                                            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-modal-close="removeStudentModal<?= $student['id'] ?>">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" name="remove_student" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Add Student Modal -->
            <div id="addStudentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="addStudentModal"></div>
                    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-auto z-10 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-primary-dark p-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    Add Student to Class
                                </h3>
                                <button type="button" class="text-white hover:text-gray-200" data-modal-close="addStudentModal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (empty($available_students)): ?>
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Available Students</h3>
                                <p class="text-gray-500 max-w-md mx-auto">
                                    There are no available students to add to this class. All students are already enrolled or you need to create new student accounts.
                                </p>
                                <div class="mt-4">
                                    <a href="createusers.php" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        Create New Student
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <form method="post">
                                <div class="mb-4">
                                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Select Student</label>
                                    <select id="student_id" name="student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                        <option value="">-- Select Student --</option>
                                        <?php foreach ($available_students as $student): ?>
                                        <option value="<?= $student['id'] ?>">
                                            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> (<?= htmlspecialchars($student['student_id']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-modal-close="addStudentModal">
                                        Cancel
                                    </button>
                                    <button type="submit" name="add_student" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Student
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Modal open buttons
            document.querySelectorAll('[data-modal-target]').forEach(button => {
                button.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal-target');
                    document.getElementById(modalId).classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });
            
            // Modal close buttons
            document.querySelectorAll('[data-modal-close]').forEach(element => {
                element.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal-close');
                    document.getElementById(modalId).classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            });
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const table = document.getElementById('studentsTable');
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const studentName = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
                        const studentId = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                        
                        if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>