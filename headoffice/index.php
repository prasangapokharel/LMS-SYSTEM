<?php
// Include necessary files
include_once '../App/Models/headoffice/Index.php';
include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Dashboard - School LMS</title>
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
</head>
<body class="bg-gray-50 font-inter">

<!-- Main Content -->
<main class="p-6 lg:p-8 ml-20">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Principal Dashboard</h1>
        <p class="text-gray-600">Welcome back! Here's what's happening at your school today.</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Students Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full font-medium">+<?= $new_students_month ?> this month</span>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['students'] ?></p>
            <p class="text-sm text-gray-500">Total Students</p>
        </div>

        <!-- Teachers Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['teachers'] ?></p>
            <p class="text-sm text-gray-500">Active Teachers</p>
        </div>

        <!-- Classes Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['classes'] ?></p>
            <p class="text-sm text-gray-500">Active Classes</p>
        </div>

        <!-- Leave Applications Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <span class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded-full font-medium"><?= $stats['pending_leaves'] ?> pending</span>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['pending_leaves'] + $stats['approved_leaves'] ?></p>
            <p class="text-sm text-gray-500">Leave Applications</p>
        </div>
    </div>

    <!-- Attendance Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Today's Attendance Overview
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- Present -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 relative">
                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" stroke="#f3f4f6" stroke-width="3"></circle>
                            <circle
                                cx="18" cy="18" r="16"
                                fill="none"
                                stroke="#22c55e"
                                stroke-width="3"
                                stroke-dasharray="100"
                                stroke-dashoffset="<?= 100 - ($attendance_today['present'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                stroke-linecap="round"
                            ></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-green-600"><?= $attendance_today['present'] ?></span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Present</p>
                </div>

                <!-- Absent -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 relative">
                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" stroke="#f3f4f6" stroke-width="3"></circle>
                            <circle
                                cx="18" cy="18" r="16"
                                fill="none"
                                stroke="#ef4444"
                                stroke-width="3"
                                stroke-dasharray="100"
                                stroke-dashoffset="<?= 100 - ($attendance_today['absent'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                stroke-linecap="round"
                            ></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-red-600"><?= $attendance_today['absent'] ?></span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Absent</p>
                </div>

                <!-- Late -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 relative">
                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" stroke="#f3f4f6" stroke-width="3"></circle>
                            <circle
                                cx="18" cy="18" r="16"
                                fill="none"
                                stroke="#eab308"
                                stroke-width="3"
                                stroke-dasharray="100"
                                stroke-dashoffset="<?= 100 - ($attendance_today['late'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                stroke-linecap="round"
                            ></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-yellow-600"><?= $attendance_today['late'] ?></span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Late</p>
                </div>

                <!-- Total Marked -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-blue-600"><?= $attendance_today['total_marked'] ?></span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Total Marked</p>
                </div>
            </div>

            <?php if ($attendance_today['total_marked'] > 0): ?>
            <div class="mt-8">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                    <span class="font-medium">Overall Attendance Rate</span>
                    <span class="font-semibold"><?= round(($attendance_today['present'] / $attendance_today['total_marked']) * 100) ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-500"
                         style="width: <?= ($attendance_today['present'] / $attendance_today['total_marked']) * 100 ?>%"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
        <!-- Pending Leave Applications -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Pending Leave Applications
                </h2>
                <a href="leave_management.php" class="text-primary text-sm font-medium hover:text-primary-dark transition-colors">View All</a>
            </div>
            <div class="p-6">
                <?php if (empty($recent_leaves)): ?>
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">All caught up!</p>
                        <p class="text-gray-400 text-sm">No pending leave applications</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_leaves as $leave): ?>
                            <div class="flex items-start space-x-4 p-4 bg-orange-50 rounded-xl border border-orange-100">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                            <?= htmlspecialchars(ucfirst($leave['user_type'])) ?>
                                        </span>
                                        <?php if ($leave['user_type'] == 'student'): ?>
                                            ID: <?= htmlspecialchars($leave['identifier']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <?= date('M j', strtotime($leave['from_date'])) ?> - <?= date('M j', strtotime($leave['to_date'])) ?>
                                        (<?= $leave['total_days'] ?> days)
                                    </p>
                                </div>
                                <a href="leave_details.php?id=<?= $leave['id'] ?>" class="text-primary hover:text-primary-dark text-sm font-medium transition-colors">
                                    Review
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Recent Assignments
                </h2>
                <span class="text-sm text-gray-500 font-medium">+<?= $assignments_month ?> this month</span>
            </div>
            <div class="p-6">
                <?php if (empty($recent_assignments)): ?>
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No recent assignments</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_assignments as $assignment): ?>
                            <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($assignment['title']) ?></h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                            <?= htmlspecialchars($assignment['subject_name']) ?>
                                        </span>
                                        <?= htmlspecialchars($assignment['class_name']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        By <?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?> •
                                        <?= $assignment['submissions'] ?> submissions
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Most Active Teachers -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                Most Active Teachers (Last 30 Days)
            </h2>
        </div>
        <div class="p-6">
            <?php if (empty($active_teachers)): ?>
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">No teacher activity data available</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Teacher</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Assignments</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Attendance Records</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Log Entries</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Activity Score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($active_teachers as $index => $teacher):
                                $activity_score = $teacher['assignments_count'] * 3 + $teacher['attendance_records'] + $teacher['log_entries'] * 2;
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">
                                            <?= strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)) ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $teacher['assignments_count'] ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $teacher['attendance_records'] ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $teacher['log_entries'] ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="text-sm font-semibold text-gray-900 mr-3"><?= $activity_score ?></div>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full transition-all duration-500" style="width: <?= min(100, $activity_score * 2) ?>%"></div>
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

    <!-- Administrative Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Administrative Actions
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="manage_users.php" class="group flex flex-col items-center p-6 bg-blue-50 rounded-xl border border-blue-100 hover:shadow-lg hover:border-blue-200 transition-all">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 text-center">Manage Users</span>
                </a>

                <a href="leave_management.php" class="group flex flex-col items-center p-6 bg-orange-50 rounded-xl border border-orange-100 hover:shadow-lg hover:border-orange-200 transition-all">
                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 text-center">Leave Management</span>
                </a>

                <a href="attendance_reports.php" class="group flex flex-col items-center p-6 bg-green-50 rounded-xl border border-green-100 hover:shadow-lg hover:border-green-200 transition-all">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 text-center">Attendance Reports</span>
                </a>

                <a href="createclass.php" class="group flex flex-col items-center p-6 bg-purple-50 rounded-xl border border-purple-100 hover:shadow-lg hover:border-purple-200 transition-all">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 text-center">Manage Classes</span>
                </a>

                <a href="teacher_logs.php" class="group flex flex-col items-center p-6 bg-pink-50 rounded-xl border border-pink-100 hover:shadow-lg hover:border-pink-200 transition-all">
                    <div class="w-12 h-12 bg-pink-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 text-center">Teacher Logs</span>
                </a>
            </div>
        </div>
    </div>
</main>

<script>
// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Update time immediately and then every minute
updateTime();
setInterval(updateTime, 60000);
</script>

</body>
</html>