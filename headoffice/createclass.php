<?php 
// Include necessary files
include_once '../App/Models/headoffice/Class.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create & Manage Classes - School LMS</title>
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
                    <i class="fas fa-school mr-3"></i>
                    Class Management
                </h1>
                <p class="text-blue-100">Create and manage classes for your school</p>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fas fa-school text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= count($existing_classes) ?></p>
                    <p class="text-sm text-grey-500">Total Classes</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= count(array_filter($existing_classes, fn($c) => $c['is_active'])) ?></p>
                    <p class="text-sm text-grey-500">Active Classes</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= array_sum(array_column($existing_classes, 'student_count')) ?></p>
                    <p class="text-sm text-grey-500">Total Students</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= count($teachers) ?></p>
                    <p class="text-sm text-grey-500">Available Teachers</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Create Class Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Create New Class
                            </h2>
                        </div>
                        <div class="p-6">
                            <form method="post">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Academic Year</label>
                                    <select name="academic_year_id" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                        <option value="">-- Select Academic Year --</option>
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?= $year['id'] ?>" <?= $year['is_current'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($year['year_name']) ?>
                                            <?= $year['is_current'] ? ' (Current)' : '' ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Class Name</label>
                                        <input type="text" name="class_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required placeholder="e.g., Class 1, Grade 5">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Section</label>
                                        <input type="text" name="section" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="A" required placeholder="A, B, C">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Class Level</label>
                                        <select name="class_level" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                            <option value="">-- Select Level --</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>">Level <?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Capacity</label>
                                        <input type="number" name="capacity" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="40" required min="1" max="100">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Description (Optional)</label>
                                    <textarea name="description" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="3" placeholder="Additional information about the class"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create Class
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Existing Classes -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-5">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-list mr-2"></i>
                                Existing Classes (<?= count($existing_classes) ?>)
                            </h2>
                        </div>
                        <div class="p-6">
                            <?php if (empty($existing_classes)): ?>
                            <div class="text-center py-10">
                                <i class="fas fa-school fa-4x text-grey-300 mb-3"></i>
                                <h5 class="text-grey-500 mb-1">No classes created yet</h5>
                                <p class="text-grey-400">Create your first class using the form on the left.</p>
                            </div>
                            <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($existing_classes as $class): ?>
                                <div class="border border-grey-200 rounded-xl overflow-hidden shadow-sm">
                                    <div class="p-4 border-b border-grey-200">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h6 class="text-lg font-semibold text-grey-800 mb-1">
                                                    <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                                                </h6>
                                                <div class="flex flex-wrap gap-2 items-center">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Level <?= $class['class_level'] ?></span>
                                                    <span class="text-grey-500 text-sm"><?= htmlspecialchars($class['year_name']) ?></span>
                                                    <?php if ($class['is_active']): ?>
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                                                    <?php else: ?>
                                                    <span class="px-2 py-1 bg-grey-100 text-grey-800 rounded-full text-xs font-medium">Inactive</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="p-2 text-grey-500 hover:text-grey-700 hover:bg-grey-100 rounded-lg">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10">
                                                    <a href="class_details.php?id=<?= $class['id'] ?>" class="block px-4 py-2 text-grey-700 hover:bg-grey-100">
                                                        <i class="fas fa-eye mr-2"></i>View Details
                                                    </a>
                                                    <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="block px-4 py-2 text-grey-700 hover:bg-grey-100">
                                                        <i class="fas fa-users mr-2"></i>Manage Students
                                                    </a>
                                                    <a href="assignteacher.php?class_id=<?= $class['id'] ?>" class="block px-4 py-2 text-grey-700 hover:bg-grey-100">
                                                        <i class="fas fa-chalkboard-teacher mr-2"></i>Assign Teachers
                                                    </a>
                                                    <hr class="my-1 border-grey-200">
                                                    <form method="post" class="block" onsubmit="return confirm('Are you sure you want to delete this class? This action cannot be undone.')">
                                                        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                                        <button type="submit" name="delete_class" class="w-full text-left px-4 py-2 text-red-600 hover:bg-grey-100">
                                                            <i class="fas fa-trash mr-2"></i>Delete Class
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                            <div class="bg-grey-50 p-3 rounded-lg">
                                                <div class="text-xs text-grey-500 mb-1">Students</div>
                                                <div class="text-sm font-medium text-grey-800"><?= $class['student_count'] ?>/<?= $class['capacity'] ?></div>
                                            </div>
                                            <div class="bg-grey-50 p-3 rounded-lg">
                                                <div class="text-xs text-grey-500 mb-1">Capacity</div>
                                                <div class="text-sm font-medium text-grey-800"><?= $class['capacity'] ?></div>
                                            </div>
                                            <div class="bg-grey-50 p-3 rounded-lg">
                                                <div class="text-xs text-grey-500 mb-1">Subjects</div>
                                                <div class="text-sm font-medium text-grey-800"><?= $class['subject_count'] ?></div>
                                            </div>
                                            <div class="bg-grey-50 p-3 rounded-lg">
                                                <div class="text-xs text-grey-500 mb-1">Created</div>
                                                <div class="text-sm font-medium text-grey-800"><?= htmlspecialchars(date('M d, Y', strtotime($class['created_at']))) ?></div>
                                            </div>
                                        </div>

                                        <!-- Teachers List -->
                                        <?php if (isset($class_teachers[$class['id']])): ?>
                                        <div class="bg-grey-50 p-3 rounded-lg mb-4">
                                            <div class="text-xs text-grey-500 mb-2">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                Assigned Teachers:
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach ($class_teachers[$class['id']] as $teacher): ?>
                                                <div class="flex items-center bg-white px-2 py-1 rounded border border-grey-200">
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs mr-1"><?= htmlspecialchars($teacher['subject_name']) ?></span>
                                                    <span class="text-xs text-grey-700"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Student Capacity Progress -->
                                        <div class="mb-4">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-xs text-grey-500">Student Capacity</span>
                                                <span class="text-xs text-grey-500">
                                                    <?= $class['capacity'] > 0 ? round(($class['student_count'] / $class['capacity']) * 100) : 0 ?>%
                                                </span>
                                            </div>
                                            <div class="h-1.5 bg-grey-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-green-500 rounded-full" style="width: <?= $class['capacity'] > 0 ? ($class['student_count'] / $class['capacity']) * 100 : 0 ?>%"></div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <a href="class_details.php?id=<?= $class['id'] ?>" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm flex items-center">
                                                <i class="fas fa-eye mr-1"></i>
                                                View Details
                                            </a>
                                            <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm flex items-center">
                                                <i class="fas fa-users mr-1"></i>
                                                Students (<?= $class['student_count'] ?>)
                                            </a>
                                            <a href="assignteacher.php?class_id=<?= $class['id'] ?>" class="px-3 py-1.5 bg-purple-600 text-white rounded-lg text-sm flex items-center">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                Teachers
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <script>
        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownButtons = document.querySelectorAll('.dropdown button');
            
            dropdownButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const menu = this.nextElementSibling;
                    menu.classList.toggle('hidden');
                    
                    // Close other dropdowns
                    dropdownButtons.forEach(otherButton => {
                        if (otherButton !== button) {
                            otherButton.nextElementSibling.classList.add('hidden');
                        }
                    });
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            });
            
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const className = document.querySelector('input[name="class_name"]').value.trim();
                const classLevel = document.querySelector('select[name="class_level"]').value;
                const capacity = document.querySelector('input[name="capacity"]').value;

                if (!className || !classLevel || !capacity) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                if (parseInt(capacity) < 1 || parseInt(capacity) > 100) {
                    e.preventDefault();
                    alert('Class capacity must be between 1 and 100.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
