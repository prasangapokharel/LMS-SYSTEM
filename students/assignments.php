
<?php
include_once '../App/Models/student/Assignment.php';
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - School LMS</title>
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
            border-radius:  0 0;
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
                        ←
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Assignments</h1>
                        <p class="text-white text-opacity-80 text-sm">Manage your assignments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="p-6">
                <!-- Filters -->
                <!-- <div class="card p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Filter Assignments</h3>
                    
                    <form method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Assignments</option>
                                <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="overdue" <?= $status_filter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                                <option value="submitted" <?= $status_filter === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                                <option value="graded" <?= $status_filter === 'graded' ? 'selected' : '' ?>>Graded</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <select name="subject" class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="all">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['id'] ?>" <?= $subject_filter == $subject['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($subject['subject_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-medium">
                            Apply Filters
                        </button>
                    </form>
                </div> -->

                <!-- Assignments List -->
                <div class="space-y-4">
                    <?php if (empty($assignments)): ?>
                        <div class="card p-8 text-center">
                            <p class="text-gray-500 text-lg mb-2">No Assignments Found</p>
                            <p class="text-gray-400 text-sm">No assignments match your current filters</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($assignments as $assignment): ?>
                            <div class="card p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 text-lg mb-1">
                                            <?= htmlspecialchars($assignment['title']) ?>
                                        </h4>
                                        <p class="text-gray-600 mb-2">
                                            <?= htmlspecialchars($assignment['subject_name']) ?>
                                        </p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span>Due: <?= date('M j, Y', strtotime($assignment['due_date'])) ?></span>
                                            <span>•</span>
                                            <span><?= $assignment['max_marks'] ?> marks</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right ml-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            <?php if ($assignment['assignment_status'] === 'graded'): ?>
                                                bg-green-100 text-green-800
                                            <?php elseif ($assignment['assignment_status'] === 'submitted'): ?>
                                                bg-blue-100 text-blue-800
                                            <?php elseif ($assignment['assignment_status'] === 'overdue'): ?>
                                                bg-red-100 text-red-800
                                            <?php else: ?>
                                                bg-yellow-100 text-yellow-800
                                            <?php endif; ?>">
                                            <?= ucfirst($assignment['assignment_status']) ?>
                                        </span>
                                        
                                        <?php if ($assignment['grade']): ?>
                                            <div class="mt-2">
                                                <span class="text-xl font-bold text-gray-900">
                                                    <?= $assignment['grade'] ?>
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    / <?= $assignment['max_marks'] ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($assignment['description']): ?>
                                    <p class="text-gray-700 mb-3 text-sm leading-relaxed">
                                        <?= htmlspecialchars($assignment['description']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($assignment['feedback']): ?>
                                    <div class="bg-blue-50 p-3 rounded-lg mb-3">
                                        <p class="text-sm text-blue-800">
                                            <span class="font-medium">Teacher Feedback:</span><br>
                                            <?= htmlspecialchars($assignment['feedback']) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="text-xs text-gray-500">
                                        <?php if ($assignment['submission_date']): ?>
                                            Submitted on <?= date('M j, Y', strtotime($assignment['submission_date'])) ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="view_assignment.php?id=<?= $assignment['id'] ?>" 
                                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                            View Details
                                        </a>
                                        
                                        <?php if ($assignment['assignment_status'] === 'pending' || $assignment['assignment_status'] === 'overdue'): ?>
                                            <a href="submit_assignment.php?id=<?= $assignment['id'] ?>" 
                                               class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                                                Submit Now
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <?php include '../include/bootoomnav.php'; ?>
    </div>
</body>
</html>