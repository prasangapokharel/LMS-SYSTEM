<?php
include_once '../App/Models/teacher/Event.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events Management - LMS</title>
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
    
    .create-section {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .events-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .event-card {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
        border-left: 4px solid var(--color-primary);
    }
    
    .event-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .event-image {
        width: 80px;
        height: 80px;
        border-radius: 0.5rem;
        object-fit: cover;
        flex-shrink: 0;
    }
    
    .event-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        background: var(--color-primary-light);
        color: var(--color-primary);
    }
    
    .event-content {
        flex: 1;
        min-width: 0;
    }
    
    .event-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 0 0 0.25rem 0;
    }
    
    .event-description {
        font-size: 0.875rem;
        color: var(--color-gray-600);
        margin: 0 0 0.75rem 0;
        line-height: 1.4;
    }
    
    .event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        font-size: 0.75rem;
        color: var(--color-gray-500);
        margin-bottom: 0.75rem;
    }
    
    .event-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
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
    
    .btn-danger {
        background: var(--color-danger);
        color: var(--color-white);
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.625rem;
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
        
        .events-grid {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }
    }
</style>
</head>
<body>
<div class="mobile-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Events Management</h1>
        <p class="page-subtitle">Create and manage school events</p>
    </div>

    <!-- Alert Messages -->
    <?= $msg ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total_events'] ?></div>
            <div class="stat-label">Total Events</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['upcoming_events'] ?></div>
            <div class="stat-label">Upcoming</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['exams'] ?></div>
            <div class="stat-label">Exams</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['meetings'] ?></div>
            <div class="stat-label">Meetings</div>
        </div>
    </div>

    <!-- Create Event Section -->
    <div class="create-section">
        <h2 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600;">Create New Event</h2>
        
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create_event">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Event Title *</label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Event Type *</label>
                    <select name="event_type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="class">Class</option>
                        <option value="exam">Exam</option>
                        <option value="assignment">Assignment</option>
                        <option value="holiday">Holiday</option>
                        <option value="meeting">Meeting</option>
                        <option value="announcement">Announcement</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" placeholder="Describe the event details..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-input">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" class="form-input">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-input" placeholder="Event location">
                </div>
                <div class="form-group">
                    <label class="form-label">Color</label>
                    <input type="color" name="color" class="form-input" value="#3498db">
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
                            if (!$course['subject_id']) continue;
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
                <label class="form-label">Event Image</label>
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="event_image" id="imageInput" style="display: none;" accept="image/*">
                    <div>🖼️ Click to select image or drag and drop</div>
                    <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                        Supported: JPG, PNG, GIF, WebP (Max: 5MB)
                    </div>
                </div>
                <div id="imagePreview" style="margin-top: 0.5rem;"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Reminder (minutes before)</label>
                    <select name="reminder_minutes" class="form-select">
                        <option value="">No Reminder</option>
                        <option value="15">15 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="1440">1 day</option>
                    </select>
                </div>
            </div>
            
            <div class="form-checkbox">
                <input type="checkbox" name="is_all_day" id="isAllDay">
                <label for="isAllDay">All day event</label>
            </div>
            
            <div class="form-checkbox">
                <input type="checkbox" name="is_public" id="isPublic" checked>
                <label for="isPublic">Make this event public to all students</label>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Create Event</button>
            </div>
        </form>
    </div>

    <!-- Events List -->
    <?php if (empty($events)): ?>
    <div class="empty-state">
        <div class="empty-title">No Events Found</div>
        <div class="empty-text">Create your first event to get started.</div>
    </div>
    <?php else: ?>
    <div class="events-grid">
        <?php foreach ($events as $event): ?>
        <div class="event-card" style="border-left-color: <?= htmlspecialchars($event['color']) ?>;">
            <div class="event-header">
                <?php if ($event['event_image']): ?>
                    <img src="../<?= htmlspecialchars($event['event_image']) ?>" alt="Event Image" class="event-image">
                <?php else: ?>
                    <div class="event-icon">
                        <?= getEventTypeIcon($event['event_type']) ?>
                    </div>
                <?php endif; ?>
                
                <div class="event-content">
                    <h4 class="event-title"><?= htmlspecialchars($event['title']) ?></h4>
                    
                    <?php if ($event['description']): ?>
                    <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
                    <?php endif; ?>
                    
                    <div class="event-meta">
                        <span>📅 <?= formatEventDate($event['start_date']) ?></span>
                        
                        <?php if ($event['start_time']): ?>
                        <span>🕐 <?= formatEventTime($event['start_time']) ?></span>
                        <?php endif; ?>
                        
                        <?php if ($event['location']): ?>
                        <span>📍 <?= htmlspecialchars($event['location']) ?></span>
                        <?php endif; ?>
                        
                        <?php if ($event['class_name']): ?>
                        <span>📚 <?= htmlspecialchars($event['class_name'] . ' ' . $event['section']) ?></span>
                        <?php endif; ?>
                        
                        <span>🏷️ <?= ucfirst($event['event_type']) ?></span>
                    </div>
                    
                    <div class="event-actions">
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteEvent(<?= $event['id'] ?>, '<?= addslashes($event['title']) ?>')">
                            Delete
                        </button>
                    </div>
                </div>
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
            <p>Are you sure you want to delete this event?</p>
            <p><strong>Title:</strong> <span id="deleteEventTitle"></span></p>
            <p style="color: var(--color-danger); font-size: 0.875rem;">This action cannot be undone.</p>
            
            <form method="post" id="deleteForm">
                <input type="hidden" name="action" value="delete_event">
                <input type="hidden" name="event_id" id="deleteEventId">
                
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Bottom Navigation -->
<?php include '../include/bottomnav.php'; ?>

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

function deleteEvent(eventId, eventTitle) {
    document.getElementById('deleteEventId').value = eventId;
    document.getElementById('deleteEventTitle').textContent = eventTitle;
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
