<?php
// Include necessary files
include_once '../App/Models/headoffice/Promotion.php';
include_once '../App/Models/headoffice/User.php';
include_once '../include/connect.php';
include_once '../include/session.php';

// Ensure user has principal role
requireRole('principal');
$current_user = getCurrentUser($pdo);

$msg = "";
$error = "";

// Initialize classes
$promotionManager = new StudentPromotion($pdo);
$userManager = new HeadOfficeUser($pdo);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'mass_promotion':
                    $new_academic_year = trim($_POST['new_academic_year']);
                    $promotion_date = $_POST['promotion_date'];
                    
                    if (empty($new_academic_year)) {
                        throw new Exception('Academic year name is required');
                    }
                    
                    if (empty($promotion_date)) {
                        throw new Exception('Promotion date is required');
                    }
                    
                    $result = $promotionManager->promoteAllStudents($new_academic_year, $promotion_date);
                    
                    if ($result['success']) {
                        $results = $result['results'];
                        $msg = "<div class='bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg'>
                                    <div class='flex items-start'>
                                        <div class='text-green-500 mr-3'>
                                            <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                                                <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class='text-green-800 font-medium'>Mass Promotion Completed!</h3>
                                            <div class='mt-2 text-sm text-green-700'>
                                                <p><strong>Promoted:</strong> {$results['promoted']} students</p>
                                                <p><strong>Graduated:</strong> {$results['graduated']} students</p>
                                                " . ($results['failed'] > 0 ? "<p><strong>Failed:</strong> {$results['failed']} students</p>" : "") . "
                                                " . (!empty($results['errors']) ? "<p class='text-red-600 mt-2'><strong>Errors:</strong></p><ul class='list-disc pl-5'>" . implode('', array_map(function($error) { return "<li>{$error['student']}: {$error['error']}</li>"; }, array_slice($results['errors'], 0, 5))) . "</ul>" : "") . "
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                    } else {
                        throw new Exception($result['message']);
                    }
                    break;
                    
                case 'individual_promotion':
                    $student_id = intval($_POST['student_id']);
                    $to_class_id = intval($_POST['to_class_id']);
                    
                    // Get or create next academic year
                    $stmt = $pdo->prepare("SELECT id FROM academic_years WHERE is_current = 0 ORDER BY start_date DESC LIMIT 1");
                    $stmt->execute();
                    $next_year = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$next_year) {
                        // Create next academic year
                        $current_year = date('Y');
                        $next_year_name = $current_year . '-' . ($current_year + 1);
                        $start_date = date('Y-04-14'); // Baisakh 1
                        $end_date = date('Y-04-13', strtotime('+1 year'));
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO academic_years (year_name, start_date, end_date, is_current) 
                            VALUES (?, ?, ?, 0)
                        ");
                        $stmt->execute([$next_year_name, $start_date, $end_date]);
                        $academic_year_id = $pdo->lastInsertId();
                    } else {
                        $academic_year_id = $next_year['id'];
                    }
                    
                    $result = $promotionManager->promoteIndividualStudent($student_id, $to_class_id, $academic_year_id);
                    
                    if ($result['success']) {
                        $msg = "<div class='bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg'>
                                    <div class='flex items-start'>
                                        <div class='text-green-500 mr-3'>
                                            <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                                                <path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class='text-green-800 font-medium'>Student Promoted Successfully!</h3>
                                        </div>
                                    </div>
                                </div>";
                    } else {
                        throw new Exception($result['message']);
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $error = "<div class='bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg'>
                    <div class='flex items-start'>
                        <div class='text-red-500 mr-3'>
                            <svg class='w-5 h-5' fill='currentColor' viewBox='0 0 20 20'>
                                <path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class='text-red-800 font-medium'>Error!</h3>
                            <p class='text-red-700'>" . htmlspecialchars($e->getMessage()) . "</p>
                        </div>
                    </div>
                </div>";
    }
}

// Get data for the page
$classes = $userManager->getAllClasses();
$promotion_history = $promotionManager->getPromotionHistory();

// Get current students for individual promotion
$current_students_query = "
    SELECT 
        s.id as student_id,
        u.first_name,
        u.last_name,
        s.student_id as student_number,
        c.class_name,
        c.section,
        c.class_level
    FROM students s
    JOIN users u ON s.user_id = u.id
    JOIN student_classes sc ON s.id = sc.student_id
    JOIN classes c ON sc.class_id = c.id
    JOIN academic_years ay ON sc.academic_year_id = ay.id
    WHERE ay.is_current = 1 AND sc.status = 'enrolled' AND u.is_active = 1 AND s.status = 'active'
    ORDER BY c.class_level, c.section, u.last_name, u.first_name
";
$stmt = $pdo->prepare($current_students_query);
$stmt->execute();
$current_students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current academic year
$stmt = $pdo->prepare("SELECT * FROM academic_years WHERE is_current = 1");
$stmt->execute();
$current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);

// Get academic year stats
$stats = $promotionManager->getAcademicYearStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Promotion - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                        'primary-light': '#dbeafe'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <div class="p-6">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-primary to-primary-dark rounded-xl p-6 text-white shadow-lg mb-6">
                <h1 class="text-2xl lg:text-3xl font-bold mb-2 flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Student Promotion
                </h1>
                <p class="text-primary-light">Promote students to next class automatically or manually</p>
                <nav class="mt-4">
                    <ol class="flex space-x-2 text-sm">
                        <li><a href="index.php" class="text-white hover:text-primary-light">Dashboard</a></li>
                        <li><span class="text-white opacity-70 mx-2">/</span></li>
                        <li class="text-white opacity-90">Student Promotion</li>
                    </ol>
                </nav>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>
            <?= $error ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_students'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Promoted</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['promoted'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Graduated</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['graduated'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['active'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Academic Year Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Academic Year</h3>
                <?php if ($current_academic_year): ?>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-blue-800 font-medium"><?= htmlspecialchars($current_academic_year['year_name']) ?></p>
                    <p class="text-blue-600 text-sm">
                        <?= date('F j, Y', strtotime($current_academic_year['start_date'])) ?> - 
                        <?= date('F j, Y', strtotime($current_academic_year['end_date'])) ?>
                    </p>
                </div>
                <?php else: ?>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-yellow-800">No active academic year found. Please create one first.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Mass Promotion Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6 rounded-t-xl">
                    <h2 class="text-xl font-bold flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        Mass Promotion (New Academic Year)
                    </h2>
                    <p class="text-green-100 text-sm mt-1">Promote all students to next class for new academic year</p>
                </div>
                
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>This will promote ALL students to the next class level</li>
                                        <li>Students in the highest class will be marked as graduated</li>
                                        <li>A new academic year will be created</li>
                                        <li>Previous enrollments will be marked as 'promoted'</li>
                                        <li>This action cannot be easily undone</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="post" class="space-y-6">
                        <input type="hidden" name="action" value="mass_promotion">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    New Academic Year Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="new_academic_year" required 
                                       value="<?= date('Y') . '-' . (date('Y') + 1) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <p class="text-sm text-gray-500 mt-1">e.g., 2024-2025</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Promotion Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="promotion_date" required 
                                       value="<?= date('Y-m-d') ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <p class="text-sm text-gray-500 mt-1">Usually Baisakh 1 (April 14)</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to promote ALL students? This action will create a new academic year and cannot be easily undone.')"
                                    class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Promote All Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Individual Promotion Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-xl">
                    <h2 class="text-xl font-bold flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Individual Student Promotion
                    </h2>
                    <p class="text-blue-100 text-sm mt-1">Promote specific students manually</p>
                </div>
                
                <div class="p-6">
                    <?php if (empty($current_students)): ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <p class="text-gray-500">No students available for promotion</p>
                    </div>
                    <?php else: ?>
                    <form method="post" class="space-y-6">
                        <input type="hidden" name="action" value="individual_promotion">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Student <span class="text-red-500">*</span>
                                </label>
                                <select name="student_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Choose a student</option>
                                    <?php foreach ($current_students as $student): ?>
                                    <option value="<?= $student['student_id'] ?>">
                                        <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?> 
                                        (<?= htmlspecialchars($student['student_number']) ?>) - 
                                        <?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Promote to Class <span class="text-red-500">*</span>
                                </label>
                                <select name="to_class_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select target class</option>
                                    <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>">
                                        <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Promote Student
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Promotion History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 rounded-t-xl">
                    <h2 class="text-xl font-bold flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Promotion History
                    </h2>
                    <p class="text-purple-100 text-sm mt-1">Recent student promotions and graduations</p>
                </div>
                
                <div class="p-6">
                    <?php if (empty($promotion_history)): ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">No promotion history found</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($promotion_history as $record): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($record['student_id']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $record['from_class'] ? htmlspecialchars($record['from_class'] . ' ' . $record['from_section']) : '-' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $record['to_class'] ? htmlspecialchars($record['to_class'] . ' ' . $record['to_section']) : 'Graduated' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($record['year_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php
                                            switch ($record['status']) {
                                                case 'promoted': echo 'bg-green-100 text-green-800'; break;
                                                case 'graduated': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'manual_promotion': echo 'bg-yellow-100 text-yellow-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?= ucfirst(str_replace('_', ' ', $record['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M j, Y', strtotime($record['promotion_date'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
