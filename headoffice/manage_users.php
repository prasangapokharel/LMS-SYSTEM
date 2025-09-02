<?php
// Include necessary files
include_once '../App/Models/headoffice/User.php';
include_once '../include/connect.php';
include_once '../include/session.php';

// Ensure user has principal role
requireRole('principal');
$current_user = getCurrentUser($pdo);

$msg = "";
$error = "";

// Initialize HeadOfficeUser class
$userManager = new HeadOfficeUser($pdo);

// Get filter parameters
$filters = [
    'role' => $_GET['role'] ?? 'all',
    'status' => $_GET['status'] ?? 'all',
    'search' => trim($_GET['search'] ?? '')
];

$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'reset_password':
                    $user_id = intval($_POST['user_id']);
                    $result = $userManager->resetPassword($user_id);
                    
                    if ($result['success']) {
                        $msg = "<div class='bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg'>
                                    <div class='flex items-start'>
                                        <div class='text-green-500 mr-3'>
                                            <i class='fas fa-check-circle'></i>
                                        </div>
                                        <div>
                                            <h3 class='text-green-800 font-medium'>Password Reset Successfully!</h3>
                                            <p class='text-green-700'>New Password: <strong class='font-mono bg-green-100 px-2 py-1 rounded'>{$result['new_password']}</strong></p>
                                            <p class='text-green-600 text-sm mt-1'>Please share this password with the user securely.</p>
                                        </div>
                                    </div>
                                </div>";
                    } else {
                        throw new Exception($result['message']);
                    }
                    break;
                    
                case 'toggle_status':
                    $user_id = intval($_POST['user_id']);
                    $new_status = intval($_POST['new_status']);
                    $result = $userManager->toggleUserStatus($user_id, $new_status);
                    
                    if ($result['success']) {
                        $status_text = $new_status ? 'activated' : 'deactivated';
                        $msg = "<div class='bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg'>
                                    <div class='flex items-start'>
                                        <div class='text-green-500 mr-3'>
                                            <i class='fas fa-check-circle'></i>
                                        </div>
                                        <div>
                                            <h3 class='text-green-800 font-medium'>User {$status_text} successfully!</h3>
                                        </div>
                                    </div>
                                </div>";
                    } else {
                        throw new Exception($result['message']);
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $error = "<div class='bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg'>
                    <div class='flex items-start'>
                        <div class='text-red-500 mr-3'>
                            <i class='fas fa-exclamation-circle'></i>
                        </div>
                        <div>
                            <h3 class='text-red-800 font-medium'>Error!</h3>
                            <p class='text-red-700'>" . $e->getMessage() . "</p>
                        </div>
                    </div>
                </div>";
    }
}

// Get user statistics and data using the model
$stats = $userManager->getUserStats();
$user_data = $userManager->getUsers($filters, $page, $per_page);
$all_users = $user_data['users'];
$total_users = $user_data['total_users'];
$total_pages = $user_data['total_pages'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        'primary-dark': '#2563eb',
                        'primary-light': '#dbeafe'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .excel-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .excel-table th {
            background: gray;
            color: white;
            font-weight: 600;
            padding: 12px 8px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        .excel-table th:last-child {
            border-right: none;
        }
        .excel-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            font-size: 13px;
            vertical-align: top;
        }
        .excel-table td:last-child {
            border-right: none;
        }
        .excel-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .excel-table tbody tr:last-child td {
            border-bottom: none;
        }
        .cell-content {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .role-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <div class="p-6">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-primary to-primary-dark rounded-xl p-6 text-white shadow-lg mb-6">
                <h1 class="text-2xl lg:text-3xl font-bold mb-2 flex items-center">
                    <i class="fas fa-users-cog mr-3"></i>
                    User Management
                </h1>
                <p class="text-primary-light">Manage students, teachers, and system users</p>
                <nav class="mt-4">
                    <ol class="flex space-x-2 text-sm">
                        <li><a href="index.php" class="text-white hover:text-primary-light">Dashboard</a></li>
                        <li><span class="text-white opacity-70 mx-2">/</span></li>
                        <li class="text-white opacity-90">User Management</li>
                    </ol>
                </nav>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>
            <?= $error ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-primary text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_users'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Teachers</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_teachers'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_students'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['active_users'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <a href="createusers.php" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create New Teacher
                </a>
                <a href="createusers.php" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-user-graduate mr-2"></i>
                    Create New Student
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-filter mr-2 text-primary"></i>
                    Filter Users
                </h3>
                <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="all" <?= $filters['role'] == 'all' ? 'selected' : '' ?>>All Roles</option>
                            <option value="principal" <?= $filters['role'] == 'principal' ? 'selected' : '' ?>>Principal</option>
                            <option value="teacher" <?= $filters['role'] == 'teacher' ? 'selected' : '' ?>>Teachers</option>
                            <option value="student" <?= $filters['role'] == 'student' ? 'selected' : '' ?>>Students</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="all" <?= $filters['status'] == 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="active" <?= $filters['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $filters['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="Name, username, email, ID..." value="<?= htmlspecialchars($filters['search']) ?>">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-medium py-2 px-4 rounded-lg">
                            <i class="fas fa-search mr-2"></i>
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Excel-Style Users Table -->
            <div class="table-container mb-6">
                <div class="overflow-x-auto">
                    <table class="excel-table">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date of Birth</th>
                                <th>Guardian Name</th>
                                <th>Guardian Phone</th>
                                <th>Class Name</th>
                                <th>Address</th>
                                <th>Blood Group</th>
                                <th>Guardian Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($all_users)): ?>
                            <tr>
                                <td colspan="14" class="text-center py-10">
                                    <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No users found matching your criteria.</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($all_users as $user): ?>
                                <tr>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['first_name']) ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['last_name']) ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['email']) ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['phone'] ?? '-') ?></div></td>
                                    <td><div class="cell-content"><?= $user['date_of_birth'] ? date('Y-m-d', strtotime($user['date_of_birth'])) : '-' ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['guardian_name'] ?? '-') ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['guardian_phone'] ?? '-') ?></div></td>
                                    <td><div class="cell-content"><?= $user['class_name'] ? htmlspecialchars($user['class_name'] . ' ' . $user['section']) : '-' ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['address'] ?? '-') ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['blood_group'] ?? '-') ?></div></td>
                                    <td><div class="cell-content"><?= htmlspecialchars($user['guardian_email'] ?? '-') ?></div></td>
                                    <td>
                                        <span class="role-badge <?php
                                            switch ($user['role_name']) {
                                                case 'principal': echo 'bg-purple-100 text-purple-800'; break;
                                                case 'teacher': echo 'bg-green-100 text-green-800'; break;
                                                case 'student': echo 'bg-blue-100 text-blue-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                        ?>"><?= ucfirst($user['role_name']) ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex space-x-1">
                                            <button onclick="openViewModal(<?= $user['id'] ?>)" class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded" title="View">
                                                <i class="fas fa-eye text-xs"></i>
                                            </button>
                                            <button onclick="openResetPasswordModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>')" class="text-indigo-600 hover:text-indigo-900 p-1 hover:bg-indigo-50 rounded" title="Reset Password">
                                                <i class="fas fa-key text-xs"></i>
                                            </button>
                                            <form method="post" class="inline" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="new_status" value="<?= $user['is_active'] ? '0' : '1' ?>">
                                                <button type="submit" class="<?= $user['is_active'] ? 'text-red-600 hover:text-red-900 hover:bg-red-50' : 'text-green-600 hover:text-green-900 hover:bg-green-50' ?> p-1 rounded" title="<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                    <i class="fas <?= $user['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?> text-xs"></i>
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
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <?= (($page - 1) * $per_page) + 1 ?> to <?= min($page * $per_page, $total_users) ?> of <?= $total_users ?> results
                        </div>
                        <div class="flex space-x-1">
                            <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="px-3 py-2 text-sm <?= $i == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50' ?> border border-gray-300 rounded-lg"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Reset Password</h3>
                    <button onclick="closeResetPasswordModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-gray-700 mb-2">Reset password for</p>
                    <p class="font-semibold text-gray-900" id="resetUserName">User Name</p>
                </div>
                
                <form method="post" id="resetPasswordForm">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" id="resetUserId">
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeResetPasswordModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div id="viewUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-primary to-primary-dark text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">User Details</h3>
                    <button onclick="closeViewModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" id="viewUserContent">
                <!-- Content populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openResetPasswordModal(userId, userName) {
            document.getElementById('resetUserId').value = userId;
            document.getElementById('resetUserName').textContent = userName;
            document.getElementById('resetPasswordModal').classList.remove('hidden');
            document.getElementById('resetPasswordModal').classList.add('flex');
        }

        function closeResetPasswordModal() {
            document.getElementById('resetPasswordModal').classList.add('hidden');
            document.getElementById('resetPasswordModal').classList.remove('flex');
        }

        function openViewModal(userId) {
            const users = <?= json_encode($all_users) ?>;
            const user = users.find(u => u.id == userId);
            
            if (!user) return;
            
            const content = document.getElementById('viewUserContent');
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Personal Information</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Name:</strong> ${user.first_name} ${user.last_name}</div>
                            <div><strong>Email:</strong> ${user.email}</div>
                            <div><strong>Phone:</strong> ${user.phone || 'N/A'}</div>
                            <div><strong>Username:</strong> ${user.username}</div>
                            <div><strong>Role:</strong> ${user.role_name}</div>
                            <div><strong>Status:</strong> ${user.is_active ? 'Active' : 'Inactive'}</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Additional Details</h4>
                        <div class="space-y-2 text-sm">
                            ${user.student_id ? `<div><strong>Student ID:</strong> ${user.student_id}</div>` : ''}
                            ${user.class_name ? `<div><strong>Class:</strong> ${user.class_name} ${user.section}</div>` : ''}
                            ${user.guardian_name ? `<div><strong>Guardian:</strong> ${user.guardian_name}</div>` : ''}
                            ${user.guardian_phone ? `<div><strong>Guardian Phone:</strong> ${user.guardian_phone}</div>` : ''}
                            ${user.blood_group ? `<div><strong>Blood Group:</strong> ${user.blood_group}</div>` : ''}
                            <div><strong>Address:</strong> ${user.address || 'N/A'}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button onclick="closeViewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Close</button>
                </div>
            `;
            
            document.getElementById('viewUserModal').classList.remove('hidden');
            document.getElementById('viewUserModal').classList.add('flex');
        }

        function closeViewModal() {
            document.getElementById('viewUserModal').classList.add('hidden');
            document.getElementById('viewUserModal').classList.remove('flex');
        }

        // Close modals when clicking outside
        document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
            if (e.target === this) closeResetPasswordModal();
        });

        document.getElementById('viewUserModal').addEventListener('click', function(e) {
            if (e.target === this) closeViewModal();
        });
    </script>
</body>
</html>
