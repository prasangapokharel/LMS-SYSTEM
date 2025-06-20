<?php
include_once '../App/Models/teacher/Message.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages - LMS</title>
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
    
    .message-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
    
    .message-tabs {
        background: var(--color-white);
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
        overflow: hidden;
    }
    
    .tab-buttons {
        display: flex;
        border-bottom: 1px solid var(--color-gray-200);
    }
    
    .tab-button {
        flex: 1;
        padding: 1rem;
        background: none;
        border: none;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--color-gray-600);
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }
    
    .tab-button.active {
        color: var(--color-primary);
        border-bottom-color: var(--color-primary);
        background: var(--color-primary-light);
    }
    
    .tab-content {
        display: none;
        padding: 1.5rem;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .message-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .message-item {
        background: var(--color-gray-50);
        border-radius: 0.5rem;
        padding: 1rem;
        border: 1px solid var(--color-gray-200);
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .message-item:hover {
        background: var(--color-white);
        box-shadow: var(--shadow-sm);
    }
    
    .message-item.unread {
        background: var(--color-primary-light);
        border-color: var(--color-primary);
    }
    
    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }
    
    .message-sender {
        font-weight: 600;
        color: var(--color-gray-900);
        font-size: 0.875rem;
    }
    
    .message-date {
        font-size: 0.75rem;
        color: var(--color-gray-500);
    }
    
    .message-subject {
        font-weight: 500;
        color: var(--color-gray-800);
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    
    .message-preview {
        color: var(--color-gray-600);
        font-size: 0.75rem;
        line-height: 1.4;
    }
    
    .message-priority {
        display: inline-block;
        padding: 0.125rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 500;
        margin-left: 0.5rem;
    }
    
    .priority-high {
        background: var(--color-danger-light);
        color: var(--color-danger);
    }
    
    .priority-urgent {
        background: var(--color-danger);
        color: var(--color-white);
    }
    
    .compose-form {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--color-gray-700);
        margin-bottom: 0.5rem;
    }
    
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-gray-300);
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .recipient-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.5rem;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid var(--color-gray-300);
        border-radius: 0.375rem;
        padding: 0.75rem;
    }
    
    .recipient-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: var(--color-primary);
        color: var(--color-white);
    }
    
    .btn-secondary {
        background: var(--color-gray-500);
        color: var(--color-white);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--color-gray-500);
    }
    
    @media (min-width: 768px) {
        .mobile-container {
            max-width: 1200px;
            padding: 2rem;
        }
    }
</style>
</head>
<body>
<div class="mobile-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Messages</h1>
        <p class="page-subtitle">Communicate with students and parents</p>
    </div>

    <!-- Alert Messages -->
    <?= $msg ?>

    <!-- Message Statistics -->
    <div class="message-stats">
        <div class="stat-card">
            <div class="stat-number"><?= $message_stats['sent_count'] ?></div>
            <div class="stat-label">Sent</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $message_stats['received_count'] ?></div>
            <div class="stat-label">Received</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $message_stats['unread_count'] ?></div>
            <div class="stat-label">Unread</div>
        </div>
    </div>

    <!-- Message Tabs -->
    <div class="message-tabs">
        <div class="tab-buttons">
            <button class="tab-button active" onclick="showTab('compose')">Compose</button>
            <button class="tab-button" onclick="showTab('received')">Inbox (<?= $message_stats['unread_count'] ?>)</button>
            <button class="tab-button" onclick="showTab('sent')">Sent</button>
        </div>

        <!-- Compose Tab -->
        <div id="compose" class="tab-content active">
            <form method="post" class="compose-form">
                <input type="hidden" name="action" value="send_message">
                
                <div class="form-group">
                    <label class="form-label">Recipients *</label>
                    <div class="recipient-grid">
                        <?php foreach ($students as $student): ?>
                        <div class="recipient-item">
                            <input type="checkbox" name="recipients[]" value="<?= $student['id'] ?>" id="student_<?= $student['id'] ?>">
                            <label for="student_<?= $student['id'] ?>">
                                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                <br><small><?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?></small>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <button type="button" onclick="selectAllStudents()" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Select All</button>
                        <button type="button" onclick="clearAllStudents()" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Clear All</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Subject *</label>
                    <input type="text" name="subject" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Message Type</label>
                    <select name="message_type" class="form-select">
                        <option value="personal">Personal Message</option>
                        <option value="announcement">Announcement</option>
                        <option value="assignment">Assignment Related</option>
                    </select>
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
                
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>

        <!-- Received Messages Tab -->
        <div id="received" class="tab-content">
            <?php if (empty($received_messages)): ?>
            <div class="empty-state">
                <div class="empty-title">No Messages</div>
                <div class="empty-text">You haven't received any messages yet.</div>
            </div>
            <?php else: ?>
            <div class="message-list">
                <?php foreach ($received_messages as $message): ?>
                <div class="message-item <?= $message['is_read'] ? '' : 'unread' ?>" onclick="viewMessage(<?= $message['id'] ?>)">
                    <div class="message-header">
                        <div class="message-sender">
                            <?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?>
                            <?php if ($message['priority'] != 'normal'): ?>
                            <span class="message-priority priority-<?= $message['priority'] ?>">
                                <?= strtoupper($message['priority']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="message-date"><?= date('M j, Y g:i A', strtotime($message['sent_at'])) ?></div>
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
                <div class="empty-title">No Sent Messages</div>
                <div class="empty-text">You haven't sent any messages yet.</div>
            </div>
            <?php else: ?>
            <div class="message-list">
                <?php foreach ($sent_messages as $message): ?>
                <div class="message-item">
                    <div class="message-header">
                        <div class="message-sender">
                            To: <?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?>
                            <?php if ($message['priority'] != 'normal'): ?>
                            <span class="message-priority priority-<?= $message['priority'] ?>">
                                <?= strtoupper($message['priority']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="message-date"><?= date('M j, Y g:i A', strtotime($message['sent_at'])) ?></div>
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
