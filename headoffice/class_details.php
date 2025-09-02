<?php
// Include necessary files
include_once '../App/Models/headoffice/Class.php';
include '../include/sidebar.php';

// Get class ID from URL parameter
$class_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$class_id) {
    header('Location: createclass.php');
    exit;
}

try {
    // Get class details
    $stmt = $pdo->prepare("
        SELECT c.*, ay.year_name, ay.is_current,
               (SELECT COUNT(*) FROM student_classes sc WHERE sc.class_id = c.id AND sc.status = 'enrolled') as student_count
        FROM classes c 
        JOIN academic_years ay ON c.academic_year_id = ay.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$class) {
        header('Location: createclass.php');
        exit;
    }

    // Get current academic year
    $stmt = $pdo->prepare("SELECT * FROM academic_years WHERE is_current = 1");
    $stmt->execute();
    $current_academic_year = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get students in this class
    $stmt = $pdo->prepare("
        SELECT s.*, u.first_name, u.last_name, u.email, st.student_id, st.date_of_birth,
               COALESCE(
                   ROUND(
                       (COUNT(CASE WHEN a.status = 'present' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(a.id), 0)), 2
                   ), 0
               ) as attendance_percentage
        FROM student_classes sc
        JOIN students s ON sc.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN students st ON s.id = st.id
        LEFT JOIN attendance a ON s.id = a.student_id
        WHERE sc.class_id = ? AND sc.status = 'enrolled' AND u.is_active = 1
        GROUP BY s.id, u.first_name, u.last_name, u.email, st.student_id, st.date_of_birth
        ORDER BY u.first_name, u.last_name
    ");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get teacher assignments for this class
    $stmt = $pdo->prepare("
        SELECT cst.*, u.first_name, u.last_name, u.email, s.subject_name, s.subject_code,
               cst.assigned_date
        FROM class_subject_teachers cst
        JOIN users u ON cst.teacher_id = u.id
        JOIN subjects s ON cst.subject_id = s.id
        WHERE cst.class_id = ? AND cst.is_active = 1
        ORDER BY s.subject_name
    ");
    $stmt->execute([$class_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get subjects for this class
    $stmt = $pdo->prepare("
        SELECT DISTINCT s.*
        FROM subjects s
        JOIN class_subject_teachers cst ON s.id = cst.subject_id
        WHERE cst.class_id = ? AND cst.is_active = 1
        ORDER BY s.subject_name
    ");
    $stmt->execute([$class_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count students
    $student_count = count($students);

} catch (Exception $e) {
    error_log("Error in class_details.php: " . $e->getMessage());
    header('Location: createclass.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Details - School LMS</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                        'primary-light': '#dbeafe',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-inter">

<!-- Main Content -->
<main class="p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <svg class="w-8 h-8 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                </h1>
                <p class="text-gray-600">Class Level: <?= htmlspecialchars($class['class_level']) ?></p>
                
                <nav class="mt-4">
                    <ol class="flex space-x-2 text-sm">
                        <li><a href="index.php" class="text-primary hover:text-primary-dark">Dashboard</a></li>
                        <li><span class="text-gray-400 mx-2">/</span></li>
                        <li><a href="createclass.php" class="text-primary hover:text-primary-dark">Classes</a></li>
                        <li><span class="text-gray-400 mx-2">/</span></li>
                        <li class="text-gray-600">Class Details</li>
                    </ol>
                </nav>
            </div>
            
            <div class="mt-4 lg:mt-0 flex flex-wrap gap-3">
                <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Manage Students
                </a>
                <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Assign Teachers
                </a>
            </div>
        </div>
    </div>

    <!-- Class Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Class Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                <div class="text-sm text-gray-500 font-medium">Class Name</div>
                <div class="text-gray-900 font-semibold mt-1"><?= htmlspecialchars($class['class_name']) ?></div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-purple-500">
                <div class="text-sm text-gray-500 font-medium">Section</div>
                <div class="text-gray-900 font-semibold mt-1"><?= htmlspecialchars($class['section']) ?></div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-green-500">
                <div class="text-sm text-gray-500 font-medium">Class Level</div>
                <div class="text-gray-900 font-semibold mt-1"><?= htmlspecialchars($class['class_level']) ?></div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-yellow-500">
                <div class="text-sm text-gray-500 font-medium">Academic Year</div>
                <div class="text-gray-900 font-semibold mt-1"><?= htmlspecialchars($class['year_name']) ?></div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-indigo-500">
                <div class="text-sm text-gray-500 font-medium">Students Enrolled</div>
                <div class="text-gray-900 font-semibold mt-1"><?= $student_count ?> / <?= htmlspecialchars($class['capacity']) ?></div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-pink-500">
                <div class="text-sm text-gray-500 font-medium">Subjects</div>
                <div class="text-gray-900 font-semibold mt-1"><?= count($subjects) ?></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Students Overview -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Students Overview
                    </h2>
                    <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="px-3 py-1.5 bg-white bg-opacity-20 rounded-lg text-white text-sm hover:bg-opacity-30 transition-colors">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Manage Students
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($students)): ?>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Students Enrolled</h3>
                        <p class="text-gray-500 max-w-md mx-auto">
                            There are no students enrolled in this class yet. 
                            Use the Manage Students button to add students to this class.
                        </p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Student</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Student ID</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Date of Birth</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Attendance</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($students as $student): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm mr-3">
                                                <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                                </div>
                                                <div class="text-xs text-gray-500"><?= htmlspecialchars($student['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?= htmlspecialchars($student['student_id']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?= $student['date_of_birth'] ? date('M d, Y', strtotime($student['date_of_birth'])) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4">
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
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full <?= $status_class ?>">
                                            <?= number_format($attendance_percentage, 1) ?>%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="student_details.php?id=<?= $student['id'] ?>" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="edit_student.php?id=<?= $student['id'] ?>" class="text-indigo-600 hover:text-indigo-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        </div>
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
        
        <!-- Teacher Assignments -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Teacher Assignments
                    </h2>
                </div>
                <div class="p-6">
                    <?php if (empty($assignments)): ?>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Teacher Assignments</h3>
                        <p class="text-gray-500 max-w-md mx-auto mb-4">
                            There are no teachers assigned to subjects for this class yet.
                        </p>
                        <div class="mt-4">
                            <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Assign Teachers
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($assignments as $assignment): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($assignment['subject_name']) ?>
                                    <span class="ml-1 text-xs text-blue-600">(<?= htmlspecialchars($assignment['subject_code']) ?>)</span>
                                </span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-sm mr-3">
                                    <?= strtoupper(substr($assignment['first_name'], 0, 1) . substr($assignment['last_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        <?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Assigned: <?= date('M d, Y', strtotime($assignment['assigned_date'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="mt-6">
                            <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 w-full justify-center bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Manage Assignments
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>