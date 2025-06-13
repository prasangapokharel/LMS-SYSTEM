
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/ui.css">
</head>
<body class="bg-gray-50">
    <div class="flex">
        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8 ml-0 lg:ml-64">
            <!-- Welcome Header -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl p-6 text-white shadow-lg mb-6">
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
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">+<?= $new_students_month ?> this month</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['students'] ?></p>
                    <p class="text-sm text-gray-500">Total Students</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['teachers'] ?></p>
                    <p class="text-sm text-gray-500">Active Teachers</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                            <i class="fas fa-school text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['classes'] ?></p>
                    <p class="text-sm text-gray-500">Active Classes</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                            <i class="fas fa-clipboard-list text-xl"></i>
                        </div>
                        <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full"><?= $stats['pending_leaves'] ?> pending</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['pending_leaves'] + $stats['approved_leaves'] ?></p>
                    <p class="text-sm text-gray-500">Leave Applications</p>
                </div>
            </div>

            <!-- Attendance Overview -->
            <div class="bg-white rounded-xl shadow mb-6">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        Today's Attendance Overview
                    </h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 relative">
                                <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2"></circle>
                                    <circle 
                                        cx="18" cy="18" r="16" 
                                        fill="none" 
                                        stroke="#22c55e" 
                                        stroke-width="2" 
                                        stroke-dasharray="100" 
                                        stroke-dashoffset="<?= 100 - ($attendance_today['present'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                        stroke-linecap="round"
                                    ></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-lg font-bold text-green-600"><?= $attendance_today['present'] ?></span>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-700">Present</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 relative">
                                <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2"></circle>
                                    <circle 
                                        cx="18" cy="18" r="16" 
                                        fill="none" 
                                        stroke="#ef4444" 
                                        stroke-width="2" 
                                        stroke-dasharray="100" 
                                        stroke-dashoffset="<?= 100 - ($attendance_today['absent'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                        stroke-linecap="round"
                                    ></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-lg font-bold text-red-600"><?= $attendance_today['absent'] ?></span>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-700">Absent</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 relative">
                                <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2"></circle>
                                    <circle 
                                        cx="18" cy="18" r="16" 
                                        fill="none" 
                                        stroke="#eab308" 
                                        stroke-width="2" 
                                        stroke-dasharray="100" 
                                        stroke-dashoffset="<?= 100 - ($attendance_today['late'] / max(1, $attendance_today['total_marked']) * 100) ?>"
                                        stroke-linecap="round"
                                    ></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-lg font-bold text-yellow-600"><?= $attendance_today['late'] ?></span>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-700">Late</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-blue-600"><?= $attendance_today['total_marked'] ?></span>
                            </div>
                            <p class="text-sm font-medium text-gray-700">Total Marked</p>
                        </div>
                    </div>
                    
                    <?php if ($attendance_today['total_marked'] > 0): ?>
                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                            <span>Overall Attendance Rate</span>
                            <span><?= round(($attendance_today['present'] / $attendance_today['total_marked']) * 100) ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full" 
                                 style="width: <?= ($attendance_today['present'] / $attendance_today['total_marked']) * 100 ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Pending Leave Applications -->
                <div class="bg-white rounded-xl shadow">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-calendar-times text-orange-500 mr-2"></i>
                            Pending Leave Applications
                        </h2>
                        <a href="leave_management.php" class="text-blue-600 text-sm font-medium hover:text-blue-800">View All</a>
                    </div>
                    <div class="p-5">
                        <?php if (empty($recent_leaves)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 text-lg">All caught up!</p>
                                <p class="text-gray-400 text-sm">No pending leave applications</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($recent_leaves as $leave): ?>
                                    <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl border border-orange-100">
                                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-orange-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h3>
                                            <p class="text-sm text-gray-600">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                    <?= htmlspecialchars(ucfirst($leave['user_type'])) ?>
                                                </span>
                                                <?php if ($leave['user_type'] == 'student'): ?>
                                                    ID: <?= htmlspecialchars($leave['identifier']) ?>
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <?= date('M j', strtotime($leave['from_date'])) ?> - <?= date('M j', strtotime($leave['to_date'])) ?> 
                                                (<?= $leave['total_days'] ?> days)
                                            </p>
                                        </div>
                                        <a href="leave_details.php?id=<?= $leave['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Review
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Assignments -->
                <div class="bg-white rounded-xl shadow">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                            Recent Assignments
                        </h2>
                        <span class="text-sm text-gray-500">+<?= $assignments_month ?> this month</span>
                    </div>
                    <div class="p-5">
                        <?php if (empty($recent_assignments)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-file-alt text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 text-lg">No recent assignments</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($recent_assignments as $assignment): ?>
                                    <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-100">
                                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-file-alt text-blue-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($assignment['title']) ?></h3>
                                            <p class="text-sm text-gray-600">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                                    <?= htmlspecialchars($assignment['subject_name']) ?>
                                                </span>
                                                <?= htmlspecialchars($assignment['class_name']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
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
            <div class="bg-white rounded-xl shadow mb-6">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-award text-yellow-500 mr-2"></i>
                        Most Active Teachers (Last 30 Days)
                    </h2>
                </div>
                <div class="p-5">
                    <?php if (empty($active_teachers)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-user-tie text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 text-lg">No teacher activity data available</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignments</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Records</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log Entries</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Score</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($active_teachers as $index => $teacher): 
                                        $activity_score = $teacher['assignments_count'] * 3 + $teacher['attendance_records'] + $teacher['log_entries'] * 2;
                                    ?>
                                    <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                                    <?= strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)) ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= $teacher['assignments_count'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= $teacher['attendance_records'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= $teacher['log_entries'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900 mr-2"><?= $activity_score ?></div>
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
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
            <div class="bg-white rounded-xl shadow">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Administrative Actions
                    </h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <a href="manage_users.php" class="group flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100 hover:shadow-lg transition-all">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800 text-center">Manage Users</span>
                        </a>

                        <a href="leave_management.php" class="group flex flex-col items-center p-4 bg-gradient-to-br from-orange-50 to-red-50 rounded-xl border border-orange-100 hover:shadow-lg transition-all">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-calendar-times text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800 text-center">Leave Management</span>
                        </a>

                        <a href="attendance_reports.php" class="group flex flex-col items-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-100 hover:shadow-lg transition-all">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800 text-center">Attendance Reports</span>
                        </a>

                        <a href="createclass.php" class="group flex flex-col items-center p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border border-purple-100 hover:shadow-lg transition-all">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-school text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800 text-center">Manage Classes</span>
                        </a>
                        
                        <a href="teacher_logs.php" class="group flex flex-col items-center p-4 bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl border border-pink-100 hover:shadow-lg transition-all">
                            <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-clipboard-list text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800 text-center">Teacher Logs</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>


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
