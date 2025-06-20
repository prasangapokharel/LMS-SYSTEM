<?php 
// Include necessary files
require_once '../include/session.php';
require_once '../include/connect.php';

// Check if user is logged in and has principal role
requireRole('principal');

// Include the Academic model
include_once '../App/Models/headoffice/Academic.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Analytics - School LMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/ui.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8 ml-0 lg:ml-64">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">
                            <i class="fas fa-chart-line mr-3"></i>
                            Academic Analytics
                        </h1>
                        <p class="text-blue-100">Comprehensive academic performance insights and trends</p>
                    </div>
                    <div class="flex space-x-3 mt-4 md:mt-0">
                        <button class="btn btn2">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                        <button class="btn btn1">
                            <i class="fas fa-plus mr-2"></i>
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Analytics Filters
                </h5>
                <form method="get" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-input">
                                <option value="">All Classes</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= $class_filter == $class['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Subject</label>
                            <select name="subject_id" class="form-input">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= $subject_filter == $subject['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($subject['subject_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" class="form-input" value="<?= htmlspecialchars($date_from) ?>">
                        </div>
                        <div>
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" class="form-input" value="<?= htmlspecialchars($date_to) ?>">
                        </div>
                        <div>
                            <label class="form-label">Time Period</label>
                            <select name="period" class="form-input">
                                <option value="monthly" <?= $period == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                <option value="weekly" <?= $period == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn1">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Generate Analytics
                        </button>
                    </div>
                </form>
            </div>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mr-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $overall_stats['total_students'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mr-4">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Teachers</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $overall_stats['total_teachers'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mr-4">
                            <i class="fas fa-school text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Classes</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $overall_stats['total_classes'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 mr-4">
                            <i class="fas fa-book text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Subjects</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $overall_stats['total_subjects'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Trends Chart -->
            <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        Attendance Trends
                    </h3>
                </div>
                <div class="p-6">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Class-wise Attendance -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-school mr-2"></i>
                            Class-wise Attendance
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($class_attendance)): ?>
                            <div class="space-y-5">
                                <?php foreach ($class_attendance as $class_data): ?>
                                <?php 
                                $percentage = $class_data['class_percentage'];
                                $status_class = 'bg-red-500';
                                if ($percentage >= 90) $status_class = 'bg-green-500';
                                elseif ($percentage >= 80) $status_class = 'bg-blue-500';
                                elseif ($percentage >= 70) $status_class = 'bg-yellow-500';
                                ?>
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <h6 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($class_data['class_name'] . ' ' . $class_data['section']) ?></h6>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= $percentage ?>%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="<?= $status_class ?> h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <div class="flex justify-between mt-2">
                                        <span class="text-xs text-gray-500"><?= $class_data['class_students'] ?> students</span>
                                        <span class="text-xs text-gray-500"><?= $class_data['class_present'] ?> present / <?= $class_data['class_records'] ?> total</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <i class="fas fa-chart-bar text-gray-300 text-5xl mb-3"></i>
                                <h3 class="text-sm font-medium text-gray-900">No attendance data available</h3>
                                <p class="text-sm text-gray-500 mt-1">No data available for the selected criteria and date range.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Subject Performance -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-book mr-2"></i>
                            Subject Performance
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($subject_performance)): ?>
                            <div class="space-y-5">
                                <?php foreach ($subject_performance as $subject): ?>
                                <?php 
                                $avg_score = round($subject['avg_score'], 1);
                                $status_class = 'bg-red-500';
                                if ($avg_score >= 90) $status_class = 'bg-green-500';
                                elseif ($avg_score >= 80) $status_class = 'bg-blue-500';
                                elseif ($avg_score >= 70) $status_class = 'bg-yellow-500';
                                ?>
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <h6 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($subject['subject_name']) ?></h6>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= $avg_score ?>/100
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="<?= $status_class ?> h-2 rounded-full" style="width: <?= $avg_score ?>%"></div>
                                    </div>
                                    <div class="flex justify-between mt-2">
                                        <span class="text-xs text-gray-500"><?= $subject['total_assignments'] ?> assignments</span>
                                        <span class="text-xs text-gray-500">Range: <?= round($subject['min_score'], 1) ?> - <?= round($subject['max_score'], 1) ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <i class="fas fa-book text-gray-300 text-5xl mb-3"></i>
                                <h3 class="text-sm font-medium text-gray-900">No subject performance data available</h3>
                                <p class="text-sm text-gray-500 mt-1">No data available for the selected criteria and date range.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Teacher Performance -->
            <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-5">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        Teacher Performance
                    </h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($teacher_performance)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classes Taught</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Logs</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Attendance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teaching Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($teacher_performance as $teacher): ?>
                                    <?php 
                                    $performance_score = min(100, ($teacher['total_logs'] * 5 + $teacher['total_hours'] / 10) / 2);
                                    $status_class = 'bg-red-500';
                                    if ($performance_score >= 90) $status_class = 'bg-green-500';
                                    elseif ($performance_score >= 75) $status_class = 'bg-blue-500';
                                    elseif ($performance_score >= 60) $status_class = 'bg-yellow-500';
                                    ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    <?= strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)) ?>
                                                </div>
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $teacher['classes_taught'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $teacher['subjects_taught'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $teacher['total_logs'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= round($teacher['avg_attendance'], 1) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= round($teacher['total_hours'] / 60, 1) ?> hrs</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-2.5 w-2.5 rounded-full <?= $status_class ?> mr-2"></div>
                                                <span class="text-sm text-gray-900"><?= round($performance_score) ?>/100</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-10">
                            <i class="fas fa-chalkboard-teacher text-gray-300 text-5xl mb-3"></i>
                            <h3 class="text-sm font-medium text-gray-900">No teacher performance data available</h3>
                            <p class="text-sm text-gray-500 mt-1">No data available for the selected criteria and date range.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Assignment Submission Rates -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-clipboard-check mr-2"></i>
                            Assignment Submission Rates
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($submission_rates)): ?>
                            <div style="position: relative; height: 300px; width: 100%;">
                                <canvas id="submissionRatesChart"></canvas>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <i class="fas fa-clipboard-check text-gray-300 text-5xl mb-3"></i>
                                <h3 class="text-sm font-medium text-gray-900">No submission data available</h3>
                                <p class="text-sm text-gray-500 mt-1">No data available for the selected criteria and date range.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Top Performing Students -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-star mr-2"></i>
                            Top Performing Students
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($top_students)): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignments</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Score</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($top_students as $student): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $student['assignments_completed'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php 
                                                $avg_score = round($student['avg_score'], 1);
                                                $status_class = 'bg-red-500';
                                                if ($avg_score >= 90) $status_class = 'bg-green-500';
                                                elseif ($avg_score >= 80) $status_class = 'bg-blue-500';
                                                elseif ($avg_score >= 70) $status_class = 'bg-yellow-500';
                                                ?>
                                                <div class="flex items-center">
                                                    <div class="h-2.5 w-2.5 rounded-full <?= $status_class ?> mr-2"></div>
                                                    <span class="text-sm text-gray-900"><?= $avg_score ?>/100</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <i class="fas fa-star text-gray-300 text-5xl mb-3"></i>
                                <h3 class="text-sm font-medium text-gray-900">No student performance data available</h3>
                                <p class="text-sm text-gray-500 mt-1">No data available for the selected criteria and date range.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
        
    <script>
        // Attendance Trends Chart
        const attendanceTrendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        const attendanceTrendData = {
            labels: <?= json_encode(array_column($attendance_trends, 'period_label')) ?>,
            datasets: [
                {
                    label: 'Attendance Percentage',
                    data: <?= json_encode(array_column($attendance_trends, 'attendance_percentage')) ?>,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        };
        
        new Chart(attendanceTrendCtx, {
            type: 'line',
            data: attendanceTrendData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Attendance Percentage (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '<?= $period === 'weekly' ? 'Week' : 'Month' ?>'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Attendance: ${context.parsed.y}%`;
                            }
                        }
                    }
                }
            }
        });

        // Submission Rates Chart
        <?php if (!empty($submission_rates)): ?>
        const submissionRatesCtx = document.getElementById('submissionRatesChart').getContext('2d');
        const submissionRatesData = {
            labels: <?= json_encode(array_map(function($item) { return $item['class_name'] . ' ' . $item['section']; }, $submission_rates)) ?>,
            datasets: [
                {
                    label: 'Submission Rate',
                    data: <?= json_encode(array_column($submission_rates, 'submission_rate')) ?>,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderWidth: 0
                }
            ]
        };
        
        new Chart(submissionRatesCtx, {
            type: 'bar',
            data: submissionRatesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Submission Rate (%)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const dataIndex = context.dataIndex;
                                const dataset = context.dataset;
                                const value = dataset.data[dataIndex];
                                const total = <?= json_encode(array_column($submission_rates, 'total_assignments')) ?>[dataIndex];
                                const submitted = <?= json_encode(array_column($submission_rates, 'total_submissions')) ?>[dataIndex];
                                return [
                                    `Submission Rate: ${value}%`,
                                    `Submitted: ${submitted} / ${total} assignments`
                                ];
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
