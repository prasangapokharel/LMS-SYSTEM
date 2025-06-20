<?php
include_once '../App/Models/student/Event.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - School LMS</title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <style>
        .student-app {
            font-family: var(--font-inter);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .student-header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .back-btn {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 18px;
        }

        .header-info h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .header-info p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .content-wrapper {
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0 0 4px 0;
        }

        .stat-label {
            font-size: 12px;
            color: var(--color-gray-600);
            margin: 0;
        }

        .filters-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
        }

        .filters-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0 0 16px 0;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .filter-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-gray-700);
            margin-bottom: 8px;
        }

        .events-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .event-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-gray-200);
            border-left: 4px solid var(--color-primary);
            cursor: pointer;
        }

        .event-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .event-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 12px;
        }

        .event-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .event-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            background: var(--color-primary-light);
            color: var(--color-primary);
        }

        .event-content {
            flex: 1;
            min-width: 0;
        }

        .event-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 14px;
            color: var(--color-gray-600);
            margin-bottom: 8px;
        }

        .event-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .event-description {
            font-size: 14px;
            color: var(--color-gray-600);
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .event-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-exam {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-assignment {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-meeting {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .badge-holiday {
            background: #dcfce7;
            color: #166534;
        }

        .badge-announcement {
            background: #fce7f3;
            color: #be185d;
        }

        .badge-other {
            background: #f1f5f9;
            color: #475569;
        }

        .days-until {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 8px;
            background: var(--color-primary-light);
            color: var(--color-primary);
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
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 24px 24px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--color-gray-500);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: var(--color-gray-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: var(--color-gray-400);
        }

        .empty-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-gray-700);
            margin: 0 0 8px 0;
        }

        .empty-text {
            font-size: 14px;
            color: var(--color-gray-500);
            margin: 0;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 0 16px;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .event-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .event-image,
            .event-icon {
                width: 100%;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="student-app">
        <div class="student-header">
            <div class="header-content">
                <a href="menu.php" class="back-btn">←</a>
                <div class="header-info">
                    <h1>Events</h1>
                    <p>View upcoming events and announcements</p>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Statistics
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
                    <div class="stat-number"><?= $stats['today_events'] ?></div>
                    <div class="stat-label">Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['exams'] ?></div>
                    <div class="stat-label">Exams</div>
                </div>
            </div> -->

            <!-- Filters -->
            <!-- <div class="filters-card">
                <h3 class="filters-title">Filter Events</h3>
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="type">Event Type</label>
                            <select name="type" id="type" class="form-input">
                                <option value="all" <?= $event_type === 'all' ? 'selected' : '' ?>>All Types</option>
                                <option value="class" <?= $event_type === 'class' ? 'selected' : '' ?>>Classes</option>
                                <option value="exam" <?= $event_type === 'exam' ? 'selected' : '' ?>>Exams</option>
                                <option value="assignment" <?= $event_type === 'assignment' ? 'selected' : '' ?>>Assignments</option>
                                <option value="holiday" <?= $event_type === 'holiday' ? 'selected' : '' ?>>Holidays</option>
                                <option value="meeting" <?= $event_type === 'meeting' ? 'selected' : '' ?>>Meetings</option>
                                <option value="announcement" <?= $event_type === 'announcement' ? 'selected' : '' ?>>Announcements</option>
                                <option value="other" <?= $event_type === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="date">Date Filter</label>
                            <select name="date" id="date" class="form-input">
                                <option value="all" <?= $date_filter === 'all' ? 'selected' : '' ?>>All Events</option>
                                <option value="today" <?= $date_filter === 'today' ? 'selected' : '' ?>>Today</option>
                                <option value="upcoming" <?= $date_filter === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                                <option value="this_week" <?= $date_filter === 'this_week' ? 'selected' : '' ?>>This Week</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn1" style="margin-top: 16px;">Apply Filters</button>
                </form>
            </div> -->

            <!-- Events List -->
            <?php if (empty($events)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📅</div>
                    <h3 class="empty-title">No Events Found</h3>
                    <p class="empty-text">No events match your current filters. Try adjusting your search criteria.</p>
                </div>
            <?php else: ?>
                <div class="events-list">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card" style="border-left-color: <?= htmlspecialchars($event['color']) ?>;" onclick="showEventModal(<?= htmlspecialchars(json_encode($event)) ?>)">
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
                                    
                                    <div class="event-meta">
                                        <span>📅 <?= formatEventDate($event['start_date']) ?></span>
                                        
                                        <?php if ($event['start_time']): ?>
                                            <span>🕐 <?= formatEventTime($event['start_time']) ?></span>
                                        <?php endif; ?>
                                        
                                        <?php if ($event['location']): ?>
                                            <span>📍 <?= htmlspecialchars($event['location']) ?></span>
                                        <?php endif; ?>
                                        
                                        <span class="days-until"><?= getDaysUntilEvent($event['start_date']) ?></span>
                                    </div>
                                    
                                    <?php if ($event['description']): ?>
                                        <p class="event-description"><?= htmlspecialchars(substr($event['description'], 0, 100)) ?><?= strlen($event['description']) > 100 ? '...' : '' ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="event-badges">
                                        <span class="badge badge-<?= $event['event_type'] ?>"><?= ucfirst($event['event_type']) ?></span>
                                        
                                        <?php if ($event['class_name']): ?>
                                            <span class="badge badge-other"><?= htmlspecialchars($event['class_name'] . ' ' . $event['section']) ?></span>
                                        <?php endif; ?>
                                        
                                        <?php if ($event['subject_name']): ?>
                                            <span class="badge badge-other"><?= htmlspecialchars($event['subject_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Event Details</h3>
                <button type="button" class="modal-close" onclick="hideEventModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Event details will be populated here -->
            </div>
        </div>
    </div>

    <?php include '../include/bootoomnav.php'; ?>

    <script>
        // Auto-submit form when filters change
        document.getElementById('type').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('date').addEventListener('change', function() {
            this.form.submit();
        });

        function showEventModal(event) {
            const modal = document.getElementById('eventModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            
            modalTitle.textContent = event.title;
            
            let modalContent = '';
            
            // Event image
            if (event.event_image) {
                modalContent += `<img src="../${event.event_image}" alt="Event Image" class="modal-image">`;
            }
            
            // Event details
            modalContent += `
                <div style="margin-bottom: 16px;">
                    <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">📅 Date & Time</h4>
                    <p style="margin: 0; color: var(--color-gray-600);">
                        ${formatEventDate(event.start_date)}
                        ${event.start_time ? ' at ' + formatEventTime(event.start_time) : ''}
                        ${event.end_time ? ' - ' + formatEventTime(event.end_time) : ''}
                    </p>
                </div>
            `;
            
            if (event.description) {
                modalContent += `
                    <div style="margin-bottom: 16px;">
                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">📝 Description</h4>
                        <p style="margin: 0; color: var(--color-gray-600); line-height: 1.5;">${event.description}</p>
                    </div>
                `;
            }
            
            if (event.location) {
                modalContent += `
                    <div style="margin-bottom: 16px;">
                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">📍 Location</h4>
                        <p style="margin: 0; color: var(--color-gray-600);">${event.location}</p>
                    </div>
                `;
            }
            
            modalContent += `
                <div style="margin-bottom: 16px;">
                    <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">🏷️ Event Type</h4>
                    <span class="badge badge-${event.event_type}">${event.event_type.charAt(0).toUpperCase() + event.event_type.slice(1)}</span>
                </div>
            `;
            
            if (event.class_name || event.subject_name) {
                modalContent += `
                    <div style="margin-bottom: 16px;">
                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">📚 Class & Subject</h4>
                        <p style="margin: 0; color: var(--color-gray-600);">
                            ${event.class_name ? event.class_name + ' ' + event.section : 'All Classes'}
                            ${event.subject_name ? ' - ' + event.subject_name : ''}
                        </p>
                    </div>
                `;
            }
            
            modalContent += `
                <div>
                    <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">👨‍🏫 Created By</h4>
                    <p style="margin: 0; color: var(--color-gray-600);">${event.first_name} ${event.last_name}</p>
                </div>
            `;
            
            modalBody.innerHTML = modalContent;
            modal.classList.add('show');
        }

        function hideEventModal() {
            document.getElementById('eventModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('eventModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideEventModal();
            }
        });

        function formatEventDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        function formatEventTime(timeString) {
            const time = new Date('2000-01-01 ' + timeString);
            return time.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        }
    </script>
</body>
</html>
