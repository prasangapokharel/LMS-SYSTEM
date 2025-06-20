<?php 
// Include necessary files
include_once '../App/Models/headoffice/Attendance.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Reports - School LMS</title>
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
                    <i class="fas fa-chart-line mr-3"></i>
                    Attendance Reports
                </h1>
                <p class="text-blue-100">Comprehensive attendance analytics and insights</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-filter mr-2 text-blue-600"></i>
                    Report Filters
                </h5>
                <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-input" value="<?= htmlspecialchars($date_from) ?>">
                    </div>
                    <div>
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-input" value="<?= htmlspecialchars($date_to) ?>">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn1 w-full">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $overall_stats['total_students'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Total Students</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-percentage text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $overall_stats['overall_percentage'] ?? 0 ?>%</p>
                    <p class="text-sm text-gray-500">Overall Attendance</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $overall_stats['total_present'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Total Present</p>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                            <i class="fas fa-times-circle text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $overall_stats['total_absent'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Total Absent</p>
                </div>
            </div>

            <!-- Class-wise Statistics -->
            <?php if (!empty($class_stats)): ?>
            <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-5">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-school mr-2"></i>
                        Class-wise Attendance Overview
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($class_stats as $class_stat): ?>
                        <?php 
                        $percentage = $class_stat['class_percentage'];
                        $status_class = 'border-red-200 bg-red-50';
                        $text_class = 'text-red-700';
                        $badge_class = 'bg-red-100 text-red-800';
                        
                        if ($percentage >= 90) {
                            $status_class = 'border-green-200 bg-green-50';
                            $text_class = 'text-green-700';
                            $badge_class = 'bg-green-100 text-green-800';
                        } elseif ($percentage >= 80) {
                            $status_class = 'border-blue-200 bg-blue-50';
                            $text_class = 'text-blue-700';
                            $badge_class = 'bg-blue-100 text-blue-800';
                        } elseif ($percentage >= 70) {
                            $status_class = 'border-yellow-200 bg-yellow-50';
                            $text_class = 'text-yellow-700';
                            $badge_class = 'bg-yellow-100 text-yellow-800';
                        }
                        ?>
                        <div class="border-2 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow <?= $status_class ?>">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <h6 class="font-semibold text-gray-800"><?= htmlspecialchars($class_stat['class_name'] . ' ' . $class_stat['section']) ?></h6>
                                    <span class="px-3 py-1 rounded-full text-sm font-bold <?= $badge_class ?>">
                                        <?= $percentage ?>%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300 <?= $percentage >= 90 ? 'bg-green-500' : ($percentage >= 80 ? 'bg-blue-500' : ($percentage >= 70 ? 'bg-yellow-500' : 'bg-red-500')) ?>" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                            <div class="p-4 grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div class="text-lg font-bold text-gray-800"><?= $class_stat['class_students'] ?></div>
                                    <div class="text-xs text-gray-500">Students</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-green-600"><?= $class_stat['class_present'] ?></div>
                                    <div class="text-xs text-gray-500">Present</div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detailed Report -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-table mr-2"></i>
                        Detailed Attendance Report
                    </h2>
                    <div class="flex space-x-2">
                        <span class="text-blue-100 text-sm">
                            <?= htmlspecialchars($date_from) ?> to <?= htmlspecialchars($date_to) ?>
                        </span>
                        <button onclick="exportToCSV()" class="btn btn2 btn-sm">
                            <i class="fas fa-download mr-1"></i>
                            Export CSV
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full" id="attendanceTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Late</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Half Day</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance %</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($attendance_data as $record): ?>
                            <?php 
                            $percentage = $record['attendance_percentage'];
                            $status = 'poor';
                            $badge_class = 'bg-red-100 text-red-800';
                            $progress_class = 'bg-red-500';
                            
                            if ($percentage >= 90) {
                                $status = 'excellent';
                                $badge_class = 'bg-green-100 text-green-800';
                                $progress_class = 'bg-green-500';
                            } elseif ($percentage >= 80) {
                                $status = 'good';
                                $badge_class = 'bg-blue-100 text-blue-800';
                                $progress_class = 'bg-blue-500';
                            } elseif ($percentage >= 70) {
                                $status = 'average';
                                $badge_class = 'bg-yellow-100 text-yellow-800';
                                $progress_class = 'bg-yellow-500';
                            }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                            <?= strtoupper(substr($record['first_name'], 0, 1) . substr($record['last_name'], 0, 1)) ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></div>
                                            <div class="text-sm text-gray-500">ID: <?= htmlspecialchars($record['student_id']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium">
                                        <?= htmlspecialchars($record['class_name'] . ' ' . $record['section']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= $record['total_days'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?= $record['present_days'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <?= $record['absent_days'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <?= $record['late_days'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= $record['half_days'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 h-2 bg-gray-200 rounded-full mr-3 overflow-hidden">
                                            <div class="h-full <?= $progress_class ?> rounded-full transition-all duration-300" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900"><?= $percentage ?>%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge_class ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($attendance_data)): ?>
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center">
                                    <i class="fas fa-chart-line text-gray-300 text-5xl mb-3"></i>
                                    <h5 class="text-gray-500 text-lg font-medium mb-1">No attendance data found</h5>
                                    <p class="text-gray-400">No data available for the selected criteria and date range.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 flex flex-wrap gap-4">
                <button onclick="printReport()" class="btn btn2">
                    <i class="fas fa-print mr-2"></i>
                    Print Report
                </button>
                <button onclick="emailReport()" class="btn btn3">
                    <i class="fas fa-envelope mr-2"></i>
                    Email Report
                </button>
                <a href="attendance_summary.php" class="btn btn1">
                    <i class="fas fa-chart-pie mr-2"></i>
                    View Summary
                </a>
            </div>
        </main>
    </div>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
        
    <script>
        function exportToCSV() {
            const table = document.getElementById('attendanceTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cols = row.querySelectorAll('td, th');
                let csvRow = [];
                
                for (let j = 0; j < cols.length - 1; j++) { // Exclude the progress bar column
                    let cellText = cols[j].innerText.replace(/"/g, '""');
                    if (j === 0 && i > 0) { // Student name column
                        cellText = cellText.split('\n')[0]; // Get only the name, not the ID
                    }
                    csvRow.push('"' + cellText + '"');
                }
                
                csv.push(csvRow.join(','));
            }
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'attendance_report_<?= date("Y-m-d") ?>.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function printReport() {
            window.print();
        }

        function emailReport() {
            // This would typically open an email modal or redirect to email functionality
            alert('Email functionality would be implemented here');
        }

        // Add smooth animations to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                if (row.querySelector('td[colspan]')) return; // Skip empty state row
                
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });

            // Add smooth animations to class cards
            const classCards = document.querySelectorAll('.grid > div');
            classCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Add print styles
        const printStyles = `
            @media print {
                .btn, button, .no-print { display: none !important; }
                .bg-gradient-to-r { background: #3b82f6 !important; }
                body { background: white !important; }
                .shadow, .shadow-lg, .shadow-md { box-shadow: none !important; }
            }
        `;
        const styleSheet = document.createElement('style');
        styleSheet.textContent = printStyles;
        document.head.appendChild(styleSheet);
    </script>
</body>
</html>
