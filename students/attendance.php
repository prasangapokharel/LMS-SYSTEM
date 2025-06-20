<?php
include_once '../App/Models/student/Attendance.php';
include '../include/buffer.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - School LMS</title>
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
        
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(163, 57, 228, 0.1);
            border: 1px solid rgba(163, 57, 228, 0.1);
        }
        
        .text-primary { color: #a339e4; }
        .bg-primary { background-color: #a339e4; }
        .border-primary { border-color: #a339e4; }
        
        .progress-circle {
            transform: rotate(-90deg);
        }
        
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
                        ←
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Attendance</h1>
                        <p class="text-white text-opacity-80 text-sm">Track your attendance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="p-6">
                <!-- Statistics Overview -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- Overall Percentage -->
                    <div class="card p-6 text-center">
                        <div class="relative w-20 h-20 mx-auto mb-4">
                            <svg class="w-20 h-20 progress-circle">
                                <circle cx="40" cy="40" r="32" stroke="#e5e7eb" stroke-width="6" fill="transparent"/>
                                <circle cx="40" cy="40" r="32" stroke="#a339e4" stroke-width="6" fill="transparent"
                                        stroke-dasharray="<?= $attendance_percentage * 2.01 ?> 201"
                                        stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xl font-bold text-gray-900"><?= $attendance_percentage ?>%</span>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-600">Overall Attendance</p>
                    </div>
                    
                    <!-- Statistics Breakdown -->
                    <div class="space-y-3">
                        <div class="card p-3 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-700">Present</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900"><?= $present_days ?></span>
                        </div>
                        
                        <div class="card p-3 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-gray-700">Absent</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900"><?= $absent_days ?></span>
                        </div>
                        
                        <div class="card p-3 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                                <span class="text-sm text-gray-700">Late</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900"><?= $late_days ?></span>
                        </div>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="card p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Filter by Date Range</h3>
                    <form method="GET" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                                <input type="date" name="start_date" value="<?= $start_date ?>" 
                                       class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                                <input type="date" name="end_date" value="<?= $end_date ?>" 
                                       class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-medium">
                            Apply Filter
                        </button>
                    </form>
                </div>

                <!-- Calendar View -->
                <div class="card p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Calendar View</h3>
                    
                    <?php
                    $current_month = date('Y-m', strtotime($start_date));
                    $days_in_month = date('t', strtotime($current_month));
                    $first_day = date('w', strtotime($current_month . '-01'));
                    ?>
                    
                    <div class="text-center mb-4">
                        <h4 class="text-lg font-medium text-gray-900"><?= date('F Y', strtotime($current_month)) ?></h4>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-1 text-center text-sm">
                        <div class="p-3 font-medium text-gray-500">Sun</div>
                        <div class="p-3 font-medium text-gray-500">Mon</div>
                        <div class="p-3 font-medium text-gray-500">Tue</div>
                        <div class="p-3 font-medium text-gray-500">Wed</div>
                        <div class="p-3 font-medium text-gray-500">Thu</div>
                        <div class="p-3 font-medium text-gray-500">Fri</div>
                        <div class="p-3 font-medium text-gray-500">Sat</div>
                        
                        <?php for ($i = 0; $i < $first_day; $i++): ?>
                            <div class="p-3"></div>
                        <?php endfor; ?>
                        
                        <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
                            <div class="p-3 relative">
                                <span class="text-gray-700 font-medium"><?= $day ?></span>
                                <?php if (isset($calendar_data[$current_month][$day])): ?>
                                    <div class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 rounded-full
                                        <?php
                                        $status = $calendar_data[$current_month][$day];
                                        if ($status === 'present') echo 'bg-green-500';
                                        elseif ($status === 'absent') echo 'bg-red-500';
                                        elseif ($status === 'late') echo 'bg-yellow-500';
                                        ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Legend -->
                    <div class="flex justify-center space-x-6 mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">Present</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">Absent</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">Late</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Records -->
                <div class="card">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Recent Records</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <?php if (empty($attendance_records)): ?>
                            <div class="p-8 text-center">
                                <p class="text-gray-500 text-lg mb-2">No Records Found</p>
                                <p class="text-gray-400 text-sm">No attendance records found for the selected period</p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($attendance_records, 0, 10) as $record): ?>
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                <?= date('M j, Y', strtotime($record['attendance_date'])) ?>
                                            </p>
                                            <p class="text-sm text-gray-500"><?= $record['day_name'] ?></p>
                                            <?php if ($record['subject_name']): ?>
                                                <p class="text-xs text-gray-400"><?= htmlspecialchars($record['subject_name']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="text-right">
                                            <?php if ($record['check_in_time']): ?>
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <?= date('g:i A', strtotime($record['check_in_time'])) ?>
                                                </p>
                                            <?php endif; ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                <?php if ($record['status'] === 'present'): ?>
                                                    bg-green-100 text-green-800
                                                <?php elseif ($record['status'] === 'absent'): ?>
                                                    bg-red-100 text-red-800
                                                <?php else: ?>
                                                    bg-yellow-100 text-yellow-800
                                                <?php endif; ?>">
                                                <?= ucfirst($record['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <?php include '../include/bootoomnav.php'; ?>
    </div>
</body>
</html>