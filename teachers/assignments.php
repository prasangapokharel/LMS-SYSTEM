<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Handle form submission for new assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $subject_id = $_POST['subject_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $max_marks = $_POST['max_marks'];
    $assignment_type = $_POST['assignment_type'];
    $instructions = $_POST['instructions'];
    
    // Validate due date is in the future
    if (strtotime($due_date) <= time()) {
        $msg = "<div class='alert alert-danger'>Due date must be in the future.</div>";
    } else {
        try {
            // Handle file upload
            $attachment_url = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $upload_dir = '../uploads/assignments/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $filename = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                        $attachment_url = 'uploads/assignments/' . $filename;
                    }
                }
            }
            
            // Get class_id from subject
            $stmt = $pdo->prepare("SELECT cst.class_id FROM class_subject_teachers cst WHERE cst.subject_id = ? AND cst.teacher_id = ?");
            $stmt->execute([$subject_id, $user['id']]);
            $class_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$class_info) {
                throw new Exception("Invalid subject selection.");
            }
            
            $stmt = $pdo->prepare("INSERT INTO assignments 
                                  (title, description, class_id, subject_id, teacher_id, due_date, max_marks, assignment_type, instructions, attachment_url, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $description, $class_info['class_id'], $subject_id, $user['id'], $due_date, $max_marks, $assignment_type, $instructions, $attachment_url]);
            
            $assignment_id = $pdo->lastInsertId();
            logActivity($pdo, 'assignment_created', 'assignments', $assignment_id);
            
            $msg = "<div class='alert alert-success alert-modern'>
                    <div class='alert-icon'>✅</div>
                    <div><strong>Assignment created successfully!</strong></div>
                   </div>";
        } catch (Exception $e) {
            $msg = "<div class='alert alert-danger alert-modern'>
                    <div class='alert-icon'>❌</div>
                    <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
                   </div>";
        }
    }
}

// Handle assignment deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_assignment'])) {
    $assignment_id = $_POST['assignment_id'];
    
    try {
        // Check if assignment belongs to this teacher
        $stmt = $pdo->prepare("SELECT id FROM assignments WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$assignment_id, $user['id']]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM assignments WHERE id = ? AND teacher_id = ?");
            $stmt->execute([$assignment_id, $user['id']]);
            
            logActivity($pdo, 'assignment_deleted', 'assignments', $assignment_id);
            $msg = "<div class='alert alert-success alert-modern'>
                    <div class='alert-icon'>✅</div>
                    <div><strong>Assignment deleted successfully!</strong></div>
                   </div>";
        } else {
            $msg = "<div class='alert alert-danger alert-modern'>
                    <div class='alert-icon'>❌</div>
                    <div><strong>Error:</strong> Assignment not found or access denied.</div>
                   </div>";
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>❌</div>
                <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
               </div>";
    }
}

// Get teacher's subjects with class information
$stmt = $pdo->prepare("SELECT s.id, s.subject_name, c.class_name, cst.class_id
                      FROM class_subject_teachers cst
                      JOIN subjects s ON cst.subject_id = s.id
                      JOIN classes c ON cst.class_id = c.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get teacher's assignments with submission counts
$stmt = $pdo->prepare("SELECT a.*, s.subject_name, c.class_name,
                      (SELECT COUNT(*) FROM assignment_submissions asub WHERE asub.assignment_id = a.id) as submissions,
                      (SELECT COUNT(DISTINCT sc.student_id) FROM student_classes sc WHERE sc.class_id = a.class_id AND sc.status = 'enrolled') as total_students
                      FROM assignments a
                      JOIN subjects s ON a.subject_id = s.id
                      JOIN classes c ON a.class_id = c.id
                      WHERE a.teacher_id = ?
                      ORDER BY a.created_at DESC");
$stmt->execute([$user['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assignments - School LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
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
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-modern {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 600;
        }

        .form-control-modern {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-modern {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-modern {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-success-modern {
            background: var(--success-gradient);
            color: white;
        }

        .btn-warning-modern {
            background: var(--warning-gradient);
            color: white;
        }

        .btn-danger-modern {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-info-modern {
            background: var(--info-gradient);
            color: white;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .table-modern {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .table-modern thead {
            background: var(--primary-gradient);
            color: white;
        }

        .table-modern th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table-modern td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .table-modern tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge-type-homework {
            background: var(--info-gradient);
            color: white;
        }

        .badge-type-project {
            background: var(--warning-gradient);
            color: white;
        }

        .badge-type-quiz {
            background: var(--success-gradient);
            color: white;
        }

        .badge-type-exam {
            background: var(--danger-gradient);
            color: white;
        }

        .alert-modern {
            border-radius: 16px;
            border: none;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .alert-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .assignment-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.3s ease;
        }

        .assignment-card:hover {
            transform: translateY(-2px);
        }

        .assignment-card.homework { border-left-color: #4facfe; }
        .assignment-card.project { border-left-color: #f093fb; }
        .assignment-card.quiz { border-left-color: #11998e; }
        .assignment-card.exam { border-left-color: #ff6b6b; }

        .assignment-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .meta-item {
            background: rgba(255, 255, 255, 0.5);
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
        }

        .meta-label {
            font-size: 0.875rem;
            color: #718096;
            font-weight: 500;
        }

        .meta-value {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .progress-modern {
            height: 8px;
            border-radius: 10px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .progress-bar-modern {
            height: 100%;
            border-radius: 10px;
            background: var(--success-gradient);
            transition: width 0.3s ease;
        }

        .file-upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .file-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .assignment-meta {
                grid-template-columns: 1fr;
            }
            
            .btn-modern {
                justify-content: center;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-tasks me-3"></i>
                    Manage Assignments
                </h1>
                <p class="page-subtitle">Create, manage, and track assignments for your classes</p>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <div class="row">
                <!-- Create Assignment Form -->
                <div class="col-lg-4">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5>
                                <i class="fas fa-plus-circle me-2"></i>
                                Create New Assignment
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Subject & Class</label>
                                    <select name="subject_id" class="form-select form-control-modern" required>
                                        <option value="">-- Select Subject --</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>">
                                            <?= htmlspecialchars($subject['subject_name']) ?> 
                                            (<?= htmlspecialchars($subject['class_name']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assignment Type</label>
                                    <select name="assignment_type" class="form-select form-control-modern" required>
                                        <option value="homework">Homework</option>
                                        <option value="project">Project</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="exam">Exam</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control form-control-modern" required placeholder="Enter assignment title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control form-control-modern" rows="3" required placeholder="Describe the assignment"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Instructions</label>
                                    <textarea name="instructions" class="form-control form-control-modern" rows="2" placeholder="Additional instructions for students"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" name="due_date" class="form-control form-control-modern" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Maximum Marks</label>
                                        <input type="number" name="max_marks" class="form-control form-control-modern" required min="1" max="1000" value="100">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Attachment (Optional)</label>
                                    <div class="file-upload-area" onclick="document.getElementById('attachment').click()">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Click to upload or drag and drop</p>
                                        <small class="text-muted">PDF, DOC, DOCX, TXT, Images (Max 10MB)</small>
                                    </div>
                                    <input type="file" id="attachment" name="attachment" class="d-none" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                                </div>

                                <button type="submit" class="btn btn-primary-modern btn-modern w-100">
                                    <i class="fas fa-plus"></i>
                                    Create Assignment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Assignments List -->
                <div class="col-lg-8">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5>
                                <i class="fas fa-list me-2"></i>
                                My Assignments (<?= count($assignments) ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($assignments)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No assignments created yet</h5>
                                <p class="text-muted">Create your first assignment using the form on the left.</p>
                            </div>
                            <?php else: ?>
                            <?php foreach ($assignments as $assignment): ?>
                            <div class="assignment-card <?= $assignment['assignment_type'] ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($assignment['title']) ?></h6>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge badge-modern badge-type-<?= $assignment['assignment_type'] ?>">
                                                <?= htmlspecialchars(ucfirst($assignment['assignment_type'])) ?>
                                            </span>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($assignment['subject_name']) ?> - <?= htmlspecialchars($assignment['class_name']) ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="view_submissions.php?id=<?= $assignment['id'] ?>">
                                                <i class="fas fa-eye me-2"></i>View Submissions
                                            </a></li>
                                            <li><a class="dropdown-item" href="edit_assignment.php?id=<?= $assignment['id'] ?>">
                                                <i class="fas fa-edit me-2"></i>Edit Assignment
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                                    <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                                    <button type="submit" name="delete_assignment" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Delete Assignment
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <p class="text-muted mb-3"><?= htmlspecialchars($assignment['description']) ?></p>

                                <div class="assignment-meta">
                                    <div class="meta-item">
                                        <div class="meta-label">Due Date</div>
                                        <div class="meta-value"><?= htmlspecialchars(date('M d, Y', strtotime($assignment['due_date']))) ?></div>
                                    </div>
                                    <div class="meta-item">
                                        <div class="meta-label">Max Marks</div>
                                        <div class="meta-value"><?= htmlspecialchars($assignment['max_marks']) ?></div>
                                    </div>
                                    <div class="meta-item">
                                        <div class="meta-label">Submissions</div>
                                        <div class="meta-value"><?= $assignment['submissions'] ?>/<?= $assignment['total_students'] ?></div>
                                    </div>
                                    <div class="meta-item">
                                        <div class="meta-label">Created</div>
                                        <div class="meta-value"><?= htmlspecialchars(date('M d', strtotime($assignment['created_at']))) ?></div>
                                    </div>
                                </div>

                                <!-- Submission Progress -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Submission Progress</small>
                                        <small class="text-muted">
                                            <?= $assignment['total_students'] > 0 ? round(($assignment['submissions'] / $assignment['total_students']) * 100) : 0 ?>%
                                        </small>
                                    </div>
                                    <div class="progress-modern">
                                        <div class="progress-bar-modern" style="width: <?= $assignment['total_students'] > 0 ? ($assignment['submissions'] / $assignment['total_students']) * 100 : 0 ?>%"></div>
                                    </div>
                                </div>

                                <?php if ($assignment['attachment_url']): ?>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-paperclip me-1"></i>
                                        <a href="../<?= htmlspecialchars($assignment['attachment_url']) ?>" target="_blank" class="text-decoration-none">
                                            View Attachment
                                        </a>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="view_submissions.php?id=<?= $assignment['id'] ?>" class="btn btn-sm btn-info-modern btn-modern">
                                        <i class="fas fa-eye"></i>
                                        View Submissions (<?= $assignment['submissions'] ?>)
                                    </a>
                                    <a href="edit_assignment.php?id=<?= $assignment['id'] ?>" class="btn btn-sm btn-warning-modern btn-modern">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload drag and drop functionality
        const fileUploadArea = document.querySelector('.file-upload-area');
        const fileInput = document.getElementById('attachment');

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileUploadText(files[0].name);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                updateFileUploadText(e.target.files[0].name);
            }
        });

        function updateFileUploadText(filename) {
            const uploadArea = document.querySelector('.file-upload-area');
            uploadArea.innerHTML = `
                <i class="fas fa-file fa-2x text-success mb-2"></i>
                <p class="text-success mb-0">${filename}</p>
                <small class="text-muted">Click to change file</small>
            `;
        }

        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
