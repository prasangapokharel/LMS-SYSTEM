<?php
include '../App/Models/teacher/Attendance.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Student Details</h2>
            <a href="students.php" class="btn btn-secondary">Back to Students</a>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile Picture">
                        <h4><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h4>
                        <p class="text-muted">Student ID: <?= htmlspecialchars($student['student_id']) ?></p>
                        <p class="text-muted">Class: <?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?></p>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone'] ?? 'N/A') ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($student['address'] ?? 'N/A') ?></p>
                        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['date_of_birth'] ?? 'N/A') ?></p>
                        <p><strong>Blood Group:</strong> <?= htmlspecialchars($student['blood_group'] ?? 'N/A') ?></p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Guardian Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($student['guardian_name'] ?? 'N/A') ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($student['guardian_phone'] ?? 'N/A') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($student['guardian_email'] ?? 'N/A') ?></p>
                        <p><strong>Emergency Contact:</strong> <?= htmlspecialchars($student['emergency_contact'] ?? 'N/A') ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="attendance-tab" data-bs-toggle="tab" href="#attendance" role="tab">Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="assignments-tab" data-bs-toggle="tab" href="#assignments" role="tab">Assignments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="leaves-tab" data-bs-toggle="tab" href="#leaves" role="tab">Leave Applications</a>
                    </li>
                </ul>
                
                <div class="tab-content p-3 border border-top-0 rounded-bottom">
                    <div class="tab-pane fade show active" id="attendance" role="tabpanel">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Present</h5>
                                        <p class="display-4"><?= $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100) : 0 ?>%</p>
                                        <p><?= $stats['present'] ?> days</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Absent</h5>
                                        <p class="display-4"><?= $stats['total'] > 0 ? round(($stats['absent'] / $stats['total']) * 100) : 0 ?>%</p>
                                        <p><?= $stats['absent'] ?> days</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Late</h5>
                                        <p class="display-4"><?= $stats['total'] > 0 ? round(($stats['late'] / $stats['total']) * 100) : 0 ?>%</p>
                                        <p><?= $stats['late'] ?> days</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total</h5>
                                        <p class="display-4"><?= $stats['total'] ?></p>
                                        <p>days recorded</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_records as $record): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($record['attendance_date']) ?></td>
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
                                        <td colspan="4" class="text-center">No attendance records found.</td>
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
                                        <th>Assignment</th>
                                        <th>Subject</th>
                                        <th>Submission Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($submissions as $submission): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($submission['assignment_title']) ?></td>
                                        <td><?= htmlspecialchars($submission['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($submission['submission_date']) ?></td>
                                        <td><?= htmlspecialchars($submission['due_date']) ?></td>
                                        <td>
                                            <?php if ($submission['status'] == 'graded'): ?>
                                                <span class="badge bg-success">Graded</span>
                                            <?php elseif ($submission['status'] == 'late'): ?>
                                                <span class="badge bg-warning text-dark">Late</span>
                                            <?php elseif ($submission['status'] == 'returned'): ?>
                                                <span class="badge bg-info">Returned</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Submitted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($submission['grade']): ?>
                                                <?= $submission['grade'] ?> / <?= $submission['max_marks'] ?>
                                                (<?= round(($submission['grade'] / $submission['max_marks']) * 100) ?>%)
                                            <?php else: ?>
                                                Not graded
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($submissions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No assignment submissions found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="leaves" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Days</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Applied On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leave_applications as $leave): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(ucfirst($leave['leave_type'])) ?></td>
                                        <td><?= htmlspecialchars($leave['from_date']) ?></td>
                                        <td><?= htmlspecialchars($leave['to_date']) ?></td>
                                        <td><?= htmlspecialchars($leave['total_days']) ?></td>
                                        <td><?= htmlspecialchars($leave['reason']) ?></td>
                                        <td>
                                            <?php if ($leave['status'] == 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($leave['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php elseif ($leave['status'] == 'cancelled'): ?>
                                                <span class="badge bg-secondary">Cancelled</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($leave['applied_date']))) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($leave_applications)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No leave applications found.</td>
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
