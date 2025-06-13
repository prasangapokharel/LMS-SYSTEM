<?php 
// Include necessary files
include_once '../App/Models/headoffice/User.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - School LMS</title>
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
                    <i class="fas fa-users-cog mr-3"></i>
                    User Management
                </h1>
                <p class="text-blue-100">Manage students, teachers, and system users</p>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['total_users'] ?></p>
                    <p class="text-sm text-grey-500">Total Users</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <i class="fas fa-user-graduate text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['total_students'] ?></p>
                    <p class="text-sm text-grey-500">Students</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['total_teachers'] ?></p>
                    <p class="text-sm text-grey-500">Teachers</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['active_users'] ?></p>
                    <p class="text-sm text-grey-500">Active Users</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <a href="createusers.php?type=teacher" class="bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-xl shadow hover:shadow-md transition-shadow flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create New Teacher
                </a>
                <a href="createusers.php?type=student" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-xl shadow hover:shadow-md transition-shadow flex items-center justify-center">
                    <i class="fas fa-user-graduate mr-2"></i>
                    Create New Student
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow p-5 mb-6">
                <h3 class="text-lg font-semibold text-grey-800 mb-4">
                    <i class="fas fa-filter mr-2 text-blue-600"></i>
                    Filter Users
                </h3>
                <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-grey-700 mb-1">Role</label>
                        <select name="role" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="all" <?= $role_filter == 'all' ? 'selected' : '' ?>>All Roles</option>
                            <option value="principal" <?= $role_filter == 'principal' ? 'selected' : '' ?>>Principal</option>
                            <option value="teacher" <?= $role_filter == 'teacher' ? 'selected' : '' ?>>Teachers</option>
                            <option value="student" <?= $role_filter == 'student' ? 'selected' : '' ?>>Students</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status_filter == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-700 mb-1">Search</label>
                        <input type="text" name="search" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                               placeholder="Name, username, email, ID..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg shadow-sm transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 text-white">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-list mr-2"></i>
                        All Users (<?= count($all_users) ?> records)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-grey-50 text-grey-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Credentials</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Class</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grey-200">
                            <?php if (empty($all_users)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center">
                                    <i class="fas fa-users text-grey-300 text-5xl mb-3"></i>
                                    <p class="text-grey-500">No users found matching your criteria.</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($all_users as $user_record): ?>
                                <tr class="hover:bg-grey-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                                <?= strtoupper(substr($user_record['first_name'], 0, 1) . substr($user_record['last_name'], 0, 1)) ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-grey-900">
                                                    <?= htmlspecialchars($user_record['first_name'] . ' ' . $user_record['last_name']) ?>
                                                </div>
                                                <div class="text-sm text-grey-500">
                                                    <?= htmlspecialchars($user_record['email']) ?>
                                                </div>
                                                <div class="text-sm text-grey-500">
                                                    <?= htmlspecialchars($user_record['phone'] ?? 'No phone') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-grey-900 font-mono bg-grey-100 px-2 py-1 rounded">
                                            <?= htmlspecialchars($user_record['username']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php
                                            switch ($user_record['role_name']) {
                                                case 'principal':
                                                    echo 'bg-purple-100 text-purple-800';
                                                    break;
                                                case 'teacher':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'student':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                default:
                                                    echo 'bg-grey-100 text-grey-800';
                                            }
                                            ?>">
                                            <?= ucfirst(htmlspecialchars($user_record['role_name'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-grey-500">
                                        <?= $user_record['student_id'] ? htmlspecialchars($user_record['student_id']) : '-' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-grey-500">
                                        <?php if ($user_record['class_name']): ?>
                                            <?= htmlspecialchars($user_record['class_name'] . ' ' . $user_record['section']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $user_record['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= $user_record['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-900" data-bs-toggle="modal" data-bs-target="#viewUserModal<?= $user_record['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-indigo-600 hover:text-indigo-900" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?= $user_record['id'] ?>">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <form method="post" class="inline" onsubmit="return confirm('Are you sure you want to <?= $user_record['is_active'] ? 'deactivate' : 'activate' ?> this user?');">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?= $user_record['id'] ?>">
                                                <input type="hidden" name="new_status" value="<?= $user_record['is_active'] ? '0' : '1' ?>">
                                                <button type="submit" class="<?= $user_record['is_active'] ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' ?>">
                                                    <i class="fas <?= $user_record['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Reset Password Modals -->
    <?php foreach ($all_users as $user_record): ?>
    <div class="modal fade" id="resetPasswordModal<?= $user_record['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-key mr-2"></i>
                        Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4">Are you sure you want to reset the password for <strong><?= htmlspecialchars($user_record['first_name'] . ' ' . $user_record['last_name']) ?></strong>?</p>
                    <p class="text-sm text-grey-600 mb-4">A new random password will be generated.</p>
                    
                    <form method="post">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="user_id" value="<?= $user_record['id'] ?>">
                        
                        <div class="flex justify-end">
                            <button type="button" class="bg-grey-300 hover:bg-grey-400 text-grey-800 py-2 px-4 rounded-lg mr-2" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg">
                                <i class="fas fa-key mr-2"></i>
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- View User Modals -->
    <?php foreach ($all_users as $user_record): ?>
    <div class="modal fade" id="viewUserModal<?= $user_record['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user mr-2"></i>
                        User Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                            <?= strtoupper(substr($user_record['first_name'], 0, 1) . substr($user_record['last_name'], 0, 1)) ?>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-grey-800"><?= htmlspecialchars($user_record['first_name'] . ' ' . $user_record['last_name']) ?></h3>
                            <p class="text-grey-600">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php
                                    switch ($user_record['role_name']) {
                                        case 'principal':
                                            echo 'bg-purple-100 text-purple-800';
                                            break;
                                        case 'teacher':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'student':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        default:
                                            echo 'bg-grey-100 text-grey-800';
                                    }
                                    ?>">
                                    <?= ucfirst(htmlspecialchars($user_record['role_name'])) ?>
                                </span>
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $user_record['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $user_record['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-grey-500 uppercase tracking-wider mb-3">Account Information</h4>
                            <div class="bg-grey-50 rounded-lg p-4">
                                <div class="grid grid-cols-3 gap-4 mb-2">
                                    <div class="text-sm font-medium text-grey-500">Username</div>
                                    <div class="col-span-2 text-sm font-mono bg-white px-2 py-1 rounded border border-grey-200"><?= htmlspecialchars($user_record['username']) ?></div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-2">
                                    <div class="text-sm font-medium text-grey-500">Email</div>
                                    <div class="col-span-2 text-sm"><?= htmlspecialchars($user_record['email']) ?></div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-2">
                                    <div class="text-sm font-medium text-grey-500">Phone</div>
                                    <div class="col-span-2 text-sm"><?= htmlspecialchars($user_record['phone'] ?? 'Not provided') ?></div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-sm font-medium text-grey-500">Created</div>
                                    <div class="col-span-2 text-sm"><?= date('M d, Y', strtotime($user_record['created_at'])) ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-semibold text-grey-500 uppercase tracking-wider mb-3">Additional Information</h4>
                            <div class="bg-grey-50 rounded-lg p-4">
                                <?php if ($user_record['role_name'] == 'student'): ?>
                                <div class="grid grid-cols-3 gap-4 mb-2">
                                    <div class="text-sm font-medium text-grey-500">Student ID</div>
                                    <div class="col-span-2 text-sm font-mono bg-white px-2 py-1 rounded border border-grey-200"><?= htmlspecialchars($user_record['student_id']) ?></div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 mb-2">
                                    <div class="text-sm font-medium text-grey-500">Class</div>
                                    <div class="col-span-2 text-sm"><?= htmlspecialchars($user_record['class_name'] . ' ' . $user_record['section']) ?></div>
                                </div>
                                <?php endif; ?>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-sm font-medium text-grey-500">Address</div>
                                    <div class="col-span-2 text-sm"><?= htmlspecialchars($user_record['address'] ?? 'Not provided') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="bg-grey-300 hover:bg-grey-400 text-grey-800 py-2 px-4 rounded-lg" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            var modals = [].slice.call(document.querySelectorAll('.modal'));
            modals.map(function(modal) {
                var myModal = new bootstrap.Modal(modal);
                
                // Show modal if it's in the URL hash
                if (window.location.hash === '#' + modal.id) {
                    myModal.show();
                }
                
                // Handle modal triggers
                document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#' + modal.id + '"]').forEach(function(trigger) {
                    trigger.addEventListener('click', function() {
                        myModal.show();
                    });
                });
            });
        });
    </script>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
</body>
</html>
