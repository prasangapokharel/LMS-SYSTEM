<?php
include_once '../App/Models/teacher/Notice.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notices Management - LMS</title>
<link rel="stylesheet" href="../assets/css/ui.css">
<style>
    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 1rem;
        background-color: var(--color-gray-50);
        min-height: 100vh;
        padding-bottom: 80px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.25rem;
        text-align: center;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--color-primary);
        margin: 0;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: var(--color-gray-600);
        margin: 0.25rem 0 0 0;
    }
    
    .create-section {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .notices-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .notice-card {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
        border-left: 4px solid var(--color-primary);
    }
    
    .notice-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .notice-image {
        width: 80px;
        height: 80px;
        border-radius: 0.5rem;
        object-fit: cover;
        flex-shrink: 0;
    }
    
    .notice-content {
        flex: 1;
        min-width: 0;
    }
    
    .notice-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 0 0 0.5rem 0;
    }
    
    .notice-text {
        font-size: 0.875rem;
        color: var(--color-gray-600);
        margin: 0 0 0.75rem 0;
        line-height: 1.4;
    }
    
    .notice-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: var(--color-gray-500);
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--color-gray-700);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-input,
    .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-gray-300);
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .upload-area {
        border: 2px dashed var(--color-gray-300);
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        background: var(--color-gray-50);
        cursor: pointer;
    }
    
    .upload-area.dragover {
        border-color: var(--color-primary);
        background: var(--color-primary-light);
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .btn-primary {
        background: var(--color-primary);
        color: var(--color-white);
    }
    
    .btn-danger {
        background: var(--color-danger);
        color: var(--color-white);
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: var(--color-success-light);
        color: var(--color-success-dark);
        border: 1px solid var(--color-success);
    }
    
    .alert-danger {
        background: var(--color-danger-light);
        color: var(--color-danger-dark);
        border: 1px solid var(--color-danger);
    }
    
    @media (min-width: 768px) {
        .mobile-container {
            max-width: 1200px;
            padding: 2rem;
        }
        
        .notices-grid {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }
    }
</style>
</head>
<body>
<div class="mobile-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Notices Management</h1>
        <p class="page-subtitle">Create and manage school notices</p>
    </div>

    <!-- Alert Messages -->
    <?= $msg ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total_notices'] ?></div>
            <div class="stat-label">Total Notices</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['today_notices'] ?></div>
            <div class="stat-label">Today's Notices</div>
        </div>
    </div>

    <!-- Create Notice Section -->
    <div class="create-section">
        <h2 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600;">Create New Notice</h2>
        
        <form method="post" enctype="multipart/form-data">
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
                    <input type="file" name="notice_image" id="imageInput" style="display: none;" accept="image/*">
                    <div>🖼️ Click to select image or drag and drop</div>
                    <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                        Supported: JPG, PNG, GIF, WebP (Max: 5MB)
                    </div>
                </div>
                <div id="imagePreview" style="margin-top: 0.5rem;"></div>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Create Notice</button>
            </div>
        </form>
    </div>

    <!-- Notices List -->
    <?php if (empty($notices)): ?>
    <div class="empty-state">
        <div class="empty-title">No Notices Found</div>
        <div class="empty-text">Create your first notice to get started.</div>
    </div>
    <?php else: ?>
    <div class="notices-grid">
        <?php foreach ($notices as $notice): ?>
        <div class="notice-card">
            <div class="notice-header">
                <div class="notice-content">
                    <h4 class="notice-title"><?= htmlspecialchars($notice['title']) ?></h4>
                    <p class="notice-text"><?= htmlspecialchars(substr($notice['content'], 0, 150)) ?><?= strlen($notice['content']) > 150 ? '...' : '' ?></p>
                    <div class="notice-meta">
                        <span>📅 <?= date('M j, Y g:i A', strtotime($notice['created_at'])) ?></span>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteNotice(<?= $notice['id'] ?>, '<?= addslashes($notice['title']) ?>')">
                            Delete
                        </button>
                    </div>
                </div>
                
                <?php if ($notice['notice_image']): ?>
                    <img src="../<?= htmlspecialchars($notice['notice_image']) ?>" alt="Notice Image" class="notice-image">
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
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
                <img src="${e.target.result}" style="max-width: 200px; max-height: 150px; border-radius: 0.5rem; object-fit: cover;">
                <div style="font-size: 0.875rem; color: var(--color-gray-700); margin-top: 0.5rem;">
                    Selected: ${file.name} (${formatFileSize(file.size)})
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
