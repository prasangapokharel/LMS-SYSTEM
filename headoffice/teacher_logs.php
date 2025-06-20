<?php 
// Include necessary files
include_once '../App/Models/headoffice/Logteacher.php';
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Logs Management - School LMS</title>
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
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">
                            <i class="fas fa-clipboard-list mr-3"></i>
                            Teacher Logs Management
                        </h1>
                        <p class="text-blue-100">Monitor and analyze teaching activities</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-xl lg:text-2xl font-bold"><?= $stats['total_logs'] ?></div>
                            <div class="text-xs text-white text-opacity-80">Total Logs</div>
                        </div>
                        <div class="text-center bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-xl lg:text-2xl font-bold"><?= $stats['active_teachers'] ?></div>
                            <div class="text-xs text-white text-opacity-80">Active Teachers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fas fa-clipboard-list text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total_logs'] ?></p>
                    <p class="text-sm text-gray-500">Total Logs</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">This Month</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['logs_this_month'] ?></p>
                    <p class="text-sm text-gray-500">Monthly Logs</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['active_teachers'] ?></p>
                    <p class="text-sm text-gray-500">Active Teachers</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                            <i class="fas fa-chart-bar text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['avg_logs_per_teacher'] ?></p>
                    <p class="text-sm text-gray-500">Avg per Teacher</p>
                </div>
            </div>

            <!-- Filters and Export -->
            <div class="bg-white rounded-xl shadow mb-6">
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center mb-3 sm:mb-0">
                        <i class="fas fa-filter text-blue-600 mr-2"></i>
                        Filter & Search
                    </h2>
                    <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" class="btn btn3">
                        <i class="fas fa-download mr-2"></i>
                        Export CSV
                    </a>
                </div>
                <div class="p-5">
                    <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                        <div>
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-input">
                                <option value="">All Teachers</option>
                                <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" <?= $teacher_id == $teacher['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-input">
                                <option value="">All Classes</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= $class_id == $class['id'] ? 'selected' : '' ?>>
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
                                <option value="<?= $subject['id'] ?>" <?= $subject_id == $subject['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($subject['subject_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" class="form-input">
                        </div>
                        
                        <div>
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" class="form-input">
                        </div>
                        
                        <div>
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search logs..." class="form-input">
                        </div>
                        
                        <div class="xl:col-span-6 flex items-center space-x-2 mt-2">
                            <button type="submit" class="btn btn1">
                                <i class="fas fa-search mr-2"></i>
                                Apply Filters
                            </button>
                            <a href="teacher_logs.php" class="btn btn2">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Teacher Logs Table -->
            <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-green-700 p-5">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-list mr-2"></i>
                        Teacher Logs (<?= $total_records ?> records)
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chapter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($teacher_logs)): ?>
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center">
                                    <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-3"></i>
                                    <h5 class="text-gray-500 text-lg font-medium mb-1">No teacher logs found</h5>
                                    <p class="text-gray-400">Try adjusting your search criteria</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($teacher_logs as $log): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('M j, Y', strtotime($log['log_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                            <?= strtoupper(substr($log['first_name'], 0, 1) . substr($log['last_name'], 0, 1)) ?>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($log['class_name'] . ' ' . $log['section']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($log['subject_name']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    <?= htmlspecialchars($log['chapter_title']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($log['teaching_method'] ?: 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $log['lesson_duration'] ? $log['lesson_duration'] . ' min' : 'N/A' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $log['students_present'] ?: 'N/A' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewLogDetails(<?= htmlspecialchars(json_encode($log)) ?>)" class="text-blue-600 hover:text-blue-900 transition-colors p-1 hover:bg-blue-50 rounded">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="px-6 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <?= ($page - 1) * $per_page + 1 ?> to <?= min($page * $per_page, $total_records) ?> of <?= $total_records ?> results
                        </div>
                        <div class="flex space-x-1">
                            <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                Previous
                            </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="px-4 py-2 text-sm font-medium <?= $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' ?> border rounded-lg transition-colors">
                                <?= $i ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                Next
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Summary Reports -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Teaching Methods Distribution -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-chart-pie mr-2"></i>
                            Teaching Methods Distribution
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($teaching_methods)): ?>
                            <?php foreach ($teaching_methods as $method): ?>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($method['teaching_method']) ?></span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" 
                                            style="width: <?= ($method['count'] / $teaching_methods[0]['count']) * 100 ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 font-medium"><?= $method['count'] ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-32">
                                <p class="text-gray-500 text-sm">No teaching method data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Monthly Trends -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-chart-line mr-2"></i>
                            Monthly Log Trends
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($monthly_trends)): ?>
                            <?php foreach ($monthly_trends as $trend): ?>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-medium text-gray-700"><?= date('M Y', strtotime($trend['month'] . '-01')) ?></span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                            style="width: <?= ($trend['count'] / max(array_column($monthly_trends, 'count'))) * 100 ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 font-medium"><?= $trend['count'] ?> logs</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-32">
                                <p class="text-gray-500 text-sm">No monthly trend data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Log Details Modal -->
    <div id="logModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95" id="logModalContent">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Teacher Log Details
                    </h3>
                    <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div id="logModalBody" class="p-6">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-end rounded-b-xl">
                <button onclick="closeModal()" class="btn btn2">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <script>
        function viewLogDetails(log) {
            const modal = document.getElementById('logModal');
            const content = document.getElementById('logModalContent');
            const body = document.getElementById('logModalBody');
            
            body.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <h4 class="font-semibold text-blue-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Basic Information
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-blue-600 block">Date</label>
                                <p class="text-sm text-blue-800 font-medium">${new Date(log.log_date).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-blue-600 block">Teacher</label>
                                <p class="text-sm text-blue-800 font-medium">${log.first_name} ${log.last_name}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-blue-600 block">Class</label>
                                <p class="text-sm text-blue-800 font-medium">${log.class_name} ${log.section}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-blue-600 block">Subject</label>
                                <p class="text-sm text-blue-800 font-medium">${log.subject_name}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-blue-600 block">Teaching Method</label>
                                <p class="text-sm text-blue-800 font-medium">${log.teaching_method || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-blue-600 block">Duration</label>
                                <p class="text-sm text-blue-800 font-medium">${log.lesson_duration ? log.lesson_duration + ' minutes' : 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-blue-600 block">Students Present</label>
                                <p class="text-sm text-blue-800 font-medium">${log.students_present || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <h4 class="font-semibold text-green-800 mb-4 flex items-center">
                            <i class="fas fa-book mr-2"></i>
                            Content Details
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-green-600 block">Chapter Title</label>
                                <p class="text-sm text-green-800 font-medium">${log.chapter_title}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-green-600 block">Chapter Content</label>
                                <p class="text-sm text-green-800">${log.chapter_content || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-green-600 block">Topics Covered</label>
                                <p class="text-sm text-green-800">${log.topics_covered || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-green-600 block">Homework Assigned</label>
                                <p class="text-sm text-green-800">${log.homework_assigned || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-green-600 block">Additional Notes</label>
                                <p class="text-sm text-green-800">${log.notes || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
            
            document.body.classList.add('overflow-hidden');
        }
        
        function closeModal() {
            const modal = document.getElementById('logModal');
            const content = document.getElementById('logModalContent');
            
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            
            document.body.classList.remove('overflow-hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('logModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Add smooth animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('main > div');
            cards.forEach((card, index) => {
                if (card.classList.contains('fixed')) return; // Skip modals
                
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>