<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Handle AJAX file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_upload'])) {
    header('Content-Type: application/json');
    
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/leave_attachments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $file_name = $user['id'] . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
            echo json_encode(['success' => true, 'file_path' => 'uploads/leave_attachments/' . $file_name, 'file_name' => $_FILES['attachment']['name']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    }
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_upload'])) {
    $leave_type = $_POST['leave_type'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = $_POST['reason'];
    $leave_details = $_POST['leave_details'] ?? '';
    $emergency_contact = $_POST['emergency_contact'] ?? '';
    $attachment_url = $_POST['attachment_url'] ?? '';
    
    // Calculate total days
    $start = new DateTime($from_date);
    $end = new DateTime($to_date);
    $total_days = $end->diff($start)->days + 1;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO leave_applications 
                              (user_id, user_type, leave_type, from_date, to_date, total_days, reason, leave_details, emergency_contact, attachment_url, status, applied_date) 
                              VALUES (?, 'student', ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        
        $stmt->execute([$user['id'], $leave_type, $from_date, $to_date, $total_days, $reason, $leave_details, $emergency_contact, $attachment_url]);
        
        $success_message = "Leave application submitted successfully!";
        
        // Log the action
        logActivity($pdo, $user['id'], 'leave_application_submitted', 'leave_applications', $pdo->lastInsertId());
        
    } catch (Exception $e) {
        $error_message = "Error submitting leave application: " . $e->getMessage();
    }
}

// Get leave applications for this student
$stmt = $pdo->prepare("SELECT * FROM leave_applications 
                      WHERE user_id = ? AND user_type = 'student' 
                      ORDER BY applied_date DESC");
$stmt->execute([$user['id']]);
$leave_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Notice - School LMS</title>
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
            padding-bottom: 100px;
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
    </style>
</head>
<body class="bg-gray-100">
    <div class="pwa-container">
        <!-- Header -->
        <div class="absolute top-0 left-0 right-0 p-6 text-white z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <a href="index.php" class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <!-- Arrow Left Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                    </a>
                    <!-- <div>
                        <h1 class="text-xl font-bold">Leave Application</h1>
                        <p class="text-white text-opacity-80 text-sm">Apply for leave</p>
                    </div> -->
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <!-- Calendar X Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h6m-6 3h6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="p-6">
                
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
                        <div class="flex items-center">
                            <!-- Check Circle Icon -->
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <?= $success_message ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6">
                        <div class="flex items-center">
                            <!-- Exclamation Circle Icon -->
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <?= $error_message ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Leave Application Form -->
                <div class="card p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <!-- Plus Circle Icon -->
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        New Leave Application
                    </h3>
                    
                    <form method="POST" class="space-y-4" id="leaveForm">
                        <input type="hidden" name="attachment_url" id="attachment_url">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                            <select name="leave_type" required class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Select leave type</option>
                                <option value="sick">Sick Leave</option>
                                <option value="personal">Personal Leave</option>
                                <option value="emergency">Emergency Leave</option>
                                <option value="vacation">Vacation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                                <input type="date" name="from_date" required 
                                       class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                                <input type="date" name="to_date" required 
                                       class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                            <textarea name="reason" required rows="3" 
                                      class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Please provide a brief reason for your leave"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Details (Optional)</label>
                            <textarea name="leave_details" rows="2" 
                                      class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Any additional information"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact (Optional)</label>
                            <input type="tel" name="emergency_contact" 
                                   class="w-full p-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="Emergency contact number">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Attachment (Optional)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4">
                                <input type="file" id="fileInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden">
                                <div id="uploadArea" class="text-center cursor-pointer">
                                    <!-- Cloud Upload Icon -->
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm text-gray-600">Click to upload file</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG (Max 5MB)</p>
                                </div>
                                <div id="uploadProgress" class="hidden">
                                    <div class="flex items-center justify-center">
                                        <!-- Loading Spinner -->
                                        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Uploading...</span>
                                    </div>
                                </div>
                                <div id="uploadSuccess" class="hidden">
                                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                                        <div class="flex items-center">
                                            <!-- Document Icon -->
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                            </svg>
                                            <span id="fileName" class="text-sm text-gray-700"></span>
                                        </div>
                                        <button type="button" onclick="removeFile()" class="text-red-500">
                                            <!-- X Icon -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-medium flex items-center justify-center">
                            <!-- Paper Airplane Icon -->
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.768 59.768 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                            </svg>
                            Submit Application
                        </button>
                    </form>
                </div>

                <!-- Leave Applications History -->
                <div class="card">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900 flex items-center">
                            <!-- Clock Icon -->
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Application History
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <?php if (empty($leave_applications)): ?>
                            <div class="p-8 text-center">
                                <!-- Calendar X Icon -->
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h6m-6 3h6"/>
                                </svg>
                                <p class="text-gray-500">No leave applications found</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($leave_applications as $application): ?>
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="font-medium text-gray-900 capitalize">
                                                <?= htmlspecialchars($application['leave_type']) ?> Leave
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                <?= date('M j, Y', strtotime($application['from_date'])) ?> - 
                                                <?= date('M j, Y', strtotime($application['to_date'])) ?>
                                                (<?= $application['total_days'] ?> day<?= $application['total_days'] > 1 ? 's' : '' ?>)
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            <?php if ($application['status'] === 'approved'): ?>
                                                bg-green-100 text-green-800
                                            <?php elseif ($application['status'] === 'rejected'): ?>
                                                bg-red-100 text-red-800
                                            <?php else: ?>
                                                bg-yellow-100 text-yellow-800
                                            <?php endif; ?>">
                                            <?= ucfirst($application['status']) ?>
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-700 mb-2">
                                        <strong>Reason:</strong> <?= htmlspecialchars($application['reason']) ?>
                                    </p>
                                    
                                    <?php if ($application['leave_details']): ?>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Details:</strong> <?= htmlspecialchars($application['leave_details']) ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($application['attachment_url']): ?>
                                        <div class="mb-2">
                                            <a href="../<?= $application['attachment_url'] ?>" target="_blank" class="text-primary text-sm flex items-center">
                                                <!-- Paperclip Icon -->
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                View Attachment
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>Applied: <?= date('M j, Y g:i A', strtotime($application['applied_date'])) ?></span>
                                        <?php if ($application['approved_date']): ?>
                                            <span>Processed: <?= date('M j, Y', strtotime($application['approved_date'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($application['rejection_reason']): ?>
                                        <div class="mt-2 p-2 bg-red-50 rounded-lg">
                                            <p class="text-sm text-red-700">
                                                <strong>Rejection Reason:</strong> <?= htmlspecialchars($application['rejection_reason']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
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

    <script>
        // File upload functionality
        document.getElementById('uploadArea').addEventListener('click', function() {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                uploadFile(file);
            }
        });

        function uploadFile(file) {
            const formData = new FormData();
            formData.append('attachment', file);
            formData.append('ajax_upload', '1');

            document.getElementById('uploadArea').classList.add('hidden');
            document.getElementById('uploadProgress').classList.remove('hidden');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('uploadProgress').classList.add('hidden');
                
                if (data.success) {
                    document.getElementById('attachment_url').value = data.file_path;
                    document.getElementById('fileName').textContent = data.file_name;
                    document.getElementById('uploadSuccess').classList.remove('hidden');
                } else {
                    alert('Upload failed: ' + data.error);
                    document.getElementById('uploadArea').classList.remove('hidden');
                }
            })
            .catch(error => {
                document.getElementById('uploadProgress').classList.add('hidden');
                document.getElementById('uploadArea').classList.remove('hidden');
                alert('Upload failed: ' + error.message);
            });
        }

        function removeFile() {
            document.getElementById('attachment_url').value = '';
            document.getElementById('uploadSuccess').classList.add('hidden');
            document.getElementById('uploadArea').classList.remove('hidden');
            document.getElementById('fileInput').value = '';
        }

        // Auto-calculate total days
        document.addEventListener('DOMContentLoaded', function() {
            const fromDate = document.querySelector('input[name="from_date"]');
            const toDate = document.querySelector('input[name="to_date"]');
            
            function calculateDays() {
                if (fromDate.value && toDate.value) {
                    const start = new Date(fromDate.value);
                    const end = new Date(toDate.value);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    
                    const existingInfo = document.querySelector('.days-info');
                    if (existingInfo) existingInfo.remove();
                    
                    if (diffDays > 0) {
                        const info = document.createElement('div');
                        info.className = 'days-info text-sm text-primary mt-1 flex items-center';
                        info.innerHTML = `
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                            Total: ${diffDays} day${diffDays > 1 ? 's' : ''}
                        `;
                        toDate.parentNode.appendChild(info);
                    }
                }
            }
            
            fromDate.addEventListener('change', calculateDays);
            toDate.addEventListener('change', calculateDays);
        });
    </script>
</body>
</html>