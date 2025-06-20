<?php 
// Include necessary files
include_once '../App/Models/headoffice/LeaveManage.php';
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management - School LMS</title>
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
                            <i class="fas fa-calendar-alt mr-3"></i>
                            Leave Management
                        </h1>
                        <p class="text-blue-100">Review and manage leave applications</p>
                    </div>
                    <div class="flex space-x-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold"><?= $stats['total_applications'] ?></div>
                            <div class="text-sm text-blue-100">Total Applications</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-200"><?= $stats['pending_applications'] ?></div>
                            <div class="text-sm text-blue-100">Pending</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Key Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fas fa-clipboard text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total_applications'] ?></p>
                    <p class="text-sm text-gray-500">Total Applications</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['pending_applications'] ?></p>
                    <p class="text-sm text-gray-500">Pending</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['approved_applications'] ?></p>
                    <p class="text-sm text-gray-500">Approved</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                            <i class="fas fa-times text-xl"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['rejected_applications'] ?></p>
                    <p class="text-sm text-gray-500">Rejected</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow mb-6">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-filter text-blue-600 mr-2"></i>
                        Filter Applications
                    </h2>
                </div>
                <div class="p-5">
                    <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="form-label">Status</label>
                            <select name="status" class="form-input">
                                <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Status</option>
                                <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $status_filter == 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">User Type</label>
                            <select name="user_type" class="form-input">
                                <option value="all" <?= $user_type_filter == 'all' ? 'selected' : '' ?>>All Types</option>
                                <option value="student" <?= $user_type_filter == 'student' ? 'selected' : '' ?>>Students</option>
                                <option value="teacher" <?= $user_type_filter == 'teacher' ? 'selected' : '' ?>>Teachers</option>
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
                                <i class="fas fa-search mr-2"></i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Leave Applications -->
            <div class="bg-white rounded-xl shadow mb-6">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                        Leave Applications
                    </h2>
                    <span class="text-sm text-gray-500"><?= count($leave_applications) ?> of <?= $total_records ?> records</span>
                </div>
                <div class="p-5">
                    <?php if (empty($leave_applications)): ?>
                    <div class="text-center py-10">
                        <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                        <h5 class="text-gray-500 text-lg font-medium mb-2">No leave applications found</h5>
                        <p class="text-gray-400">No applications match your current filter criteria.</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($leave_applications as $leave): ?>
                        <div class="bg-white rounded-xl border-2 <?= $leave['status'] == 'pending' ? 'border-yellow-200 bg-yellow-50' : ($leave['status'] == 'approved' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50') ?> shadow-sm hover:shadow-md transition-all duration-300 p-5">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br <?= $leave['status'] == 'pending' ? 'from-yellow-400 to-orange-500' : ($leave['status'] == 'approved' ? 'from-green-400 to-emerald-600' : 'from-red-400 to-rose-600') ?> flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-lg">
                                    <?= strtoupper(substr($leave['first_name'], 0, 1) . substr($leave['last_name'], 0, 1)) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h3>
                                        <span class="px-3 py-1 rounded-full text-sm font-bold <?= $leave['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : ($leave['status'] == 'approved' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300') ?>">
                                            <i class="fas <?= $leave['status'] == 'pending' ? 'fa-clock' : ($leave['status'] == 'approved' ? 'fa-check' : 'fa-times') ?> mr-1"></i>
                                            <?= htmlspecialchars(ucfirst($leave['status'])) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center mb-4">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200 mr-3">
                                            <i class="fas <?= $leave['user_type'] == 'student' ? 'fa-user-graduate' : 'fa-chalkboard-teacher' ?> mr-1"></i>
                                            <?= htmlspecialchars(ucfirst($leave['user_type'])) ?>
                                        </span>
                                        <?php if ($leave['user_type'] == 'student'): ?>
                                        <span class="text-sm text-gray-600 font-medium">ID: <?= htmlspecialchars($leave['identifier']) ?></span>
                                        <?php else: ?>
                                        <span class="text-sm text-gray-600 font-medium"><?= htmlspecialchars($leave['email']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                        <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                            <p class="text-xs text-gray-500 font-medium mb-1">Leave Type</p>
                                            <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars(ucfirst($leave['leave_type'])) ?></p>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                            <p class="text-xs text-gray-500 font-medium mb-1">Duration</p>
                                            <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars(date('M d', strtotime($leave['from_date']))) ?> - <?= htmlspecialchars(date('M d', strtotime($leave['to_date']))) ?></p>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                            <p class="text-xs text-gray-500 font-medium mb-1">Total Days</p>
                                            <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($leave['total_days']) ?> days</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                            <p class="text-xs text-gray-500 font-medium mb-1">Applied Date</p>
                                            <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars(date('M d, Y', strtotime($leave['applied_date']))) ?></p>
                                        </div>
                                    </div>
                                    
                                    <?php if ($leave['reason']): ?>
                                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm mb-4">
                                        <p class="text-xs text-gray-500 font-medium mb-2">
                                            <i class="fas fa-comment-alt mr-1"></i>
                                            Reason
                                        </p>
                                        <p class="text-sm text-gray-700 leading-relaxed"><?= htmlspecialchars($leave['reason']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($leave['status'] != 'pending' && $leave['approver_first_name']): ?>
                                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm mb-4">
                                        <p class="text-xs text-gray-500 font-medium mb-2">
                                            <i class="fas <?= $leave['status'] == 'approved' ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' ?> mr-1"></i>
                                            <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?> by
                                        </p>
                                        <p class="text-sm font-medium text-gray-800">
                                            <?= htmlspecialchars($leave['approver_first_name'] . ' ' . $leave['approver_last_name']) ?>
                                            <?php if ($leave['approved_date']): ?>
                                            <span class="text-xs text-gray-500 ml-2">on <?= htmlspecialchars(date('M d, Y', strtotime($leave['approved_date']))) ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($leave['rejection_reason']): ?>
                                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm mb-4">
                                        <p class="text-xs text-gray-500 font-medium mb-2">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <?= $leave['status'] == 'approved' ? 'Remarks' : 'Rejection Reason' ?>
                                        </p>
                                        <p class="text-sm text-gray-700 leading-relaxed"><?= htmlspecialchars($leave['rejection_reason']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex flex-wrap gap-3">
                                        <a href="leave_details.php?id=<?= $leave['id'] ?>" class="btn btn2 btn-sm">
                                            <i class="fas fa-eye mr-1"></i>
                                            View Details
                                        </a>
                                        
                                        <?php if ($leave['status'] == 'pending'): ?>
                                        <button type="button" onclick="openApproveModal(<?= $leave['id'] ?>, '<?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?>')" class="btn btn3 btn-sm">
                                            <i class="fas fa-check mr-1"></i>
                                            Approve
                                        </button>
                                        <button type="button" onclick="openRejectModal(<?= $leave['id'] ?>, '<?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?>')" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                                            <i class="fas fa-times mr-1"></i>
                                            Reject
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <?php if ($page > 1): ?>
                            <a href="?page=1<?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="?page=<?= $page - 1 ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                <i class="fas fa-angle-left"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                            <a href="?page=<?= $i ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" class="px-4 py-2 text-sm font-medium <?= $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' ?> border rounded-lg transition-colors">
                                <?= $i ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="?page=<?= $total_pages ?><?= $status_filter != 'all' ? '&status=' . urlencode($status_filter) : '' ?><?= $user_type_filter != 'all' ? '&user_type=' . urlencode($user_type_filter) : '' ?><?= $date_from ? '&date_from=' . urlencode($date_from) : '' ?><?= $date_to ? '&date_to=' . urlencode($date_to) : '' ?>" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95" id="approveModalContent">
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-check-circle mr-2"></i>
                        Approve Leave Application
                    </h3>
                    <button onclick="closeApproveModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-700 mb-2">Are you sure you want to approve this leave application for</p>
                    <p class="font-semibold text-gray-900" id="approveUserName">User Name</p>
                </div>
                
                <form method="post" id="approveForm">
                    <input type="hidden" name="leave_id" id="approveLeaveId">
                    <input type="hidden" name="action" value="approve">
                    
                    <div class="mb-4">
                        <label class="form-label">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-input" rows="3" placeholder="Add any remarks or conditions..."></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeApproveModal()" class="btn btn2 flex-1">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn3 flex-1">
                            <i class="fas fa-check mr-2"></i>
                            Approve Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95" id="rejectModalContent">
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-times-circle mr-2"></i>
                        Reject Leave Application
                    </h3>
                    <button onclick="closeRejectModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-700 mb-2">Are you sure you want to reject this leave application for</p>
                    <p class="font-semibold text-gray-900" id="rejectUserName">User Name</p>
                </div>
                
                <form method="post" id="rejectForm">
                    <input type="hidden" name="leave_id" id="rejectLeaveId">
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="mb-4">
                        <label class="form-label">Reason for Rejection <span class="text-red-500">*</span></label>
                        <textarea name="remarks" class="form-input" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeRejectModal()" class="btn btn2 flex-1">
                            Cancel
                        </button>
                        <button type="submit" class="btn flex-1" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                            <i class="fas fa-times mr-2"></i>
                            Reject Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include sidebar -->
    <?php include '../include/sidebar.php'; ?>

    <script>
        // Modal functionality
        function openApproveModal(leaveId, userName) {
            document.getElementById('approveLeaveId').value = leaveId;
            document.getElementById('approveUserName').textContent = userName;
            const modal = document.getElementById('approveModal');
            const content = document.getElementById('approveModalContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeApproveModal() {
            const modal = document.getElementById('approveModal');
            const content = document.getElementById('approveModalContent');
            
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                // Reset form
                document.getElementById('approveForm').reset();
            }, 300);
        }

        function openRejectModal(leaveId, userName) {
            document.getElementById('rejectLeaveId').value = leaveId;
            document.getElementById('rejectUserName').textContent = userName;
            const modal = document.getElementById('rejectModal');
            const content = document.getElementById('rejectModalContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            const content = document.getElementById('rejectModalContent');
            
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                // Reset form
                document.getElementById('rejectForm').reset();
            }, 300);
        }

        // Close modals when clicking outside
        document.getElementById('approveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });

        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApproveModal();
                closeRejectModal();
            }
        });

        // Add smooth animations to leave cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.space-y-4 > div');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
