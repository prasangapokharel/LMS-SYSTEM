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

// Get current Nepali date
$current_nepali_date = date('Y-m-d'); // Default to English date
try {
    // If you have Nepali calendar system, use it here
    // $current_nepali_date = convertToNepaliDate(date('Y-m-d'));
} catch (Exception $e) {
    // Fallback to English date
    $current_nepali_date = date('Y-m-d');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = intval($_POST['class_id']);
    $date = $_POST['date'];
    $attendance = $_POST['attendance'] ?? [];
    $remarks = $_POST['remarks'] ?? [];
    
    if (!empty($attendance)) {
        try {
            $pdo->beginTransaction();
            
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
                $student_remarks = isset($remarks[$student_id]) ? trim($remarks[$student_id]) : '';
                $stmt->execute([intval($student_id), $class_id, $user['id'], $date, $status, $student_remarks]);
            }
            
            $pdo->commit();
            logActivity($pdo, 'attendance_recorded', 'attendance', null, null, ['class_id' => $class_id, 'date' => $date]);
            $msg = "<div class='alert alert-success'><i class='fas fa-check-circle me-2'></i>Attendance recorded successfully!</div>";
        } catch (Exception $e) {
            $pdo->rollBack();
            $msg = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle me-2'></i>Error recording attendance. Please try again.</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle me-2'></i>Please mark attendance for at least one student.</div>";
    }
}
?>
