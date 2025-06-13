<?php 
// Include necessary files
include_once '../App/Models/headoffice/Index.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Dashboard - School LMS</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
</head>
<body class="bg-grey-50">
    <!-- Main Content -->
    <main class="main-content">
        <div class="p-4 lg:p-6">
            <!-- Welcome Header -->
            <div class="bg-primary rounded-xl p-6 text-white shadow-md mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-2xl md:text-3xl font-bold mb-1">Welcome, Principal <?= htmlspecialchars($user['last_name']) ?></h1>
                        <p class="text-blue-100">School Management Dashboard • <?= date('l, F j, Y') ?></p>
                    </div>
                    <div class="flex space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold"><?= $stats['students'] ?></div>
                            <div class="text-sm text-blue-100">Students</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold"><?= $stats['teachers'] ?></div>
                            <div class="text-sm text-blue-100">Teachers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">+<?= $new_students_month ?> this month</span>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['students'] ?></p>
                    <p class="text-sm text-grey-500">Total Students</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['teachers'] ?></p>
                    <p class="text-sm text-grey-500">Active Teachers</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['classes'] ?></p>
                    <p class="text-sm text-grey-500">Active Classes</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full"><?= $stats['pending_leaves'] ?> pending</span>
                    </div>
                    <p class="text-2xl font-bold text-grey-800"><?= $stats['pending_leaves'] + $stats['approved_leaves'] ?></p>
                    <p class="text-sm text-grey-500">Leave Applications</p>
                </div>
            </div>

            <!-- Attendance Overview -->
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-5 border-b border-grey-100">
                    <h2 class="text-lg font-semibold text-grey-800 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Today's Attendance Overview
                    </h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 relative">
                                <svg class="w-16 h-16" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2"></circle>
                                    <circle 
                                        cx="18" cy="18" r="16" 
                                        fill="none" 
                                        stroke="#22c55e" 
                                        stroke-width="2" 
                                        stroke-dasharray="100" 
                                        stroke-dashoffset="<?= 100 - ($attendance_today['present'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                        stroke-linecap="round"
                                        transform="rotate(-90 18 18)"
                                    ></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-lg font-bold text-green-600"><?= $attendance_today['present'] ?></span>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-grey-700">Present</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 relative">
                                <svg class="w-16 h-16" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2"></circle>
                                    <circle 
                                        cx="18" cy="18" r="16" 
                                        fill="none" 
                                        stroke="#ef4444" 
                                        stroke-width="2" 
                                        stroke-dasharray="100" 
                                        stroke-dashoffset="<?= 100 - ($attendance_today['absent'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                        stroke-linecap="round"
                                        transform="rotate(-90 18 18)"
                                    ></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-lg font-bold text-red-600"><?= $attendance_today['absent'] ?></span>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-grey-700">Absent</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 relative">
                                <svg class="w-16 h-16" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2"></circle>
                                    <circle 
                                        cx="18" cy="18" r="16" 
                                        fill="none" 
                                        stroke="#eab308" 
                                        stroke-width="2" 
                                        stroke-dasharray="100" 
                                        stroke-dashoffset="<?= 100 - ($attendance_today['late'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                        stroke-linecap="round"
                                        transform="rotate(-90 18 18)"
                                    ></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-lg font-bold text-yellow-600"><?= $attendance_today['late'] ?></span>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-grey-700">Late</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-blue-600"><?= $attendance_today['total_marked'] ?></span>
                            </div>
                            <p class="text-sm font-medium text-grey-700">Total Marked</p>
                        </div>
                    </div>
                    
                    <?php if ($attendance_today['total_marked'] > 0): ?>
                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm text-grey-600 mb-2">
                            <span>Overall Attendance Rate</span>
                            <span><?= round(($attendance_today['present'] / $attendance_today['total_marked']) * 100) ?>%</span>
                        </div>
                        <div class="w-full bg-grey-200 rounded-full h-2.5 overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" 
                                 style="width: <?= ($attendance_today['present'] / $attendance_today['total_marked']) * 100 ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Pending Leave Applications -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-5 border-b border-grey-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-grey-800 flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Pending Leave Applications
                        </h2>
                        <a href="leave_management.php" class="text-blue-600 text-sm font-medium">View All</a>
                    </div>
                    <div class="p-5">
                        <?php if (empty($recent_leaves)): ?>
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-grey-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-grey-500 text-lg">All caught up!</p>
                                <p class="text-grey-400 text-sm">No pending leave applications</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($recent_leaves as $leave): ?>
                                    <div class="flex items-start space-x-4 p-4 bg-orange-50 rounded-xl border border-orange-100">
                                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-grey-900"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h3>
                                            <p class="text-sm text-grey-600">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                    <?= htmlspecialchars(ucfirst($leave['user_type'])) ?>
                                                </span>
                                                <?php if ($leave['user_type'] == 'student'): ?>
                                                    ID: <?= htmlspecialchars($leave['identifier']) ?>
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-xs text-grey-500 mt-1">
                                                <?= date('M j', strtotime($leave['from_date'])) ?> - <?= date('M j', strtotime($leave['to_date'])) ?> 
                                                (<?= $leave['total_days'] ?> days)
                                            </p>
                                        </div>
                                        <a href="leave_details.php?id=<?= $leave['id'] ?>" class="text-blue-600 text-sm font-medium">
                                            Review
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Assignments -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-5 border-b border-grey-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-grey-800 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Recent Assignments
                        </h2>
                        <span class="text-sm text-grey-500">+<?= $assignments_month ?> this month</span>
                    </div>
                    <div class="p-5">
                        <?php if (empty($recent_assignments)): ?>
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-grey-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-grey-500 text-lg">No recent assignments</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($recent_assignments as $assignment): ?>
                                    <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-grey-900 truncate"><?= htmlspecialchars($assignment['title']) ?></h3>
                                            <p class="text-sm text-grey-600">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                                    <?= htmlspecialchars($assignment['subject_name']) ?>
                                                </span>
                                                <?= htmlspecialchars($assignment['class_name']) ?>
                                            </p>
                                            <p class="text-xs text-grey-500 mt-1">
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
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-5 border-b border-grey-100">
                    <h2 class="text-lg font-semibold text-grey-800 flex items-center">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        Most Active Teachers (Last 30 Days)
                    </h2>
                </div>
                <div class="p-5">
                    <?php if (empty($active_teachers)): ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-grey-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="text-grey-500 text-lg">No teacher activity data available</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-grey-200">
                                <thead class="bg-grey-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Teacher</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Assignments</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Attendance Records</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Log Entries</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Activity Score</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-grey-200">
                                    <?php foreach ($active_teachers as $index => $teacher): 
                                        $activity_score = $teacher['assignments_count'] * 3 + $teacher['attendance_records'] + $teacher['log_entries'] * 2;
                                    ?>
                                    <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-grey-50' ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                                    <?= strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)) ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-grey-900"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-grey-900"><?= $teacher['assignments_count'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-grey-900"><?= $teacher['attendance_records'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-grey-900"><?= $teacher['log_entries'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-grey-900 mr-2"><?= $activity_score ?></div>
                                                <div class="w-24 bg-grey-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= min(100, $activity_score * 2) ?>%"></div>
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
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-5 border-b border-grey-100">
                    <h2 class="text-lg font-semibold text-grey-800 flex items-center">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Administrative Actions
                    </h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <a href="manage_users.php" class="flex flex-col items-center p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-grey-800 text-center">Manage Users</span>
                        </a>

                        <a href="leave_management.php" class="flex flex-col items-center p-4 bg-orange-50 rounded-xl border border-orange-100">
                            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-grey-800 text-center">Leave Management</span>
                        </a>

                        <a href="attendance_reports.php" class="flex flex-col items-center p-4 bg-green-50 rounded-xl border border-green-100">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-grey-800 text-center">Attendance Reports</span>
                        </a>

                        <a href="createclass.php" class="flex flex-col items-center p-4 bg-purple-50 rounded-xl border border-purple-100">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-grey-800 text-center">Manage Classes</span>
                        </a>
                        
                        <a href="teacher_logs.php" class="flex flex-col items-center p-4 bg-pink-50 rounded-xl border border-pink-100">
                            <div class="w-12 h-12 bg-pink-500 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-grey-800 text-center">Teacher Logs</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
</body>
</html>
