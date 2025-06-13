<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher's classes
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $date = $_POST['date'];
    $attendance = $_POST['attendance'];
    $remarks = $_POST['remarks'] ?? [];
    
    // Check if attendance already exists for this date and class
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance 
                          WHERE class_id = ? AND attendance_date = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $date, $user['id']]);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        // Delete existing attendance records
        $stmt = $pdo->prepare("DELETE FROM attendance 
                              WHERE class_id = ? AND attendance_date = ? AND teacher_id = ?");
        $stmt->execute([$class_id, $date, $user['id']]);
    }
    
    // Insert new attendance records
    $stmt = $pdo->prepare("INSERT INTO attendance 
                          (student_id, class_id, teacher_id, attendance_date, status, remarks) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($attendance as $student_id => $status) {
        $student_remarks = isset($remarks[$student_id]) ? $remarks[$student_id] : '';
        $stmt->execute([$student_id, $class_id, $user['id'], $date, $status, $student_remarks]);
    }
    
    logActivity($pdo, 'attendance_recorded', 'attendance', null, null, ['class_id' => $class_id, 'date' => $date]);
    $msg = "<div class='alert alert-success'>Attendance recorded successfully.</div>";
}

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="container mt-4">
        <h2>Take Attendance</h2>
        <?= $msg ?>
        
        <div class="card">
            <div class="card-body">
                <form id="classForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Class</label>
                            <select id="class_id" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>">
                                    <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Load Students</button>
                </form>
            </div>
        </div>
        
        <div id="attendanceForm" class="mt-4" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h5>Mark Attendance</h5>
                </div>
                <div class="card-body">
                    <form method="post" id="markAttendanceForm">
                        <input type="hidden" name="class_id" id="hidden_class_id">
                        <input type="hidden" name="date" id="hidden_date">
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Attendance</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="studentList">
                                <!-- Students will be loaded here -->
                            </tbody>
                        </table>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Save Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('classForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const classId = document.getElementById('class_id').value;
    const date = document.getElementById('date').value;
    
    if (!classId || !date) {
        alert('Please select class and date');
        return;
    }
    
    // Set hidden fields
    document.getElementById('hidden_class_id').value = classId;
    document.getElementById('hidden_date').value = date;
    
    // Fetch students for this class
    fetch(`get_students.php?class_id=${classId}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            const studentList = document.getElementById('studentList');
            studentList.innerHTML = '';
            
            data.forEach(student => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${student.student_id}</td>
                    <td>${student.first_name} ${student.last_name}</td>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="attendance[${student.id}]" 
                                id="present_${student.id}" value="present" 
                                ${student.status === 'present' ? 'checked' : ''} required>
                            <label class="form-check-label" for="present_${student.id}">Present</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="attendance[${student.id}]" 
                                id="absent_${student.id}" value="absent"
                                ${student.status === 'absent' ? 'checked' : ''}>
                            <label class="form-check-label" for="absent_${student.id}">Absent</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="attendance[${student.id}]" 
                                id="late_${student.id}" value="late"
                                ${student.status === 'late' ? 'checked' : ''}>
                            <label class="form-check-label" for="late_${student.id}">Late</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="attendance[${student.id}]" 
                                id="half_day_${student.id}" value="half_day"
                                ${student.status === 'half_day' ? 'checked' : ''}>
                            <label class="form-check-label" for="half_day_${student.id}">Half Day</label>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="remarks[${student.id}]" 
                            value="${student.remarks || ''}">
                    </td>
                `;
                
                studentList.appendChild(row);
            });
            
            document.getElementById('attendanceForm').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load students');
        });
});
</script>
</body>
</html>
