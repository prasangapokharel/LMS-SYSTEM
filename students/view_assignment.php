<?php
include '../include/loader.php';
requireRole('student');

$student = getStudentData($pdo);
$assignment_id = $_GET['id'] ?? 0;

// Get assignment details
$stmt = $pdo->prepare("SELECT a.*, s.subject_name, u.first_name, u.last_name
                      FROM assignments a 
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN users u ON s.teacher_id = u.id
                      WHERE a.id = ?");
$stmt->execute([$assignment_id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    header('Location: assignments.php');
    exit;
}

// Check if student has submitted
$stmt = $pdo->prepare("SELECT * FROM assignment_submissions 
                      WHERE assignment_id = ? AND student_id = ?");
$stmt->execute([$assignment_id, $student['id']]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if graded
$stmt = $pdo->prepare("SELECT * FROM assignment_grades 
                      WHERE assignment_id = ? AND student_id = ?");
$stmt->execute([$assignment_id, $student['id']]);
$grade = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Details - School LMS</title>
    <link href="../vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }
        
        body {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .card-header {
            background-color: var(--secondary-color);
            color: white;
            border-bottom: none;
        }
        
        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .text-primary {
            color: var(--accent-color) !important;
        }
        
        .grade-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color), #2980b9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">School LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="assignments.php">Assignments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attendance.php">Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($user['first_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../include/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="assignments.php">Assignments</a></li>
                <li class="breadcrumb-item active">Assignment Details</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Assignment Details -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?= htmlspecialchars($assignment['title']) ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Subject</h6>
                                <p class="mb-3"><?= htmlspecialchars($assignment['subject_name']) ?></p>
                                
                                <h6 class="text-muted">Teacher</h6>
                                <p class="mb-3"><?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Due Date</h6>
                                <p class="mb-3">
                                    <?= date('l, M j, Y', strtotime($assignment['due_date'])) ?><br>
                                    <small class="text-muted"><?= date('g:i A', strtotime($assignment['due_date'])) ?></small>
                                </p>
                                
                                <h6 class="text-muted">Maximum Marks</h6>
                                <p class="mb-3"><?= $assignment['max_marks'] ?> points</p>
                            </div>
                        </div>
                        
                        <?php if ($assignment['description']): ?>
                            <div class="mb-4">
                                <h6>Description</h6>
                                <div class="p-3 bg-light rounded">
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($assignment['description'])) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($assignment['instructions']): ?>
                            <div class="mb-4">
                                <h6>Instructions</h6>
                                <div class="alert alert-info">
                                    <?= nl2br(htmlspecialchars($assignment['instructions'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Submission Status -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Submission Status</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($submission): ?>
                            <div class="alert alert-success">
                                <h6 class="alert-heading">Assignment Submitted Successfully!</h6>
                                <p class="mb-0">Submitted on <?= date('M j, Y g:i A', strtotime($submission['submission_date'])) ?></p>
                            </div>
                            
                            <?php if ($submission['submission_text']): ?>
                                <div class="mb-3">
                                    <h6>Your Submission:</h6>
                                    <div class="p-3 bg-light rounded">
                                        <?= nl2br(htmlspecialchars($submission['submission_text'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($submission['attachment_url']): ?>
                                <div class="mb-3">
                                    <h6>Attached File:</h6>
                                    <div class="alert alert-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><?= basename($submission['attachment_url']) ?></span>
                                            <a href="../<?= $submission['attachment_url'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">Assignment Not Submitted</h6>
                                <p class="mb-3">
                                    <?php if (strtotime($assignment['due_date']) < time()): ?>
                                        This assignment is overdue.
                                    <?php else: ?>
                                        Due in <?= ceil((strtotime($assignment['due_date']) - time()) / 86400) ?> days.
                                    <?php endif; ?>
                                </p>
                                <a href="submit_assignment.php?id=<?= $assignment['id'] ?>" class="btn btn-primary">
                                    Submit Assignment
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Grade (if available) -->
                <?php if ($grade): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Grade</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="grade-circle mb-3">
                                <div>
                                    <div class="h2 mb-0"><?= number_format($grade['grade'], 1) ?></div>
                                    <small>/ <?= number_format($grade['max_grade'], 0) ?></small>
                                </div>
                            </div>
                            <h4 class="text-primary"><?= round(($grade['grade'] / $grade['max_grade']) * 100, 1) ?>%</h4>
                            <p class="text-muted mb-0">Graded on <?= date('M j, Y', strtotime($grade['graded_at'])) ?></p>
                            
                            <?php if ($grade['feedback']): ?>
                                <div class="mt-3">
                                    <h6>Teacher Feedback:</h6>
                                    <div class="alert alert-info text-start">
                                        <?= nl2br(htmlspecialchars($grade['feedback'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Assignment Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Assignment Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-1">Status</h6>
                                    <span class="badge 
                                        <?php if ($grade): ?>
                                            bg-success
                                        <?php elseif ($submission): ?>
                                            bg-info
                                        <?php elseif (strtotime($assignment['due_date']) < time()): ?>
                                            bg-danger
                                        <?php else: ?>
                                            bg-warning
                                        <?php endif; ?>">
                                        <?php
                                        if ($grade) echo 'Graded';
                                        elseif ($submission) echo 'Submitted';
                                        elseif (strtotime($assignment['due_date']) < time()) echo 'Overdue';
                                        else echo 'Pending';
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-1">Created</h6>
                                    <small><?= date('M j, Y', strtotime($assignment['created_at'])) ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-1">Due</h6>
                                    <small><?= date('M j, Y', strtotime($assignment['due_date'])) ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 d-grid">
                            <a href="assignments.php" class="btn btn-outline-primary">
                                Back to Assignments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <?php include '../include/bootoomnav.php'; ?>

    <script src="../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
