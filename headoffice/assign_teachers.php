<?php
include_once '../App/Models/headoffice/Assign.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teachers - School LMS</title>
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
                        <div class="flex items-center mb-2">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h1 class="text-2xl lg:text-3xl font-bold">Assign Teachers</h1>
                        </div>
                        <p class="text-white text-opacity-90">
                            Manage teacher assignments for <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                        </p>
                        
                        <nav class="mt-4">
                            <ol class="flex space-x-2 text-sm">
                                <li><a href="index.php" class="text-white hover:text-primary-light">Dashboard</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li><a href="createclass.php" class="text-white hover:text-primary-light">Classes</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li class="text-white opacity-90">Assign Teachers</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="mt-4 lg:mt-0">
                        <a href="createclass.php" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Classes
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
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary">
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
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary">
                        <div class="text-sm text-gray-500 font-medium">Students Enrolled</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= $student_count ?> / <?= htmlspecialchars($class['capacity']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-pink-500">
                        <div class="text-sm text-gray-500 font-medium">Subjects Assigned</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= count($assignments) ?></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Current Assignments -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-primary-dark p-5 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Current Teacher Assignments
                            </h2>
                            <button type="button" class="px-3 py-1.5 bg-white bg-opacity-20 rounded-lg text-white text-sm hover:bg-opacity-30 transition-colors" data-modal-target="bulkAssignModal">
                                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Bulk Assign
                            </button>
                        </div>
                        <div class="p-5">
                            <?php if (empty($assignments)): ?>
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Teacher Assignments</h3>
                                <p class="text-gray-500 max-w-md mx-auto">
                                    There are no teachers assigned to subjects for this class yet. 
                                    Use the form on the right to assign teachers.
                                </p>
                            </div>
                            <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Date</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($assignments as $assignment): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-light text-primary">
                                                    <?= htmlspecialchars($assignment['subject_name']) ?>
                                                    <span class="ml-1 text-xs text-primary-dark">(<?= htmlspecialchars($assignment['subject_code']) ?>)</span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-medium text-sm mr-3">
                                                        <?= strtoupper(substr($assignment['first_name'], 0, 1) . substr($assignment['last_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            <?= htmlspecialchars($assignment['email']) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= date('M d, Y', strtotime($assignment['assigned_date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button type="button" class="text-primary hover:text-primary-dark mr-3" data-modal-target="editAssignmentModal<?= $assignment['id'] ?>">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="text-red-600 hover:text-red-900" data-modal-target="removeAssignmentModal<?= $assignment['id'] ?>">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>

                                                <!-- Edit Assignment Modal -->
                                                <div id="editAssignmentModal<?= $assignment['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                                    <div class="flex items-center justify-center min-h-screen p-4">
                                                        <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="editAssignmentModal<?= $assignment['id'] ?>"></div>
                                                        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                                            <div class="bg-gradient-to-r from-primary to-primary-dark p-5">
                                                                <div class="flex justify-between items-center">
                                                                    <h3 class="text-lg font-bold text-white flex items-center">
                                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                        </svg>
                                                                        Edit Teacher Assignment
                                                                    </h3>
                                                                    <button type="button" class="text-white hover:text-gray-200" data-modal-close="editAssignmentModal<?= $assignment['id'] ?>">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <form method="post" class="p-6">
                                                                <input type="hidden" name="subject_id" value="<?= $assignment['subject_id'] ?>">
                                                                
                                                                <div class="mb-4">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" value="<?= htmlspecialchars($assignment['subject_name']) ?> (<?= htmlspecialchars($assignment['subject_code']) ?>)" disabled>
                                                                </div>
                                                                
                                                                <div class="mb-6">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                                                                    <select name="teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                                                        <?php foreach ($teachers as $teacher): ?>
                                                                        <option value="<?= $teacher['id'] ?>" <?= $teacher['id'] == $assignment['teacher_id'] ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> (<?= htmlspecialchars($teacher['email']) ?>)
                                                                        </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="flex justify-end space-x-3">
                                                                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-modal-close="editAssignmentModal<?= $assignment['id'] ?>">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="submit" name="assign_teacher" class="px-4 py-2 bg-gradient-to-r from-primary to-primary-dark text-white rounded-lg hover:from-primary-dark hover:to-primary transition-colors">
                                                                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                        </svg>
                                                                        Update Assignment
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Remove Assignment Modal -->
                                                <div id="removeAssignmentModal<?= $assignment['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                                    <div class="flex items-center justify-center min-h-screen p-4">
                                                        <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="removeAssignmentModal<?= $assignment['id'] ?>"></div>
                                                        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                                            <div class="bg-gradient-to-r from-red-600 to-red-700 p-5">
                                                                <div class="flex justify-between items-center">
                                                                    <h3 class="text-lg font-bold text-white flex items-center">
                                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                        </svg>
                                                                        Remove Teacher Assignment
                                                                    </h3>
                                                                    <button type="button" class="text-white hover:text-gray-200" data-modal-close="removeAssignmentModal<?= $assignment['id'] ?>">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <form method="post" class="p-6">
                                                                <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                                                
                                                                <div class="text-center mb-6">
                                                                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                                                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                                        </svg>
                                                                    </div>
                                                                    
                                                                    <p class="text-gray-700 mb-2">
                                                                        Are you sure you want to remove <strong><?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></strong> from teaching <strong><?= htmlspecialchars($assignment['subject_name']) ?></strong>?
                                                                    </p>
                                                                    
                                                                    <p class="text-red-600 text-sm">This action cannot be undone.</p>
                                                                </div>
                                                                
                                                                <div class="flex justify-end space-x-3">
                                                                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-modal-close="removeAssignmentModal<?= $assignment['id'] ?>">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="submit" name="remove_assignment" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-colors">
                                                                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                        </svg>
                                                                        Remove Assignment
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
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
                
                <!-- Assign Teacher Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-5">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Assign Teacher
                            </h2>
                        </div>
                        <div class="p-5">
                            <form method="post">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Subject</label>
                                    <select name="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                        <option value="">-- Select Subject --</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>">
                                            <?= htmlspecialchars($subject['subject_name']) ?> (<?= htmlspecialchars($subject['subject_code']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                                    <select name="teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                        <option value="">-- Select Teacher --</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> (<?= htmlspecialchars($teacher['email']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" name="assign_teacher" class="w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    Assign Teacher
                                </button>
                            </form>
                            
                            <div class="my-6 border-t border-gray-200"></div>
                            
                            <button type="button" class="w-full px-4 py-2 bg-gradient-to-r from-primary to-primary-dark text-white rounded-lg hover:from-primary-dark hover:to-primary transition-colors flex items-center justify-center" data-modal-target="addSubjectModal">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add New Subject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Subject Modal -->
            <div id="addSubjectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="addSubjectModal"></div>
                    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-primary-dark p-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Add New Subject
                                </h3>
                                <button type="button" class="text-white hover:text-gray-200" data-modal-close="addSubjectModal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form method="post" class="p-6">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                <p class="mt-1 text-sm text-gray-500">Must be unique across all subjects</p>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-modal-close="addSubjectModal">
                                    Cancel
                                </button>
                                <button type="submit" name="add_subject" class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Subject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Bulk Assign Modal -->
            <div id="bulkAssignModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50" data-modal-close="bulkAssignModal"></div>
                    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-auto z-10 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-primary-dark p-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Bulk Teacher Assignment
                                </h3>
                                <button type="button" class="text-white hover:text-gray-200" data-modal-close="bulkAssignModal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form method="post" class="p-6">
                            <p class="text-gray-500 mb-6">
                                Assign multiple subjects to teachers at once. Select a teacher and check the subjects you want to assign.
                            </p>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                                <select id="bulkTeacher" name="bulk_teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                    <option value="">-- Select Teacher --</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>">
                                        <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> (<?= htmlspecialchars($teacher['email']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="overflow-x-auto mb-6">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                                <div class="flex items-center">
                                                    <input id="selectAll" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary" type="checkbox">
                                                </div>
                                            </th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Teacher</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($subjects as $subject):
                                            // Find current assignment for this subject
                                            $current_teacher = null;
                                            foreach ($assignments as $assignment) {
                                                if ($assignment['subject_id'] == $subject['id']) {
                                                    $current_teacher = $assignment;
                                                    break;
                                                }
                                            }
                                        ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <input class="subject-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary" type="checkbox" name="bulk_subjects[]" value="<?= $subject['id'] ?>">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-light text-primary">
                                                    <?= htmlspecialchars($subject['subject_name']) ?>
                                                    <span class="ml-1 text-xs text-primary-dark">(<?= htmlspecialchars($subject['subject_code']) ?>)</span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($current_teacher): ?>
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-medium text-sm mr-3">
                                                        <?= strtoupper(substr($current_teacher['first_name'], 0, 1) . substr($current_teacher['last_name'], 0, 1)) ?>
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?= htmlspecialchars($current_teacher['first_name'] . ' ' . $current_teacher['last_name']) ?>
                                                    </div>
                                                </div>
                                                <?php else: ?>
                                                <span class="text-gray-500 text-sm">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-modal-close="bulkAssignModal">
                                    Cancel
                                </button>
                                <button type="submit" id="bulkAssignBtn" name="bulk_assign" class="px-4 py-2 bg-gradient-to-r from-primary to-primary-dark text-white rounded-lg hover:from-primary-dark hover:to-primary transition-colors" disabled>
                                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Assign Selected Subjects
                                </button>
                            </div>
                        </form>
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
            
            // Select all checkbox functionality
            const selectAll = document.getElementById('selectAll');
            const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');
            const bulkTeacher = document.getElementById('bulkTeacher');
            const bulkAssignBtn = document.getElementById('bulkAssignBtn');
            
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    subjectCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                    updateBulkAssignButton();
                });
            }
            
            subjectCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateBulkAssignButton();
                    
                    // Update "Select All" checkbox state
                    let allChecked = true;
                    subjectCheckboxes.forEach(function(cb) {
                        if (!cb.checked) allChecked = false;
                    });
                    
                    if (selectAll) {
                        selectAll.checked = allChecked;
                    }
                });
            });
            
            if (bulkTeacher) {
                bulkTeacher.addEventListener('change', updateBulkAssignButton);
            }
            
            function updateBulkAssignButton() {
                let hasTeacher = bulkTeacher && bulkTeacher.value;
                let hasSubjects = false;
                
                subjectCheckboxes.forEach(function(checkbox) {
                    if (checkbox.checked) hasSubjects = true;
                });
                
                if (bulkAssignBtn) {
                    bulkAssignBtn.disabled = !(hasTeacher && hasSubjects);
                }
            }
        });
    </script>
</body>
</html>