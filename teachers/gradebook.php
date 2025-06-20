<?php
include_once '../App/Models/teacher/Gradebook.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gradebook - <?= htmlspecialchars($course_info['class_name'] . ' ' . $course_info['section'] . ' - ' . $course_info['subject_name']) ?></title>
<link rel="stylesheet" href="../assets/css/ui.css">
<style>
    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 1rem;
        background-color: var(--color-gray-50);
        min-height: 100vh;
        padding-bottom: 80px;
    }
    
    .course-selector {
        background: var(--color-white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
    }
    
    .gradebook-container {
        background: var(--color-white);
        border-radius: 0.75rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-gray-200);
        overflow: hidden;
    }
    
    .gradebook-header {
        background: var(--color-gray-50);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--color-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .gradebook-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 0;
    }
    
    .gradebook-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .gradebook-table-container {
        overflow-x: auto;
        max-height: 70vh;
    }
    
    .gradebook-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }
    
    .gradebook-table th {
        background: var(--color-gray-50);
        padding: 0.75rem 0.5rem;
        text-align: center;
        font-weight: 600;
        color: var(--color-gray-700);
        border-bottom: 1px solid var(--color-gray-200);
        border-right: 1px solid var(--color-gray-200);
        font-size: 0.75rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .gradebook-table th:first-child {
        position: sticky;
        left: 0;
        z-index: 11;
        min-width: 150px;
        text-align: left;
        padding-left: 1rem;
    }
    
    .gradebook-table td {
        padding: 0.5rem;
        border-bottom: 1px solid var(--color-gray-200);
        border-right: 1px solid var(--color-gray-200);
        text-align: center;
        font-size: 0.75rem;
        vertical-align: middle;
    }
    
    .gradebook-table td:first-child {
        position: sticky;
        left: 0;
        background: var(--color-white);
        z-index: 9;
        text-align: left;
        padding-left: 1rem;
        font-weight: 500;
        min-width: 150px;
    }
    
    .student-name {
        font-weight: 500;
        color: var(--color-gray-900);
    }
    
    .student-id {
        font-size: 0.625rem;
        color: var(--color-gray-500);
        margin-top: 0.25rem;
    }
    
    .grade-input {
        width: 60px;
        padding: 0.25rem;
        border: 1px solid var(--color-gray-300);
        border-radius: 0.25rem;
        text-align: center;
        font-size: 0.75rem;
    }
    
    .grade-display {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    
    .grade-excellent {
        background: var(--color-success-light);
        color: var(--color-success);
    }
    
    .grade-good {
        background: var(--color-primary-light);
        color: var(--color-primary);
    }
    
    .grade-average {
        background: var(--color-warning-light);
        color: var(--color-warning);
    }
    
    .grade-poor {
        background: var(--color-danger-light);
        color: var(--color-danger);
    }
    
    .grade-missing {
        background: var(--color-gray-100);
        color: var(--color-gray-500);
    }
    
    .assignment-header {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        min-width: 80px;
        max-width: 80px;
    }
    
    .assignment-info {
        font-size: 0.625rem;
        color: var(--color-gray-500);
        margin-top: 0.25rem;
    }
    
    .average-column {
        background: var(--color-primary-light) !important;
        color: var(--color-primary);
        font-weight: 600;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: var(--color-primary);
        color: var(--color-white);
    }
    
    .btn-secondary {
        background: var(--color-gray-500);
        color: var(--color-white);
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.625rem;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }
    
    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background: var(--color-white);
        border-radius: 0.75rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--color-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 0;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--color-gray-500);
        cursor: pointer;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--color-gray-700);
        margin-bottom: 0.5rem;
    }
    
    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--color-gray-300);
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    @media (min-width: 768px) {
        .mobile-container {
            max-width: 1400px;
            padding: 2rem;
        }
        
        .gradebook-table th,
        .gradebook-table td {
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        
        .assignment-header {
            writing-mode: horizontal-tb;
            text-orientation: initial;
            min-width: 120px;
            max-width: 120px;
        }
    }
</style>
</head>
<body>
<div class="mobile-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Gradebook</h1>
        <p class="page-subtitle"><?= htmlspecialchars($course_info['class_name'] . ' ' . $course_info['section'] . ' - ' . $course_info['subject_name']) ?></p>
    </div>

    <!-- Alert Messages -->
    <?= $msg ?>

    <!-- Course Selector -->
    <div class="course-selector">
        <form method="get" class="form-row">
            <div class="form-group">
                <label class="form-label">Select Course</label>
                <select name="course" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Course --</option>
                    <?php foreach ($teacher_courses as $course): ?>
                    <option value="<?= $course['id'] ?>_<?= $course['subject_id'] ?>" 
                            <?= ($course['id'] == $class_id && $course['subject_id'] == $subject_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['class_name'] . ' ' . $course['section'] . ' - ' . $course['subject_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if ($class_id && $subject_id): ?>
    <!-- Gradebook -->
    <div class="gradebook-container">
        <div class="gradebook-header">
            <h2 class="gradebook-title">Student Grades</h2>
            <div class="gradebook-actions">
                <a href="assignments.php?class_id=<?= $class_id ?>&subject_id=<?= $subject_id ?>" class="btn btn-primary">
                    Manage Assignments
                </a>
                <button type="button" class="btn btn-secondary" onclick="exportGrades()">
                    Export Grades
                </button>
            </div>
        </div>
        
        <div class="gradebook-table-container">
            <?php if (empty($students)): ?>
            <div class="empty-state">
                <div class="empty-title">No Students Found</div>
                <div class="empty-text">No students are enrolled in this class.</div>
            </div>
            <?php elseif (empty($assignments)): ?>
            <div class="empty-state">
                <div class="empty-title">No Assignments Found</div>
                <div class="empty-text">Create assignments to start grading students.</div>
            </div>
            <?php else: ?>
            <table class="gradebook-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <?php foreach ($assignments as $assignment): ?>
                        <th class="assignment-header">
                            <div><?= htmlspecialchars($assignment['title']) ?></div>
                            <div class="assignment-info">
                                <?= $assignment['max_marks'] ?>pts<br>
                                <?= date('M j', strtotime($assignment['due_date'])) ?>
                            </div>
                        </th>
                        <?php endforeach; ?>
                        <th class="average-column">Average</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td>
                            <div class="student-name"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
                            <div class="student-id"><?= htmlspecialchars($student['student_id']) ?></div>
                        </td>
                        <?php foreach ($assignments as $assignment): ?>
                        <td>
                            <?php 
                            $grade_data = $grades[$student['id']][$assignment['id']] ?? null;
                            $grade = $grade_data['grade'] ?? null;
                            $status = $grade_data['status'] ?? 'not_submitted';
                            
                            if ($grade !== null) {
                                $percentage = ($grade / $assignment['max_marks']) * 100;
                                $grade_class = 'grade-missing';
                                if ($percentage >= 90) $grade_class = 'grade-excellent';
                                elseif ($percentage >= 80) $grade_class = 'grade-good';
                                elseif ($percentage >= 70) $grade_class = 'grade-average';
                                elseif ($percentage >= 60) $grade_class = 'grade-poor';
                                
                                echo "<div class='grade-display $grade_class' onclick='editGrade({$assignment['id']}, {$student['id']}, \"$grade\", \"{$assignment['title']}\", \"{$student['first_name']} {$student['last_name']}\")'>";
                                echo number_format($grade, 1) . '/' . $assignment['max_marks'];
                                echo "</div>";
                            } elseif ($status == 'submitted') {
                                echo "<button class='btn btn-sm btn-primary' onclick='editGrade({$assignment['id']}, {$student['id']}, \"\", \"{$assignment['title']}\", \"{$student['first_name']} {$student['last_name']}\")'>Grade</button>";
                            } else {
                                echo "<span class='grade-display grade-missing'>-</span>";
                            }
                            ?>
                        </td>
                        <?php endforeach; ?>
                        <td class="average-column">
                            <?php 
                            $avg_data = $student_averages[$student['id']];
                            if ($avg_data['graded_assignments'] > 0) {
                                echo number_format($avg_data['average'], 1) . '%';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Grade Edit Modal -->
<div id="gradeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Grade</h3>
            <button type="button" class="modal-close" onclick="closeGradeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" id="gradeForm">
                <input type="hidden" name="action" value="update_grade">
                <input type="hidden" name="assignment_id" id="modalAssignmentId">
                <input type="hidden" name="student_id" id="modalStudentId">
                
                <div class="form-group">
                    <label class="form-label">Assignment</label>
                    <div id="modalAssignmentTitle" style="font-weight: 500;"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <div id="modalStudentName" style="font-weight: 500;"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Grade</label>
                    <input type="number" name="grade" id="modalGrade" class="form-input" step="0.1" min="0">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Feedback (Optional)</label>
                    <textarea name="feedback" id="modalFeedback" class="form-textarea" placeholder="Provide feedback to the student..."></textarea>
                </div>
                
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeGradeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Bottom Navigation -->
<?php include '../include/bootoomnav.php'; ?>

<script>
function editGrade(assignmentId, studentId, currentGrade, assignmentTitle, studentName) {
    document.getElementById('modalAssignmentId').value = assignmentId;
    document.getElementById('modalStudentId').value = studentId;
    document.getElementById('modalGrade').value = currentGrade;
    document.getElementById('modalAssignmentTitle').textContent = assignmentTitle;
    document.getElementById('modalStudentName').textContent = studentName;
    document.getElementById('modalFeedback').value = '';
    document.getElementById('gradeModal').classList.add('show');
}

function closeGradeModal() {
    document.getElementById('gradeModal').classList.remove('show');
}

function exportGrades() {
    // Simple CSV export functionality
    const table = document.querySelector('.gradebook-table');
    let csv = '';
    
    // Get headers
    const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim());
    csv += headers.join(',') + '\n';
    
    // Get data rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td')).map(td => {
            return '"' + td.textContent.trim().replace(/"/g, '""') + '"';
        });
        csv += cells.join(',') + '\n';
    });
    
    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'gradebook_<?= $course_info['class_name'] ?>_<?= $course_info['subject_name'] ?>.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Handle course selection
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.querySelector('select[name="course"]');
    if (courseSelect) {
        courseSelect.addEventListener('change', function() {
            if (this.value) {
                const [classId, subjectId] = this.value.split('_');
                window.location.href = `gradebook.php?class_id=${classId}&subject_id=${subjectId}`;
            }
        });
    }
});

// Close modal when clicking outside
document.getElementById('gradeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGradeModal();
    }
});
</script>
</body>
</html>
