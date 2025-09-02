<?php
// Include necessary files
include_once '../App/Models/headoffice/Class.php';
include '../include/buffer.php';
include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create & Manage Classes - School LMS</title>
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
    <style>
        /* Enhanced mobile-friendly form styles */
        .form-input, .form-select {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-white text-gray-900 text-base;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            min-height: 48px; /* Better touch targets on mobile */
        }
        
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px 12px;
            padding-right: 40px;
        }
        
        .form-label {
            @apply block text-sm font-semibold text-gray-700 mb-2;
        }
        
        .form-textarea {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-white text-gray-900 text-base resize-none;
            min-height: 48px;
        }
        
        /* Mobile-optimized buttons */
        .btn {
            @apply px-4 py-3 rounded-lg font-semibold text-sm transition-all duration-200 inline-flex items-center justify-center;
            min-height: 48px;
        }
        
        .btn-primary {
            @apply bg-primary text-white hover:bg-primary-dark shadow-sm;
        }
        
        .btn-secondary {
            @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
        }
        
        .btn-success {
            @apply bg-green-500 text-white hover:bg-green-600;
        }
        
        .btn-danger {
            @apply bg-red-500 text-white hover:bg-red-600;
        }
        
        .btn-sm {
            @apply px-3 py-2 text-xs;
            min-height: 36px;
        }
        
        /* Mobile dropdown improvements */
        @media (max-width: 768px) {
            .dropdown-menu {
                @apply left-0 right-0 w-auto mx-4;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-inter">

<!-- Main Content -->
<main class="p-4 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2 flex items-center">
            <svg class="w-8 h-8 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Class Management
        </h1>
        <p class="text-gray-600">Create and manage classes for your school</p>
    </div>

    <!-- Alert Messages -->
    <?= $msg ?>

    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1"><?= count($existing_classes) ?></p>
            <p class="text-sm text-gray-500">Total Classes</p>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1"><?= count(array_filter($existing_classes, fn($c) => $c['is_active'])) ?></p>
            <p class="text-sm text-gray-500">Active Classes</p>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1"><?= array_sum(array_column($existing_classes, 'student_count')) ?></p>
            <p class="text-sm text-gray-500">Total Students</p>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1"><?= count($teachers) ?></p>
            <p class="text-sm text-gray-500">Available Teachers</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8">
        <!-- Create Class Form -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-primary p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Class
                    </h2>
                </div>
                <div class="p-6">
                    <form method="post" class="space-y-6">
                        <div>
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">-- Select Academic Year --</option>
                                <?php foreach ($academic_years as $year): ?>
                                <option value="<?= $year['id'] ?>" <?= $year['is_current'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year['year_name']) ?>
                                    <?= $year['is_current'] ? ' (Current)' : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="form-label">Class Name</label>
                                <input type="text" name="class_name" class="form-input" required placeholder="e.g., Class 1, Grade 5">
                            </div>
                            <div>
                                <label class="form-label">Section</label>
                                <input type="text" name="section" class="form-input" value="A" required placeholder="A, B, C">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Class Level</label>
                                <select name="class_level" class="form-select" required>
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
                            <textarea name="description" class="form-textarea" rows="3" placeholder="Additional information about the class"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Class
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Classes -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Existing Classes (<?= count($existing_classes) ?>)
                    </h2>
                </div>
                <div class="p-6">
                    <?php if (empty($existing_classes)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h5 class="text-gray-500 text-lg font-medium mb-2">No classes created yet</h5>
                        <p class="text-gray-400">Create your first class using the form on the left.</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($existing_classes as $class): ?>
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="p-4 lg:p-6 border-b border-gray-100 bg-gray-50">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                                    <div class="flex-1">
                                        <h6 class="text-lg lg:text-xl font-semibold text-gray-900 mb-3">
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
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        <div id="dropdown-<?= $class['id'] ?>" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200 dropdown-menu">
                                            <a href="class_details.php?id=<?= $class['id'] ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 transition-colors">
                                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View Details
                                            </a>
                                            <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 transition-colors">
                                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                                Manage Students
                                            </a>
                                            <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 transition-colors">
                                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                Assign Teachers
                                            </a>
                                            <hr class="my-1 border-gray-200">
                                            <button onclick="deleteClass(<?= $class['id'] ?>)" class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete Class
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 lg:p-6">
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="text-xs text-gray-500 mb-1">Students</div>
                                        <div class="text-sm font-semibold text-gray-900"><?= $class['student_count'] ?>/<?= $class['capacity'] ?></div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="text-xs text-gray-500 mb-1">Capacity</div>
                                        <div class="text-sm font-semibold text-gray-900"><?= $class['capacity'] ?></div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="text-xs text-gray-500 mb-1">Subjects</div>
                                        <div class="text-sm font-semibold text-gray-900"><?= $class['subject_count'] ?></div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="text-xs text-gray-500 mb-1">Created</div>
                                        <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars(date('M d, Y', strtotime($class['created_at']))) ?></div>
                                    </div>
                                </div>

                                <!-- Teachers List -->
                                <?php if (isset($class_teachers[$class['id']])): ?>
                                <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
                                    <div class="text-sm text-blue-700 mb-3 font-semibold flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
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
                                <div class="mb-6">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-sm text-gray-600 font-semibold">Student Capacity</span>
                                        <span class="text-sm text-gray-500 font-medium">
                                            <?= $class['capacity'] > 0 ? round(($class['student_count'] / $class['capacity']) * 100) : 0 ?>%
                                        </span>
                                    </div>
                                    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-500" style="width: <?= $class['capacity'] > 0 ? ($class['student_count'] / $class['capacity']) * 100 : 0 ?>%"></div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <a href="class_details.php?id=<?= $class['id'] ?>" class="btn btn-primary btn-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="btn btn-success btn-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Students (<?= $class['student_count'] ?>)
                                    </a>
                                    <a href="assign_teachers.php?class_id=<?= $class['id'] ?>" class="btn btn-secondary btn-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="deleteModalContent">
        <div class="bg-red-500 text-white p-6 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Delete Class
                </h3>
                <button onclick="closeDeleteModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <p class="text-gray-700 mb-2 font-medium">Are you sure you want to delete this class?</p>
                <p class="text-sm text-gray-500">This action cannot be undone and will remove all associated data.</p>
            </div>

            <form method="post" id="deleteForm">
                <input type="hidden" name="class_id" id="deleteClassId">
                <input type="hidden" name="delete_class" value="1">

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary flex-1">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger flex-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteModalContent');

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
    const cards = document.querySelectorAll('.space-y-6 > div');
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