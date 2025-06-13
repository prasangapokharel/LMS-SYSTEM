<?php 
// Include necessary files
include_once '../App/Models/headoffice/Leave.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/ui.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
  
        <?php include_once '../include/sidebar.php'; ?>
    
     
    <div class="flex">
        <!-- Main Content -->
        <div class="flex-1 ml-0 lg:ml-64 p-4 lg:p-8">
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex items-center mb-2">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-check text-xl"></i>
                            </div>
                            <h1 class="text-2xl lg:text-3xl font-bold">Leave Management</h1>
                        </div>
                        <p class="text-white text-opacity-90">
                            Review and manage leave applications from students and teachers
                        </p>
                        
                        <nav class="mt-4">
                            <ol class="flex space-x-2 text-sm">
                                <li><a href="index.php" class="text-white">Dashboard</a></li>
                                <li><span class="text-white opacity-70 mx-2">/</span></li>
                                <li class="text-white opacity-90">Leave Management</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-indigo-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard text-indigo-500 text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['total_applications'] ?></p>
                        <p class="text-sm text-gray-600">Total Applications</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-yellow-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['pending_applications'] ?></p>
                        <p class="text-sm text-gray-600">Pending</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check text-green-500 text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['approved_applications'] ?></p>
                        <p class="text-sm text-gray-600">Approved</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-red-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-times text-red-500 text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['rejected_applications'] ?></p>
                        <p class="text-sm text-gray-600">Rejected</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Filter Applications
                </h2>
                <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $status_filter == 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">User Type</label>
                        <select name="user_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="all" <?= $user_type_filter == 'all' ? 'selected' : '' ?>>All Users</option>
                            <option value="student" <?= $user_type_filter == 'student' ? 'selected' : '' ?>>Students</option>
                            <option value="teacher" <?= $user_type_filter == 'teacher' ? 'selected' : '' ?>>Teachers</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="date_from" value="<?= $date_from ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="<?= $date_to ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg flex items-center">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>
                        
                        <a href="leave_management.php" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg flex items-center">
                            <i class="fas fa-redo-alt mr-2"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Leave Applications -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-list-alt mr-2"></i>
                        Leave Applications
                    </h2>
                </div>
                
                <?php if (empty($leave_applications)): ?>
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Leave Applications Found</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        There are no leave applications matching your filter criteria. Try adjusting your filters or check back later.
                    </p>
                </div>
                <?php else: ?>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($leave_applications as $leave): ?>
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full 
                                    <?= $leave['user_type'] == 'student' ? 'bg-blue-600' : 'bg-purple-600' ?> 
                                    flex items-center justify-center text-white font-medium text-sm mr-3">
                                    <?= strtoupper(substr($leave['first_name'], 0, 1) . substr($leave['last_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= ucfirst($leave['user_type']) ?> 
                                        <?php if ($leave['identifier']): ?>
                                        • ID: <?= htmlspecialchars($leave['identifier']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <?php if ($leave['status'] == 'pending'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                    Pending
                                </span>
                                <?php elseif ($leave['status'] == 'approved'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                    Approved
                                </span>
                                <?php elseif ($leave['status'] == 'rejected'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                    Rejected
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                    <?= ucfirst($leave['status']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">From Date</div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= date('M d, Y', strtotime($leave['from_date'])) ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">To Date</div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= date('M d, Y', strtotime($leave['to_date'])) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="text-xs text-gray-500 mb-1">Leave Type</div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($leave['leave_type']) ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="text-xs text-gray-500 mb-1">Reason</div>
                                <div class="text-sm text-gray-700 line-clamp-2">
                                    <?= htmlspecialchars($leave['reason']) ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="text-xs text-gray-500 mb-1">Applied On</div>
                                <div class="text-sm text-gray-700">
                                    <?= date('M d, Y', strtotime($leave['applied_date'])) ?>
                                </div>
                            </div>
                            
                            <?php if ($leave['status'] == 'approved' || $leave['status'] == 'rejected'): ?>
                            <div class="mb-4">
                                <div class="text-xs text-gray-500 mb-1">
                                    <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?> By
                                </div>
                                <div class="text-sm text-gray-700">
                                    <?= htmlspecialchars($leave['approver_first_name'] . ' ' . $leave['approver_last_name']) ?>
                                    on <?= date('M d, Y', strtotime($leave['approved_date'])) ?>
                                </div>
                            </div>
                            
                            <?php if ($leave['status'] == 'rejected' && $leave['rejection_reason']): ?>
                            <div class="mb-4">
                                <div class="text-xs text-gray-500 mb-1">Rejection Reason</div>
                                <div class="text-sm text-red-600">
                                    <?= htmlspecialchars($leave['rejection_reason']) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                            
                            <div class="flex justify-end">
                                <button type="button" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm"
                                        data-modal-target="viewLeaveModal<?= $leave['id'] ?>">
                                    <i class="fas fa-eye mr-1"></i>
                                    View Details
                                </button>
                            </div>
                        </div>
                        
                        <!-- View Leave Modal -->
                        <div id="viewLeaveModal<?= $leave['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="fixed inset-0 bg-black bg-opacity-50"
                                     data-modal-close="viewLeaveModal<?= $leave['id'] ?>"></div>
                                
                                <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-auto z-10 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-lg font-bold text-white flex items-center">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                Leave Application Details
                                            </h3>
                                            <button type="button" class="text-white"
                                                    data-modal-close="viewLeaveModal<?= $leave['id'] ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <div class="flex items-center mb-6">
                                            <div class="w-12 h-12 rounded-full 
                                                <?= $leave['user_type'] == 'student' ? 'bg-blue-600' : 'bg-purple-600' ?> 
                                                flex items-center justify-center text-white font-medium text-lg mr-4">
                                                <?= strtoupper(substr($leave['first_name'], 0, 1) . substr($leave['last_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="text-lg font-medium text-gray-900">
                                                    <?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($leave['email']) ?> • <?= ucfirst($leave['user_type']) ?>
                                                    <?php if ($leave['identifier']): ?>
                                                    • ID: <?= htmlspecialchars($leave['identifier']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                            <div>
                                                <div class="text-sm text-gray-500 mb-1">Leave Type</div>
                                                <div class="text-base font-medium text-gray-900">
                                                    <?= htmlspecialchars($leave['leave_type']) ?>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="text-sm text-gray-500 mb-1">Status</div>
                                                <div>
                                                    <?php if ($leave['status'] == 'pending'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                                        Pending
                                                    </span>
                                                    <?php elseif ($leave['status'] == 'approved'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                                        Approved
                                                    </span>
                                                    <?php elseif ($leave['status'] == 'rejected'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                                        Rejected
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                                        <?= ucfirst($leave['status']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="text-sm text-gray-500 mb-1">From Date</div>
                                                <div class="text-base font-medium text-gray-900">
                                                    <?= date('F d, Y', strtotime($leave['from_date'])) ?>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="text-sm text-gray-500 mb-1">To Date</div>
                                                <div class="text-base font-medium text-gray-900">
                                                    <?= date('F d, Y', strtotime($leave['to_date'])) ?>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="text-sm text-gray-500 mb-1">Duration</div>
                                                <div class="text-base font-medium text-gray-900">
                                                    <?php
                                                    $from = new DateTime($leave['from_date']);
                                                    $to = new DateTime($leave['to_date']);
                                                    $interval = $from->diff($to);
                                                    echo $interval->days + 1 . ' day' . ($interval->days > 0 ? 's' : '');
                                                    ?>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="text-sm text-gray-500 mb-1">Applied On</div>
                                                <div class="text-base font-medium text-gray-900">
                                                    <?= date('F d, Y', strtotime($leave['applied_date'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-6">
                                            <div class="text-sm text-gray-500 mb-1">Reason</div>
                                            <div class="text-base text-gray-700 p-3 bg-gray-50 rounded-lg">
                                                <?= nl2br(htmlspecialchars($leave['reason'])) ?>
                                            </div>
                                        </div>
                                        
                                        <?php if ($leave['status'] == 'approved' || $leave['status'] == 'rejected'): ?>
                                        <div class="mb-6">
                                            <div class="text-sm text-gray-500 mb-1">
                                                <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?> By
                                            </div>
                                            <div class="text-base text-gray-700">
                                                <?= htmlspecialchars($leave['approver_first_name'] . ' ' . $leave['approver_last_name']) ?>
                                                on <?= date('F d, Y', strtotime($leave['approved_date'])) ?>
                                            </div>
                                        </div>
                                        
                                        <?php if ($leave['status'] == 'rejected' && $leave['rejection_reason']): ?>
                                        <div class="mb-6">
                                            <div class="text-sm text-gray-500 mb-1">Rejection Reason</div>
                                            <div class="text-base text-red-600 p-3 bg-red-50 rounded-lg">
                                                <?= nl2br(htmlspecialchars($leave['rejection_reason'])) ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if ($leave['status'] == 'pending'): ?>
                                        <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
                                            <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg"
                                                    data-modal-target="rejectLeaveModal<?= $leave['id'] ?>">
                                                <i class="fas fa-times mr-1"></i>
                                                Reject
                                            </button>
                                            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg"
                                                    data-modal-target="approveLeaveModal<?= $leave['id'] ?>">
                                                <i class="fas fa-check mr-1"></i>
                                                Approve
                                            </button>
                                        </div>
                                        <?php else: ?>
                                        <div class="border-t border-gray-200 pt-6 flex justify-end">
                                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                                                    data-modal-close="viewLeaveModal<?= $leave['id'] ?>">
                                                Close
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Approve Leave Modal -->
                        <div id="approveLeaveModal<?= $leave['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="fixed inset-0 bg-black bg-opacity-50"
                                     data-modal-close="approveLeaveModal<?= $leave['id'] ?>"></div>
                                
                                <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-5">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-lg font-bold text-white flex items-center">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Approve Leave Application
                                            </h3>
                                            <button type="button" class="text-white"
                                                    data-modal-close="approveLeaveModal<?= $leave['id'] ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <form method="post" class="p-6">
                                        <input type="hidden" name="leave_id" value="<?= $leave['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        
                                        <div class="text-center mb-6">
                                            <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-check text-green-500 text-2xl"></i>
                                            </div>
                                            
                                            <p class="text-gray-700 mb-2">
                                                Are you sure you want to approve the leave application for 
                                                <strong><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></strong>?
                                            </p>
                                            
                                            <p class="text-sm text-gray-500">
                                                From <?= date('M d, Y', strtotime($leave['from_date'])) ?> 
                                                to <?= date('M d, Y', strtotime($leave['to_date'])) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Remarks (Optional)
                                            </label>
                                            <textarea name="remarks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                        </div>
                                        
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                                                    data-modal-close="approveLeaveModal<?= $leave['id'] ?>">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg">
                                                <i class="fas fa-check mr-1"></i>
                                                Confirm Approval
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reject Leave Modal -->
                        <div id="rejectLeaveModal<?= $leave['id'] ?>" class="fixed inset-0 z-50 hidden overflow-y-auto">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="fixed inset-0 bg-black bg-opacity-50"
                                     data-modal-close="rejectLeaveModal<?= $leave['id'] ?>"></div>
                                
                                <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-auto z-10 overflow-hidden">
                                    <div class="bg-gradient-to-r from-red-600 to-red-700 p-5">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-lg font-bold text-white flex items-center">
                                                <i class="fas fa-times-circle mr-2"></i>
                                                Reject Leave Application
                                            </h3>
                                            <button type="button" class="text-white"
                                                    data-modal-close="rejectLeaveModal<?= $leave['id'] ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <form method="post" class="p-6">
                                        <input type="hidden" name="leave_id" value="<?= $leave['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        
                                        <div class="text-center mb-6">
                                            <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-times text-red-500 text-2xl"></i>
                                            </div>
                                            
                                            <p class="text-gray-700 mb-2">
                                                Are you sure you want to reject the leave application for 
                                                <strong><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></strong>?
                                            </p>
                                            
                                            <p class="text-sm text-gray-500">
                                                From <?= date('M d, Y', strtotime($leave['from_date'])) ?> 
                                                to <?= date('M d, Y', strtotime($leave['to_date'])) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Rejection Reason <span class="text-red-500">*</span>
                                            </label>
                                            <textarea name="remarks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required></textarea>
                                            <p class="mt-1 text-sm text-gray-500">Please provide a reason for rejecting this leave application.</p>
                                        </div>
                                        
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                                                    data-modal-close="rejectLeaveModal<?= $leave['id'] ?>">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg">
                                                <i class="fas fa-times mr-1"></i>
                                                Confirm Rejection
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="p-5 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing <?= ($page - 1) * $per_page + 1 ?> to <?= min($page * $per_page, $total_records) ?> 
                            of <?= $total_records ?> applications
                        </div>
                        
                        <div class="flex space-x-1">
                            <?php if ($page > 1): ?>
                            <a href="?status=<?= $status_filter ?>&user_type=<?= $user_type_filter ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>&page=<?= $page - 1 ?>" 
                               class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<a href="?status=' . $status_filter . '&user_type=' . $user_type_filter . '&date_from=' . $date_from . '&date_to=' . $date_to . '&page=1" 
                                      class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg">1</a>';
                                
                                if ($start_page > 2) {
                                    echo '<span class="px-3 py-1">...</span>';
                                }
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo '<span class="px-3 py-1 bg-blue-600 text-white rounded-lg">' . $i . '</span>';
                                } else {
                                    echo '<a href="?status=' . $status_filter . '&user_type=' . $user_type_filter . '&date_from=' . $date_from . '&date_to=' . $date_to . '&page=' . $i . '" 
                                          class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg">' . $i . '</a>';
                                }
                            }
                            
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<span class="px-3 py-1">...</span>';
                                }
                                
                                echo '<a href="?status=' . $status_filter . '&user_type=' . $user_type_filter . '&date_from=' . $date_from . '&date_to=' . $date_to . '&page=' . $total_pages . '" 
                                      class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg">' . $total_pages . '</a>';
                            }
                            ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?status=<?= $status_filter ?>&user_type=<?= $user_type_filter ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>&page=<?= $page + 1 ?>" 
                               class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Modal open buttons
            document.querySelectorAll('[data-modal-target]').forEach(button => {
                button.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal-target');
                    document.getElementById(modalId).classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });
            
            // Modal close buttons
            document.querySelectorAll('[data-modal-close]').forEach(element => {
                element.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal-close');
                    document.getElementById(modalId).classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            });
        });
    </script>
</body>
</html>
