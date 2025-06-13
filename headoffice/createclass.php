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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-grey-50">
    <!-- Main Content -->
    <main class="main-content">
        <div class="p-4 lg:p-6">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl p-6 text-white shadow-lg mb-6">
                <h1 class="text-2xl md:text-3xl font-bold mb-2">
                    <i class="fas fa-school me-3"></i>
                    Class Management
                </h1>
                <p class="text-blue-100">Create and manage classes for your school</p>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-full flex items-center justify-center text-white text-xl mx-auto mb-3">
                        <i class="fas fa-school"></i>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= count($existing_classes) ?></p>
                    <p class="text-sm text-grey-500">Total Classes</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-xl mx-auto mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= count(array_filter($existing_classes, fn($c) => $c['is_active'])) ?></p>
                    <p class="text-sm text-grey-500">Active Classes</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full flex items-center justify-center text-white text-xl mx-auto mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= array_sum(array_column($existing_classes, 'student_count')) ?></p>
                    <p class="text-sm text-grey-500">Total Students</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-amber-500 to-amber-600 rounded-full flex items-center justify-center text-white text-xl mx-auto mb-3">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= count($teachers) ?></p>
                    <p class="text-sm text-grey-500">Available Teachers</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Create Class Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 text-white">
                            <h3 class="text-lg font-semibold">
                                <i class="fas fa-plus-circle me-2"></i>
                                Create New Class
                            </h3>
                        </div>
                        <div class="p-6">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label text-sm font-medium text-grey-700">Academic Year</label>
                                    <select name="academic_year_id" class="form-select rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 w-full" required>
                                        <option value="">-- Select Academic Year --</option>
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?= $year['id'] ?>" <?= $year['is_current'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($year['year_name']) ?>
                                            <?= $year['is_current'] ? ' (Current)' : '' ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <div class="md:col-span-2">
                                        <label class="form-label text-sm font-medium text-grey-700">Class Name</label>
                                        <input type="text" name="class_name" class="form-control rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required placeholder="e.g., Class 1, Grade 5">
                                    </div>
                                    <div>
                                        <label class="form-label text-sm font-medium text-grey-700">Section</label>
                                        <input type="text" name="section" class="form-control rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="A" required placeholder="A, B, C">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="form-label text-sm font-medium text-grey-700">Class Level</label>
                                        <select name="class_level" class="form-select rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 w-full" required>
                                            <option value="">-- Select Level --</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>">Level <?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label text-sm font-medium text-grey-700">Capacity</label>
                                        <input type="number" name="capacity" class="form-control rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="40" required min="1" max="100">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-sm font-medium text-grey-700">Description (Optional)</label>
                                    <textarea name="description" class="form-control rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="3" placeholder="Additional information about the class"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-2 px-4 rounded-lg shadow-sm hover:shadow-md transition-colors">
                                    <i class="fas fa-plus me-2"></i>
                                    Create Class
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Existing Classes -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 text-white">
                            <h3 class="text-lg font-semibold">
                                <i class="fas fa-list me-2"></i>
                                Existing Classes (<?= count($existing_classes) ?>)
                            </h3>
                        </div>
                        <div class="p-6">
                            <?php if (empty($existing_classes)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-school fa-4x text-grey-300 mb-3"></i>
                                <p class="text-grey-500 text-lg">No classes created yet</p>
                                <p class="text-grey-400 text-sm">Create your first class using the form on the left.</p>
                            </div>
                            <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($existing_classes as $class): ?>
                                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow border-l-4 
                                    <?php 
                                    switch ($class['class_level'] % 5) {
                                        case 1: echo 'border-blue-500'; break;
                                        case 2: echo 'border-green-500'; break;
                                        case 3: echo 'border-purple-500'; break;
                                        case 4: echo 'border-red-500'; break;
                                        case 0: echo 'border-indigo-500'; break;
                                    }
                                    ?>">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                        <div>
                                            <h4 class="text-lg font-semibold text-grey-800 mb-1">
                                                <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                                            </h4>
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Level <?= $class['class_level'] ?></span>
                                                <span class="text-sm text-grey-500"><?= htmlspecialchars($class['year_name']) ?></span>
                                                <?php if ($class['is_active']): ?>
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Active</span>
                                                <?php else: ?>
                                                <span class="px-2 py-1 bg-grey-100 text-grey-800 text-xs font-medium rounded-full">Inactive</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="mt-3 md:mt-0">
                                            <div class="dropdown">
                                                <button class="bg-grey-100 hover:bg-grey-200 text-grey-700 py-1 px-2 rounded-lg" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="class_details.php?id=<?= $class['id'] ?>">
                                                        <i class="fas fa-eye me-2"></i>View Details
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="manage_students.php?class_id=<?= $class['id'] ?>">
                                                        <i class="fas fa-users me-2"></i>Manage Students
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="assign_teachers.php?class_id=<?= $class['id'] ?>">
                                                        <i class="fas fa-chalkboard-teacher me-2"></i>Assign Teachers
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this class? This action cannot be undone.')">
                                                            <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                                            <button type="submit" name="delete_class" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>Delete Class
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                        <div class="bg-grey-50 p-3 rounded-lg text-center">
                                            <div class="text-sm text-grey-500 mb-1">Students</div>
                                            <div class="text-lg font-semibold text-grey-800"><?= $class['student_count'] ?>/<?= $class['capacity'] ?></div>
                                        </div>
                                        <div class="bg-grey-50 p-3 rounded-lg text-center">
                                            <div class="text-sm text-grey-500 mb-1">Capacity</div>
                                            <div class="text-lg font-semibold text-grey-800"><?= $class['capacity'] ?></div>
                                        </div>
                                        <div class="bg-grey-50 p-3 rounded-lg text-center">
                                            <div class="text-sm text-grey-500 mb-1">Subjects</div>
                                            <div class="text-lg font-semibold text-grey-800"><?= $class['subject_count'] ?></div>
                                        </div>
                                        <div class="bg-grey-50 p-3 rounded-lg text-center">
                                            <div class="text-sm text-grey-500 mb-1">Created</div>
                                            <div class="text-lg font-semibold text-grey-800"><?= htmlspecialchars(date('M d', strtotime($class['created_at']))) ?></div>
                                        </div>
                                    </div>

                                    <!-- Teachers List -->
                                    <?php if (isset($class_teachers[$class['id']])): ?>
                                    <div class="bg-grey-50 p-3 rounded-lg mb-4">
                                        <div class="text-sm text-grey-500 mb-2">
                                            <i class="fas fa-chalkboard-teacher me-1"></i>
                                            Assigned Teachers:
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($class_teachers[$class['id']] as $teacher): ?>
                                            <div class="flex items-center bg-white px-2 py-1 rounded-lg border border-grey-200">
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full me-1"><?= htmlspecialchars($teacher['subject_name']) ?></span>
                                                <span class="text-sm"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></span>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Student Capacity Progress -->
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm text-grey-500">Student Capacity</span>
                                            <span class="text-sm text-grey-500">
                                                <?= $class['capacity'] > 0 ? round(($class['student_count'] / $class['capacity']) * 100) : 0 ?>%
                                            </span>
                                        </div>
                                        <div class="w-full bg-grey-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full"
                                                style="width: <?= $class['capacity'] > 0 ? ($class['student_count'] / $class['capacity']) * 100 : 0 ?>%"></div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="class_details.php?id=<?= $class['id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-lg text-sm flex items-center">
                                            <i class="fas fa-eye me-1"></i>
                                            View Details
                                        </a>
                                        <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded-lg text-sm flex items-center">
                                            <i class="fas fa-users me-1"></i>
                                            Students (<?= $class['student_count'] ?>)
                                        </a>
                                        <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="bg-amber-600 hover:bg-amber-700 text-white py-1 px-3 rounded-lg text-sm flex items-center">
                                            <i class="fas fa-chalkboard-teacher me-1"></i>
                                            Teachers
                                        </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

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
    </script>
    
    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
</body>
</html>
