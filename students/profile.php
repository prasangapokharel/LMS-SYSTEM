<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $emergency_contact = $_POST['emergency_contact'] ?? '';
    $guardian_phone = $_POST['guardian_phone'] ?? '';
    
    try {
        // Update user table
        $stmt = $pdo->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$phone, $address, $user['id']]);
        
        // Update student table
        $stmt = $pdo->prepare("UPDATE students SET emergency_contact = ?, guardian_phone = ? WHERE id = ?");
        $stmt->execute([$emergency_contact, $guardian_phone, $student['id']]);
        
        $success_message = "Profile updated successfully!";
        
        // Refresh data
        $user = getCurrentUser($pdo);
        $student = getStudentData($pdo);
        
    } catch (Exception $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}

// Get academic performance
$stmt = $pdo->prepare("SELECT AVG(sub.grade) as avg_grade, COUNT(*) as total_assignments
                      FROM assignment_submissions sub
                      WHERE sub.student_id = ? AND sub.grade IS NOT NULL");
$stmt->execute([$student['id']]);
$performance = $stmt->fetch(PDO::FETCH_ASSOC);

// Get recent grades
$stmt = $pdo->prepare("SELECT a.title, a.max_marks, sub.grade, sub.graded_at, s.subject_name
                      FROM assignment_submissions sub
                      JOIN assignments a ON sub.assignment_id = a.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE sub.student_id = ? AND sub.grade IS NOT NULL
                      ORDER BY sub.graded_at DESC LIMIT 5");
$stmt->execute([$student['id']]);
$recent_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pwa-container {
            max-width: 428px;
            margin: 0 auto;
            min-height: 100vh;
            background: #a339e4;
            position: relative;
        }
        
        .content-wrapper {
            background: #fff;
            min-height: calc(100vh - 80px);
            border-radius: 24px 24px 0 0;
            margin-top: 80px;
            padding-bottom: 80px;
        }
        
        .avatar-gradient {
            background: #a339e4;
        }
        
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(163, 57, 228, 0.1);
            border: 1px solid rgba(163, 57, 228, 0.1);
        }
        
        .text-primary { color: #a339e4; }
        .bg-primary { background-color: #a339e4; }
        .border-primary { border-color: #a339e4; }
        .focus\:ring-primary:focus { --tw-ring-color: #a339e4; }
        
        @media (max-width: 768px) {
            .desktop-sidebar { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="pwa-container">
        <!-- Header -->
        <div class="absolute top-0 left-0 right-0 p-6 text-white z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <a href="index.php" class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <!-- Arrow Left Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                    </a>
                    <!-- <div>
                        <h1 class="text-xl font-bold">My Profile</h1>
                        <p class="text-white text-opacity-80 text-sm">Personal information</p>
                    </div> -->
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <!-- User Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="p-6">
                
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
                        <div class="flex items-center">
                            <!-- Check Circle Icon -->
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <?= $success_message ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6">
                        <div class="flex items-center">
                            <!-- Exclamation Circle Icon -->
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <?= $error_message ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Profile Header -->
                <div class="card p-6 mb-6 text-center">
                    <div class="w-24 h-24 avatar-gradient rounded-full flex items-center justify-center mx-auto mb-4">
                        <!-- User Icon -->
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </h2>
                    <p class="text-gray-600 mb-2">Student ID: <?= htmlspecialchars($student['student_id']) ?></p>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                </div>

                <!-- Academic Performance -->
                <div class="card p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <!-- Chart Bar Icon -->
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        Academic Performance
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <p class="text-2xl font-bold text-gray-900">
                                <?= $performance['avg_grade'] ? number_format($performance['avg_grade'], 1) : '0' ?>
                            </p>
                            <p class="text-sm text-gray-600">Average Grade</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <p class="text-2xl font-bold text-gray-900"><?= $performance['total_assignments'] ?></p>
                            <p class="text-sm text-gray-600">Assignments Graded</p>
                        </div>
                    </div>
                    
                    <?php if (!empty($recent_grades)): ?>
                        <h4 class="font-medium text-gray-900 mb-2">Recent Grades</h4>
                        <div class="space-y-2">
                            <?php foreach ($recent_grades as $grade): ?>
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($grade['title']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($grade['subject_name']) ?></p>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">
                                        <?= $grade['grade'] ?>/<?= $grade['max_marks'] ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Personal Information -->
                <div class="card p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <!-- Information Circle Icon -->
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        Personal Information
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Full Name</span>
                            <span class="font-medium text-gray-900">
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Email</span>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Student ID</span>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($student['student_id']) ?></span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Admission Date</span>
                            <span class="font-medium text-gray-900">
                                <?= date('M j, Y', strtotime($student['admission_date'])) ?>
                            </span>
                        </div>
                        
                        <?php if ($student['date_of_birth']): ?>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Date of Birth</span>
                                <span class="font-medium text-gray-900">
                                    <?= date('M j, Y', strtotime($student['date_of_birth'])) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($student['blood_group']): ?>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Blood Group</span>
                                <span class="font-medium text-gray-900"><?= htmlspecialchars($student['blood_group']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Editable Contact Information -->
                <div class="card p-4">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <!-- Pencil Icon -->
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                        Contact Information
                    </h3>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="3"
                                      class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
                            <input type="tel" name="emergency_contact" value="<?= htmlspecialchars($student['emergency_contact'] ?? '') ?>"
                                   class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Phone</label>
                            <input type="tel" name="guardian_phone" value="<?= htmlspecialchars($student['guardian_phone'] ?? '') ?>"
                                   class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-medium hover:opacity-90 transition-opacity flex items-center justify-center">
                            <!-- Save Icon -->
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                            </svg>
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <?php include '../include/bootoomnav.php'; ?>
    </div>
</body>
</html>