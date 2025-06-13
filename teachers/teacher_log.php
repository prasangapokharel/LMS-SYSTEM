<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get class_id from URL parameter
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// Validate that this teacher has access to this class
$stmt = $pdo->prepare("SELECT c.* FROM classes c
                      JOIN class_subject_teachers cst ON c.id = cst.class_id
                      WHERE cst.teacher_id = ? AND c.id = ? AND cst.is_active = 1
                      LIMIT 1");
$stmt->execute([$user['id'], $class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class && $class_id > 0) {
    // Redirect to teacher's classes page if they don't have access to this class
    header("Location: index.php");
    exit;
}

// Get teacher's classes if no specific class is selected
if ($class_id == 0) {
    $stmt = $pdo->prepare("SELECT DISTINCT c.id, c.class_name, c.section
                          FROM classes c
                          JOIN class_subject_teachers cst ON c.id = cst.class_id
                          WHERE cst.teacher_id = ? AND cst.is_active = 1
                          ORDER BY c.class_level, c.class_name, c.section");
    $stmt->execute([$user['id']]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($classes) == 1) {
        // If teacher has only one class, redirect to that class's log page
        header("Location: teacher_log.php?class_id=" . $classes[0]['id']);
        exit;
    }
}

// Get subjects for selected class
$subjects = [];
if ($class_id > 0) {
    $stmt = $pdo->prepare("SELECT s.* 
                          FROM subjects s
                          JOIN class_subject_teachers cst ON s.id = cst.subject_id
                          WHERE cst.class_id = ? AND cst.teacher_id = ? AND cst.is_active = 1
                          ORDER BY s.subject_name");
    $stmt->execute([$class_id, $user['id']]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get subject filter
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_log') {
    $subject_id = $_POST['subject_id'];
    $log_date = $_POST['log_date'];
    $chapter_title = $_POST['chapter_title'];
    $chapter_content = $_POST['chapter_content'];
    $topics_covered = $_POST['topics_covered'];
    $teaching_method = $_POST['teaching_method'];
    $homework_assigned = $_POST['homework_assigned'];
    $notes = $_POST['notes'];
    $lesson_duration = $_POST['lesson_duration'];
    
    // Validate inputs
    if (empty($subject_id) || empty($log_date) || empty($chapter_title) || empty($topics_covered)) {
        $msg = "<div class='alert alert-danger alert-dismissible fade show'>
                    <strong>Error!</strong> Please fill in all required fields.
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    } else {
        // Get students present count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance 
                              WHERE class_id = ? AND teacher_id = ? AND subject_id = ? 
                              AND attendance_date = ? AND status = 'present'");
        $stmt->execute([$class_id, $user['id'], $subject_id, $log_date]);
        $students_present = $stmt->fetchColumn();
        
        // Check if log already exists
        $stmt = $pdo->prepare("SELECT id FROM teacher_logs 
                              WHERE teacher_id = ? AND class_id = ? AND subject_id = ? AND log_date = ?");
        $stmt->execute([$user['id'], $class_id, $subject_id, $log_date]);
        $existing_log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        try {
            if ($existing_log) {
                // Update existing log
                $stmt = $pdo->prepare("UPDATE teacher_logs SET 
                                      chapter_title = ?, chapter_content = ?, topics_covered = ?,
                                      teaching_method = ?, homework_assigned = ?, notes = ?,
                                      lesson_duration = ?, students_present = ?, updated_at = NOW()
                                      WHERE id = ?");
                $stmt->execute([
                    $chapter_title, $chapter_content, $topics_covered,
                    $teaching_method, $homework_assigned, $notes,
                    $lesson_duration, $students_present, $existing_log['id']
                ]);
                $msg = "<div class='alert alert-success alert-dismissible fade show'>
                            <strong>Success!</strong> Teacher log updated successfully.
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>";
                
                // Log activity
                logActivity($pdo, 'update_teacher_log', 'teacher_logs', $existing_log['id']);
            } else {
                // Insert new log
                $stmt = $pdo->prepare("INSERT INTO teacher_logs 
                                      (teacher_id, class_id, subject_id, log_date, chapter_title, 
                                      chapter_content, topics_covered, teaching_method, homework_assigned, 
                                      notes, lesson_duration, students_present)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user['id'], $class_id, $subject_id, $log_date, $chapter_title,
                    $chapter_content, $topics_covered, $teaching_method, $homework_assigned,
                    $notes, $lesson_duration, $students_present
                ]);
                
                $log_id = $pdo->lastInsertId();
                $msg = "<div class='alert alert-success alert-dismissible fade show'>
                            <strong>Success!</strong> Teacher log added successfully.
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>";
                
                // Log activity
                logActivity($pdo, 'create_teacher_log', 'teacher_logs', $log_id);
            }
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger alert-dismissible fade show'>
                        <strong>Error!</strong> " . htmlspecialchars($e->getMessage()) . "
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
        }
    }
}

// Get recent logs
$recent_logs = [];
if ($class_id > 0) {
    $where_clause = "tl.teacher_id = ? AND tl.class_id = ?";
    $params = [$user['id'], $class_id];
    
    if ($subject_id > 0) {
        $where_clause .= " AND tl.subject_id = ?";
        $params[] = $subject_id;
    }
    
    $stmt = $pdo->prepare("SELECT tl.*, c.class_name, c.section, s.subject_name
                          FROM teacher_logs tl
                          JOIN classes c ON tl.class_id = c.id
                          JOIN subjects s ON tl.subject_id = s.id
                          WHERE $where_clause
                          ORDER BY tl.log_date DESC
                          LIMIT 20");
    $stmt->execute($params);
    $recent_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get class details for header
$class_details = "";
if ($class) {
    $class_details = htmlspecialchars($class['class_name'] . ' ' . $class['section']);
}

// Get log for editing if edit_id is provided
$edit_log = null;
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM teacher_logs WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$edit_id, $user['id']]);
    $edit_log = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$edit_log) {
        header("Location: teacher_log.php?class_id=$class_id");
        exit;
    }
}

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Log<?= $class_details ? " - $class_details" : "" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
        
        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6ecc 0%, #6a4494 100%);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: var(--success-gradient);
            border: none;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #0e8a7f 0%, #2dd66a 100%);
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: var(--info-gradient);
            border: none;
            color: white;
        }
        
        .btn-info:hover {
            background: linear-gradient(135deg, #3d99e8 0%, #00d8e4 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .table thead {
            background: var(--primary-gradient);
            color: white;
        }
        
        .table thead th {
            font-weight: 600;
            border: none;
            padding: 1rem;
        }
        
        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .badge-subject {
            background: var(--info-gradient);
            color: white;
        }
        
        .badge-date {
            background: var(--warning-gradient);
            color: white;
        }
        
        .class-selector {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .class-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        
        .class-card:hover {
            transform: translateY(-5px);
        }
        
        .class-card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .class-card-body {
            padding: 1.5rem;
            text-align: center;
        }
        
        .class-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .class-section {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }
        
        .log-date {
            font-weight: 600;
            color: #6c757d;
        }
        
        .chapter-title {
            font-weight: 700;
            color: #343a40;
            margin-bottom: 0.25rem;
        }
        
        .log-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .log-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }
        
        .empty-state-text {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-book-open me-2"></i>
                    Teacher Log
                </h1>
                <?php if ($class_details): ?>
                <p class="page-subtitle">
                    <i class="fas fa-chalkboard me-2"></i>
                    <?= $class_details ?>
                </p>
                <?php endif; ?>
            </div>
            
            <!-- Alert Messages -->
            <?= $msg ?>
            
            <?php if ($class_id == 0): ?>
            <!-- Class Selection -->
            <div class="class-selector">
                <h4 class="mb-4">Select a Class to Manage Logs</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($classes as $cls): ?>
                    <div class="col">
                        <a href="teacher_log.php?class_id=<?= $cls['id'] ?>" class="text-decoration-none">
                            <div class="class-card">
                                <div class="class-card-header">
                                    <div class="class-name"><?= htmlspecialchars($cls['class_name']) ?></div>
                                    <div class="class-section">Section <?= htmlspecialchars($cls['section']) ?></div>
                                </div>
                                <div class="class-card-body">
                                    <p class="mb-0">Click to manage logs</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($classes)): ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div class="empty-state-text">
                                You don't have any classes assigned yet.
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            
            <div class="row">
                <!-- Log Form -->
                <div class="col-lg-5 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?= $edit_log ? 'Edit Log' : 'Add New Log' ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="action" value="save_log">
                                
                                <div class="mb-3">
                                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="form-select" required>
                                        <option value="">-- Select Subject --</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>" <?= ($subject_id == $subject['id'] || ($edit_log && $edit_log['subject_id'] == $subject['id'])) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subject['subject_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="log_date" class="form-control" value="<?= $edit_log ? $edit_log['log_date'] : date('Y-m-d') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Chapter Title <span class="text-danger">*</span></label>
                                    <input type="text" name="chapter_title" class="form-control" value="<?= $edit_log ? htmlspecialchars($edit_log['chapter_title']) : '' ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Chapter Content</label>
                                    <textarea name="chapter_content" class="form-control" rows="3"><?= $edit_log ? htmlspecialchars($edit_log['chapter_content']) : '' ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Topics Covered <span class="text-danger">*</span></label>
                                    <textarea name="topics_covered" class="form-control" rows="3" required><?= $edit_log ? htmlspecialchars($edit_log['topics_covered']) : '' ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Teaching Method</label>
                                    <select name="teaching_method" class="form-select">
                                        <option value="Lecture" <?= ($edit_log && $edit_log['teaching_method'] == 'Lecture') ? 'selected' : '' ?>>Lecture</option>
                                        <option value="Discussion" <?= ($edit_log && $edit_log['teaching_method'] == 'Discussion') ? 'selected' : '' ?>>Discussion</option>
                                        <option value="Group Work" <?= ($edit_log && $edit_log['teaching_method'] == 'Group Work') ? 'selected' : '' ?>>Group Work</option>
                                        <option value="Practical" <?= ($edit_log && $edit_log['teaching_method'] == 'Practical') ? 'selected' : '' ?>>Practical</option>
                                        <option value="Demonstration" <?= ($edit_log && $edit_log['teaching_method'] == 'Demonstration') ? 'selected' : '' ?>>Demonstration</option>
                                        <option value="Project" <?= ($edit_log && $edit_log['teaching_method'] == 'Project') ? 'selected' : '' ?>>Project</option>
                                        <option value="Other" <?= ($edit_log && $edit_log['teaching_method'] == 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Homework Assigned</label>
                                    <textarea name="homework_assigned" class="form-control" rows="2"><?= $edit_log ? htmlspecialchars($edit_log['homework_assigned']) : '' ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"><?= $edit_log ? htmlspecialchars($edit_log['notes']) : '' ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Lesson Duration (minutes)</label>
                                    <input type="number" name="lesson_duration" class="form-control" value="<?= $edit_log ? $edit_log['lesson_duration'] : '45' ?>" required>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <?= $edit_log ? 'Update Log' : 'Save Log' ?>
                                    </button>
                                    
                                    <?php if ($edit_log): ?>
                                    <a href="teacher_log.php?class_id=<?= $class_id ?>" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        Cancel
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Logs -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Logs</h5>
                            
                            <?php if (count($subjects) > 1): ?>
                            <div>
                                <form method="get" class="d-flex">
                                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                                    <select name="subject_id" class="form-select form-select-sm me-2" style="width: auto;">
                                        <option value="0">All Subjects</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>" <?= ($subject_id == $subject['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subject['subject_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_logs)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="empty-state-text">
                                    No logs found for this class.
                                </div>
                                <p class="text-muted">
                                    Start by adding a new log using the form.
                                </p>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Subject</th>
                                            <th>Chapter</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_logs as $log): ?>
                                        <tr>
                                            <td>
                                                <span class="log-date">
                                                    <?= date('M d, Y', strtotime($log['log_date'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-subject">
                                                    <?= htmlspecialchars($log['subject_name']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="chapter-title">
                                                    <?= htmlspecialchars($log['chapter_title']) ?>
                                                </div>
                                                <div class="log-meta">
                                                    <?= $log['teaching_method'] ?> • <?= $log['lesson_duration'] ?> mins
                                                </div>
                                            </td>
                                            <td>
                                                <div class="log-actions">
                                                    <a href="view_log.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="teacher_log.php?class_id=<?= $class_id ?>&edit_id=<?= $log['id'] ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal<?= $log['id'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?= $log['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this log entry?</p>
                                                                <p><strong>Date:</strong> <?= date('M d, Y', strtotime($log['log_date'])) ?></p>
                                                                <p><strong>Chapter:</strong> <?= htmlspecialchars($log['chapter_title']) ?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <a href="delete_log.php?id=<?= $log['id'] ?>&class_id=<?= $class_id ?>" class="btn btn-danger">Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
