<?php
include_once '../App/Models/student/Index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
    <meta name="description" content="Student Learning Management System Dashboard">
    <meta name="theme-color" content="#3b82f6">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <link rel="stylesheet" href="../assets/css/student/index.css">
</head>
<body>
    <div class="student-app">
        <!-- Header -->
        <div class="student-header">
            <div class="header-content">
                <div class="student-info">
                    <h1 class="student-name"> <?= htmlspecialchars($user['first_name']) ?>!</h1>
                    <p class="student-role"><?= htmlspecialchars($student_class['class_name'] ?? 'Student') ?> Dashboard</p>
                </div>
            </div>
        </div>

        <div class="student-content">
            <!-- Quick Stats -->
            <!-- <div class="stats-section">
                <div class="stat-card">
                    <div class="stat-icon"><img class=" menu-icon icon-yellow h-8 w-8"  src="../assets/icons/notice.png"></div>
                    <div class="stat-content">
                        <div class="stat-number"><?= count($notices) ?></div>
                        <div class="stat-label">Notices</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <div class="stat-number"><?= count($events) ?></div>
                        <div class="stat-label">Events</div>
                    </div>
                </div>
            
            </div> -->

            <!-- Recent Notices -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="section-icon"><img class=" menu-icon icon-yellow h-8 w-8"  src="../assets/icons/assingn.png"></span>
                        Recent Notices
                    </h2>
                    <a href="notices.php" class="section-action">View All</a>
                </div>
                <div class="section-body">
                    <?php if (empty($notices)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h4 class="empty-title">No Notices</h4>
                        <p class="empty-text">No recent notices available</p>
                    </div>
                    <?php else: ?>
                    <div class="notices-list">
                        <?php foreach ($notices as $notice): ?>
                        <div class="notice-item" onclick="openNoticeModal(<?= $notice['id'] ?>)">
                            <div class="item-icon"><img class=" menu-icon icon-yellow h-8 w-8"  src="../assets/icons/announce.png"></div>
                            <div class="item-content">
                                <h3 class="item-title"><?= htmlspecialchars($notice['title']) ?></h3>
                                <p class="item-subtitle"><?= htmlspecialchars(substr($notice['content'], 0, 60)) ?><?= strlen($notice['content']) > 60 ? '...' : '' ?></p>
                                <div class="item-meta">
                                    <span class="item-time"><?= $notice['time_ago'] ?></span>
                                </div>
                            </div>
                            <?php if (!empty($notice['notice_image']) && $notice['notice_image'] !== ''): ?>
                            <div class="item-image-container">
                                <img src="../<?= htmlspecialchars($notice['notice_image']) ?>" alt="Notice" class="item-image">
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Events -->
            <!-- <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="section-icon">📅</span>
                        Upcoming Events
                    </h2>
                    <a href="events.php" class="section-action">View All</a>
                </div>
                <div class="section-body">
                    <?php if (empty($events)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📅</div>
                        <h4 class="empty-title">No Events</h4>
                        <p class="empty-text">No upcoming events scheduled</p>
                    </div>
                    <?php else: ?>
                    <div class="events-list">
                        <?php foreach ($events as $event): ?>
                        <div class="event-item" onclick="openEventModal(<?= $event['id'] ?>)">
                            <div class="item-icon"><?= getEventTypeIcon($event['event_type']) ?></div>
                            <div class="item-content">
                                <h3 class="item-title"><?= htmlspecialchars($event['title']) ?></h3>
                                <p class="item-subtitle">
                                    🏷️ <?= htmlspecialchars($event['event_type']) ?>
                                    <?php if ($event['subject_name']): ?>
                                    - 📚 <?= htmlspecialchars($event['subject_name']) ?>
                                    <?php endif; ?>
                                </p>
                                <div class="item-meta">
                                    <span class="item-time">⏰ <?= $event['time_until'] ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div> -->

            <!-- Quick Actions -->
            <!-- <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="section-icon">⚡</span>
                        Quick Actions
                    </h2>
                </div>
                <div class="section-body">
                    <div class="actions-grid">
                        <a href="assignments.php" class="action-item">
                            <div class="action-icon">📝</div>
                            <div class="action-text">Assignments</div>
                        </a>
                        <a href="grades.php" class="action-item">
                            <div class="action-icon">📊</div>
                            <div class="action-text">Grades</div>
                        </a>
                        <a href="schedule.php" class="action-item">
                            <div class="action-icon">🗓️</div>
                            <div class="action-text">Schedule</div>
                        </a>
                        <a href="resources.php" class="action-item">
                            <div class="action-icon">📁</div>
                            <div class="action-text">Resources</div>
                        </a>
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    <!-- Notice Modal -->
    <div id="noticeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="noticeModalTitle"><img class=" menu-icon icon-yellow h-8 w-8"  src="../assets/icons/assingn.png"> Notice</h3>
                <button class="modal-close" onclick="closeModal('noticeModal')">✕</button>
            </div>
            <div class="modal-body" id="noticeModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="eventModalTitle">📅 Event</h3>
                <button class="modal-close" onclick="closeModal('eventModal')">✕</button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
        function openNoticeModal(noticeId) {
            fetch(`../App/Models/student/Notice.php?action=get_notice&id=${noticeId}`)
                .then(response => response.json())
                .then(notice => {
                    if (notice) {
                        document.getElementById('noticeModalTitle').textContent = '📋 ' + notice.title;
                        
                        let content = '';
                        if (notice.notice_image && notice.notice_image !== '') {
                            content += `<img src="../${notice.notice_image}" alt="Notice Image" class="modal-image">`;
                        }
                        content += `<p class="modal-content-text">${notice.content}</p>`;
                        content += `<div class="modal-meta">👤 Posted by ${notice.first_name} ${notice.last_name}</div>`;
                        
                        document.getElementById('noticeModalBody').innerHTML = content;
                        document.getElementById('noticeModal').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function openEventModal(eventId) {
            fetch(`../App/Models/student/Event.php?action=get_event&id=${eventId}`)
                .then(response => response.json())
                .then(event => {
                    if (event) {
                        document.getElementById('eventModalTitle').textContent = '📅 ' + event.title;
                        
                        let content = `<p class="modal-content-text">${event.description || 'No description available'}</p>`;
                        content += `<div class="modal-meta">`;
                        content += `<div class="meta-item">🏷️ <strong>Type:</strong> ${event.event_type}</div>`;
                        content += `<div class="meta-item">📅 <strong>Date:</strong> ${new Date(event.start_date).toLocaleDateString()}</div>`;
                        if (event.start_time) {
                            content += `<div class="meta-item">⏰ <strong>Time:</strong> ${event.start_time}</div>`;
                        }
                        if (event.subject_name) {
                            content += `<div class="meta-item">📚 <strong>Subject:</strong> ${event.subject_name}</div>`;
                        }
                        content += `<div class="meta-item">👤 <strong>Created by:</strong> ${event.first_name} ${event.last_name}</div>`;
                        content += `</div>`;
                        
                        document.getElementById('eventModalBody').innerHTML = content;
                        document.getElementById('eventModal').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const noticeModal = document.getElementById('noticeModal');
            const eventModal = document.getElementById('eventModal');
            if (event.target === noticeModal) {
                noticeModal.style.display = 'none';
            }
            if (event.target === eventModal) {
                eventModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
