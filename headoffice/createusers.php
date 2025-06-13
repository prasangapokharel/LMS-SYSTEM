<?php 
// Include necessary files
include_once '../App/Models/headoffice/User.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Users - School LMS</title>
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
                    <i class="fas fa-user-plus mr-3"></i>
                    Create New Users
                </h1>
                <p class="text-blue-100">Add new teachers and students to the system</p>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Create Teacher Card -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            Create New Teacher
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="post" id="createTeacherForm">
                            <input type="hidden" name="action" value="create_teacher">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">First Name *</label>
                                    <input type="text" name="first_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Last Name *</label>
                                    <input type="text" name="last_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Email *</label>
                                    <input type="email" name="email" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Phone *</label>
                                    <input type="tel" name="phone" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-grey-700 mb-1">Address</label>
                                <textarea name="address" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="3"></textarea>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A username and password will be automatically generated for the teacher.
                                </p>
                            </div>
                            
                            <div class="flex justify-end">
                                <a href="manage_users.php" class="bg-grey-300 hover:bg-grey-400 text-grey-800 py-2 px-4 rounded-lg mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Create Teacher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Create Student Card -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Create New Student
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="post" id="createStudentForm">
                            <input type="hidden" name="action" value="create_student">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">First Name *</label>
                                    <input type="text" name="first_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Last Name *</label>
                                    <input type="text" name="last_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Email *</label>
                                    <input type="email" name="email" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Phone *</label>
                                    <input type="tel" name="phone" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Date of Birth *</label>
                                    <input type="date" name="date_of_birth" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Blood Group</label>
                                    <select name="blood_group" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-grey-700 mb-1">Class *</label>
                                <select name="class_id" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-grey-700 mb-1">Address</label>
                                <textarea name="address" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="2"></textarea>
                            </div>
                            
                            <div class="border-t border-grey-200 pt-4 mb-4">
                                <h6 class="text-sm font-semibold text-grey-700 mb-3">Guardian Information</h6>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Guardian Name *</label>
                                        <input type="text" name="guardian_name" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-grey-700 mb-1">Guardian Phone *</label>
                                        <input type="tel" name="guardian_phone" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-grey-700 mb-1">Guardian Email</label>
                                    <input type="email" name="guardian_email" class="w-full rounded-lg border-grey-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A student ID, username, and password will be automatically generated.
                                </p>
                            </div>
                            
                            <div class="flex justify-end">
                                <a href="manage_users.php" class="bg-grey-300 hover:bg-grey-400 text-grey-800 py-2 px-4 rounded-lg mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
                                    <i class="fas fa-user-graduate mr-2"></i>
                                    Create Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Credentials Display -->
            <?php if (isset($new_credentials)): ?>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 text-white">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-key mr-2"></i>
                        New User Credentials
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-green-50 p-4 rounded-lg mb-4">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mr-4">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-green-800">User Created Successfully!</h4>
                                <p class="text-green-700">Please save these credentials and share with the user.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if (isset($new_credentials['student_id'])): ?>
                            <div class="bg-white p-3 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-grey-500 mb-1">Student ID</div>
                                <div class="text-lg font-mono bg-grey-100 p-2 rounded"><?= $new_credentials['student_id'] ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="bg-white p-3 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-grey-500 mb-1">Username</div>
                                <div class="text-lg font-mono bg-grey-100 p-2 rounded"><?= $new_credentials['username'] ?></div>
                            </div>
                            
                            <div class="bg-white p-3 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-grey-500 mb-1">Password</div>
                                <div class="text-lg font-mono bg-grey-100 p-2 rounded"><?= $new_credentials['password'] ?></div>
                            </div>
                            
                            <div class="bg-white p-3 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-grey-500 mb-1">Full Name</div>
                                <div class="text-lg p-2"><?= htmlspecialchars($new_credentials['first_name'] . ' ' . $new_credentials['last_name']) ?></div>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <a href="manage_users.php" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">
                                <i class="fas fa-users mr-2"></i>
                                Go to User Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
</body>
</html>
