<?php
include_once '../App/Models/teacher/Message.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - LMS</title>
    <meta name="description" content="Messages - Communicate with students and parents">
    <meta name="theme-color" content="#10b981">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/teacher.css">
    <link rel="stylesheet" href="../assets/css/teacher/messages.css">
</head>
<body>
    <div class="container ">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="header-title">Messages</h1>
                <p class="header-subtitle">Communicate with students and parents</p>
            </div>
        </div>

        <!-- Alert Messages -->
        <?= $msg ?>

        <!-- Message Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $message_stats['sent_count'] ?></div>
                <div class="stat-label">Sent</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $message_stats['received_count'] ?></div>
                <div class="stat-label">Received</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <div class="stat-number"><?= $message_stats['unread_count'] ?></div>
                <div class="stat-label">Unread</div>
            </div>
        </div>

        <!-- Message Tabs -->
        <div class="card">
            <div class="message-tabs">
                <div class="tab-buttons">
                    <button class="tab-button active" onclick="showTab('compose')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3h18v18h-18z"/>
                            <path d="M21 9l-9 6-9-6"/>
                        </svg>
                        Compose
                    </button>
                    <button class="tab-button" onclick="showTab('received')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                        Inbox (<?= $message_stats['unread_count'] ?>)
                    </button>
                    <button class="tab-button" onclick="showTab('sent')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                        </svg>
                        Sent
                    </button>
                </div>

                <!-- Compose Tab -->
                <div id="compose" class="tab-content active">
                    <form method="post" class="compose-form">
                        <input type="hidden" name="action" value="send_message">
                        
                        <!-- Bulk Messaging Section -->
                        <?php if (!empty($teacher_classes)): ?>
                        <div class="bulk-message-section">
                            <div class="bulk-message-header">
                                <div class="bulk-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <div class="bulk-title">Send to Entire Classes</div>
                            </div>
                            <div class="class-grid">
                                <?php foreach ($teacher_classes as $class): ?>
                                <div class="class-item">
                                    <input type="checkbox" name="bulk_classes[]" value="<?= $class['id'] ?>" id="class_<?= $class['id'] ?>" onchange="toggleClassStudents(<?= $class['id'] ?>)">
                                    <label for="class_<?= $class['id'] ?>">
                                        <span class="class-name"><?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></span>
                                        <span class="class-count">(<?= $class['student_count'] ?> students)</span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label class="form-label">Recipients *</label>
                            <div class="recipient-section">
                                <div class="recipient-controls">
                                    <button type="button" onclick="selectAllStudents()" class="btn btn-small btn-secondary">Select All</button>
                                    <button type="button" onclick="clearAllStudents()" class="btn btn-small btn-secondary">Clear All</button>
                                </div>
                                <div class="recipient-grid">
                                    <?php if (empty($students)): ?>
                                    <div class="empty-recipients">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                        </div>
                                        <div class="empty-text">No students found. Make sure you are assigned to classes.</div>
                                    </div>
                                    <?php else: ?>
                                    <?php foreach ($students as $student): ?>
                                    <div class="recipient-item">
                                        <input type="checkbox" name="recipients[]" value="<?= $student['id'] ?>" id="student_<?= $student['id'] ?>">
                                        <label for="student_<?= $student['id'] ?>">
                                            <div class="student-name"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
                                            <div class="student-class"><?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?></div>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Subject *</label>
                                <input type="text" name="subject" class="form-input" required placeholder="Enter message subject">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Message Type</label>
                                <select name="message_type" class="form-select">
                                    <option value="personal">Personal Message</option>
                                    <option value="announcement">Announcement</option>
                                    <option value="assignment">Assignment Related</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Message *</label>
                            <textarea name="message_body" class="form-textarea" required placeholder="Type your message here..."></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                                </svg>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Received Messages Tab -->
                <div id="received" class="tab-content">
                    <?php if (empty($received_messages)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div class="empty-title">No Messages</div>
                        <div class="empty-text">You haven't received any messages yet.</div>
                    </div>
                    <?php else: ?>
                    <div class="message-list">
                        <?php foreach ($received_messages as $message): ?>
                        <div class="message-item <?= $message['is_read'] ? '' : 'unread' ?>" onclick="viewMessage(<?= $message['id'] ?>)">
                            <div class="message-header">
                                <div class="message-sender">
                                    <div class="sender-avatar">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                    <div class="sender-info">
                                        <div class="sender-name"><?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?></div>
                                        <?php if ($message['priority'] != 'normal'): ?>
                                        <span class="message-priority priority-<?= $message['priority'] ?>">
                                            <?= strtoupper($message['priority']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="message-date">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12,6 12,12 16,14"/>
                                    </svg>
                                    <?= date('M j, Y g:i A', strtotime($message['sent_at'])) ?>
                                </div>
                            </div>
                            <div class="message-subject"><?= htmlspecialchars($message['subject']) ?></div>
                            <div class="message-preview"><?= htmlspecialchars(substr($message['message_body'], 0, 100)) ?>...</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sent Messages Tab -->
                <div id="sent" class="tab-content">
                    <?php if (empty($sent_messages)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                            </svg>
                        </div>
                        <div class="empty-title">No Sent Messages</div>
                        <div class="empty-text">You haven't sent any messages yet.</div>
                    </div>
                    <?php else: ?>
                    <div class="message-list">
                        <?php foreach ($sent_messages as $message): ?>
                        <div class="message-item">
                            <div class="message-header">
                                <div class="message-sender">
                                    <div class="sender-avatar">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                    <div class="sender-info">
                                        <div class="sender-name">To: <?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?></div>
                                        <?php if ($message['priority'] != 'normal'): ?>
                                        <span class="message-priority priority-<?= $message['priority'] ?>">
                                            <?= strtoupper($message['priority']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="message-date">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12,6 12,12 16,14"/>
                                    </svg>
                                    <?= date('M j, Y g:i A', strtotime($message['sent_at'])) ?>
                                </div>
                            </div>
                            <div class="message-subject"><?= htmlspecialchars($message['subject']) ?></div>
                            <div class="message-preview"><?= htmlspecialchars(substr($message['message_body'], 0, 100)) ?>...</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function selectAllStudents() {
            document.querySelectorAll('input[name="recipients[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function clearAllStudents() {
            document.querySelectorAll('input[name="recipients[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        function toggleClassStudents(classId) {
            const classCheckbox = document.getElementById('class_' + classId);
            const studentCheckboxes = document.querySelectorAll('input[name="recipients[]"]');
            
            if (classCheckbox.checked) {
                // Get students for this class and check them
                // This would require AJAX to get class students, for now just select all
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            }
        }

        function viewMessage(messageId) {
            // Mark as read and show message details
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'mark_read';
            
            const messageIdInput = document.createElement('input');
            messageIdInput.type = 'hidden';
            messageIdInput.name = 'message_id';
            messageIdInput.value = messageId;
            
            form.appendChild(actionInput);
            form.appendChild(messageIdInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>