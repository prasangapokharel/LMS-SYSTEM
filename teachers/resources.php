<?php
include_once '../App/Models/teacher/Resource.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Learning Resources - LMS</title>
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
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
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
    
    .upload-section {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .filters-section {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .resources-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .resource-card {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .resource-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .resource-icon.document {
        background: var(--color-primary-light);
        color: var(--color-primary);
    }
    
    .resource-icon.video {
        background: var(--color-danger-light);
        color: var(--color-danger);
    }
    
    .resource-icon.link {
        background: var(--color-success-light);
        color: var(--color-success);
    }
    
    .resource-icon.image {
        background: var(--color-warning-light);
        color: var(--color-warning);
    }
    
    .resource-icon.audio {
        background: var(--color-info-light);
        color: var(--color-info);
    }
    
    .resource-icon.presentation {
        background: var(--color-secondary-light);
        color: var(--color-secondary);
    }
    
    .resource-content {
        flex: 1;
        min-width: 0;
    }
    
    .resource-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 0 0 0.25rem 0;
    }
    
    .resource-description {
        font-size: 0.875rem;
        color: var(--color-gray-600);
        margin: 0 0 0.75rem 0;
        line-height: 1.4;
    }
    
    .resource-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        font-size: 0.75rem;
        color: var(--color-gray-500);
        margin-bottom: 0.75rem;
    }
    
    .resource-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .resource-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        margin-top: 0.5rem;
    }
    
    .tag {
        background: var(--color-gray-100);
        color: var(--color-gray-700);
        padding: 0.125rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
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
    
    .btn-secondary {
        background: var(--color-gray-500);
        color: var(--color-white);
    }
    
    .btn-danger {
        background: var(--color-danger);
        color: var(--color-white);
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.625rem;
    }
    
    .form-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--color-gray-700);
        margin-bottom: 0.5rem;
    }
    
    .form-input,
    .form-select,
    .form-textarea {
        padding: 0.75rem;
        border: 1px solid var(--color-gray-300);
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .upload-area {
        border: 2px dashed var(--color-gray-300);
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        background: var(--color-gray-50);
        margin-bottom: 1rem;
        cursor: pointer;
    }
    
    .upload-area.dragover {
        border-color: var(--color-primary);
        background: var(--color-primary-light);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--color-gray-500);
        background: var(--color-white);
        border-radius: 0.75rem;
        box-shadow: var(--shadow-sm);
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
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }
    
    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background: white;
        border-radius: 0.75rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 1.5rem 1.5rem 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--color-gray-500);
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    @media (min-width: 768px) {
        .mobile-container {
            max-width: 1200px;
            padding: 2rem;
        }
        
        .form-row {
            flex-direction: row;
        }
        
        .form-group {
            flex: 1;
        }
        
        .resources-grid {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }
    }
</style>
</head>
<body>
<div class="mobile-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Learning Resources</h1>
        <p class="page-subtitle">Manage and share learning materials with your students</p>
    </div>

    <!-- Alert Messages -->
    <?= $msg ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total_resources'] ?></div>
            <div class="stat-label">Total Resources</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['documents'] ?></div>
            <div class="stat-label">Documents</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['videos'] ?></div>
            <div class="stat-label">Videos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total_downloads'] ?></div>
            <div class="stat-label">Downloads</div>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="upload-section">
        <h2 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600;">Upload New Resource</h2>
        
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_resource">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Resource Type *</label>
                    <select name="resource_type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="document">Document</option>
                        <option value="video">Video</option>
                        <option value="audio">Audio</option>
                        <option value="link">External Link</option>
                        <option value="image">Image</option>
                        <option value="presentation">Presentation</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class (Optional)</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        <?php 
                        $unique_classes = [];
                        foreach ($teacher_courses as $course): 
                            $class_key = $course['class_id'];
                            if (!isset($unique_classes[$class_key])):
                                $unique_classes[$class_key] = true;
                        ?>
                        <option value="<?= $course['class_id'] ?>">
                            <?= htmlspecialchars($course['class_name'] . ' ' . $course['section']) ?>
                        </option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject (Optional)</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        <?php 
                        $unique_subjects = [];
                        foreach ($teacher_courses as $course): 
                            $subject_key = $course['subject_id'];
                            if (!isset($unique_subjects[$subject_key])):
                                $unique_subjects[$subject_key] = true;
                        ?>
                        <option value="<?= $course['subject_id'] ?>">
                            <?= htmlspecialchars($course['subject_name']) ?>
                        </option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" placeholder="Describe the resource and how it should be used..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">File Upload</label>
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="resource_file" id="fileInput" style="display: none;" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.mp4,.mp3,.zip">
                    <div>📁 Click to select file or drag and drop</div>
                    <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                        Supported: PDF, DOC, PPT, XLS, Images, Videos, Audio, ZIP (Max: 50MB)
                    </div>
                </div>
                <div id="fileName" style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--color-gray-700);"></div>
            </div>
            
            <div class="form-group">
                <label class="form-label">External URL (Optional)</label>
                <input type="url" name="external_url" class="form-input" placeholder="https://example.com/resource">
                <small style="color: var(--color-gray-500); font-size: 0.75rem;">Provide either a file upload or external URL</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tags (Optional)</label>
                <input type="text" name="tags" class="form-input" placeholder="homework, chapter1, important">
            </div>
            
            <div class="form-checkbox">
                <input type="checkbox" name="is_public" id="isPublic">
                <label for="isPublic">Make this resource public to all students</label>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Upload Resource</button>
            </div>
        </form>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Filter Resources</h3>
        
        <form method="get" class="form-row">
            <div class="form-group">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Classes</option>
                    <?php 
                    $unique_classes = [];
                    foreach ($teacher_courses as $course): 
                        $class_key = $course['class_id'];
                        if (!isset($unique_classes[$class_key])):
                            $unique_classes[$class_key] = true;
                    ?>
                    <option value="<?= $course['class_id'] ?>" <?= ($class_filter == $course['class_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['class_name'] . ' ' . $course['section']) ?>
                    </option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    <?php 
                    $unique_subjects = [];
                    foreach ($teacher_courses as $course): 
                        $subject_key = $course['subject_id'];
                        if (!isset($unique_subjects[$subject_key])):
                            $unique_subjects[$subject_key] = true;
                    ?>
                    <option value="<?= $course['subject_id'] ?>" <?= ($subject_filter == $course['subject_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['subject_name']) ?>
                    </option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="document" <?= ($type_filter == 'document') ? 'selected' : '' ?>>Documents</option>
                    <option value="video" <?= ($type_filter == 'video') ? 'selected' : '' ?>>Videos</option>
                    <option value="audio" <?= ($type_filter == 'audio') ? 'selected' : '' ?>>Audio</option>
                    <option value="link" <?= ($type_filter == 'link') ? 'selected' : '' ?>>Links</option>
                    <option value="image" <?= ($type_filter == 'image') ? 'selected' : '' ?>>Images</option>
                    <option value="presentation" <?= ($type_filter == 'presentation') ? 'selected' : '' ?>>Presentations</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Resources List -->
    <?php if (empty($resources)): ?>
    <div class="empty-state">
        <div class="empty-title">No Resources Found</div>
        <div class="empty-text">Upload your first learning resource to get started.</div>
    </div>
    <?php else: ?>
    <div class="resources-grid">
        <?php foreach ($resources as $resource): ?>
        <div class="resource-card">
            <div class="resource-icon <?= $resource['resource_type'] ?>">
                <?php
                $icons = [
                    'document' => '📄',
                    'video' => '🎥',
                    'audio' => '🎵',
                    'link' => '🔗',
                    'image' => '🖼️',
                    'presentation' => '📊'
                ];
                echo $icons[$resource['resource_type']] ?? '📁';
                ?>
            </div>
            
            <div class="resource-content">
                <h4 class="resource-title"><?= htmlspecialchars($resource['title']) ?></h4>
                
                <?php if ($resource['description']): ?>
                <p class="resource-description"><?= htmlspecialchars($resource['description']) ?></p>
                <?php endif; ?>
                
                <div class="resource-meta">
                    <?php if ($resource['class_name']): ?>
                    <span>📚 <?= htmlspecialchars($resource['class_name'] . ' ' . $resource['section']) ?></span>
                    <?php else: ?>
                    <span>📚 All Classes</span>
                    <?php endif; ?>
                    
                    <?php if ($resource['subject_name']): ?>
                    <span>📖 <?= htmlspecialchars($resource['subject_name']) ?></span>
                    <?php else: ?>
                    <span>📖 All Subjects</span>
                    <?php endif; ?>
                    
                    <span>📅 <?= date('M j, Y', strtotime($resource['created_at'])) ?></span>
                    
                    <?php if ($resource['download_count'] > 0): ?>
                    <span>⬇️ <?= $resource['download_count'] ?> downloads</span>
                    <?php endif; ?>
                    
                    <?php if ($resource['file_size'] > 0): ?>
                    <span>💾 <?= formatFileSize($resource['file_size']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="resource-actions">
                    <?php if ($resource['file_url']): ?>
                    <a href="../<?= htmlspecialchars($resource['file_url']) ?>" class="btn btn-primary btn-sm" target="_blank">
                        Download
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($resource['external_url']): ?>
                    <a href="<?= htmlspecialchars($resource['external_url']) ?>" class="btn btn-secondary btn-sm" target="_blank">
                        Open Link
                    </a>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteResource(<?= $resource['id'] ?>, '<?= addslashes($resource['title']) ?>')">
                        Delete
                    </button>
                </div>
                
                <?php if ($resource['tags']): ?>
                <div class="resource-tags">
                    <?php foreach (explode(',', $resource['tags']) as $tag): ?>
                    <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Delete</h3>
            <button type="button" class="modal-close" onclick="hideDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this resource?</p>
            <p><strong>Title:</strong> <span id="deleteResourceTitle"></span></p>
            <p style="color: var(--color-danger); font-size: 0.875rem;">This action cannot be undone.</p>
            
            <form method="post" id="deleteForm">
                <input type="hidden" name="action" value="delete_resource">
                <input type="hidden" name="resource_id" id="deleteResourceId">
                
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Bottom Navigation -->
<?php include '../include/bootoomnav.php'; ?>

<script>
// File upload handling
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    
    uploadArea.addEventListener('click', () => fileInput.click());
    
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
            fileInput.files = files;
            updateFileName(files[0]);
        }
    });
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            updateFileName(e.target.files[0]);
        }
    });
    
    function updateFileName(file) {
        fileName.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});

function deleteResource(resourceId, resourceTitle) {
    document.getElementById('deleteResourceId').value = resourceId;
    document.getElementById('deleteResourceTitle').textContent = resourceTitle;
    document.getElementById('deleteModal').classList.add('show');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>
</body>
</html>

<?php
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>
