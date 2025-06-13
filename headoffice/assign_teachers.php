<?php 
// Include necessary files
include_once '../App/Models/headoffice/Assign.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teachers - School LMS</title>
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
        
        a {
            text-decoration: none;
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
            overflow-x: hidden;
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

        .breadcrumb {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-top: 1rem;
        }

        .breadcrumb-item a {
            color: white;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.8);
        }

        .class-info {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .class-info h3 {
            margin-top: 0;
            color: #4a5568;
            font-weight: 600;
        }

        .class-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .meta-item {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .meta-label {
            font-size: 0.875rem;
            color: #718096;
            font-weight: 500;
        }

        .meta-value {
            font-weight: 600;
            color: #2d3748;
            margin-top: 0.25rem;
        }

        .card-header-modern {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border: none;
            border-radius: 20px 20px 0 0;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 600;
        }

        .modern-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
            margin-bottom: 2rem;
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

        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern th {
            background: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-modern td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-modern tr:last-child td {
            border-bottom: none;
        }

        .table-modern tr:hover {
            background-color: #f8fafc;
        }

        .badge-subject {
            background: var(--info-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .teacher-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .teacher-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .teacher-details h6 {
            margin: 0;
            font-weight: 600;
        }

        .teacher-details small {
            color: #718096;
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

        .modal-modern .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-modern .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: #4a5568;
            font-weight: 600;
        }

        .empty-state p {
            color: #718096;
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-chalkboard-teacher me-3"></i>
                    Assign Teachers
                </h1>
                <p class="page-subtitle">Manage teacher assignments for <?= htmlspecialchars($class['class_name']) ?> - Section <?= htmlspecialchars($class['section']) ?></p>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="createclass.php">Classes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Assign Teachers</li>
                    </ol>
                </nav>
            </div>

            <!-- Alert Messages -->
            <?php if ($msg): ?>
            <div class="alert alert-success alert-modern">
                <div class="alert-icon">✅</div>
                <div><strong>Success!</strong> <?= $msg ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger alert-modern">
                <div class="alert-icon">❌</div>
                <div><strong>Error!</strong> <?= $error ?></div>
            </div>
            <?php endif; ?>

            <!-- Class Information -->
            <div class="class-info">
                <h3>Class Information</h3>
                <div class="class-meta">
                    <div class="meta-item">
                        <div class="meta-label">Class Name</div>
                        <div class="meta-value"><?= htmlspecialchars($class['class_name']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Section</div>
                        <div class="meta-value"><?= htmlspecialchars($class['section']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Class Level</div>
                        <div class="meta-value"><?= htmlspecialchars($class['class_level']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Academic Year</div>
                        <div class="meta-value"><?= htmlspecialchars($current_academic_year['year_name']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Students Enrolled</div>
                        <div class="meta-value"><?= $student_count ?> / <?= htmlspecialchars($class['capacity']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Subjects Assigned</div>
                        <div class="meta-value"><?= count($assignments) ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Current Assignments -->
                <div class="col-lg-8">
                    <div class="modern-card">
                        <div class="card-header-modern d-flex justify-content-between align-items-center">
                            <h5>
                                <i class="fas fa-user-check me-2"></i>
                                Current Teacher Assignments
                            </h5>
                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                                <i class="fas fa-tasks me-1"></i>
                                Bulk Assign
                            </button>
                        </div>
                        <div class="card-body">
                            <?php if (empty($assignments)): ?>
                            <div class="empty-state">
                                <i class="fas fa-user-slash"></i>
                                <h5>No Teacher Assignments</h5>
                                <p>There are no teachers assigned to subjects for this class yet. Use the form on the right to assign teachers.</p>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Teacher</th>
                                            <th>Assigned Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assignments as $assignment): ?>
                                        <tr>
                                            <td>
                                                <span class="badge-subject">
                                                    <?= htmlspecialchars($assignment['subject_name']) ?> 
                                                    <small>(<?= htmlspecialchars($assignment['subject_code']) ?>)</small>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="teacher-info">
                                                    <div class="teacher-avatar">
                                                        <?= strtoupper(substr($assignment['first_name'], 0, 1) . substr($assignment['last_name'], 0, 1)) ?>
                                                    </div>
                                                    <div class="teacher-details">
                                                        <h6><?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></h6>
                                                        <small><?= htmlspecialchars($assignment['email']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($assignment['assigned_date'])) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editAssignmentModal<?= $assignment['id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#removeAssignmentModal<?= $assignment['id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                
                                                <!-- Edit Assignment Modal -->
                                                <div class="modal fade modal-modern" id="editAssignmentModal<?= $assignment['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-edit me-2"></i>
                                                                    Edit Teacher Assignment
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="subject_id" value="<?= $assignment['subject_id'] ?>">
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Subject</label>
                                                                        <input type="text" class="form-control form-control-modern" 
                                                                               value="<?= htmlspecialchars($assignment['subject_name']) ?> (<?= htmlspecialchars($assignment['subject_code']) ?>)" 
                                                                               disabled>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Select Teacher</label>
                                                                        <select name="teacher_id" class="form-select form-control-modern" required>
                                                                            <?php foreach ($teachers as $teacher): ?>
                                                                            <option value="<?= $teacher['id'] ?>" <?= $teacher['id'] == $assignment['teacher_id'] ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> 
                                                                                (<?= htmlspecialchars($teacher['email']) ?>)
                                                                            </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" name="assign_teacher" class="btn btn-primary-modern btn-modern">
                                                                        <i class="fas fa-save"></i>
                                                                        Update Assignment
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Remove Assignment Modal -->
                                                <div class="modal fade modal-modern" id="removeAssignmentModal<?= $assignment['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-trash me-2"></i>
                                                                    Remove Teacher Assignment
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body text-center">
                                                                    <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                                                    
                                                                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                                                    
                                                                    <p>Are you sure you want to remove <strong><?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></strong> 
                                                                    from teaching <strong><?= htmlspecialchars($assignment['subject_name']) ?></strong>?</p>
                                                                    
                                                                    <p class="text-danger">This action cannot be undone.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" name="remove_assignment" class="btn btn-danger-modern btn-modern">
                                                                        <i class="fas fa-trash"></i>
                                                                        Remove Assignment
                                                                    </button>
                                                                </div>
                                                            </form>
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
                
                <!-- Assign Teacher Form -->
                <div class="col-lg-4">
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h5>
                                <i class="fas fa-user-plus me-2"></i>
                                Assign Teacher
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">Select Subject</label>
                                    <select name="subject_id" class="form-select form-control-modern" required>
                                        <option value="">-- Select Subject --</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>">
                                            <?= htmlspecialchars($subject['subject_name']) ?> 
                                            (<?= htmlspecialchars($subject['subject_code']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Select Teacher</label>
                                    <select name="teacher_id" class="form-select form-control-modern" required>
                                        <option value="">-- Select Teacher --</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> 
                                            (<?= htmlspecialchars($teacher['email']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" name="assign_teacher" class="btn btn-primary-modern btn-modern w-100">
                                    <i class="fas fa-user-plus"></i>
                                    Assign Teacher
                                </button>
                            </form>
                            
                            <hr class="my-4">
                            
                            <div class="d-grid">
                                <button type="button" class="btn btn-info-modern btn-modern" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                                    <i class="fas fa-plus"></i>
                                    Add New Subject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Subject Modal -->
            <div class="modal fade modal-modern" id="addSubjectModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-book me-2"></i>
                                Add New Subject
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                                    <input type="text" name="subject_name" class="form-control form-control-modern" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                                    <input type="text" name="subject_code" class="form-control form-control-modern" required>
                                    <small class="text-muted">Must be unique across all subjects</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="add_subject" class="btn btn-success-modern btn-modern">
                                    <i class="fas fa-plus"></i>
                                    Add Subject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Bulk Assign Modal -->
            <div class="modal fade modal-modern" id="bulkAssignModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-tasks me-2"></i>
                                Bulk Teacher Assignment
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post">
                            <div class="modal-body">
                                <p class="text-muted mb-4">Assign multiple subjects to teachers at once. Select a teacher and check the subjects you want to assign.</p>
                                
                                <div class="mb-4">
                                    <label class="form-label">Select Teacher</label>
                                    <select id="bulkTeacher" class="form-select form-control-modern" required>
                                        <option value="">-- Select Teacher --</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?> 
                                            (<?= htmlspecialchars($teacher['email']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-modern">
                                        <thead>
                                            <tr>
                                                <th width="50">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                                    </div>
                                                </th>
                                                <th>Subject</th>
                                                <th>Current Teacher</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($subjects as $subject): 
                                                // Find current assignment for this subject
                                                $current_teacher = null;
                                                foreach ($assignments as $assignment) {
                                                    if ($assignment['subject_id'] == $subject['id']) {
                                                        $current_teacher = $assignment;
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input subject-checkbox" type="checkbox" 
                                                               name="bulk_subjects[]" value="<?= $subject['id'] ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge-subject">
                                                        <?= htmlspecialchars($subject['subject_name']) ?> 
                                                        <small>(<?= htmlspecialchars($subject['subject_code']) ?>)</small>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($current_teacher): ?>
                                                    <div class="teacher-info">
                                                        <div class="teacher-avatar">
                                                            <?= strtoupper(substr($current_teacher['first_name'], 0, 1) . substr($current_teacher['last_name'], 0, 1)) ?>
                                                        </div>
                                                        <div class="teacher-details">
                                                            <h6><?= htmlspecialchars($current_teacher['first_name'] . ' ' . $current_teacher['last_name']) ?></h6>
                                                        </div>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="text-muted">Not assigned</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" id="bulkAssignBtn" name="bulk_assign" class="btn btn-primary-modern btn-modern" disabled>
                                    <i class="fas fa-save"></i>
                                    Assign Selected Subjects
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
            
            // Select all checkbox functionality
            const selectAll = document.getElementById('selectAll');
            const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');
            const bulkTeacher = document.getElementById('bulkTeacher');
            const bulkAssignBtn = document.getElementById('bulkAssignBtn');
            
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    subjectCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                    updateBulkAssignButton();
                });
            }
            
            subjectCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateBulkAssignButton();
                    
                    // Update "Select All" checkbox state
                    let allChecked = true;
                    subjectCheckboxes.forEach(function(cb) {
                        if (!cb.checked) allChecked = false;
                    });
                    
                    if (selectAll) {
                        selectAll.checked = allChecked;
                    }
                });
            });
            
            if (bulkTeacher) {
                bulkTeacher.addEventListener('change', updateBulkAssignButton);
            }
            
            function updateBulkAssignButton() {
                let hasTeacher = bulkTeacher && bulkTeacher.value;
                let hasSubjects = false;
                
                subjectCheckboxes.forEach(function(checkbox) {
                    if (checkbox.checked) hasSubjects = true;
                });
                
                if (bulkAssignBtn) {
                    bulkAssignBtn.disabled = !(hasTeacher && hasSubjects);
                }
            }
        });
    </script>
</body>
</html>
