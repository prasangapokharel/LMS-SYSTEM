<?php
include_once '../App/Models/teacher/Resource.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Resources - LMS</title>
    <meta name="description" content="Learning Resources - Manage and share learning materials">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <link rel="stylesheet" href="../assets/css/teacher/resources.css">
</head>
<body>
    <div class="container ">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="header-title">Learning Resources</h1>
                <p class="header-subtitle">Manage and share learning materials with your students</p>
            </div>
        </div>

        <!-- Alert Messages -->
        <?= $msg ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10,9 9,9 8,9"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $stats['total_resources'] ?></div>
                <div class="stat-label">Total Resources</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10,9 9,9 8,9"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $stats['documents'] ?></div>
                <div class="stat-label">Documents</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="23 7 16 12 23 17 23 7"/>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $stats['videos'] ?></div>
                <div class="stat-label">Videos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $stats['total_downloads'] ?></div>
                <div class="stat-label">Downloads</div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="card">
            <div class="card-title">
                <div class="card-title-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="12" y1="11" x2="12" y2="17"/>
                        <polyline points="9,14 12,11 15,14"/>
                    </svg>
                </div>
                Upload New Resource
            </div>
            
            <form method="post" enctype="multipart/form-data" class="resource-form">
                <input type="hidden" name="action" value="upload_resource">
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-input" required placeholder="Enter resource title">
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
                        <input type="file" name="resource_file" id="fileInput" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.mp4,.mp3,.zip">
                        <div class="upload-content">
                            <div class="upload-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                    <line x1="12" y1="11" x2="12" y2="17"/>
                                    <polyline points="9,14 12,11 15,14"/>
                                </svg>
                            </div>
                            <div class="upload-text">Click to select file or drag and drop</div>
                            <div class="upload-hint">Supported: PDF, DOC, PPT, XLS, Images, Videos, Audio, ZIP (Max: 50MB)</div>
                        </div>
                    </div>
                    <div id="fileName"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">External URL (Optional)</label>
                    <input type="url" name="external_url" class="form-input" placeholder="https://example.com/resource">
                    <div class="form-hint">Provide either a file upload or external URL</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tags (Optional)</label>
                    <input type="text" name="tags" class="form-input" placeholder="homework, chapter1, important">
                </div>
                
                <div class="form-checkbox">
                    <input type="checkbox" name="is_public" id="isPublic">
                    <label for="isPublic">Make this resource public to all students</label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="12" y1="11" x2="12" y2="17"/>
                            <polyline points="9,14 12,11 15,14"/>
                        </svg>
                        Upload Resource
                    </button>
                </div>
            </form>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-title">
                <div class="card-title-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3"/>
                    </svg>
                </div>
                Filter Resources
            </div>
            
            <form method="get" class="filter-form">
                <div class="form-row">
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
                </div>
            </form>
        </div>

        <!-- Resources List -->
        <?php if (empty($resources)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10,9 9,9 8,9"/>
                    </svg>
                </div>
                <div class="empty-title">No Resources Found</div>
                <div class="empty-text">Upload your first learning resource to get started.</div>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-title">
                <div class="card-title-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                Learning Resources
            </div>
            
            <div class="resources-list">
                <?php foreach ($resources as $resource): ?>
                <div class="resource-item">
                    <div class="resource-icon <?= $resource['resource_type'] ?>">
                        <?php
                        $icons = [
                            'document' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>',
                            'video' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>',
                            'audio' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
                            'link' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
                            'image' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21,15 16,10 5,21"/></svg>',
                            'presentation' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>'
                        ];
                        echo $icons[$resource['resource_type']] ?? $icons['document'];
                        ?>
                    </div>
                    
                    <div class="resource-content">
                        <h4 class="resource-title"><?= htmlspecialchars($resource['title']) ?></h4>
                        
                        <?php if ($resource['description']): ?>
                        <p class="resource-description"><?= htmlspecialchars($resource['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="resource-meta">
                            <div class="meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                </svg>
                                <?php if ($resource['class_name']): ?>
                                    <?= htmlspecialchars($resource['class_name'] . ' ' . $resource['section']) ?>
                                <?php else: ?>
                                    All Classes
                                <?php endif; ?>
                            </div>
                            
                            <div class="meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                                <?php if ($resource['subject_name']): ?>
                                    <?= htmlspecialchars($resource['subject_name']) ?>
                                <?php else: ?>
                                    All Subjects
                                <?php endif; ?>
                            </div>
                            
                            <div class="meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <?= date('M j, Y', strtotime($resource['created_at'])) ?>
                            </div>
                            
                            <?php if ($resource['download_count'] > 0): ?>
                            <div class="meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <?= $resource['download_count'] ?> downloads
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($resource['file_size'] > 0): ?>
                            <div class="meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                </svg>
                                <?= formatFileSize($resource['file_size']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($resource['tags']): ?>
                        <div class="resource-tags">
                            <?php foreach (explode(',', $resource['tags']) as $tag): ?>
                            <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="resource-actions">
                            <?php if ($resource['file_url']): ?>
                            <a href="../<?= htmlspecialchars($resource['file_url']) ?>" class="btn btn-primary btn-small" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Download
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($resource['external_url']): ?>
                            <a href="<?= htmlspecialchars($resource['external_url']) ?>" class="btn btn-secondary btn-small" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15,3 21,3 21,9"/>
                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                </svg>
                                Open Link
                            </a>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-danger btn-small" onclick="deleteResource(<?= $resource['id'] ?>, '<?= addslashes($resource['title']) ?>')">
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
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Delete</h3>
                <button type="button" class="modal-close" onclick="hideDeleteModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this resource?</p>
                <p><strong>Title:</strong> <span id="deleteResourceTitle"></span></p>
                <p class="warning-text">This action cannot be undone.</p>
                
                <form method="post" id="deleteForm">
                    <input type="hidden" name="action" value="delete_resource">
                    <input type="hidden" name="resource_id" id="deleteResourceId">
                    
                    <div class="modal-actions">
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
                fileName.innerHTML = `
                    <div class="file-preview">
                        <div class="file-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                            </svg>
                        </div>
                        <div class="file-info">
                            <span class="file-name">${file.name}</span>
                            <span class="file-size">(${formatFileSize(file.size)})</span>
                        </div>
                    </div>
                `;
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