<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$class_id = $_GET['id'];

// Verify that this class is taught by the current teacher
$stmt = $pdo->prepare("SELECT COUNT(*) FROM class_subject_teachers 
                      WHERE class_id = ? AND teacher_id = ? AND is_active = 1");
$stmt->execute([$class_id, $user['id']]);
if ($stmt->fetchColumn() == 0) {
    header('Location: index.php');
    exit;
}

// Get class details
$stmt = $pdo->prepare("SELECT c.*, ay.year_name 
                      FROM classes c
                      JOIN academic_years ay ON c.academic_year_id = ay.id
                      WHERE c.id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

// Get subjects taught by this teacher in this class
$stmt = $pdo->prepare("SELECT s.*, cst.id as assignment_id 
                      FROM subjects s
                      JOIN class_subject_teachers cst ON s.id = cst.subject_id
                      WHERE cst.class_id = ? AND cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$class_id, $user['id']]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get students in this class
$stmt = $pdo->prepare("SELECT s.*, u.first_name, u.last_name, u.email, u.phone,
                      (SELECT COUNT(*) FROM attendance a 
                       WHERE a.student_id = s.id AND a.class_id = ? AND a.teacher_id = ? AND a.status = 'present') as present_count,
                      (SELECT COUNT(*) FROM attendance a 
                       WHERE a.student_id = s.id AND a.class_id = ? AND a.teacher_id = ?) as total_attendance
                      FROM students s
                      JOIN users u ON s.user_id = u.id
                      JOIN student_enrollments se ON s.id = se.student_id
                      WHERE se.class_id = ? AND se.status = 'enrolled'
                      ORDER BY u.first_name, u.last_name");
$stmt->execute([$class_id, $user['id'], $class_id, $user['id'], $class_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent attendance records
$stmt = $pdo->prepare("SELECT a.*, s.subject_name, u.first_name, u.last_name
                      FROM attendance a
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN students st ON a.student_id = st.id
                      JOIN users u ON st.user_id = u.id
                      WHERE a.class_id = ? AND a.teacher_id = ?
                      ORDER BY a.attendance_date DESC, u.first_name, u.last_name
                      LIMIT 50");
$stmt->execute([$class_id, $user['id']]);
$attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent assignments
$stmt = $pdo->prepare("SELECT a.*, s.subject_name,
                      (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id) as submissions
                      FROM assignments a
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE a.class_id = ? AND a.teacher_id = ?
                      ORDER BY a.assigned_date DESC
                      LIMIT 10");
$stmt->execute([$class_id, $user['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Class: <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?></h2>
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Academic Year</h5>
                        <p class="display-6"><?= htmlspecialchars($class['year_name']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Students</h5>
                        <p class="display-6"><?= count($students) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Subjects</h5>
                        <p class="display-6"><?= count($subjects) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>My Subjects</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($subjects as $subject): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($subject['subject_name']) ?>
                                <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($subject['subject_code']) ?></span>
                            </li>
                            <?php endforeach; ?>
                            <?php if (empty($subjects)): ?>
                            <li class="list-group-item">No subjects assigned.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="attendance.php?class_id=<?= $class_id ?>" class="btn btn-outline-primary">Take Attendance</a>
                            <a href="assignments.php?class_id=<?= $class_id ?>" class="btn btn-outline-success">Manage Assignments</a>
                            <a href="students.php?class_id=<?= $class_id ?>" class="btn btn-outline-info">View Students</a>
                            <a href="teacher_log.php?class_id=<?= $class_id ?>" class="btn btn-outline-secondary">Add Teacher Log</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <ul class="nav nav-tabs" id="classTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="students-tab" data-bs-toggle="tab" href="#students" role="tab">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="attendance-tab" data-bs-toggle="tab" href="#attendance" role="tab">Recent Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="assignments-tab" data-bs-toggle="tab" href="#assignments" role="tab">Recent Assignments</a>
                    </li>
                </ul>
                
                <div class="tab-content p-3 border border-top-0 rounded-bottom">
                    <div class="tab-pane fade show active" id="students" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Attendance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                        <td><?= htmlspecialchars($student['email']) ?></td>
                                        <td>
                                            <?php 
                                            $attendance_percentage = $student['total_attendance'] > 0 ? 
                                                round(($student['present_count'] / $student['total_attendance']) * 100) : 0;
                                            
                                            if ($attendance_percentage >= 75) {
                                                $badge_class = 'bg-success';
                                            } elseif ($attendance_percentage >= 50) {
                                                $badge_class = 'bg-warning text-dark';
                                            } else {
                                                $badge_class = 'bg-danger';
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>"><?= $attendance_percentage ?>%</span>
                                        </td>
                                        <td>
                                            <a href="student_details.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No students found in this class.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="attendance" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_records as $record): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($record['attendance_date']) ?></td>
                                        <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                                        <td><?= htmlspecialchars($record['subject_name']) ?></td>
                                        <td>
                                            <?php if ($record['status'] == 'present'): ?>
                                                <span class="badge bg-success">Present</span>
                                            <?php elseif ($record['status'] == 'absent'): ?>
                                                <span class="badge bg-danger">Absent</span>
                                            <?php elseif ($record['status'] == 'late'): ?>
                                                <span class="badge bg-warning text-dark">Late</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Half Day</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($record['remarks'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($attendance_records)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No attendance records found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="assignments" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Subject</th>
                                        <th>Assigned Date</th>
                                        <th>Due Date</th>
                                        <th>Submissions</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($assignment['title']) ?></td>
                                        <td><?= htmlspecialchars($assignment['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($assignment['assigned_date']) ?></td>
                                        <td><?= htmlspecialchars($assignment['due_date']) ?></td>
                                        <td><?= $assignment['submissions'] ?></td>
                                        <td>
                                            <a href="view_submissions.php?id=<?= $assignment['id'] ?>" class="btn btn-sm btn-primary">View Submissions</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($assignments)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No assignments found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
