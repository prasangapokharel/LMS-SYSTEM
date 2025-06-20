<?php
include_once '../App/Models/headoffice/Leavedetails.php';

// The rest of the PHP logic is handled by the Leavedetails model
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Details - School LMS</title>
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
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">
                            <i class="fas fa-file-alt mr-3"></i>
                            Leave Application Details
                        </h1>
                        <p class="text-blue-100">Review detailed information about this leave request</p>
                    </div>
                    <a href="leave_management.php" class="btn btn2 mt-4 md:mt-0">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Leave Management
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- User Information Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-l-4 <?= $leave['status'] == 'pending' ? 'border-yellow-400' : ($leave['status'] == 'approved' ? 'border-green-400' : 'border-red-400') ?>">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br <?= $leave['status'] == 'pending' ? 'from-yellow-400 to-orange-500' : ($leave['status'] == 'approved' ? 'from-green-400 to-emerald-600' : 'from-red-400 to-rose-600') ?> flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            <?= strtoupper(substr($leave['first_name'], 0, 1) . substr($leave['last_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></h3>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas <?= $leave['user_type'] == 'student' ? 'fa-user-graduate' : 'fa-chalkboard-teacher' ?> mr-1"></i>
                                    <?= htmlspecialchars(ucfirst($leave['user_type'])) ?>
                                </span>
                                <span class="px-3 py-1 rounded-full text-sm font-bold <?= $leave['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : ($leave['status'] == 'approved' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300') ?>">
                                    <i class="fas <?= $leave['status'] == 'pending' ? 'fa-clock' : ($leave['status'] == 'approved' ? 'fa-check' : 'fa-times') ?> mr-1"></i>
                                    <?= htmlspecialchars(ucfirst($leave['status'])) ?>
                                </span>
                            </div>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p class="flex items-center">
                                    <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                    <?= htmlspecialchars($leave['email']) ?>
                                </p>
                                <?php if ($leave['phone']): ?>
                                <p class="flex items-center">
                                    <i class="fas fa-phone mr-2 text-green-500"></i>
                                    <?= htmlspecialchars($leave['phone']) ?>
                                </p>
                                <?php endif; ?>
                                <?php if ($leave['user_type'] == 'student'): ?>
                                <p class="flex items-center">
                                    <i class="fas fa-id-card mr-2 text-purple-500"></i>
                                    Student ID: <?= htmlspecialchars($leave['identifier']) ?>
                                    <?php if ($leave['class_name']): ?>
                                    | Class: <?= htmlspecialchars($leave['class_name']) ?>
                                    <?php endif; ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($leave['status'] == 'pending'): ?>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" onclick="openApproveModal()" class="btn btn3">
                            <i class="fas fa-check mr-2"></i>
                            Approve
                        </button>
                        <button type="button" onclick="openRejectModal()" class="btn" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                            <i class="fas fa-times mr-2"></i>
                            Reject
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-5">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Leave Application Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-tag text-blue-600 mr-2"></i>
                                <span class="text-sm font-medium text-blue-700">Leave Type</span>
                            </div>
                            <p class="text-lg font-bold text-blue-800"><?= htmlspecialchars(ucfirst($leave['leave_type'])) ?></p>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-day text-green-600 mr-2"></i>
                                <span class="text-sm font-medium text-green-700">From Date</span>
                            </div>
                            <p class="text-lg font-bold text-green-800"><?= htmlspecialchars(date('M d, Y', strtotime($leave['from_date']))) ?></p>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-day text-green-600 mr-2"></i>
                                <span class="text-sm font-medium text-green-700">To Date</span>
                            </div>
                            <p class="text-lg font-bold text-green-800"><?= htmlspecialchars(date('M d, Y', strtotime($leave['to_date']))) ?></p>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-clock text-yellow-600 mr-2"></i>
                                <span class="text-sm font-medium text-yellow-700">Total Days</span>
                            </div>
                            <p class="text-lg font-bold text-yellow-800"><?= $duration ?> days</p>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-plus text-purple-600 mr-2"></i>
                                <span class="text-sm font-medium text-purple-700">Applied Date</span>
                            </div>
                            <p class="text-lg font-bold text-purple-800"><?= htmlspecialchars(date('M d, Y', strtotime($leave['applied_date']))) ?></p>
                            <p class="text-sm text-purple-600"><?= htmlspecialchars(date('g:i A', strtotime($leave['applied_date']))) ?></p>
                        </div>
                        
                        <?php if ($leave['emergency_contact']): ?>
                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-phone-alt text-red-600 mr-2"></i>
                                <span class="text-sm font-medium text-red-700">Emergency Contact</span>
                            </div>
                            <p class="text-lg font-bold text-red-800"><?= htmlspecialchars($leave['emergency_contact']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($leave['reason']): ?>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-comment-alt text-gray-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Reason for Leave</span>
                        </div>
                        <p class="text-gray-800 leading-relaxed"><?= nl2br(htmlspecialchars($leave['reason'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($leave['leave_details']): ?>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-blue-700">Additional Details</span>
                        </div>
                        <p class="text-blue-800 leading-relaxed"><?= nl2br(htmlspecialchars($leave['leave_details'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($leave['attachment_url']): ?>
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-paperclip text-yellow-600 mr-2"></i>
                            <span class="text-sm font-medium text-yellow-700">Attachment</span>
                        </div>
                        <div class="attachment-preview">
                            <?php 
                            $file_extension = strtolower(pathinfo($leave['attachment_url'], PATHINFO_EXTENSION));
                            if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): 
                            ?>
                            <div class="mb-4">
                                <img src="../<?= htmlspecialchars($leave['attachment_url']) ?>" alt="Leave attachment" class="max-w-full h-auto rounded-lg shadow-md border border-gray-200" style="max-height: 300px;">
                            </div>
                            <?php endif; ?>
                            <a href="../<?= htmlspecialchars($leave['attachment_url']) ?>" 
                               class="btn btn1 btn-sm" 
                               target="_blank">
                                <i class="fas fa-download mr-2"></i>
                                Download Attachment
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Approval History -->
            <?php if ($leave['status'] != 'pending'): ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 p-5">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-history mr-2"></i>
                        Approval History
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <!-- Timeline line -->
                        <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                        
                        <!-- Application Submitted -->
                        <div class="relative flex items-start mb-8">
                            <div class="flex-shrink-0 w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                <i class="fas fa-paper-plane text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-6 flex-1">
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <h3 class="text-lg font-bold text-blue-800 mb-2">Application Submitted</h3>
                                    <p class="text-blue-700 mb-2 flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <?= htmlspecialchars(date('M d, Y g:i A', strtotime($leave['applied_date']))) ?>
                                    </p>
                                    <p class="text-blue-600">Leave application submitted by <?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Application Decision -->
                        <div class="relative flex items-start">
                            <div class="flex-shrink-0 w-16 h-16 <?= $leave['status'] == 'approved' ? 'bg-green-100' : 'bg-red-100' ?> rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                <i class="fas <?= $leave['status'] == 'approved' ? 'fa-check text-green-600' : 'fa-times text-red-600' ?> text-xl"></i>
                            </div>
                            <div class="ml-6 flex-1">
                                <div class="<?= $leave['status'] == 'approved' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?> rounded-lg p-4 border">
                                    <h3 class="text-lg font-bold <?= $leave['status'] == 'approved' ? 'text-green-800' : 'text-red-800' ?> mb-2">
                                        Application <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?>
                                    </h3>
                                    <?php if ($leave['approved_date']): ?>
                                    <p class="<?= $leave['status'] == 'approved' ? 'text-green-700' : 'text-red-700' ?> mb-2 flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <?= htmlspecialchars(date('M d, Y g:i A', strtotime($leave['approved_date']))) ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php if ($leave['approver_first_name']): ?>
                                    <p class="<?= $leave['status'] == 'approved' ? 'text-green-600' : 'text-red-600' ?> mb-3">
                                        <?= $leave['status'] == 'approved' ? 'Approved' : 'Rejected' ?> by 
                                        <strong><?= htmlspecialchars($leave['approver_first_name'] . ' ' . $leave['approver_last_name']) ?></strong>
                                    </p>
                                    <?php endif; ?>
                                    <?php if ($leave['rejection_reason']): ?>
                                    <div class="<?= $leave['status'] == 'approved' ? 'bg-green-100 border-green-300' : 'bg-red-100 border-red-300' ?> rounded-lg p-3 border">
                                        <strong class="<?= $leave['status'] == 'approved' ? 'text-green-800' : 'text-red-800' ?>">
                                            <?= $leave['status'] == 'approved' ? 'Remarks:' : 'Rejection Reason:' ?>
                                        </strong>
                                        <p class="<?= $leave['status'] == 'approved' ? 'text-green-700' : 'text-red-700' ?> mt-1 leading-relaxed">
                                            <?= nl2br(htmlspecialchars($leave['rejection_reason'])) ?>
                                        </p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></p>
                </div>
                
                <form method="post" id="approveForm">
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
                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></p>
                </div>
                
                <form method="post" id="rejectForm">
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
        function openApproveModal() {
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

        function openRejectModal() {
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
                }, index * 150);
            });
        });
    </script>
</body>
</html>
