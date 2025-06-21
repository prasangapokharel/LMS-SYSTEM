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
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        .student-app {
            font-family: var(--font-inter);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .student-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            padding: 24px 20px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .student-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            pointer-events: none;
        }

        .header-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .student-info h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: -0.025em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .student-role {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
            font-weight: 400;
        }

        .student-content {
            padding: 0 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .dashboard-section {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .section-header {
            background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
            padding: 20px 24px;
            border-bottom: 1px solid var(--color-gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-icon {
            width: 24px;
            height: 24px;
            padding: 4px;
            background: var(--color-primary-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .section-icon svg {
            width: 16px;
            height: 16px;
            color: var(--color-primary);
        }

        .section-action {
            font-size: 14px;
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .section-action:hover {
            color: var(--color-primary-dark);
            text-decoration: none;
        }

        .section-body {
            padding: 0;
        }

        .notice-item, .event-item {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid var(--color-gray-100);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notice-item:last-child, .event-item:last-child {
            border-bottom: none;
        }

        .notice-item:hover, .event-item:hover {
            background: var(--color-gray-50);
        }

        .item-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .item-icon svg {
            width: 20px;
            height: 20px;
            color: var(--color-primary);
        }

        .item-content {
            flex: 1;
            min-width: 0;
        }

        .item-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-gray-900);
            margin: 0 0 4px 0;
            line-height: 1.3;
        }

        .item-subtitle {
            font-size: 14px;
            color: var(--color-gray-600);
            margin: 0;
            line-height: 1.3;
        }

        .item-meta {
            text-align: right;
            flex-shrink: 0;
            margin-left: 16px;
        }

        .item-time {
            font-size: 12px;
            color: var(--color-gray-500);
            margin: 0 0 4px 0;
            font-weight: 500;
        }

        .item-image {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid var(--color-gray-200);
        }

        .event-type {
            font-size: 20px;
            margin: 0;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--color-gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: var(--color-gray-400);
        }

        .empty-icon svg {
            width: 24px;
            height: 24px;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-gray-700);
            margin: 0 0 8px 0;
        }

        .empty-text {
            font-size: 14px;
            color: var(--color-gray-500);
            margin: 0;
            line-height: 1.5;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--color-gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0;
        }

        .close {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-gray-100);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .close:hover {
            background: var(--color-gray-200);
        }

        .close svg {
            width: 16px;
            height: 16px;
            color: var(--color-gray-600);
        }

        .modal-body {
            padding: 24px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal-image {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .modal-content-text {
            font-size: 16px;
            line-height: 1.6;
            color: var(--color-gray-700);
            margin: 0;
        }

        .modal-meta {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--color-gray-200);
            font-size: 14px;
            color: var(--color-gray-500);
        }

        @media (max-width: 767px) {
            .student-content {
                padding: 0 16px;
            }

            .modal-content {
                margin: 5% auto;
                width: 95%;
                max-height: 90vh;
            }

            .modal-header,
            .modal-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="student-app">
        <div class="student-header">
            <div class="header-content">
                <div class="student-info">
                    <h1>Hello, <?= htmlspecialchars($user['first_name']) ?>!</h1>
                    <p class="student-role"><?= htmlspecialchars($student_class['class_name'] ?? 'Student') ?> Dashboard</p>
                </div>
            </div>
        </div>

        <div class="student-content">
            <!-- Recent Notices -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="16" x2="8" y1="13" y2="13"/>
                                <line x1="16" x2="8" y1="17" y2="17"/>
                                <polyline points="10,9 9,9 8,9"/>
                            </svg>
                        </div>
                        Recent Notices
                    </h2>
                    <a href="notices.php" class="section-action">View All</a>
                </div>
                <div class="section-body">
                    <?php if (empty($notices)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                            </svg>
                        </div>
                        <h4 class="empty-title">No Notices</h4>
                        <p class="empty-text">No recent notices available</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($notices as $notice): ?>
                    <div class="notice-item" onclick="openNoticeModal(<?= $notice['id'] ?>)">
                        <div class="item-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" x2="21" y1="6" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                        </div>
                        <div class="item-content">
                            <h3 class="item-title"><?= htmlspecialchars($notice['title']) ?></h3>
                            <p class="item-subtitle"><?= htmlspecialchars(substr($notice['content'], 0, 60)) ?><?= strlen($notice['content']) > 60 ? '...' : '' ?></p>
                        </div>
                        <div class="item-meta">
                            <p class="item-time"><?= $notice['time_ago'] ?></p>
                            <?php if (!empty($notice['notice_image']) && $notice['notice_image'] !== ''): ?>
                            <img src="../<?= htmlspecialchars($notice['notice_image']) ?>" alt="Notice" class="item-image">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Events
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                <line x1="16" x2="16" y1="2" y2="6"/>
                                <line x1="8" x2="8" y1="2" y2="6"/>
                                <line x1="3" x2="21" y1="10" y2="10"/>
                            </svg>
                        </div>
                        Upcoming Events
                    </h2>
                    <a href="events.php" class="section-action">View All</a>
                </div>
                <div class="section-body">
                    <?php if (empty($events)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                <line x1="16" x2="16" y1="2" y2="6"/>
                                <line x1="8" x2="8" y1="2" y2="6"/>
                                <line x1="3" x2="21" y1="10" y2="10"/>
                            </svg>
                        </div>
                        <h4 class="empty-title">No Events</h4>
                        <p class="empty-text">No upcoming events scheduled</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($events as $event): ?>
                    <div class="event-item" onclick="openEventModal(<?= $event['id'] ?>)">
                        <div class="item-icon">
                            <p class="event-type"><?= getEventTypeIcon($event['event_type']) ?></p>
                        </div>
                        <div class="item-content">
                            <h3 class="item-title"><?= htmlspecialchars($event['title']) ?></h3>
                            <p class="item-subtitle">
                                <?= htmlspecialchars($event['event_type']) ?>
                                <?php if ($event['subject_name']): ?>
                                - <?= htmlspecialchars($event['subject_name']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="item-meta">
                            <p class="item-time"><?= $event['time_until'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div> -->
        </div>
    </div>

    <!-- Notice Modal -->
    <div id="noticeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="noticeModalTitle">Notice</h3>
                <button class="close" onclick="closeModal('noticeModal')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" x2="6" y1="6" y2="18"/>
                        <line x1="6" x2="18" y1="6" y2="18"/>
                    </svg>
                </button>
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
                <h3 class="modal-title" id="eventModalTitle">Event</h3>
                <button class="close" onclick="closeModal('eventModal')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" x2="6" y1="6" y2="18"/>
                        <line x1="6" x2="18" y1="6" y2="18"/>
                    </svg>
                </button>
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
                        document.getElementById('noticeModalTitle').textContent = notice.title;
                        
                        let content = '';
                        if (notice.notice_image && notice.notice_image !== '') {
                            content += `<img src="../${notice.notice_image}" alt="Notice Image" class="modal-image">`;
                        }
                        content += `<p class="modal-content-text">${notice.content}</p>`;
                        content += `<div class="modal-meta">Posted by ${notice.first_name} ${notice.last_name}</div>`;
                        
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
                        document.getElementById('eventModalTitle').textContent = event.title;
                        
                        let content = `<p class="modal-content-text">${event.description || 'No description available'}</p>`;
                        content += `<div class="modal-meta">`;
                        content += `<strong>Type:</strong> ${event.event_type}<br>`;
                        content += `<strong>Date:</strong> ${new Date(event.start_date).toLocaleDateString()}<br>`;
                        if (event.start_time) {
                            content += `<strong>Time:</strong> ${event.start_time}<br>`;
                        }
                        if (event.subject_name) {
                            content += `<strong>Subject:</strong> ${event.subject_name}<br>`;
                        }
                        content += `<strong>Created by:</strong> ${event.first_name} ${event.last_name}`;
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

        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.notice-item, .event-item');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            items.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(item);
            });
        });
    </script>
</body>
</html>
