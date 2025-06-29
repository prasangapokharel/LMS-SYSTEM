<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('principal');

$user = getCurrentUser($pdo);
$msg = "";

// Handle form submission for new class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_name'])) {
    $class_name = trim($_POST['class_name']);
    $class_level = $_POST['class_level'];
    $section = trim($_POST['section']);
    $capacity = $_POST['capacity'];
    $academic_year_id = $_POST['academic_year_id'];
    $class_teacher_id = $_POST['class_teacher_id'] ?? null;
    $room_number = trim($_POST['room_number']);
    $description = trim($_POST['description']);
    
    try {
        // Check if class already exists for this academic year
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE class_name = ? AND section = ? AND academic_year_id = ?");
        $stmt->execute([$class_name, $section, $academic_year_id]);
        
        if ($stmt->fetch()) {
            throw new Exception("Class with this name and section already exists for the selected academic year.");
        }
        
        // Insert new class
        $stmt = $pdo->prepare("INSERT INTO classes 
                              (class_name, class_level, section, academic_year_id, capacity, room_number, description, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$class_name, $class_level, $section, $academic_year_id, $capacity, $room_number, $description]);
        
        $class_id = $pdo->lastInsertId();
        
        // Assign class teacher if selected
        if ($class_teacher_id) {
            $stmt = $pdo->prepare("UPDATE classes SET class_teacher_id = ? WHERE id = ?");
            $stmt->execute([$class_teacher_id, $class_id]);
        }
        
        logActivity($pdo, 'class_created', 'classes', $class_id);
        
        $msg = "<div class='alert alert-success alert-modern'>
                <div class='alert-icon'>✅</div>
                <div><strong>Class created successfully!</strong> Class ID: $class_id</div>
               </div>";
               
        // Clear form data
        $_POST = array();
        
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>❌</div>
                <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
               </div>";
    }
}

// Handle class deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_class'])) {
    $class_id = $_POST['class_id'];
    
    try {
        // Check if class has students
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_classes WHERE class_id = ? AND status = 'enrolled'");
        $stmt->execute([$class_id]);
        $student_count = $stmt->fetchColumn();
        
        if ($student_count > 0) {
            throw new Exception("Cannot delete class with enrolled students. Please transfer students first.");
        }
        
        // Delete class
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->execute([$class_id]);
        
        logActivity($pdo, 'class_deleted', 'classes', $class_id);
        
        $msg = "<div class='alert alert-success alert-modern'>
                <div class='alert-icon'>✅</div>
                <div><strong>Class deleted successfully!</strong></div>
               </div>";
               
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger alert-modern'>
                <div class='alert-icon'>❌</div>
                <div><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>
               </div>";
    }
}

// Get academic years
$stmt = $pdo->prepare("SELECT * FROM academic_years ORDER BY start_date DESC");
$stmt->execute();
$academic_years = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available teachers
$stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name, u.email 
                      FROM users u 
                      WHERE u.role_id = 2 AND u.is_active = 1 
                      ORDER BY u.first_name, u.last_name");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get existing classes with details
$stmt = $pdo->prepare("SELECT c.*, ay.year_name, 
                      CONCAT(t.first_name, ' ', t.last_name) as teacher_name,
                      (SELECT COUNT(*) FROM student_classes sc WHERE sc.class_id = c.id AND sc.status = 'enrolled') as student_count
                      FROM classes c
                      JOIN academic_years ay ON c.academic_year_id = ay.id
                      LEFT JOIN users t ON c.class_teacher_id = t.id
                      ORDER BY c.class_level, c.class_name, c.section");
$stmt->execute();
$existing_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../include/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create & Manage Classes - School LMS</title>
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

        .class-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.3s ease;
        }

        .class-card:hover {
            transform: translateY(-2px);
        }

        .class-card.level-1 { border-left-color: #4facfe; }
        .class-card.level-2 { border-left-color: #11998e; }
        .class-card.level-3 { border-left-color: #f093fb; }
        .class-card.level-4 { border-left-color: #ff6b6b; }
        .class-card.level-5 { border-left-color: #667eea; }

        .class-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.total { background: var(--primary-gradient); }
        .stat-icon.active { background: var(--success-gradient); }
        .stat-icon.students { background: var(--info-gradient); }
        .stat-icon.teachers { background: var(--warning-gradient); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #718096;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .class-meta {
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
                    <i class="fas fa-school me-3"></i>
                    Class Management
                </h1>
                <p class="page-subtitle">Create and manage classes for your school</p>
            </div>

            <!-- Alert Messages -->
            <?= $msg ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-number"><?= count($existing_classes) ?></div>
                    <div class="stat-label">Total Classes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon active">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number"><?= count(array_filter($existing_classes, fn($c) => $c['is_active'])) ?></div>
                    <div class="stat-label">Active Classes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon students">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?= array_sum(array_column($existing_classes, 'student_count')) ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon teachers">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-number"><?= count($teachers) ?></div>
                    <div class="stat-label">Available Teachers</div>
                </div>
            </div>

            <div class="row">
                <!-- Create Class Form -->
                <div class="col-lg-4">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5>
                                <i class="fas fa-plus-circle me-2"></i>
                                Create New Class
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">Academic Year</label>
                                    <select name="academic_year_id" class="form-select form-control-modern" required>
                                        <option value="">-- Select Academic Year --</option>
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?= $year['id'] ?>" <?= $year['is_current'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($year['year_name']) ?>
                                            <?= $year['is_current'] ? ' (Current)' : '' ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">Class Name</label>
                                        <input type="text" name="class_name" class="form-control form-control-modern" required placeholder="e.g., Class 1, Grade 5">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Section</label>
                                        <input type="text" name="section" class="form-control form-control-modern" value="A" required placeholder="A, B, C">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Class Level</label>
                                        <select name="class_level" class="form-select form-control-modern" required>
                                            <option value="">-- Select Level --</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>">Level <?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Capacity</label>
                                        <input type="number" name="capacity" class="form-control form-control-modern" value="40" required min="1" max="100">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Class Teacher (Optional)</label>
                                    <select name="class_teacher_id" class="form-select form-control-modern">
                                        <option value="">-- Select Class Teacher --</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                                            <small>(<?= htmlspecialchars($teacher['email']) ?>)</small>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Room Number (Optional)</label>
                                    <input type="text" name="room_number" class="form-control form-control-modern" placeholder="e.g., Room 101, Lab A">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description (Optional)</label>
                                    <textarea name="description" class="form-control form-control-modern" rows="3" placeholder="Additional information about the class"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary-modern btn-modern w-100">
                                    <i class="fas fa-plus"></i>
                                    Create Class
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Existing Classes -->
                <div class="col-lg-8">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5>
                                <i class="fas fa-list me-2"></i>
                                Existing Classes (<?= count($existing_classes) ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($existing_classes)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-school fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No classes created yet</h5>
                                <p class="text-muted">Create your first class using the form on the left.</p>
                            </div>
                            <?php else: ?>
                            <?php foreach ($existing_classes as $class): ?>
                            <div class="class-card level-<?= $class['class_level'] ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?>
                                        </h6>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge bg-primary">Level <?= $class['class_level'] ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($class['year_name']) ?></small>
                                            <?php if ($class['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="class_details.php?id=<?= $class['id'] ?>">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a></li>
                                            <li><a class="dropdown-item" href="edit_class.php?id=<?= $class['id'] ?>">
                                                <i class="fas fa-edit me-2"></i>Edit Class
                                            </a></li>
                                            <li><a class="dropdown-item" href="manage_students.php?class_id=<?= $class['id'] ?>">
                                                <i class="fas fa-users me-2"></i>Manage Students
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this class? This action cannot be undone.')">
                                                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                                    <button type="submit" name="delete_class" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Delete Class
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <?php if ($class['description']): ?>
                                <p class="text-muted mb-3"><?= htmlspecialchars($class['description']) ?></p>
                                <?php endif; ?>

                                <div class="class-meta">
                                    <div class="meta-item">
                                        <div class="meta-label">Students</div>
                                        <div class="meta-value"><?= $class['student_count'] ?>/<?= $class['capacity'] ?></div>
                                    </div>
                                    <div class="meta-item">
                                        <div class="meta-label">Capacity</div>
                                        <div class="meta-value"><?= $class['capacity'] ?></div>
                                    </div>
                                    <?php if ($class['room_number']): ?>
                                    <div class="meta-item">
                                        <div class="meta-label">Room</div>
                                        <div class="meta-value"><?= htmlspecialchars($class['room_number']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="meta-item">
                                        <div class="meta-label">Created</div>
                                        <div class="meta-value"><?= htmlspecialchars(date('M d, Y', strtotime($class['created_at']))) ?></div>
                                    </div>
                                </div>

                                <?php if ($class['teacher_name']): ?>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>
                                        Class Teacher: <strong><?= htmlspecialchars($class['teacher_name']) ?></strong>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <!-- Student Capacity Progress -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Student Capacity</small>
                                        <small class="text-muted">
                                            <?= $class['capacity'] > 0 ? round(($class['student_count'] / $class['capacity']) * 100) : 0 ?>%
                                        </small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: <?= $class['capacity'] > 0 ? ($class['student_count'] / $class['capacity']) * 100 : 0 ?>%"></div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="class_details.php?id=<?= $class['id'] ?>" class="btn btn-sm btn-info-modern btn-modern">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                    <a href="manage_students.php?class_id=<?= $class['id'] ?>" class="btn btn-sm btn-success-modern btn-modern">
                                        <i class="fas fa-users"></i>
                                        Students (<?= $class['student_count'] ?>)
                                    </a>
                                    <a href="edit_class.php?id=<?= $class['id'] ?>" class="btn btn-sm btn-warning-modern btn-modern">
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
        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const className = document.querySelector('input[name="class_name"]').value.trim();
            const classLevel = document.querySelector('select[name="class_level"]').value;
            const capacity = document.querySelector('input[name="capacity"]').value;

            if (!className || !classLevel || !capacity) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }

            if (parseInt(capacity) < 1 || parseInt(capacity) > 100) {
                e.preventDefault();
                alert('Class capacity must be between 1 and 100.');
                return false;
            }
        });
    </script>
</body>
</html>
