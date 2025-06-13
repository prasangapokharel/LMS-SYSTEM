<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);

// Get teacher's classes
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section
                      FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get class filter
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : '';

// Get students based on filter
if ($class_id) {
    $stmt = $pdo->prepare("SELECT s.*, u.first_name, u.last_name, u.email, u.phone,
                          se.status as enrollment_status,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ? AND a.status = 'present') as present_count,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ?) as total_attendance
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          JOIN student_enrollments se ON s.id = se.student_id
                          WHERE se.class_id = ? AND se.status = 'enrolled'
                          ORDER BY u.first_name, u.last_name");
    $stmt->execute([$user['id'], $user['id'], $class_id]);
} else {
    $stmt = $pdo->prepare("SELECT s.*, u.first_name, u.last_name, u.email, u.phone,
                          c.class_name, c.section,
                          se.status as enrollment_status,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ? AND a.status = 'present') as present_count,
                          (SELECT COUNT(*) FROM attendance a 
                           WHERE a.student_id = s.id AND a.teacher_id = ?) as total_attendance
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          JOIN student_enrollments se ON s.id = se.student_id
                          JOIN classes c ON se.class_id = c.id
                          JOIN class_subject_teachers cst ON c.id = cst.class_id
                          WHERE cst.teacher_id = ? AND cst.is_active = 1 AND se.status = 'enrolled'
                          GROUP BY s.id
                          ORDER BY c.class_name, c.section, u.first_name, u.last_name");
    $stmt->execute([$user['id'], $user['id'], $user['id']]);
}

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="container mt-4">
        <h2>My Students</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Filter by Class</label>
                        <select name="class_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class['class_name'] . ' ' . $class['section']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <?php if ($class_id): ?>
                        <a href="students.php" class="btn btn-secondary">Clear Filter</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Student List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <?php if (!$class_id): ?>
                                <th>Class</th>
                                <?php endif; ?>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Attendance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['student_id']) ?></td>
                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <?php if (!$class_id): ?>
                                <td><?= htmlspecialchars($student['class_name'] . ' ' . $student['section']) ?></td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                                <td><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></td>
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
                                <td colspan="<?= $class_id ? '6' : '7' ?>" class="text-center">No students found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
