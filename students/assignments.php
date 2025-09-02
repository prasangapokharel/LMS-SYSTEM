<?php
include_once '../App/Models/student/Assignment.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Student Dashboard</title>
    <meta name="description" content="View and submit your assignments">
    <meta name="theme-color" content="#3b82f6">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/ui.css">
    <link rel="stylesheet" href="../assets/css/student/assignments.css">
</head>
<body>
    <div class="student-app">
        <!-- Header -->
        <div class="assignments-header">
            <div class="header-nav">
                <a href="index.php" class="back-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="header-title">Assignments</h1>
            </div>
        </div>

        <div class="assignments-content">
            <!-- Status Tabs -->
            <div class="status-tabs">
                <a href="?status=all&subject=<?= $subject_filter ?>" class="tab-item <?= $status_filter === 'all' ? 'active' : '' ?>">
                    All
                </a>
                <a href="?status=pending&subject=<?= $subject_filter ?>" class="tab-item <?= $status_filter === 'pending' ? 'active' : '' ?>">
                    Pending
                </a>
                <a href="?status=overdue&subject=<?= $subject_filter ?>" class="tab-item <?= $status_filter === 'overdue' ? 'active' : '' ?>">
                    Overdue
                </a>
                <a href="?status=submitted&subject=<?= $subject_filter ?>" class="tab-item <?= $status_filter === 'submitted' ? 'active' : '' ?>">
                    Submitted
                </a>
                <a href="?status=graded&subject=<?= $subject_filter ?>" class="tab-item <?= $status_filter === 'graded' ? 'active' : '' ?>">
                    Graded
                </a>
            </div>

            <!-- Subject Filter Pills -->
            <div class="subject-filters">
                <a href="?status=<?= $status_filter ?>&subject=all" class="filter-pill <?= $subject_filter === 'all' ? 'active' : '' ?>">
                    All Subjects
                </a>
                <?php foreach ($subjects as $subject): ?>
                <a href="?status=<?= $status_filter ?>&subject=<?= $subject['id'] ?>" class="filter-pill <?= $subject_filter == $subject['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($subject['subject_name']) ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Assignments List -->
            <div class="assignments-container">
                <?php if (empty($assignments)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="empty-title">No Assignments Found</h3>
                    <p class="empty-text">No assignments match your current filters</p>
                </div>
                <?php else: ?>
                <?php foreach ($assignments as $assignment): ?>
                <div class="assignment-card">
                    <div class="assignment-content">
                        <div class="assignment-header">
                            <?php if ($assignment['assignment_status'] === 'submitted' || $assignment['assignment_status'] === 'graded'): ?>
                            <div class="status-indicator submitted">
                                <?php if ($assignment['assignment_status'] === 'graded'): ?>
                                Graded: <?= $assignment['grade'] ?>/<?= $assignment['max_marks'] ?>
                                <?php else: ?>
                                Submitted: <?= date('M j, Y', strtotime($assignment['submission_date'])) ?>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="due-date">Due: <?= date('M j, Y', strtotime($assignment['due_date'])) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="assignment-title"><?= htmlspecialchars($assignment['title']) ?></h3>
                        <p class="assignment-subject"><?= htmlspecialchars($assignment['subject_name']) ?></p>
                        
                        <div class="assignment-actions">
                            <button class="view-details-btn" onclick="viewAssignment(<?= $assignment['id'] ?>)">
                                View Details
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            
                            <?php if ($assignment['assignment_status'] === 'pending' || $assignment['assignment_status'] === 'overdue'): ?>
                            <button class="submit-btn" onclick="openSubmissionModal(<?= $assignment['id'] ?>)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Submit Now
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="assignment-thumbnail">
                        <?php
                        // Generate subject-based thumbnail
                        $subject_name = strtolower($assignment['subject_name']);
                        $thumbnail_class = 'default';
                        $thumbnail_icon = 'document';
                        
                        if (strpos($subject_name, 'math') !== false || strpos($subject_name, 'algebra') !== false || strpos($subject_name, 'geometry') !== false) {
                            $thumbnail_class = 'math';
                            $thumbnail_icon = 'calculator';
                        } elseif (strpos($subject_name, 'science') !== false || strpos($subject_name, 'chemistry') !== false || strpos($subject_name, 'physics') !== false || strpos($subject_name, 'biology') !== false) {
                            $thumbnail_class = 'science';
                            $thumbnail_icon = 'beaker';
                        } elseif (strpos($subject_name, 'history') !== false || strpos($subject_name, 'social') !== false) {
                            $thumbnail_class = 'history';
                            $thumbnail_icon = 'book';
                        } elseif (strpos($subject_name, 'english') !== false || strpos($subject_name, 'literature') !== false) {
                            $thumbnail_class = 'english';
                            $thumbnail_icon = 'pencil';
                        }
                        ?>
                        <div class="thumbnail-container <?= $thumbnail_class ?>">
                            <?php if ($thumbnail_icon === 'calculator'): ?>
                            <svg class="thumbnail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <?php elseif ($thumbnail_icon === 'beaker'): ?>
                            <svg class="thumbnail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547A1.934 1.934 0 004 16.684v4.650a2 2 0 002 2h12a2 2 0 002-2v-4.65a1.934 1.934 0 00-.572-1.006z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2v2.789a2 2 0 01-.211.896L7.5 8h9l-1.289-2.315A2 2 0 0115 4.789V2a1 1 0 00-1-1H10a1 1 0 00-1 1z"></path>
                            </svg>
                            <?php elseif ($thumbnail_icon === 'book'): ?>
                            <svg class="thumbnail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <?php elseif ($thumbnail_icon === 'pencil'): ?>
                            <svg class="thumbnail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            <?php else: ?>
                            <svg class="thumbnail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Submission Modal -->
    <div id="submissionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Submit Assignment
                </h3>
                <button class="modal-close" onclick="closeSubmissionModal()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Submitting assignment...</p>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        let currentAssignmentId = null;

        function openSubmissionModal(assignmentId) {
            console.log('Opening submission modal for assignment:', assignmentId);
            currentAssignmentId = assignmentId;
            const modal = document.getElementById('submissionModal');
            const modalBody = document.getElementById('modalBody');
            
            // Show loading
            modalBody.innerHTML = `
                <div class="loading-content">
                    <div class="spinner"></div>
                    <p>Loading assignment details...</p>
                </div>
            `;
            modal.style.display = 'block';
            
            // Construct the correct URL path
            const baseUrl = window.location.origin;
            const currentPath = window.location.pathname;
            const basePath = currentPath.substring(0, currentPath.lastIndexOf('/'));
            const url = `${baseUrl}${basePath}/../App/Models/student/SubmitAssignment.php?action=get_assignment&id=${assignmentId}`;
            
            console.log('Fetching from URL:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response');
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    renderSubmissionForm(data.data);
                } else {
                    modalBody.innerHTML = `
                        <div class="error-message">
                            <h4>Error Loading Assignment</h4>
                            <p>${data.message}</p>
                            <button class="btn-primary" onclick="closeSubmissionModal()">Close</button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                modalBody.innerHTML = `
                    <div class="error-message">
                        <h4>Error Loading Assignment</h4>
                        <p>${error.message}</p>
                        <p>Please try refreshing the page or contact support if the problem persists.</p>
                        <button class="btn-primary" onclick="closeSubmissionModal()">Close</button>
                    </div>
                `;
            });
        }

        function renderSubmissionForm(assignment) {
            const modalBody = document.getElementById('modalBody');
            
            if (assignment.already_submitted) {
                modalBody.innerHTML = `
                    <div class="submission-complete">
                        <div class="success-icon">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4>Assignment Already Submitted</h4>
                        <p>You have already submitted this assignment.</p>
                        <button class="btn-primary" onclick="closeSubmissionModal()">Close</button>
                    </div>
                `;
                return;
            }
            
            const dueStatus = assignment.days_left < 0 ? 'overdue' : 
                             assignment.days_left === 0 ? 'due-today' : 'upcoming';
            
            const dueText = assignment.days_left < 0 ? 'Overdue' :
                           assignment.days_left === 0 ? 'Due Today' :
                           assignment.days_left === 1 ? 'Due Tomorrow' :
                           `${assignment.days_left} days left`;
            
            modalBody.innerHTML = `
                <div class="assignment-details">
                    <h4 class="assignment-title">${assignment.title}</h4>
                    <p class="assignment-subject">
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        ${assignment.subject_name} • 
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        ${assignment.first_name} ${assignment.last_name}
                    </p>
                    
                    ${assignment.description ? `
                        <div class="assignment-description">
                            <h5>
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Description:
                            </h5>
                            <p>${assignment.description.replace(/\n/g, '<br>')}</p>
                        </div>
                    ` : ''}
                    
                    ${assignment.instructions ? `
                        <div class="assignment-instructions">
                            <h5>
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                Instructions:
                            </h5>
                            <p>${assignment.instructions.replace(/\n/g, '<br>')}</p>
                        </div>
                    ` : ''}
                    
                    <div class="assignment-info">
                        <div class="info-item">
                            <span class="info-label">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Due Date:
                            </span>
                            <span class="info-value">${new Date(assignment.due_date).toLocaleDateString('en-US', { 
                                year: 'numeric', month: 'short', day: 'numeric', 
                                hour: '2-digit', minute: '2-digit' 
                            })}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                Max Marks:
                            </span>
                            <span class="info-value">${assignment.max_marks} points</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Status:
                            </span>
                            <span class="status-badge status-${dueStatus}">${dueText}</span>
                        </div>
                    </div>
                </div>
                
                <form id="submissionForm" class="submission-form">
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Your Response *
                        </label>
                        <textarea name="submission_text" rows="6" required 
                                  class="form-textarea" 
                                  placeholder="Type your assignment response here..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Attach File (Optional)
                        </label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <input type="file" name="submission_file" id="fileInput" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png" style="display: none;">
                            <div class="upload-content">
                                <div class="upload-icon">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <p class="upload-text">Click to upload or drag and drop</p>
                                <p class="upload-hint">PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</p>
                            </div>
                        </div>
                        <div id="fileInfo" class="file-info" style="display: none;">
                            <div class="file-details">
                                <span class="file-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </span>
                                <div class="file-text">
                                    <span id="fileName"></span>
                                    <span id="fileSize"></span>
                                </div>
                                <button type="button" class="remove-file" onclick="removeFile()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeSubmissionModal()">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Submit Assignment
                        </button>
                    </div>
                </form>
            `;
            
            setupFileUpload();
            setupFormSubmission();
        }

        function setupFileUpload() {
            const fileUploadArea = document.getElementById('fileUploadArea');
            const fileInput = document.getElementById('fileInput');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');

            fileUploadArea.addEventListener('click', () => fileInput.click());

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

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            function handleFileSelect(file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    return;
                }

                const allowedTypes = ['application/pdf', 'application/msword', 
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                                    'text/plain', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only PDF, DOC, DOCX, TXT, JPG, PNG files are allowed.');
                    return;
                }

                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.style.display = 'block';
            }
        }

        function removeFile() {
            document.getElementById('fileInput').value = '';
            document.getElementById('fileInfo').style.display = 'none';
        }

        function setupFormSubmission() {
            const form = document.getElementById('submissionForm');
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAssignment();
            });
        }

        function submitAssignment() {
            const form = document.getElementById('submissionForm');
            const formData = new FormData(form);
            formData.append('assignment_id', currentAssignmentId);
            
            // Show loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Construct the correct URL path for submission
            const baseUrl = window.location.origin;
            const currentPath = window.location.pathname;
            const basePath = currentPath.substring(0, currentPath.lastIndexOf('/'));
            const url = `${baseUrl}${basePath}/../App/Models/student/SubmitAssignment.php`;
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').style.display = 'none';
                
                if (data.success) {
                    showSuccessMessage(data.message);
                    closeSubmissionModal();
                    // Refresh the page to show updated status
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showErrorMessage(data.message);
                }
            })
            .catch(error => {
                document.getElementById('loadingOverlay').style.display = 'none';
                showErrorMessage('Error submitting assignment. Please try again.');
            });
        }

        function closeSubmissionModal() {
            document.getElementById('submissionModal').style.display = 'none';
            currentAssignmentId = null;
        }

        function viewAssignment(assignmentId) {
            window.location.href = `view_assignment.php?id=${assignmentId}`;
        }

        function showSuccessMessage(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-success';
            alert.innerHTML = `
                <span class="alert-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
                <span>${message}</span>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }

        function showErrorMessage(message) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-error';
            alert.innerHTML = `
                <span class="alert-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
                <span>${message}</span>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('submissionModal');
            if (event.target === modal) {
                closeSubmissionModal();
            }
        }
    </script>
</body>
</html>
