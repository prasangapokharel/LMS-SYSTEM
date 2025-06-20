<?php
include '../include/connect.php';
include '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

// Get teacher profile information - teachers are users with role_id = 2
$stmt = $pdo->prepare("SELECT u.*, ur.role_name, ay.year_name as current_academic_year
                      FROM users u
                      JOIN user_roles ur ON u.role_id = ur.id
                      LEFT JOIN academic_years ay ON ay.is_current = 1
                      WHERE u.id = ? AND u.role_id = 2");
$stmt->execute([$user['id']]);
$teacher_profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher_profile) {
    header("Location: ../login.php");
    exit;
}

// Get teacher's teaching statistics
$stmt = $pdo->prepare("SELECT 
                      COUNT(DISTINCT cst.class_id) as total_classes,
                      COUNT(DISTINCT cst.subject_id) as total_subjects,
                      COUNT(DISTINCT sc.student_id) as total_students
                      FROM class_subject_teachers cst
                      LEFT JOIN student_classes sc ON cst.class_id = sc.class_id AND sc.status = 'enrolled'
                      WHERE cst.teacher_id = ? AND cst.is_active = 1");
$stmt->execute([$user['id']]);
$teaching_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get recent teaching activities
$stmt = $pdo->prepare("SELECT 'log' as type, tl.created_at, c.class_name, c.section, s.subject_name, tl.chapter_title as title
                      FROM teacher_logs tl
                      JOIN classes c ON tl.class_id = c.id
                      JOIN subjects s ON tl.subject_id = s.id
                      WHERE tl.teacher_id = ?
                      UNION ALL
                      SELECT 'assignment' as type, a.created_at, c.class_name, c.section, s.subject_name, a.title
                      FROM assignments a
                      JOIN classes c ON a.class_id = c.id
                      JOIN subjects s ON a.subject_id = s.id
                      WHERE a.teacher_id = ? AND a.is_active = 1
                      ORDER BY created_at DESC
                      LIMIT 5");
$stmt->execute([$user['id'], $user['id']]);
$recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get teacher's classes and subjects
$stmt = $pdo->prepare("SELECT c.class_name, c.section, s.subject_name, s.subject_code
                      FROM class_subject_teachers cst
                      JOIN classes c ON cst.class_id = c.id
                      JOIN subjects s ON cst.subject_id = s.id
                      WHERE cst.teacher_id = ? AND cst.is_active = 1
                      ORDER BY c.class_name, s.subject_name");
$stmt->execute([$user['id']]);
$teaching_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload_image') {
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $upload_dir = '../assets/images/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $file_name = 'profile_' . $user['id'] . '_' . time() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
                // Delete old profile image if exists
                if ($teacher_profile['profile_image'] && $teacher_profile['profile_image'] != 'default-avatar.png') {
                    $old_file = $upload_dir . $teacher_profile['profile_image'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                
                // Update database
                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$file_name, $user['id']]);
                
                $msg = "<div class='alert alert-success'>
                            <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                <path d='M9 12l2 2 4-4'/>
                                <circle cx='12' cy='12' r='10'/>
                            </svg>
                            Profile image updated successfully!
                        </div>";
                
                // Refresh profile data
                $stmt = $pdo->prepare("SELECT u.*, ur.role_name, ay.year_name as current_academic_year
                                      FROM users u
                                      JOIN user_roles ur ON u.role_id = ur.id
                                      LEFT JOIN academic_years ay ON ay.is_current = 1
                                      WHERE u.id = ? AND u.role_id = 2");
                $stmt->execute([$user['id']]);
                $teacher_profile = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $msg = "<div class='alert alert-danger'>
                            <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                                <circle cx='12' cy='12' r='10'/>
                                <line x1='15' y1='9' x2='9' y2='15'/>
                                <line x1='9' y1='9' x2='15' y2='15'/>
                            </svg>
                            Failed to upload image.
                        </div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>
                        <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                            <circle cx='12' cy='12' r='10'/>
                            <line x1='15' y1='9' x2='9' y2='15'/>
                            <line x1='9' y1='9' x2='15' y2='15'/>
                        </svg>
                        Please upload a valid image file (JPEG, PNG, GIF).
                    </div>";
        }
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    try {
        // Update users table
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $email, $phone, $address, $user['id']]);
        
        $msg = "<div class='alert alert-success'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                        <path d='M9 12l2 2 4-4'/>
                        <circle cx='12' cy='12' r='10'/>
                    </svg>
                    Profile updated successfully!
                </div>";
        
        // Refresh profile data
        $stmt = $pdo->prepare("SELECT u.*, ur.role_name, ay.year_name as current_academic_year
                              FROM users u
                              JOIN user_roles ur ON u.role_id = ur.id
                              LEFT JOIN academic_years ay ON ay.is_current = 1
                              WHERE u.id = ? AND u.role_id = 2");
        $stmt->execute([$user['id']]);
        $teacher_profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                        <circle cx='12' cy='12' r='10'/>
                        <line x1='15' y1='9' x2='9' y2='15'/>
                        <line x1='9' y1='9' x2='15' y2='15'/>
                    </svg>
                    Error updating profile: " . htmlspecialchars($e->getMessage()) . "
                </div>";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $msg = "<div class='alert alert-danger'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                        <circle cx='12' cy='12' r='10'/>
                        <line x1='15' y1='9' x2='9' y2='15'/>
                        <line x1='9' y1='9' x2='15' y2='15'/>
                    </svg>
                    New passwords do not match.
                </div>";
    } elseif (!password_verify($current_password, $user['password_hash'])) {
        $msg = "<div class='alert alert-danger'>
                    <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                        <circle cx='12' cy='12' r='10'/>
                        <line x1='15' y1='9' x2='9' y2='15'/>
                        <line x1='9' y1='9' x2='15' y2='15'/>
                    </svg>
                    Current password is incorrect.
                </div>";
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user['id']]);
            
            $msg = "<div class='alert alert-success'>
                        <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                            <path d='M9 12l2 2 4-4'/>
                            <circle cx='12' cy='12' r='10'/>
                        </svg>
                        Password changed successfully!
                    </div>";
            
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>
                        <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                            <circle cx='12' cy='12' r='10'/>
                            <line x1='15' y1='9' x2='9' y2='15'/>
                            <line x1='9' y1='9' x2='15' y2='15'/>
                        </svg>
                        Error changing password: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
        }
    }
}

// Set default values for fields that don't exist in users table
$teacher_profile['department_name'] = 'General Department';
$teacher_profile['bio'] = $teacher_profile['bio'] ?? '';
$teacher_profile['qualifications'] = $teacher_profile['qualifications'] ?? '';
$teacher_profile['experience_years'] = $teacher_profile['experience_years'] ?? 0;
?>
