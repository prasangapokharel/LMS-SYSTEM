<?php

class HeadOfficeUser {
    private $pdo;
    private $cache;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Initialize cache if available
        if (class_exists('Symfony\Component\Cache\Adapter\FilesystemAdapter')) {
            $this->cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter('user_management', 3600, __DIR__ . '/../../cache');
        }
    }
    
    /**
     * Create a new teacher with optional subject assignments
     */
    public function createTeacher($data) {
        // Validate inputs
        $validation = $this->validateTeacherData($data);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Generate secure credentials
        $teacher_count = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn();
        $username = 'teacher' . str_pad($teacher_count + 1, 3, '0', STR_PAD_LEFT);
        $password = $this->generateStrongPassword(12);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $this->pdo->beginTransaction();
            
            // Create user account
            $stmt = $this->pdo->prepare("INSERT INTO users 
                                      (username, email, password_hash, first_name, last_name, phone, address, role_id, is_active, created_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 2, 1, NOW())");
            $stmt->execute([
                $username, 
                $data['email'], 
                $password_hash, 
                $data['first_name'], 
                $data['last_name'], 
                $data['phone'], 
                $data['address'] ?? ''
            ]);
            
            $user_id = $this->pdo->lastInsertId();
            
            // Create teacher profile
            $stmt = $this->pdo->prepare("INSERT INTO teacher_profiles 
                                      (user_id, employee_id, qualification, experience_years, specialization, joining_date, salary, status) 
                                      VALUES (?, ?, ?, ?, ?, CURDATE(), ?, 'active')");
            
            $employee_id = 'EMP' . date('Y') . str_pad($teacher_count + 1, 3, '0', STR_PAD_LEFT);
            $stmt->execute([
                $user_id,
                $employee_id,
                $data['qualification'] ?? '',
                $data['experience_years'] ?? 0,
                $data['specialization'] ?? '',
                $data['salary'] ?? 0
            ]);
            
            $teacher_profile_id = $this->pdo->lastInsertId();
            
            // Get current academic year
            $stmt = $this->pdo->query("SELECT id FROM academic_years WHERE is_current = 1");
            $academic_year_id = $stmt->fetchColumn() ?: 1;
            
            // Assign subjects if provided
            if (!empty($data['subjects']) && is_array($data['subjects'])) {
                $this->assignSubjectsToTeacher($teacher_profile_id, $data['subjects']);
            }
            
            // Assign classes if provided - FIXED: Store in both tables for compatibility
            if (!empty($data['classes']) && is_array($data['classes'])) {
                $this->assignClassesToTeacher($user_id, $teacher_profile_id, $data['classes'], $academic_year_id);
            }
            
            $this->pdo->commit();
            
            // Clear caches
            $this->clearUserCaches();
            
            // Log activity
            $this->logActivity('teacher_created', 'users', $user_id);
            
            return [
                'success' => true,
                'message' => 'Teacher created successfully!',
                'credentials' => [
                    'username' => $username,
                    'password' => $password,
                    'employee_id' => $employee_id
                ],
                'user_id' => $user_id
            ];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create a new student with class enrollment
     */
    public function createStudent($data) {
        // Validate inputs
        $validation = $this->validateStudentData($data);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Generate secure credentials
        $student_count = $this->pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $username = 'student' . str_pad($student_count + 1, 3, '0', STR_PAD_LEFT);
        $password = $this->generateStrongPassword(10);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate student ID
        $current_year = date('Y');
        $student_id = 'STU' . $current_year . str_pad($student_count + 1, 3, '0', STR_PAD_LEFT);
        
        try {
            $this->pdo->beginTransaction();
            
            // Create user account
            $stmt = $this->pdo->prepare("INSERT INTO users 
                                      (username, email, password_hash, first_name, last_name, phone, address, role_id, is_active, created_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 3, 1, NOW())");
            $stmt->execute([
                $username,
                $data['email'],
                $password_hash,
                $data['first_name'],
                $data['last_name'],
                $data['phone'],
                $data['address'] ?? ''
            ]);
            
            $user_id = $this->pdo->lastInsertId();
            
            // Create student record
            $stmt = $this->pdo->prepare("INSERT INTO students 
                                      (user_id, student_id, admission_date, date_of_birth, blood_group, 
                                       guardian_name, guardian_phone, guardian_email, status) 
                                      VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([
                $user_id,
                $student_id,
                $data['date_of_birth'],
                $data['blood_group'] ?? '',
                $data['guardian_name'],
                $data['guardian_phone'],
                $data['guardian_email'] ?? ''
            ]);
            
            $student_db_id = $this->pdo->lastInsertId();
            
            // Get current academic year
            $stmt = $this->pdo->query("SELECT id FROM academic_years WHERE is_current = 1");
            $academic_year_id = $stmt->fetchColumn() ?: 1;
            
            // Enroll in class
            $stmt = $this->pdo->prepare("INSERT INTO student_enrollments 
                                      (student_id, class_id, academic_year_id, enrollment_date, status) 
                                      VALUES (?, ?, ?, CURDATE(), 'enrolled')");
            $stmt->execute([$student_db_id, $data['class_id'], $academic_year_id]);
            
            // Also add to student_classes for compatibility
            $stmt = $this->pdo->prepare("INSERT INTO student_classes 
                                      (student_id, class_id, academic_year_id, enrollment_date, status) 
                                      VALUES (?, ?, ?, CURDATE(), 'enrolled')");
            $stmt->execute([$student_db_id, $data['class_id'], $academic_year_id]);
            
            $this->pdo->commit();
            
            // Clear caches
            $this->clearUserCaches();
            
            // Log activity
            $this->logActivity('student_created', 'students', $student_db_id);
            
            return [
                'success' => true,
                'message' => 'Student created successfully!',
                'credentials' => [
                    'username' => $username,
                    'password' => $password,
                    'student_id' => $student_id
                ],
                'user_id' => $user_id
            ];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Assign subjects to a teacher
     */
    private function assignSubjectsToTeacher($teacher_profile_id, $subjects) {
        foreach ($subjects as $subject_id) {
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO teacher_subjects (teacher_id, subject_id, assigned_date) VALUES (?, ?, NOW())");
            $stmt->execute([$teacher_profile_id, $subject_id]);
        }
    }
    
    /**
     * Assign classes to a teacher - FIXED: Store in both tables
     */
    private function assignClassesToTeacher($user_id, $teacher_profile_id, $classes, $academic_year_id) {
        foreach ($classes as $class_data) {
            // Store in teacher_classes table
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO teacher_classes 
                                       (teacher_id, class_id, subject_id, academic_year_id, assigned_date) 
                                       VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $teacher_profile_id,
                $class_data['class_id'],
                $class_data['subject_id'],
                $academic_year_id
            ]);
            
            // ALSO store in class_subject_teachers table for compatibility
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO class_subject_teachers 
                                       (class_id, subject_id, teacher_id, academic_year_id, assigned_date) 
                                       VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $class_data['class_id'],
                $class_data['subject_id'],
                $user_id, // Use user_id here, not teacher_profile_id
                $academic_year_id
            ]);
        }
    }
    
    /**
     * Get teacher's assigned classes
     */
    public function getTeacherClasses($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT 
                c.id as class_id,
                c.class_name,
                c.section,
                s.id as subject_id,
                s.subject_name,
                s.subject_code,
                cst.assigned_date
            FROM class_subject_teachers cst
            JOIN classes c ON cst.class_id = c.id
            JOIN subjects s ON cst.subject_id = s.id
            WHERE cst.teacher_id = ? AND cst.is_active = 1 AND c.is_active = 1
            ORDER BY c.class_level, c.section, s.subject_name
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Validate teacher data
     */
    private function validateTeacherData($data) {
        if (empty($data['first_name']) || empty($data['last_name'])) {
            return ['valid' => false, 'message' => 'First name and last name are required'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Invalid email address'];
        }
        
        if (empty($data['phone']) || strlen(preg_replace('/[^0-9]/', '', $data['phone'])) < 10) {
            return ['valid' => false, 'message' => 'Valid phone number is required (minimum 10 digits)'];
        }
        
        // Check if email already exists
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['valid' => false, 'message' => 'Email address already exists'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Validate student data
     */
    private function validateStudentData($data) {
        if (empty($data['first_name']) || empty($data['last_name'])) {
            return ['valid' => false, 'message' => 'First name and last name are required'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Invalid email address'];
        }
        
        if (empty($data['phone']) || strlen(preg_replace('/[^0-9]/', '', $data['phone'])) < 10) {
            return ['valid' => false, 'message' => 'Valid phone number is required (minimum 10 digits)'];
        }
        
        if (empty($data['class_id'])) {
            return ['valid' => false, 'message' => 'Class selection is required'];
        }
        
        if (empty($data['date_of_birth'])) {
            return ['valid' => false, 'message' => 'Date of birth is required'];
        }
        
        if (empty($data['guardian_name']) || empty($data['guardian_phone'])) {
            return ['valid' => false, 'message' => 'Guardian name and phone are required'];
        }
        
        // Check if email already exists
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['valid' => false, 'message' => 'Email address already exists'];
        }
        
        // Validate age
        $birthDate = new DateTime($data['date_of_birth']);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        
        if ($age < 3 || $age > 25) {
            return ['valid' => false, 'message' => 'Student age must be between 3 and 25 years'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Generate strong password
     */
    private function generateStrongPassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
    
    /**
     * Get all subjects for teacher assignment
     */
    public function getAllSubjects() {
        $stmt = $this->pdo->query("SELECT id, subject_name, subject_code FROM subjects WHERE is_active = 1 ORDER BY subject_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all classes for assignment
     */
    public function getAllClasses() {
        $stmt = $this->pdo->query("SELECT id, class_name, section, class_level FROM classes WHERE is_active = 1 ORDER BY class_level, section");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Reset user password
     */
    public function resetPassword($user_id) {
        $new_password = $this->generateStrongPassword(10);
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ?, password_reset_required = 1 WHERE id = ?");
            $stmt->execute([$password_hash, $user_id]);
            
            $this->logActivity('password_reset', 'users', $user_id);
            
            return [
                'success' => true,
                'message' => 'Password reset successfully!',
                'new_password' => $new_password
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error resetting password: ' . $e->getMessage()];
        }
    }
    
    /**
     * Toggle user status
     */
    public function toggleUserStatus($user_id, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $stmt->execute([$status, $user_id]);
            
            $this->clearUserCaches();
            $status_text = $status ? 'activated' : 'deactivated';
            $this->logActivity('user_' . $status_text, 'users', $user_id);
            
            return [
                'success' => true,
                'message' => "User has been {$status_text} successfully!"
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating user status: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats() {
        if ($this->cache) {
            $cachedStats = $this->cache->getItem('user_stats');
            if ($cachedStats->isHit()) {
                return $cachedStats->get();
            }
        }
        
        $stats = [
            'total_users' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE role_id IN (2,3)")->fetchColumn(),
            'total_students' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 3")->fetchColumn(),
            'total_teachers' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn(),
            'active_users' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1 AND role_id IN (2,3)")->fetchColumn(),
            'inactive_users' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 0 AND role_id IN (2,3)")->fetchColumn()
        ];
        
        if ($this->cache) {
            $cachedStats->set($stats)->expiresAfter(3600);
            $this->cache->save($cachedStats);
        }
        
        return $stats;
    }
    
    /**
     * Clear user-related caches
     */
    private function clearUserCaches() {
        if ($this->cache) {
            $this->cache->deleteItems(['user_stats', 'user_list_all', 'class_list', 'subject_list']);
        }
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $table, $record_id) {
        if (function_exists('logActivity')) {
            logActivity($this->pdo, $action, $table, $record_id);
        }
    }
}
?>
