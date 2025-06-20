<?php 
// Include necessary files
include_once '../App/Models/headoffice/Class.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Details - School LMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/ui.css">
</head>
<body class="bg-grey-50">
    <div class="flex">
        <!-- Include sidebar -->
        <?php include '../include/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 ml-0 lg:ml-64 p-4 lg:p-8">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl p-6 text-white shadow-lg mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2 flex items-center">
                            <i class="fas fa-chalkboard mr-3"></i>
                            <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                        </h1>
                        <p class="text-purple-100">Class Level: <?= htmlspecialchars($class['class_level']) ?></p>
                        
                        <nav class="mt-4">
                            <ol class="flex space-x-2 text-sm">
                                <li><a href="index.php" class="text-white">Dashboard</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li><a href="createclass.php" class="text-white">Classes</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li class="text-white opacity-90">Class Details</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="mt-4 lg:mt-0 flex flex-wrap gap-2">
                        <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Manage Students
                        </a>
                        <a href="assignteacher.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            Assign Teachers
                        </a>
                    </div>
                </div>
            </div>

            <!-- Class Information -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Class Information
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <div class="text-sm text-gray-500 font-medium">Class Name</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($class['class_name']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <div class="text-sm text-gray-500 font-medium">Section</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($class['section']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-green-500">
                        <div class="text-sm text-gray-500 font-medium">Class Level</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($class['class_level']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-yellow-500">
                        <div class="text-sm text-gray-500 font-medium">Academic Year</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($current_academic_year['year_name']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-indigo-500">
                        <div class="text-sm text-gray-500 font-medium">Students Enrolled</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= $student_count ?> / <?= htmlspecialchars($class['capacity']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-pink-500">
                        <div class="text-sm text-gray-500 font-medium">Subjects</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= count($subjects) ?></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Students Overview -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-user-graduate mr-2"></i>
                                Students Overview
                            </h2>
                            <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="px-3 py-1.5 bg-white bg-opacity-20 rounded-lg text-white text-sm">
                                <i class="fas fa-users mr-1"></i>
                                Manage Students
                            </a>
                        </div>
                        <div class="p-5">
                            <?php if (empty($students)): ?>
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Students Enrolled</h3>
                                <p class="text-gray-500 max-w-md mx-auto">
                                    There are no students enrolled in this class yet. 
                                    Use the Manage Students button to add students to this class.
                                </p>
                            </div>
                            <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium text-sm mr-3">
                                                        <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= htmlspecialchars($student['student_id']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= htmlspecialchars($student['gender']) ?>
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
                                                <a href="student_details.php?id=<?= $student['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_student.php?id=<?= $student['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-5">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>
                                Teacher Assignments
                            </h2>
                        </div>
                        <div class="p-5">
                            <?php if (empty($assignments)): ?>
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Teacher Assignments</h3>
                                <p class="text-gray-500 max-w-md mx-auto">
                                    There are no teachers assigned to subjects for this class yet.
                                </p>
                                <div class="mt-4">
                                    <a href="assignteacher.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Assign Teachers
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($assignments as $assignment): ?>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <?= htmlspecialchars($assignment['subject_name']) ?> 
                                            <span class="ml-1 text-xs text-blue-600">(<?= htmlspecialchars($assignment['subject_code']) ?>)</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium text-sm mr-3">
                                            <?= strtoupper(substr($assignment['first_name'], 0, 1) . substr($assignment['last_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Assigned: <?= date('M d, Y', strtotime($assignment['assigned_date'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="mt-4">
                                    <a href="assignteacher.php?class_id=<?= $class['id'] ?>" class="inline-flex items-center px-4 py-2 w-full justify-center bg-purple-600 text-white rounded-lg">
                                        <i class="fas fa-edit mr-2"></i>
                                        Manage Assignments
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
