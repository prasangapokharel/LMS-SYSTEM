<?php
include_once '../App/Models/teacher/Notice.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices Management - LMS</title>
    <meta name="description" content="Notices Management - Create and manage school notices">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <link rel="stylesheet" href="../assets/css/teacher/notice.css">
</head>
<body>
    <div class="container ">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="header-title">Notices Management</h1>
                <p class="header-subtitle">Create and manage school notices</p>
            </div>
        </div>

        <!-- Alert Messages -->
        <?= $msg ?>

        <!-- Statistics -->
        <!-- <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $stats['total_notices'] ?></div>
                <div class="stat-label">Total Notices</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $stats['today_notices'] ?></div>
                <div class="stat-label">Today's Notices</div>
            </div>
        </div> -->

        <!-- Create Notice Section -->
        <div class="card">
            <div class="card-title">
                <div class="card-title-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                </div>
                Create New Notice
            </div>
            
            <form method="post" enctype="multipart/form-data" class="notice-form">
                <input type="hidden" name="action" value="create_notice">
                
                <div class="form-group">
                    <label class="form-label">Notice Title *</label>
                    <input type="text" name="title" class="form-input" required placeholder="Enter notice title">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notice Content *</label>
                    <textarea name="content" class="form-textarea" required placeholder="Enter notice content..."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notice Image (Optional)</label>
                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="notice_image" id="imageInput" accept="image/*">
                        <div class="upload-content">
                            <div class="upload-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21,15 16,10 5,21"/>
                                </svg>
                            </div>
                            <div class="upload-text">Click to select image or drag and drop</div>
                            <div class="upload-hint">Supported: JPG, PNG, GIF, WebP (Max: 5MB)</div>
                        </div>
                    </div>
                    <div id="imagePreview"></div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="16"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                        Create Notice
                    </button>
                </div>
            </form>
        </div>

        <!-- Notices List -->
        <?php if (empty($notices)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                </div>
                <div class="empty-title">No Notices Found</div>
                <div class="empty-text">Create your first notice to get started.</div>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-title">
                <div class="card-title-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                </div>
                Recent Notices
            </div>
            
            <div class="notices-list">
                <?php foreach ($notices as $notice): ?>
                <div class="notice-item">
                    <div class="notice-content">
                        <?php if ($notice['notice_image']): ?>
                            <div class="notice-image">
                                <img src="../<?= htmlspecialchars($notice['notice_image']) ?>" alt="Notice Image">
                            </div>
                        <?php endif; ?>
                        
                        <div class="notice-details">
                            <h4 class="notice-title"><?= htmlspecialchars($notice['title']) ?></h4>
                            <p class="notice-text"><?= htmlspecialchars(substr($notice['content'], 0, 150)) ?><?= strlen($notice['content']) > 150 ? '...' : '' ?></p>
                            
                            <div class="notice-meta">
                                <div class="notice-date">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <?= date('M j, Y g:i A', strtotime($notice['created_at'])) ?>
                                </div>
                                
                                <button type="button" class="btn btn-danger btn-small" onclick="deleteNotice(<?= $notice['id'] ?>, '<?= addslashes($notice['title']) ?>')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3,6 5,6 21,6"/>
                                        <path d="M19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        // Image upload handling
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            
            uploadArea.addEventListener('click', () => imageInput.click());
            
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    imageInput.files = files;
                    previewImage(files[0]);
                }
            });
            
            imageInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    previewImage(e.target.files[0]);
                }
            });
            
            function previewImage(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <div class="image-preview">
                            <img src="${e.target.result}" alt="Preview">
                            <div class="preview-info">
                                <span class="preview-name">${file.name}</span>
                                <span class="preview-size">(${formatFileSize(file.size)})</span>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
            
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        });

        function deleteNotice(noticeId, noticeTitle) {
            if (confirm(`Are you sure you want to delete "${noticeTitle}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_notice">
                    <input type="hidden" name="notice_id" value="${noticeId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>