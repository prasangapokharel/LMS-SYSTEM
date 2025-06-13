<?php 
// Include necessary files
include_once '../App/Models/headoffice/Logteacher.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Logs Management - School LMS</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-grey-50 min-h-screen">
    <!-- Main Content -->
    <main class="main-content">
        <div class="p-4 lg:p-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl p-6 text-white relative overflow-hidden shadow-lg">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                            <defs>
                                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#grid)" />
                        </svg>
                    </div>
                    
                    <div class="relative">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div class="mb-4 lg:mb-0">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h1 class="text-2xl lg:text-3xl font-bold">Teacher Logs</h1>
                                </div>
                                <p class="text-white text-opacity-90">Monitor and analyze teaching activities</p>
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
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-grey-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-grey-900 mb-1"><?= $stats['total_logs'] ?></p>
                        <p class="text-xs text-grey-500">Total Logs</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm border border-grey-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">This Month</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-grey-900 mb-1"><?= $stats['logs_this_month'] ?></p>
                        <p class="text-xs text-grey-500">Monthly Logs</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm border border-grey-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-grey-900 mb-1"><?= $stats['active_teachers'] ?></p>
                        <p class="text-xs text-grey-500">Active Teachers</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm border border-grey-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-grey-900 mb-1"><?= $stats['avg_logs_per_teacher'] ?></p>
                        <p class="text-xs text-grey-500">Avg per Teacher</p>
                    </div>
                </div>
            </div>

            <!-- Filters and Export -->
            <div class="bg-white rounded-lg shadow-sm border border-grey-100 mb-6">
                <div class="p-4 border-b border-grey-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-lg font-semibold text-grey-900 flex items-center mb-3 sm:mb-0">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                            </svg>
                            Filter & Search
                        </h2>
                        <div class="flex space-x-2">
                            <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" 
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export CSV
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-grey-700 mb-1">Teacher</label>
                            <select name="teacher_id" class="w-full px-3 py-2 text-sm border border-grey-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Teachers</option>
                                <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" <?= $teacher_id == $teacher['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-grey-700 mb-1">Class</label>
                            <select name="class_id" class="w-full px-3 py-2 text-sm border border-grey-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Classes</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= $class_id == $class['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-grey-700 mb-1">Subject</label>
                            <select name="subject_id" class="w-full px-3 py-2 text-sm border border-grey-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= $subject_id == $subject['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($subject['subject_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-grey-700 mb-1">Date From</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" 
                                class="w-full px-3 py-2 text-sm border border-grey-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-grey-700 mb-1">Date To</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" 
                                class="w-full px-3 py-2 text-sm border border-grey-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-grey-700 mb-1">Search</label>
                            <div class="relative">
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                    placeholder="Search logs..." 
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-grey-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-grey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="xl:col-span-6 flex items-center space-x-2 mt-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Apply Filters
                            </button>
                            <a href="teacher_logs.php" class="px-4 py-2 bg-grey-100 text-grey-700 text-sm rounded-lg hover:bg-grey-200 focus:outline-none focus:ring-2 focus:ring-grey-500 focus:ring-offset-2 transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Teacher Logs Table -->
            <div class="bg-white rounded-lg shadow-sm border border-grey-100 mb-6 overflow-hidden">
                <div class="p-4 border-b border-grey-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-grey-900 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Teacher Logs (<?= $total_records ?> records)
                        </h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-grey-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Teacher</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Class</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Subject</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Chapter</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Duration</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Present</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-grey-200">
                            <?php if (empty($teacher_logs)): ?>
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-grey-500">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-grey-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-base">No teacher logs found</p>
                                    <p class="text-sm text-grey-400 mt-1">Try adjusting your search criteria</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($teacher_logs as $log): ?>
                            <tr class="hover:bg-grey-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-grey-900">
                                    <?= date('M j, Y', strtotime($log['log_date'])) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs font-medium text-blue-600">
                                                <?= strtoupper(substr($log['first_name'], 0, 1) . substr($log['last_name'], 0, 1)) ?>
                                            </span>
                                        </div>
                                        <div class="text-sm font-medium text-grey-900">
                                            <?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-grey-900">
                                    <?= htmlspecialchars($log['class_name'] . ' ' . $log['section']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($log['subject_name']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-grey-900 max-w-xs truncate">
                                    <?= htmlspecialchars($log['chapter_title']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-grey-900">
                                    <?= htmlspecialchars($log['teaching_method'] ?: 'N/A') ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-grey-900">
                                    <?= $log['lesson_duration'] ? $log['lesson_duration'] . ' min' : 'N/A' ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-grey-900">
                                    <?= $log['students_present'] ?: 'N/A' ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewLogDetails(<?= htmlspecialchars(json_encode($log)) ?>)" 
                                            class="text-blue-600 hover:text-blue-900 focus:outline-none focus:underline">
                                        View
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
                <div class="px-4 py-3 border-t border-grey-200">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-grey-700">
                            Showing <?= ($page - 1) * $per_page + 1 ?> to <?= min($page * $per_page, $total_records) ?> of <?= $total_records ?> results
                        </div>
                        <div class="flex space-x-1">
                            <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                            class="px-3 py-1 text-xs bg-grey-100 text-grey-700 rounded hover:bg-grey-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Previous
                            </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                            class="px-3 py-1 text-xs <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-grey-100 text-grey-700 hover:bg-grey-200' ?> rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <?= $i ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                            class="px-3 py-1 text-xs bg-grey-100 text-grey-700 rounded hover:bg-grey-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
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
                <div class="bg-white rounded-lg shadow-sm border border-grey-100">
                    <div class="p-4 border-b border-grey-100">
                        <h3 class="text-base font-semibold text-grey-900 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Teaching Methods Distribution
                        </h3>
                    </div>
                    <div class="p-4">
                        <?php if (!empty($teaching_methods)): ?>
                            <?php foreach ($teaching_methods as $method): ?>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-grey-700"><?= htmlspecialchars($method['teaching_method']) ?></span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-grey-200 rounded-full h-2 mr-3">
                                        <div class="bg-purple-600 h-2 rounded-full" 
                                            style="width: <?= ($method['count'] / $teaching_methods[0]['count']) * 100 ?>%"></div>
                                    </div>
                                    <span class="text-xs text-grey-600"><?= $method['count'] ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-32">
                                <p class="text-grey-500 text-sm">No teaching method data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Monthly Trends -->
                <div class="bg-white rounded-lg shadow-sm border border-grey-100">
                    <div class="p-4 border-b border-grey-100">
                        <h3 class="text-base font-semibold text-grey-900 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Monthly Log Trends
                        </h3>
                    </div>
                    <div class="p-4">
                        <?php if (!empty($monthly_trends)): ?>
                            <?php foreach ($monthly_trends as $trend): ?>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-grey-700"><?= date('M Y', strtotime($trend['month'] . '-01')) ?></span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-grey-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-600 h-2 rounded-full" 
                                            style="width: <?= ($trend['count'] / max(array_column($monthly_trends, 'count'))) * 100 ?>%"></div>
                                    </div>
                                    <span class="text-xs text-grey-600"><?= $trend['count'] ?> logs</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-32">
                                <p class="text-grey-500 text-sm">No monthly trend data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Log Details Modal -->
    <div id="logModal" class="fixed inset-0 bg-grey-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto m-4">
            <div class="p-4 border-b border-grey-200 sticky top-0 bg-white z-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-grey-900">Teacher Log Details</h3>
                    <button onclick="closeModal()" class="text-grey-400 hover:text-grey-600 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="logModalContent" class="p-4">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="p-4 border-t border-grey-200 bg-grey-50 flex justify-end">
                <button onclick="closeModal()" class="px-4 py-2 bg-grey-100 text-grey-700 text-sm rounded-lg hover:bg-grey-200 focus:outline-none focus:ring-2 focus:ring-grey-500 focus:ring-offset-2 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function viewLogDetails(log) {
            const modal = document.getElementById('logModal');
            const content = document.getElementById('logModalContent');
            
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-grey-900 mb-3">Basic Information</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Date</label>
                                <p class="text-sm text-grey-900">${new Date(log.log_date).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Teacher</label>
                                <p class="text-sm text-grey-900">${log.first_name} ${log.last_name}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Class</label>
                                <p class="text-sm text-grey-900">${log.class_name} ${log.section}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Subject</label>
                                <p class="text-sm text-grey-900">${log.subject_name}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Teaching Method</label>
                                <p class="text-sm text-grey-900">${log.teaching_method || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Duration</label>
                                <p class="text-sm text-grey-900">${log.lesson_duration ? log.lesson_duration + ' minutes' : 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Students Present</label>
                                <p class="text-sm text-grey-900">${log.students_present || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-grey-900 mb-3">Content Details</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Chapter Title</label>
                                <p class="text-sm text-grey-900">${log.chapter_title}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Chapter Content</label>
                                <p class="text-sm text-grey-900">${log.chapter_content || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Topics Covered</label>
                                <p class="text-sm text-grey-900">${log.topics_covered || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Homework Assigned</label>
                                <p class="text-sm text-grey-900">${log.homework_assigned || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-grey-500 block">Additional Notes</label>
                                <p class="text-sm text-grey-900">${log.notes || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        
        function closeModal() {
            document.getElementById('logModal').classList.add('hidden');
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
            if (e.key === 'Escape' && !document.getElementById('logModal').classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>
</body>
</html>
