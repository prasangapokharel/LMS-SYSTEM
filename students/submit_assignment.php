<?php
include_once '../App/Models/student/SubmitAssignment.php';
include '../include/buffer.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment - School LMS</title>
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
        
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            background: #f9fafb;
            transition: all 0.2s;
        }
        
        .file-upload-area:hover {
            border-color: #a339e4;
            background: #f3f4f6;
        }
        
        .file-upload-area.dragover {
            border-color: #a339e4;
            background: #f3f4f6;
        }
        
        .file-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
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
                    <a href="view_assignment.php?id=<?= $assignment_id ?>" class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center text-lg">
                        ←
                    </a>
           
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="p-6">
                <!-- Success Message -->
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
                        <p class="font-medium"><?= $success_message ?></p>
                        <p class="text-sm mt-1">Redirecting to assignment view...</p>
                    </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6">
                        <p class="font-medium"><?= $error_message ?></p>
                    </div>
                <?php endif; ?>

                <!-- Assignment Info -->
                <div class="card mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3"><?= htmlspecialchars($assignment['title']) ?></h2>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($assignment['subject_name']) ?> • <?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></p>
                        
                        <?php if ($assignment['description']): ?>
                            <div class="mb-4">
                                <h3 class="font-medium text-gray-900 mb-2">Description</h3>
                                <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($assignment['description'])) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Due Date:</span>
                                <p class="font-medium text-gray-900"><?= date('M j, Y g:i A', strtotime($assignment['due_date'])) ?></p>
                            </div>
                            <?php if ($assignment['max_marks']): ?>
                                <div>
                                    <span class="text-gray-500">Max Marks:</span>
                                    <p class="font-medium text-gray-900"><?= $assignment['max_marks'] ?> points</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Due Status -->
                        <div class="mt-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                <?php if (strtotime($assignment['due_date']) < time()): ?>
                                    bg-red-100 text-red-800
                                <?php elseif (strtotime($assignment['due_date']) <= strtotime('+1 day')): ?>
                                    bg-yellow-100 text-yellow-800
                                <?php else: ?>
                                    bg-green-100 text-green-800
                                <?php endif; ?>">
                                <?php
                                $days_left = ceil((strtotime($assignment['due_date']) - time()) / 86400);
                                if ($days_left < 0) echo 'Overdue';
                                elseif ($days_left == 0) echo 'Due Today';
                                elseif ($days_left == 1) echo 'Due Tomorrow';
                                else echo $days_left . ' days left';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <?php if ($assignment['instructions']): ?>
                    <div class="bg-blue-50 rounded-2xl p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2">Instructions</h3>
                        <p class="text-blue-800 leading-relaxed"><?= nl2br(htmlspecialchars($assignment['instructions'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Submission Form -->
                <div class="card">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Your Submission</h3>
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Written Response *
                            </label>
                            <textarea name="submission_text" rows="8" required
                                      class="w-full p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                      placeholder="Type your assignment response here..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Provide a detailed response to the assignment.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Attach File (Optional)
                            </label>
                            <div class="file-upload-area" id="file-upload-area">
                                <input type="file" name="submission_file" id="file-upload" 
                                       class="hidden" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                <div class="text-center">
                                    <div class="text-4xl text-gray-400 mb-2">📎</div>
                                    <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</p>
                                </div>
                            </div>
                            <div id="file-info" class="hidden file-info">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-blue-600 mr-2">📄</span>
                                        <div>
                                            <span id="file-name" class="text-sm text-gray-700 font-medium"></span>
                                            <span id="file-size" class="text-xs text-gray-500 ml-2"></span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-medium hover:opacity-90 transition-opacity">
                                Submit Assignment
                            </button>
                            <a href="view_assignment.php?id=<?= $assignment_id ?>" 
                               class="w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-medium text-center block hover:bg-gray-200 transition-colors">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <?php include '../include/bootoomnav.php'; ?>
    </div>

    <script>
        const fileUploadArea = document.getElementById('file-upload-area');
        const fileInput = document.getElementById('file-upload');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');

        // Click to upload
        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // Drag and drop
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            // Check file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB');
                return;
            }

            // Check file type
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Only PDF, DOC, DOCX, TXT, JPG, PNG files are allowed.');
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = '(' + formatFileSize(file.size) + ')';
            fileInfo.classList.remove('hidden');
        }

        function removeFile() {
            fileInput.value = '';
            fileInfo.classList.add('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Auto-save draft
        const textarea = document.querySelector('textarea[name="submission_text"]');
        let saveTimeout;

        textarea.addEventListener('input', () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const draft = textarea.value;
                if (draft.length > 10) {
                    localStorage.setItem('assignment_draft_<?= $assignment_id ?>', draft);
                }
            }, 2000);
        });

        // Load draft on page load
        window.addEventListener('load', () => {
            const draft = localStorage.getItem('assignment_draft_<?= $assignment_id ?>');
            if (draft && textarea.value === '') {
                textarea.value = draft;
            }
        });

        // Clear draft on successful submission
        <?php if (isset($success_message)): ?>
            localStorage.removeItem('assignment_draft_<?= $assignment_id ?>');
        <?php endif; ?>
    </script>
</body>
</html>