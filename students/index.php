<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$user = getCurrentUser($pdo);

// Get student's attendance
$stmt = $pdo->prepare("SELECT COUNT(*) as total, 
                      SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present 
                      FROM attendance WHERE student_id = ?");
$stmt->execute([$student['id']]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

$attendance_percentage = $attendance['total'] > 0 ? 
    round(($attendance['present'] / $attendance['total']) * 100) : 0;

// Get student's upcoming assignments
$stmt = $pdo->prepare("SELECT a.*, s.subject_name FROM assignments a 
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN student_classes sc ON s.class_id = sc.class_id
                      WHERE sc.student_id = ? AND a.due_date >= CURDATE()
                      ORDER BY a.due_date ASC LIMIT 5");
$stmt->execute([$student['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent grades
$stmt = $pdo->prepare("SELECT ag.*, a.title as assignment_title, s.subject_name 
                      FROM assignment_grades ag
                      JOIN assignments a ON ag.assignment_id = a.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE ag.student_id = ?
                      ORDER BY ag.created_at DESC LIMIT 3");
$stmt->execute([$student['id']]);
$recent_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get today's schedule
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT s.subject_name, sch.start_time, sch.end_time, c.class_name, t.first_name, t.last_name
                      FROM schedules sch
                      JOIN subjects s ON sch.subject_id = s.id
                      JOIN classes c ON s.class_id = c.id
                      JOIN users t ON s.teacher_id = t.id
                      JOIN student_classes sc ON c.id = sc.class_id
                      WHERE sc.student_id = ? AND sch.day_of_week = DAYNAME(?)
                      ORDER BY sch.start_time ASC");
$stmt->execute([$student['id'], $today]);
$today_schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - School LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pwa-container {
            max-width: 428px;
            margin: 0 auto;
            min-height: 100vh;
            background: #a339e4;
            position: relative;
        }
        
        .content-wrapper {
            background: #fff;
            min-height: calc(100vh - 60px);
            border-radius: 24px 24px 0 0;
            margin-top: 60px;
            padding-bottom: 80px;
        }
        
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(163, 57, 228, 0.1);
            border: 1px solid rgba(163, 57, 228, 0.1);
        }
        
        .text-primary { color: #a339e4; }
        .bg-primary { background-color: #a339e4; }
        
        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @media (max-width: 768px) {
            .desktop-sidebar { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="pwa-container">
        <!-- Compact Header -->
        <header class="pt-4 pb-16 px-6 text-white">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-xl font-bold">Hello, <?= htmlspecialchars($user['first_name']) ?></h1>
                    <p class="text-white/80 text-sm"><?= htmlspecialchars($student['class_name'] ?? 'Student') ?></p>
                </div>
                <div class="text-right">
                    <p class="text-white/80 text-xs">Attendance</p>
                    <p class="text-lg font-bold"><?= $attendance_percentage ?>%</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content-wrapper">
            <div class="p-4">
                <!-- Today's Schedule -->
                <section class="mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-bold text-gray-900">Today's Classes</h2>
                        <span class="text-xs text-gray-500"><?= date('l') ?></span>
                    </div>
                    
                    <?php if (empty($today_schedule)): ?>
                        <div class="card p-4 text-center">
                            <p class="text-gray-500 text-sm">No classes today</p>
                            <p class="text-gray-400 text-xs">Enjoy your free day!</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($today_schedule as $class): ?>
                                <div class="card p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-medium text-gray-900 text-sm"><?= htmlspecialchars($class['subject_name']) ?></h3>
                                            <p class="text-xs text-gray-600"><?= htmlspecialchars($class['first_name'] . ' ' . $class['last_name']) ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-primary font-medium">
                                                <?= date('g:i A', strtotime($class['start_time'])) ?> - <?= date('g:i A', strtotime($class['end_time'])) ?>
                                            </p>
                                            <div class="status-dot bg-green-500 ml-auto mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Upcoming Assignments -->
                <section class="mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-bold text-gray-900">Upcoming Tasks</h2>
                        <a href="assignments.php" class="text-primary text-xs font-medium">View All</a>
                    </div>
                    
                    <?php if (empty($assignments)): ?>
                        <div class="card p-4 text-center">
                            <p class="text-gray-500 text-sm">All caught up!</p>
                            <p class="text-gray-400 text-xs">No pending assignments</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($assignments as $assignment): ?>
                                <div class="card p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-medium text-gray-900 text-sm truncate"><?= htmlspecialchars($assignment['title']) ?></h3>
                                            <p class="text-xs text-gray-600"><?= htmlspecialchars($assignment['subject_name']) ?></p>
                                        </div>
                                        <div class="text-right ml-2">
                                            <span class="text-xs text-red-600 font-medium">Due: <?= date('M j', strtotime($assignment['due_date'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Recent Grades -->
                <?php if (!empty($recent_grades)): ?>
                <section class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Recent Grades</h2>
                    <div class="space-y-2">
                        <?php foreach ($recent_grades as $grade): ?>
                            <div class="card p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium text-gray-900 text-sm"><?= htmlspecialchars($grade['assignment_title']) ?></h3>
                                        <p class="text-xs text-gray-600"><?= htmlspecialchars($grade['subject_name']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-primary"><?= number_format($grade['grade'], 1) ?></div>
                                        <div class="text-xs text-gray-500">/ <?= number_format($grade['max_grade'], 0) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Quick Actions -->
                <section class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Quick Actions</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="leavenotice.php" class="card p-4 text-center block hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-white text-sm">📝</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Apply Leave</span>
                        </a>
                        
                        <a href="attendance.php" class="card p-4 text-center block hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-white text-sm">📊</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Attendance</span>
                        </a>
                    </div>
                </section>

                <!-- Stats Overview -->
                <section class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Overview</h2>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="card p-3 text-center">
                            <div class="text-lg font-bold text-primary"><?= $attendance['present'] ?></div>
                            <div class="text-xs text-gray-600">Present Days</div>
                        </div>
                        <div class="card p-3 text-center">
                            <div class="text-lg font-bold text-primary"><?= count($assignments) ?></div>
                            <div class="text-xs text-gray-600">Pending Tasks</div>
                        </div>
                        <div class="card p-3 text-center">
                            <div class="text-lg font-bold text-primary"><?= count($recent_grades) ?></div>
                            <div class="text-xs text-gray-600">Recent Grades</div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <?php include '../include/bootoomnav.php'; ?>
    </div>

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true 
            });
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Update time immediately and then every minute
        updateTime();
        setInterval(updateTime, 60000);

        // Add smooth scroll behavior
        document.addEventListener('DOMContentLoaded', function() {
            // Add click animations to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 100);
                });
            });
        });

        // PWA functionality
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>
</body>
</html>