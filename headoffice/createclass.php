<?php 
// Include necessary files
include_once '../App/Models/headoffice/Class.php';
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create & Manage Classes - School LMS</title>
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
                    <p class="text-2xl font-bold text-gray-800"><?= count($existing_classes) ?></p>
                    <p class="text-sm text-gray-500">Total Classes</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= count(array_filter($existing_classes, fn($c) => $c['is_active'])) ?></p>
                    <p class="text-sm text-gray-500">Active Classes</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= array_sum(array_column($existing_classes, 'student_count')) ?></p>
                    <p class="text-sm text-gray-500">Total Students</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= count($teachers) ?></p>
                    <p class="text-sm text-gray-500">Available Teachers</p>
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
                            <form method="post" class="space-y-4">
                                <div>
                                    <label class="form-label">Academic Year</label>
                                    <select name="academic_year_id" class="form-input" required>
                                        <option value="">-- Select Academic Year --</option>
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?= $year['id'] ?>" <?= $year['is_current'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($year['year_name']) ?>
                                            <?= $year['is_current'] ? ' (Current)' : '' ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-2">
                                        <label class="form-label">Class Name</label>
                                        <input type="text" name="class_name" class="form-input" required placeholder="e.g., Class 1, Grade 5">
                                    </div>
                                    <div>
                                        <label class="form-label">Section</label>
                                        <input type="text" name="section" class="form-input" value="A" required placeholder="A, B, C">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="form-label">Class Level</label>
                                        <select name="class_level" class="form-input" required>
                                            <option value="">-- Select Level --</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>">Level <?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Capacity</label>
                                        <input type="number" name="capacity" class="form-input" value="40" required min="1" max="100">
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Description (Optional)</label>
                                    <textarea name="description" class="form-input" rows="3" placeholder="Additional information about the class"></textarea>
                                </div>

                                <button type="submit" class="btn btn1 w-full">
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
                                <i class="fas fa-school fa-4x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-500 mb-1">No classes created yet</h5>
                                <p class="text-gray-400">Create your first class using the form on the left.</p>
                            </div>
                            <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($existing_classes as $class): ?>
                                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h6 class="text-lg font-semibold text-gray-800 mb-2">
                                                    <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                                                </h6>
                                                <div class="flex flex-wrap gap-2 items-center">
                                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Level <?= $class['class_level'] ?></span>
                                                    <span class="text-gray-500 text-sm"><?= htmlspecialchars($class['year_name']) ?></span>
                                                    <?php if ($class['is_active']): ?>
                                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                                                    <?php else: ?>
                                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Inactive</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <button onclick="toggleDropdown(<?= $class['id'] ?>)" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div id="dropdown-<?= $class['id'] ?>" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                                    <a href="class_details.php?id=<?= $class['id'] ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-eye mr-2"></i>View Details
                                                    </a>
                                                    <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-users mr-2"></i>Manage Students
                                                    </a>
                                                    <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-chalkboard-teacher mr-2"></i>Assign Teachers
                                                    </a>
                                                    <hr class="my-1 border-gray-200">
                                                    <button onclick="deleteClass(<?= $class['id'] ?>)" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                                                        <i class="fas fa-trash mr-2"></i>Delete Class
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <div class="text-xs text-gray-500 mb-1">Students</div>
                                                <div class="text-sm font-medium text-gray-800"><?= $class['student_count'] ?>/<?= $class['capacity'] ?></div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <div class="text-xs text-gray-500 mb-1">Capacity</div>
                                                <div class="text-sm font-medium text-gray-800"><?= $class['capacity'] ?></div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <div class="text-xs text-gray-500 mb-1">Subjects</div>
                                                <div class="text-sm font-medium text-gray-800"><?= $class['subject_count'] ?></div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <div class="text-xs text-gray-500 mb-1">Created</div>
                                                <div class="text-sm font-medium text-gray-800"><?= htmlspecialchars(date('M d, Y', strtotime($class['created_at']))) ?></div>
                                            </div>
                                        </div>

                                        <!-- Teachers List -->
                                        <?php if (isset($class_teachers[$class['id']])): ?>
                                        <div class="bg-blue-50 p-4 rounded-lg mb-4 border border-blue-200">
                                            <div class="text-sm text-blue-700 mb-3 font-medium">
                                                <i class="fas fa-chalkboard-teacher mr-2"></i>
                                                Assigned Teachers:
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach ($class_teachers[$class['id']] as $teacher): ?>
                                                <div class="flex items-center bg-white px-3 py-2 rounded-lg border border-blue-200 shadow-sm">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium mr-2"><?= htmlspecialchars($teacher['subject_name']) ?></span>
                                                    <span class="text-sm text-gray-700 font-medium"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Student Capacity Progress -->
                                        <div class="mb-4">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm text-gray-600 font-medium">Student Capacity</span>
                                                <span class="text-sm text-gray-500">
                                                    <?= $class['capacity'] > 0 ? round(($class['student_count'] / $class['capacity']) * 100) : 0 ?>%
                                                </span>
                                            </div>
                                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-300" style="width: <?= $class['capacity'] > 0 ? ($class['student_count'] / $class['capacity']) * 100 : 0 ?>%"></div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <a href="class_details.php?id=<?= $class['id'] ?>" class="btn btn1 btn-sm">
                                                <i class="fas fa-eye mr-1"></i>
                                                View Details
                                            </a>
                                            <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="btn btn3 btn-sm">
                                                <i class="fas fa-users mr-1"></i>
                                                Students (<?= $class['student_count'] ?>)
                                            </a>
                                            <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="btn btn2 btn-sm">
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
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95" id="deleteModalContent">
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Delete Class
                    </h3>
                    <button onclick="closeDeleteModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-700 mb-2">Are you sure you want to delete this class?</p>
                    <p class="text-sm text-gray-500">This action cannot be undone and will remove all associated data.</p>
                </div>
                
                <form method="post" id="deleteForm">
                    <input type="hidden" name="class_id" id="deleteClassId">
                    <input type="hidden" name="delete_class" value="1">
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeDeleteModal()" class="btn btn2 flex-1">
                            Cancel
                        </button>
                        <button type="submit" class="btn flex-1" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <script>
        // Dropdown functionality
        function toggleDropdown(classId) {
            const dropdown = document.getElementById(`dropdown-${classId}`);
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            
            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== `dropdown-${classId}`) {
                    d.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick^="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // Delete class functionality
        function deleteClass(classId) {
            document.getElementById('deleteClassId').value = classId;
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
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

        // Add smooth animations to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.space-y-4 > div');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
