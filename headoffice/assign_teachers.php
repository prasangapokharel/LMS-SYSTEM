<?php 
// Include necessary files
include_once '../App/Models/headoffice/Assign.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teachers - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/ui.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Main Content -->
        <div class="flex-1 ml-0 lg:ml-64 p-4 lg:p-8">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex items-center mb-2">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-chalkboard-teacher text-xl"></i>
                            </div>
                            <h1 class="text-2xl lg:text-3xl font-bold">Assign Teachers</h1>
                        </div>
                        <p class="text-white text-opacity-90">
                            Manage teacher assignments for <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                        </p>
                        
                        <nav class="mt-4">
                            <ol class="flex space-x-2 text-sm">
                                <li><a href="index.php" class="text-white">Dashboard</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li><a href="createclass.php" class="text-white">Classes</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li class="text-white opacity-90">Assign Teachers</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="mt-4 lg:mt-0">
                        <a href="createclass.php" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Classes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($msg): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg flex items-start">
                <div class="text-green-500 mr-3">
                    <i class="fas fa-check-circle text-xl"></i>
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
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="text-red-800 font-medium">Error!</h3>
                    <p class="text-red-700"><?= $error ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!$current_academic_year || $current_academic_year['id'] <= 0): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg flex items-start">
                <div class="text-yellow-500 mr-3">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <h3 class="text-yellow-800 font-medium">Academic Year Warning</h3>
                    <p class="text-yellow-700">No active academic year is set. Please set an active academic year in the System Settings before assigning teachers.</p>
                </div>
            </div>
            <?php endif; ?>

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
                        <div class="text-sm text-gray-500 font-medium">Subjects Assigned</div>
                        <div class="text-gray-800 font-semibold mt-1"><?= count($assignments) ?></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Current Assignments -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-user-check mr-2"></i>
                                Current Teacher Assignments
                            </h2>
                            <button type="button" class="px-3 py-1.5 bg-white bg-opacity-20 rounded-lg text-white text-sm" 
                                    data-modal-target="bulkAssignModal">
                                <i class="fas fa-tasks mr-1"></i>
                                Bulk Assign
                            </button>
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
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    <?= htmlspecialchars($assignment['subject_name']) ?> 
                                                    <span class="ml-1 text-xs text-blue-600">(<?= htmlspecialchars($assignment['subject_code']) ?>)</span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium text-sm mr-3">
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
                                                <button type="button" class="text-indigo-600 mr-3"
                                                        data-modal-target="editAssignmentModal<?= $assignment['id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="text-red-600"
                                                        data-modal-target="removeAssignmentModal<?= $assignment['id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                
                                                <!-- Edit Assignment Modal -->
                                                <div id="editAssignmentModal<?= $assignment['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                                    <div class="flex items-center justify-center min-h-screen p-4">
                                                        <div class="fixed inset-0 bg-black bg-opacity-50"
                                                             data-modal-close="editAssignmentModal<?= $assignment['id'] ?>"></div>
                                                        
                                                        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5">
                                                                <div class="flex justify-between items-center">
                                                                    <h3 class="text-lg font-bold text-white flex items-center">
                                                                        <i class="fas fa-edit mr-2"></i>
                                                                        Edit Teacher Assignment
                                                                    </h3>
                                                                    <button type="button" class="text-white"
                                                                            data-modal-close="editAssignmentModal<?= $assignment['id'] ?>">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <form method="post" class="p-6">
                                                                <input type="hidden" name="subject_id" value="<?= $assignment['subject_id'] ?>">
                                                                
                                                                <div class="mb-4">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                                                                           value="<?= htmlspecialchars($assignment['subject_name']) ?> (<?= htmlspecialchars($assignment['subject_code']) ?>)" 
                                                                           disabled>
                                                                </div>
                                                                
                                                                <div class="mb-6">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                                                                    <select name="teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                                                        <?php foreach ($teachers as $teacher): ?>
                                                                        <option value="<?= $teacher['id'] ?>" <?= $teacher['id'] == $assignment['teacher_id'] ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> 
                                                                            (<?= htmlspecialchars($teacher['email']) ?>)
                                                                        </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="flex justify-end space-x-3">
                                                                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                                                                            data-modal-close="editAssignmentModal<?= $assignment['id'] ?>">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="submit" name="assign_teacher" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg">
                                                                        <i class="fas fa-save mr-1"></i>
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
                                                        <div class="fixed inset-0 bg-black bg-opacity-50"
                                                             data-modal-close="removeAssignmentModal<?= $assignment['id'] ?>"></div>
                                                        
                                                        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                                            <div class="bg-gradient-to-r from-red-600 to-red-700 p-5">
                                                                <div class="flex justify-between items-center">
                                                                    <h3 class="text-lg font-bold text-white flex items-center">
                                                                        <i class="fas fa-trash mr-2"></i>
                                                                        Remove Teacher Assignment
                                                                    </h3>
                                                                    <button type="button" class="text-white"
                                                                            data-modal-close="removeAssignmentModal<?= $assignment['id'] ?>">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <form method="post" class="p-6">
                                                                <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                                                
                                                                <div class="text-center mb-6">
                                                                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                                                                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                                                                    </div>
                                                                    
                                                                    <p class="text-gray-700 mb-2">
                                                                        Are you sure you want to remove <strong><?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></strong> 
                                                                        from teaching <strong><?= htmlspecialchars($assignment['subject_name']) ?></strong>?
                                                                    </p>
                                                                    
                                                                    <p class="text-red-600 text-sm">This action cannot be undone.</p>
                                                                </div>
                                                                
                                                                <div class="flex justify-end space-x-3">
                                                                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                                                                            data-modal-close="removeAssignmentModal<?= $assignment['id'] ?>">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="submit" name="remove_assignment" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg">
                                                                        <i class="fas fa-trash mr-1"></i>
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
                                <i class="fas fa-user-plus mr-2"></i>
                                Assign Teacher
                            </h2>
                        </div>
                        <div class="p-5">
                            <form method="post">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Subject</label>
                                    <select name="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                        <option value="">-- Select Subject --</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>">
                                            <?= htmlspecialchars($subject['subject_name']) ?> 
                                            (<?= htmlspecialchars($subject['subject_code']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                                    <select name="teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                        <option value="">-- Select Teacher --</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> 
                                            (<?= htmlspecialchars($teacher['email']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" name="assign_teacher" class="w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Assign Teacher
                                </button>
                            </form>
                            
                            <div class="my-6 border-t border-gray-200"></div>
                            
                            <button type="button" class="w-full px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg flex items-center justify-center"
                                    data-modal-target="addSubjectModal">
                                <i class="fas fa-plus mr-2"></i>
                                Add New Subject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Subject Modal -->
            <div id="addSubjectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50"
                         data-modal-close="addSubjectModal"></div>
                    
                    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 p-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <i class="fas fa-book mr-2"></i>
                                    Add New Subject
                                </h3>
                                <button type="button" class="text-white"
                                        data-modal-close="addSubjectModal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <form method="post" class="p-6">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                <p class="mt-1 text-sm text-gray-500">Must be unique across all subjects</p>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                                        data-modal-close="addSubjectModal">
                                    Cancel
                                </button>
                                <button type="submit" name="add_subject" class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg">
                                    <i class="fas fa-plus mr-1"></i>
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
                    <div class="fixed inset-0 bg-black bg-opacity-50"
                         data-modal-close="bulkAssignModal"></div>
                    
                    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-auto z-10 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Bulk Teacher Assignment
                                </h3>
                                <button type="button" class="text-white"
                                        data-modal-close="bulkAssignModal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <form method="post" class="p-6">
                            <p class="text-gray-500 mb-6">
                                Assign multiple subjects to teachers at once. Select a teacher and check the subjects you want to assign.
                            </p>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                                <select id="bulkTeacher" name="bulk_teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                    <option value="">-- Select Teacher --</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>">
                                        <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> 
                                        (<?= htmlspecialchars($teacher['email']) ?>)
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
                                                    <input id="selectAll" class="h-4 w-4 text-blue-600 border-gray-300 rounded" type="checkbox">
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
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <input class="subject-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded" 
                                                           type="checkbox" name="bulk_subjects[]" value="<?= $subject['id'] ?>">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    <?= htmlspecialchars($subject['subject_name']) ?> 
                                                    <span class="ml-1 text-xs text-blue-600">(<?= htmlspecialchars($subject['subject_code']) ?>)</span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($current_teacher): ?>
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium text-sm mr-3">
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
                                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                                        data-modal-close="bulkAssignModal">
                                    Cancel
                                </button>
                                <button type="submit" id="bulkAssignBtn" name="bulk_assign" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg" disabled>
                                    <i class="fas fa-save mr-1"></i>
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
 && hasSubjects);
                }
            }
        });
    </script>
</body>
</html>
