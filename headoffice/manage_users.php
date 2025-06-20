<?php 
// Include necessary files
include_once '../App/Models/headoffice/User.php';
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - School LMS</title>
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
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total_users'] ?></p>
                    <p class="text-sm text-gray-500">Total Users</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <i class="fas fa-user-graduate text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total_students'] ?></p>
                    <p class="text-sm text-gray-500">Students</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total_teachers'] ?></p>
                    <p class="text-sm text-gray-500">Teachers</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['active_users'] ?></p>
                    <p class="text-sm text-gray-500">Active Users</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <a href="createusers.php?type=teacher" class="btn btn3">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create New Teacher
                </a>
                <a href="createusers.php?type=student" class="btn btn1">
                    <i class="fas fa-user-graduate mr-2"></i>
                    Create New Student
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow p-5 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-filter mr-2 text-blue-600"></i>
                    Filter Users
                </h3>
                <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label">Role</label>
                        <select name="role" class="form-input">
                            <option value="all" <?= $role_filter == 'all' ? 'selected' : '' ?>>All Roles</option>
                            <option value="principal" <?= $role_filter == 'principal' ? 'selected' : '' ?>>Principal</option>
                            <option value="teacher" <?= $role_filter == 'teacher' ? 'selected' : '' ?>>Teachers</option>
                            <option value="student" <?= $role_filter == 'student' ? 'selected' : '' ?>>Students</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status_filter == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-input" 
                               placeholder="Name, username, email, ID..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn1 w-full">
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
                        <thead class="bg-gray-50 text-gray-700">
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
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($all_users)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center">
                                    <i class="fas fa-users text-gray-300 text-5xl mb-3"></i>
                                    <p class="text-gray-500">No users found matching your criteria.</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($all_users as $user_record): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                                <?= strtoupper(substr($user_record['first_name'], 0, 1) . substr($user_record['last_name'], 0, 1)) ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($user_record['first_name'] . ' ' . $user_record['last_name']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($user_record['email']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($user_record['phone'] ?? 'No phone') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">
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
                                                    echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?= ucfirst(htmlspecialchars($user_record['role_name'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $user_record['student_id'] ? htmlspecialchars($user_record['student_id']) : '-' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
                                        <div class="flex space-x-3">
                                            <button onclick="openViewModal(<?= $user_record['id'] ?>)" class="text-blue-600 hover:text-blue-900 transition-colors p-1 hover:bg-blue-50 rounded">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="openResetPasswordModal(<?= $user_record['id'] ?>, '<?= htmlspecialchars($user_record['first_name'] . ' ' . $user_record['last_name']) ?>')" class="text-indigo-600 hover:text-indigo-900 transition-colors p-1 hover:bg-indigo-50 rounded">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <form method="post" class="inline" onsubmit="return confirm('Are you sure you want to <?= $user_record['is_active'] ? 'deactivate' : 'activate' ?> this user?');">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?= $user_record['id'] ?>">
                                                <input type="hidden" name="new_status" value="<?= $user_record['is_active'] ? '0' : '1' ?>">
                                                <button type="submit" class="<?= $user_record['is_active'] ? 'text-red-600 hover:text-red-900 hover:bg-red-50' : 'text-green-600 hover:text-green-900 hover:bg-green-50' ?> transition-colors p-1 rounded">
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
        </main>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95" id="resetPasswordModalContent">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-key mr-2"></i>
                        Reset Password
                    </h3>
                    <button onclick="closeResetPasswordModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-key text-indigo-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-700 mb-2">Are you sure you want to reset the password for</p>
                    <p class="font-semibold text-gray-900" id="resetUserName">User Name</p>
                    <p class="text-sm text-gray-500 mt-2">A new random password will be generated and displayed.</p>
                </div>
                
                <form method="post" id="resetPasswordForm">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" id="resetUserId">
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeResetPasswordModal()" class="btn btn2 flex-1">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn1 flex-1">
                            <i class="fas fa-key mr-2"></i>
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div id="viewUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95" id="viewUserModalContent">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-user mr-2"></i>
                        User Details
                    </h3>
                    <button onclick="closeViewModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" id="viewUserContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <script>
        // Modal functionality
        function openResetPasswordModal(userId, userName) {
            document.getElementById('resetUserId').value = userId;
            document.getElementById('resetUserName').textContent = userName;
            const modal = document.getElementById('resetPasswordModal');
            const content = document.getElementById('resetPasswordModalContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeResetPasswordModal() {
            const modal = document.getElementById('resetPasswordModal');
            const content = document.getElementById('resetPasswordModalContent');
            
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function openViewModal(userId) {
            // Find user data from PHP array
            const users = <?= json_encode($all_users) ?>;
            const user = users.find(u => u.id == userId);
            
            if (!user) return;
            
            const content = document.getElementById('viewUserContent');
            content.innerHTML = `
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                        ${user.first_name.charAt(0).toUpperCase()}${user.last_name.charAt(0).toUpperCase()}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-semibold text-gray-800">${user.first_name} ${user.last_name}</h3>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${getRoleBadgeClass(user.role_name)}">
                                ${user.role_name.charAt(0).toUpperCase() + user.role_name.slice(1)}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${user.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Account Information</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Username</span>
                                <span class="text-sm font-mono bg-white px-2 py-1 rounded border">${user.username}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Email</span>
                                <span class="text-sm">${user.email}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Phone</span>
                                <span class="text-sm">${user.phone || 'Not provided'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Created</span>
                                <span class="text-sm">${new Date(user.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Additional Information</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            ${user.role_name === 'student' ? `
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Student ID</span>
                                    <span class="text-sm font-mono bg-white px-2 py-1 rounded border">${user.student_id}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Class</span>
                                    <span class="text-sm">${user.class_name} ${user.section}</span>
                                </div>
                            ` : ''}
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Address</span>
                                <span class="text-sm">${user.address || 'Not provided'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button onclick="closeViewModal()" class="btn btn2">
                        Close
                    </button>
                </div>
            `;
            
            const modal = document.getElementById('viewUserModal');
            const modalContent = document.getElementById('viewUserModalContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('opacity-100');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeViewModal() {
            const modal = document.getElementById('viewUserModal');
            const content = document.getElementById('viewUserModalContent');
            
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function getRoleBadgeClass(role) {
            switch(role) {
                case 'principal': return 'bg-purple-100 text-purple-800';
                case 'teacher': return 'bg-green-100 text-green-800';
                case 'student': return 'bg-blue-100 text-blue-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        // Close modals when clicking outside
        document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeResetPasswordModal();
            }
        });

        document.getElementById('viewUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeResetPasswordModal();
                closeViewModal();
            }
        });
    </script>
</body>
</html>
