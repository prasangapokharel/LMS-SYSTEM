<?php
include '../App/Models/teacher/Attendance.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
    <link rel="stylesheet" href="../assets/css/ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Enhanced Premium Attendance System */
        .attendance-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            padding-bottom: 80px;
            font-size: 13px;
        }

        .attendance-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .back-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            backdrop-filter: blur(10px);
            font-size: 14px;
        }

        .header-title {
            font-size: 17px;
            font-weight: 600;
            margin: 0;
            flex: 1;
        }

        .header-subtitle {
            font-size: 12px;
            opacity: 0.9;
            margin: 0;
            font-weight: 400;
        }

        .attendance-content {
            padding: 12px;
            max-width: 100%;
        }

        /* Class Selection Card */
        .selection-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .selection-header {
            margin-bottom: 16px;
        }

        .selection-title {
            font-size: 15px;
            font-weight: 600;
            color: #1a202c;
            margin: 0 0 3px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .selection-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }

        .form-grid {
            display: grid;
            gap: 12px;
            margin-bottom: 16px;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-label {
            font-size: 12px;
            font-weight: 500;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-select, .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #1a202c;
        }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08);
        }

        .load-btn {
            width: 100%;
            padding: 12px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
        }

        .load-btn:active {
            transform: scale(0.98);
        }

        /* Attendance Section */
        .attendance-section {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .section-header {
            background: #f8fafc;
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: #1a202c;
            margin: 0 0 3px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }

        /* Enhanced Quick Actions */
        .quick-actions {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            background: #fafbfc;
        }

        .quick-actions-title {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quick-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .quick-btn {
            padding: 8px 12px;
            border: 1.5px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
        }

        .quick-btn:active {
            transform: scale(0.95);
        }

        .quick-btn.present-all {
            border-color: #22c55e;
            color: #22c55e;
            background: #f0fdf4;
        }

        .quick-btn.absent-all {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        .quick-btn.late-all {
            border-color: #f59e0b;
            color: #f59e0b;
            background: #fffbeb;
        }

        .quick-btn.half-day-all {
            border-color: #8b5cf6;
            color: #8b5cf6;
            background: #faf5ff;
        }

        .quick-btn.clear-all {
            border-color: #6b7280;
            color: #6b7280;
            background: #f9fafb;
        }

        /* Select All Options */
        .select-all-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }

        .select-all-option {
            padding: 6px 8px;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            background: white;
            color: #64748b;
        }

        .select-all-option:active {
            transform: scale(0.95);
        }

        .select-all-option.present {
            border-color: #22c55e;
            color: #22c55e;
            background: #f0fdf4;
        }

        .select-all-option.absent {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        .select-all-option.late {
            border-color: #f59e0b;
            color: #f59e0b;
            background: #fffbeb;
        }

        .select-all-option.half-day {
            border-color: #8b5cf6;
            color: #8b5cf6;
            background: #faf5ff;
        }

        .select-all-option.clear {
            border-color: #6b7280;
            color: #6b7280;
            background: #f9fafb;
        }

        /* Students List */
        .students-container {
            max-height: 55vh;
            overflow-y: auto;
        }

        .student-item {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .student-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .student-info {
            flex: 1;
        }

        .student-name {
            font-size: 14px;
            font-weight: 600;
            color: #1a202c;
            margin: 0 0 2px 0;
        }

        .student-id {
            font-size: 11px;
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }

        /* Compact Attendance Options */
        .attendance-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-bottom: 12px;
        }

        .attendance-option {
            position: relative;
            cursor: pointer;
        }

        .attendance-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            cursor: pointer;
        }

        .option-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px 6px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            text-align: center;
        }

        .option-icon {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #64748b;
        }

        .option-text {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
        }

        /* Present Option */
        .attendance-option.present input:checked + .option-content {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .attendance-option.present input:checked + .option-content .option-icon {
            background: #22c55e;
            color: white;
        }

        .attendance-option.present input:checked + .option-content .option-text {
            color: #22c55e;
        }

        /* Absent Option */
        .attendance-option.absent input:checked + .option-content {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .attendance-option.absent input:checked + .option-content .option-icon {
            background: #ef4444;
            color: white;
        }

        .attendance-option.absent input:checked + .option-content .option-text {
            color: #ef4444;
        }

        /* Late Option */
        .attendance-option.late input:checked + .option-content {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .attendance-option.late input:checked + .option-content .option-icon {
            background: #f59e0b;
            color: white;
        }

        .attendance-option.late input:checked + .option-content .option-text {
            color: #f59e0b;
        }

        /* Half Day Option */
        .attendance-option.half-day input:checked + .option-content {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .attendance-option.half-day input:checked + .option-content .option-icon {
            background: #8b5cf6;
            color: white;
        }

        .attendance-option.half-day input:checked + .option-content .option-text {
            color: #8b5cf6;
        }

        /* Compact Remarks Input */
        .remarks-input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 12px;
            background: #f8fafc;
            color: #374151;
        }

        .remarks-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
        }

        .remarks-input::placeholder {
            color: #9ca3af;
            font-size: 11px;
        }

        /* Save Button */
        .save-section {
            padding: 16px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .save-btn {
            width: 100%;
            padding: 12px;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
        }

        .save-btn:active {
            transform: scale(0.98);
        }

        /* Loading State */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .loading-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 2px solid #e2e8f0;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 12px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin: 0;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 24px;
            color: #9ca3af;
        }

        .empty-title {
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 6px 0;
        }

        .empty-text {
            font-size: 12px;
            color: #64748b;
            margin: 0;
            line-height: 1.4;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            font-size: 12px;
        }

        .alert-success {
            background: #f0fdf4;
            color: #22c55e;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background: #fffbeb;
            color: #f59e0b;
            border: 1px solid #fed7aa;
        }

        .alert-info {
            background: #eff6ff;
            color: #3b82f6;
            border: 1px solid #bfdbfe;
        }

        /* Student Counter */
        .student-counter {
            background: #f1f5f9;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            text-align: center;
            margin-bottom: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .attendance-content {
                padding: 10px;
            }
            
            .selection-card, .attendance-section {
                border-radius: 10px;
            }
            
            .attendance-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            .option-content {
                padding: 10px 8px;
            }

            .select-all-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include '../include/loader.php'; ?>

    <div class="attendance-app">
        <!-- Header -->
        <div class="attendance-header">
            <div class="header-top">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="header-title">Take Attendance</h1>
            </div>
            <p class="header-subtitle">
                Mark student attendance for today: <?= date('M d, Y') ?>
            </p>
        </div>

        <div class="attendance-content">
            <!-- Message Display -->
            <?php if ($msg): ?>
            <div id="messageContainer">
                <?= $msg ?>
            </div>
            <?php endif; ?>

            <!-- Class Selection -->
            <div class="selection-card">
                <div class="selection-header">
                    <h2 class="selection-title">
                        <i class="fas fa-school"></i>
                        Select Class & Date
                    </h2>
                    <p class="selection-subtitle">Choose class and date to load students</p>
                </div>
                
                <form id="classForm">
                    <div class="form-grid">
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-users"></i>
                                Select Class
                            </label>
                            <select id="class_id" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>">
                                    <?= htmlspecialchars($class['class_name'] . ' - Section ' . $class['section']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-calendar"></i>
                                Date
                            </label>
                            <input type="date" id="date" class="form-input" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="load-btn">
                        <i class="fas fa-search"></i>
                        Load Students
                    </button>
                </form>
            </div>

            <!-- Attendance Form -->
            <div id="attendanceForm" class="attendance-section" style="display: none;">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-check-circle"></i>
                        Mark Attendance
                    </h2>
                    <p class="section-subtitle">Select attendance status for each student</p>
                </div>

                <form method="post" id="markAttendanceForm">
                    <input type="hidden" name="class_id" id="hidden_class_id">
                    <input type="hidden" name="date" id="hidden_date">
                    
                    <!-- Enhanced Quick Actions -->
                    <div class="quick-actions">
                        <div class="quick-actions-title">
                            <i class="fas fa-bolt"></i>
                            Quick Select All
                        </div>
                        <div class="select-all-grid">
                            <div class="select-all-option present" onclick="markAll('present')">
                                <i class="fas fa-check"></i> All Present
                            </div>
                            <div class="select-all-option absent" onclick="markAll('absent')">
                                <i class="fas fa-times"></i> All Absent
                            </div>
                            <div class="select-all-option late" onclick="markAll('late')">
                                <i class="fas fa-clock"></i> All Late
                            </div>
                            <div class="select-all-option half-day" onclick="markAll('half_day')">
                                <i class="fas fa-adjust"></i> All Half Day
                            </div>
                            <div class="select-all-option clear" onclick="clearAll()">
                                <i class="fas fa-undo"></i> Clear All
                            </div>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="students-container">
                        <div id="studentCounter" class="student-counter" style="display: none;">
                            <i class="fas fa-users"></i> <span id="studentCount">0</span> students loaded
                        </div>
                        <div id="studentsList">
                            <!-- Students will be loaded here -->
                        </div>
                    </div>
                    
                    <div class="save-section">
                        <button type="submit" class="save-btn">
                            <i class="fas fa-save"></i>
                            Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="loading-overlay" style="display: none;">
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <p class="loading-text">Loading students...</p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="empty-state" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3 class="empty-title">No Students Found</h3>
            <p class="empty-text">No students are enrolled in the selected class.</p>
        </div>
    </div>

    <!-- Include Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script>
    // Form submission handler
    document.getElementById('classForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const classId = document.getElementById('class_id').value;
        const date = document.getElementById('date').value;
        
        if (!classId || !date) {
            showAlert('Please select class and date', 'warning');
            return;
        }
        
        loadStudents(classId, date);
    });

    // Load students function
    function loadStudents(classId, date) {
        document.getElementById('loadingState').style.display = 'flex';
        document.getElementById('attendanceForm').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
        
        document.getElementById('hidden_class_id').value = classId;
        document.getElementById('hidden_date').value = date;
        
        fetch(`get_students.php?class_id=${classId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingState').style.display = 'none';
                
                if (data.error) {
                    showAlert(data.error, 'danger');
                    return;
                }
                
                if (data.length === 0) {
                    document.getElementById('emptyState').style.display = 'block';
                    return;
                }
                
                renderStudentsList(data);
                document.getElementById('attendanceForm').style.display = 'block';
                
                // Show student counter
                document.getElementById('studentCounter').style.display = 'block';
                document.getElementById('studentCount').textContent = data.length;
            })
            .catch(error => {
                document.getElementById('loadingState').style.display = 'none';
                console.error('Error:', error);
                showAlert('Failed to load students', 'danger');
            });
    }

    // Render students list
    function renderStudentsList(students) {
        const studentsList = document.getElementById('studentsList');
        studentsList.innerHTML = '';
        
        students.forEach((student, index) => {
            const studentItem = document.createElement('div');
            studentItem.className = 'student-item';
            
            studentItem.innerHTML = `
                <div class="student-header">
                    <div class="student-avatar">
                        ${index + 1}
                    </div>
                    <div class="student-info">
                        <h3 class="student-name">${student.first_name} ${student.last_name}</h3>
                        <p class="student-id">ID: ${student.student_id}</p>
                    </div>
                </div>
                
                <div class="attendance-grid">
                    <label class="attendance-option present">
                        <input type="radio" name="attendance[${student.id}]" value="present" 
                            ${student.status === 'present' ? 'checked' : ''} required>
                        <div class="option-content">
                            <div class="option-icon"><i class="fas fa-check"></i></div>
                            <div class="option-text">Present</div>
                        </div>
                    </label>
                    
                    <label class="attendance-option absent">
                        <input type="radio" name="attendance[${student.id}]" value="absent"
                            ${student.status === 'absent' ? 'checked' : ''}>
                        <div class="option-content">
                            <div class="option-icon"><i class="fas fa-times"></i></div>
                            <div class="option-text">Absent</div>
                        </div>
                    </label>
                    
                    <label class="attendance-option late">
                        <input type="radio" name="attendance[${student.id}]" value="late"
                            ${student.status === 'late' ? 'checked' : ''}>
                        <div class="option-content">
                            <div class="option-icon"><i class="fas fa-clock"></i></div>
                            <div class="option-text">Late</div>
                        </div>
                    </label>
                    
                    <label class="attendance-option half-day">
                        <input type="radio" name="attendance[${student.id}]" value="half_day"
                            ${student.status === 'half_day' ? 'checked' : ''}>
                        <div class="option-content">
                            <div class="option-icon"><i class="fas fa-adjust"></i></div>
                            <div class="option-text">Half Day</div>
                        </div>
                    </label>
                </div>
                
                <input type="text" class="remarks-input" name="remarks[${student.id}]" 
                    placeholder="Add remarks (optional)" value="${student.remarks || ''}">
            `;
            
            studentsList.appendChild(studentItem);
        });
    }

    // Enhanced quick action functions
    function markAll(status) {
        const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
        radios.forEach(radio => {
            radio.checked = true;
        });
        
        const statusText = {
            'present': 'Present',
            'absent': 'Absent', 
            'late': 'Late',
            'half_day': 'Half Day'
        };
        
        showAlert(`All students marked as ${statusText[status]}`, 'success');
    }

    function clearAll() {
        const radios = document.querySelectorAll('input[type="radio"]');
        radios.forEach(radio => {
            radio.checked = false;
        });
        showAlert('All selections cleared', 'info');
    }

    // Alert function
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `<i class="fas fa-info-circle"></i>${message}`;
        
        const container = document.getElementById('messageContainer') || document.querySelector('.attendance-content');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Form submission
    document.getElementById('markAttendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('attendance.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            showAlert('Attendance saved successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to save attendance', 'danger');
        });
    });
    </script>
</body>
</html>
