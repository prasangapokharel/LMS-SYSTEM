<?php
class StudentPromotion {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create new academic year and promote all students
     */
    public function promoteAllStudents($new_academic_year, $promotion_date = null) {
        if (!$promotion_date) {
            $promotion_date = date('Y-m-d'); // Default to today
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // 1. Check if academic year already exists
            $existing_year = $this->getAcademicYearByName($new_academic_year);
            if ($existing_year) {
                throw new Exception("Academic year '{$new_academic_year}' already exists");
            }
            
            // 2. Create new academic year
            $new_year_id = $this->createNewAcademicYear($new_academic_year, $promotion_date);
            
            // 3. Get all students with their current classes
            $current_students = $this->getCurrentStudents();
            
            if (empty($current_students)) {
                throw new Exception("No students found for promotion");
            }
            
            $promotion_results = [
                'promoted' => 0,
                'graduated' => 0,
                'failed' => 0,
                'errors' => []
            ];
            
            // 4. Process each student
            foreach ($current_students as $student) {
                try {
                    $result = $this->promoteStudent($student, $new_year_id, $promotion_date);
                    $promotion_results[$result['status']]++;
                    
                    // Log the promotion
                    $this->logPromotion($student['student_id'], $student['current_class_id'], 
                                      $result['new_class_id'], $new_year_id, $result['status']);
                    
                } catch (Exception $e) {
                    $promotion_results['errors'][] = [
                        'student' => $student['first_name'] . ' ' . $student['last_name'],
                        'error' => $e->getMessage()
                    ];
                    $promotion_results['failed']++;
                }
            }
            
            // 5. Mark previous academic year as inactive (only if promotion was successful)
            if ($promotion_results['promoted'] > 0 || $promotion_results['graduated'] > 0) {
                $this->deactivatePreviousAcademicYear($new_year_id);
            }
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Mass promotion completed successfully!',
                'results' => $promotion_results,
                'new_academic_year_id' => $new_year_id
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Promotion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get academic year by name
     */
    private function getAcademicYearByName($year_name) {
        $stmt = $this->pdo->prepare("SELECT * FROM academic_years WHERE year_name = ?");
        $stmt->execute([$year_name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new academic year
     */
    private function createNewAcademicYear($year_name, $start_date) {
        // Create new academic year (not marked as current yet)
        $end_date = date('Y-m-d', strtotime($start_date . ' +1 year -1 day'));
        
        $stmt = $this->pdo->prepare("
            INSERT INTO academic_years (year_name, start_date, end_date, is_current, created_at) 
            VALUES (?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$year_name, $start_date, $end_date]);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Get all currently enrolled students
     */
    private function getCurrentStudents() {
        $stmt = $this->pdo->prepare("
            SELECT 
                s.id as student_id,
                s.user_id,
                u.first_name,
                u.last_name,
                sc.class_id as current_class_id,
                c.class_name,
                c.section,
                c.class_level,
                sc.academic_year_id as current_academic_year_id,
                sc.status as enrollment_status
            FROM students s
            JOIN users u ON s.user_id = u.id
            JOIN student_classes sc ON s.id = sc.student_id
            JOIN classes c ON sc.class_id = c.id
            JOIN academic_years ay ON sc.academic_year_id = ay.id
            WHERE ay.is_current = 1 
            AND sc.status = 'enrolled'
            AND u.is_active = 1
            AND s.status = 'active'
            ORDER BY c.class_level, c.section, u.last_name, u.first_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Promote individual student
     */
    private function promoteStudent($student, $new_academic_year_id, $promotion_date) {
        $current_class_level = intval($student['class_level']);
        $current_section = $student['section'];
        
        // Define promotion rules
        $promotion_rules = $this->getPromotionRules();
        
        // Check if student is in the highest class (graduation)
        if ($current_class_level >= $promotion_rules['max_class_level']) {
            // Graduate the student
            $this->graduateStudent($student['student_id'], $new_academic_year_id, $promotion_date);
            return [
                'status' => 'graduated',
                'new_class_id' => null
            ];
        }
        
        // Find next class
        $next_class_level = $current_class_level + 1;
        $next_class = $this->findNextClass($next_class_level, $current_section);
        
        if (!$next_class) {
            throw new Exception("Next class not found for level {$next_class_level}, section {$current_section}");
        }
        
        // Check if student is already enrolled in the new academic year
        if ($this->isStudentAlreadyEnrolled($student['student_id'], $new_academic_year_id)) {
            throw new Exception("Student already enrolled in the new academic year");
        }
        
        // Enroll student in next class
        $this->enrollStudentInClass($student['student_id'], $next_class['id'], $new_academic_year_id, $promotion_date);
        
        // Mark previous enrollment as promoted
        $this->updatePreviousEnrollmentStatus($student['student_id'], $student['current_academic_year_id'], 'promoted');
        
        return [
            'status' => 'promoted',
            'new_class_id' => $next_class['id']
        ];
    }

    /**
     * Check if student is already enrolled in academic year
     */
    private function isStudentAlreadyEnrolled($student_id, $academic_year_id) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM student_classes 
            WHERE student_id = ? AND academic_year_id = ?
        ");
        $stmt->execute([$student_id, $academic_year_id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Update previous enrollment status
     */
    private function updatePreviousEnrollmentStatus($student_id, $academic_year_id, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE student_classes 
            SET status = ? 
            WHERE student_id = ? AND academic_year_id = ? AND status = 'enrolled'
        ");
        $stmt->execute([$status, $student_id, $academic_year_id]);
        
        // Also update student_enrollments if it exists
        $stmt = $this->pdo->prepare("
            UPDATE student_enrollments 
            SET status = ? 
            WHERE student_id = ? AND academic_year_id = ? AND status = 'enrolled'
        ");
        $stmt->execute([$status, $student_id, $academic_year_id]);
    }

    /**
     * Get promotion rules
     */
    private function getPromotionRules() {
        return [
            'max_class_level' => 12, // Class 12 is the highest
            'auto_promote' => true,
            'maintain_section' => true // Keep students in same section when promoting
        ];
    }

    /**
     * Find next class for promotion
     */
    private function findNextClass($class_level, $current_section) {
        $stmt = $this->pdo->prepare("
            SELECT id, class_name, section, class_level 
            FROM classes 
            WHERE class_level = ? AND section = ? AND is_active = 1
            LIMIT 1
        ");
        $stmt->execute([$class_level, $current_section]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If exact section not found, try to find any section for that level
        if (!$result) {
            $stmt = $this->pdo->prepare("
                SELECT id, class_name, section, class_level 
                FROM classes 
                WHERE class_level = ? AND is_active = 1
                ORDER BY section
                LIMIT 1
            ");
            $stmt->execute([$class_level]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $result;
    }

    /**
     * Enroll student in new class
     */
    private function enrollStudentInClass($student_id, $class_id, $academic_year_id, $enrollment_date) {
        try {
            // Insert into student_classes
            $stmt = $this->pdo->prepare("
                INSERT INTO student_classes 
                (student_id, class_id, academic_year_id, enrollment_date, status) 
                VALUES (?, ?, ?, ?, 'enrolled')
                ON DUPLICATE KEY UPDATE 
                class_id = VALUES(class_id),
                enrollment_date = VALUES(enrollment_date),
                status = VALUES(status)
            ");
            $stmt->execute([$student_id, $class_id, $academic_year_id, $enrollment_date]);
            
            // Also add to student_enrollments table for compatibility
            $stmt = $this->pdo->prepare("
                INSERT INTO student_enrollments 
                (student_id, class_id, academic_year_id, enrollment_date, status) 
                VALUES (?, ?, ?, ?, 'enrolled')
                ON DUPLICATE KEY UPDATE 
                class_id = VALUES(class_id),
                enrollment_date = VALUES(enrollment_date),
                status = VALUES(status)
            ");
            $stmt->execute([$student_id, $class_id, $academic_year_id, $enrollment_date]);
            
        } catch (PDOException $e) {
            throw new Exception("Failed to enroll student: " . $e->getMessage());
        }
    }

    /**
     * Graduate student (mark as completed)
     */
    private function graduateStudent($student_id, $academic_year_id, $graduation_date) {
        // Insert graduation record
        $stmt = $this->pdo->prepare("
            INSERT INTO student_graduations 
            (student_id, academic_year_id, graduation_date, status) 
            VALUES (?, ?, ?, 'graduated')
        ");
        $stmt->execute([$student_id, $academic_year_id, $graduation_date]);
        
        // Update student status
        $stmt = $this->pdo->prepare("
            UPDATE students 
            SET status = 'graduated', graduation_date = ? 
            WHERE id = ?
        ");
        $stmt->execute([$graduation_date, $student_id]);
        
        // Mark current enrollment as graduated
        $current_year_stmt = $this->pdo->prepare("
            SELECT id FROM academic_years WHERE is_current = 1
        ");
        $current_year_stmt->execute();
        $current_year = $current_year_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($current_year) {
            $this->updatePreviousEnrollmentStatus($student_id, $current_year['id'], 'graduated');
        }
    }

    /**
     * Deactivate previous academic year and activate new one
     */
    private function deactivatePreviousAcademicYear($new_year_id) {
        // First, deactivate all current years
        $stmt = $this->pdo->prepare("UPDATE academic_years SET is_current = 0 WHERE is_current = 1");
        $stmt->execute();
        
        // Then activate the new year
        $stmt = $this->pdo->prepare("UPDATE academic_years SET is_current = 1 WHERE id = ?");
        $stmt->execute([$new_year_id]);
    }

    /**
     * Log promotion activity
     */
    private function logPromotion($student_id, $from_class_id, $to_class_id, $academic_year_id, $status) {
        $stmt = $this->pdo->prepare("
            INSERT INTO student_promotions 
            (student_id, from_class_id, to_class_id, academic_year_id, promotion_date, status, created_at) 
            VALUES (?, ?, ?, ?, NOW(), ?, NOW())
        ");
        $stmt->execute([$student_id, $from_class_id, $to_class_id, $academic_year_id, $status]);
    }

    /**
     * Get promotion history
     */
    public function getPromotionHistory($academic_year_id = null) {
        $where_clause = $academic_year_id ? "WHERE sp.academic_year_id = ?" : "";
        $params = $academic_year_id ? [$academic_year_id] : [];
        
        $stmt = $this->pdo->prepare("
            SELECT 
                sp.*,
                u.first_name,
                u.last_name,
                s.student_id,
                c1.class_name as from_class,
                c1.section as from_section,
                c2.class_name as to_class,
                c2.section as to_section,
                ay.year_name
            FROM student_promotions sp
            JOIN students s ON sp.student_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN classes c1 ON sp.from_class_id = c1.id
            LEFT JOIN classes c2 ON sp.to_class_id = c2.id
            JOIN academic_years ay ON sp.academic_year_id = ay.id
            {$where_clause}
            ORDER BY sp.promotion_date DESC, u.last_name, u.first_name
            LIMIT 100
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Manual promotion for individual student
     */
    public function promoteIndividualStudent($student_id, $to_class_id, $academic_year_id, $notes = '') {
        try {
            $this->pdo->beginTransaction();
            
            // Get current enrollment
            $stmt = $this->pdo->prepare("
                SELECT sc.*, c.class_name, c.section, ay.year_name
                FROM student_classes sc 
                JOIN classes c ON sc.class_id = c.id
                JOIN academic_years ay ON sc.academic_year_id = ay.id
                WHERE sc.student_id = ? AND ay.is_current = 1 AND sc.status = 'enrolled'
            ");
            $stmt->execute([$student_id]);
            $current_enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$current_enrollment) {
                throw new Exception("Student not found or not currently enrolled");
            }
            
            // Check if already enrolled in target academic year
            if ($this->isStudentAlreadyEnrolled($student_id, $academic_year_id)) {
                throw new Exception("Student is already enrolled in the target academic year");
            }
            
            // Enroll in new class
            $this->enrollStudentInClass($student_id, $to_class_id, $academic_year_id, date('Y-m-d'));
            
            // Update previous enrollment status
            $this->updatePreviousEnrollmentStatus($student_id, $current_enrollment['academic_year_id'], 'promoted');
            
            // Log manual promotion
            $this->logPromotion($student_id, $current_enrollment['class_id'], $to_class_id, $academic_year_id, 'manual_promotion');
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Student promoted successfully!'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Promotion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if promotion is needed (can be run as cron job)
     */
    public function checkAutoPromotion() {
        // Check if it's promotion time (e.g., Baisakh 1 in Nepali calendar)
        $current_date = date('Y-m-d');
        $promotion_settings = $this->getPromotionSettings();
        
        if ($this->isPromotionDate($current_date, $promotion_settings)) {
            $new_year = $this->generateNewAcademicYearName();
            return $this->promoteAllStudents($new_year, $current_date);
        }
        
        return [
            'success' => false,
            'message' => 'Not promotion time yet'
        ];
    }

    /**
     * Get promotion settings
     */
    private function getPromotionSettings() {
        $stmt = $this->pdo->prepare("SELECT * FROM system_settings WHERE setting_key LIKE 'promotion_%'");
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $promotion_settings = [];
        foreach ($settings as $setting) {
            $promotion_settings[$setting['setting_key']] = $setting['setting_value'];
        }
        
        // Default settings if not found
        if (empty($promotion_settings)) {
            $promotion_settings = [
                'promotion_month' => 4, // April
                'promotion_day' => 14,  // Baisakh 1
                'auto_promotion_enabled' => 1,
                'max_class_level' => 12
            ];
        }
        
        return $promotion_settings;
    }

    /**
     * Check if current date is promotion date
     */
    private function isPromotionDate($current_date, $settings) {
        // For Nepali calendar Baisakh 1, you would need to convert
        // For now, using a simple date check
        $promotion_month = $settings['promotion_month'] ?? 4; // April (Baisakh)
        $promotion_day = $settings['promotion_day'] ?? 14; // Baisakh 1 usually falls around April 14
        
        $current_month = date('n', strtotime($current_date));
        $current_day = date('j', strtotime($current_date));
        
        return ($current_month == $promotion_month && $current_day == $promotion_day);
    }

    /**
     * Generate new academic year name
     */
    private function generateNewAcademicYearName() {
        $current_year = date('Y');
        $next_year = $current_year + 1;
        return $current_year . '-' . $next_year;
    }

    /**
     * Get academic year statistics
     */
    public function getAcademicYearStats($academic_year_id = null) {
        if (!$academic_year_id) {
            $stmt = $this->pdo->prepare("SELECT id FROM academic_years WHERE is_current = 1");
            $stmt->execute();
            $current_year = $stmt->fetch(PDO::FETCH_ASSOC);
            $academic_year_id = $current_year['id'] ?? null;
        }
        
        if (!$academic_year_id) {
            return [
                'total_students' => 0,
                'promoted' => 0,
                'graduated' => 0,
                'active' => 0
            ];
        }
        
        $stats = [];
        
        // Total students in academic year
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM student_classes 
            WHERE academic_year_id = ?
        ");
        $stmt->execute([$academic_year_id]);
        $stats['total_students'] = $stmt->fetchColumn();
        
        // Promoted students
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM student_promotions 
            WHERE academic_year_id = ? AND status = 'promoted'
        ");
        $stmt->execute([$academic_year_id]);
        $stats['promoted'] = $stmt->fetchColumn();
        
        // Graduated students
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM student_promotions 
            WHERE academic_year_id = ? AND status = 'graduated'
        ");
        $stmt->execute([$academic_year_id]);
        $stats['graduated'] = $stmt->fetchColumn();
        
        // Active enrollments
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM student_classes 
            WHERE academic_year_id = ? AND status = 'enrolled'
        ");
        $stmt->execute([$academic_year_id]);
        $stats['active'] = $stmt->fetchColumn();
        
        return $stats;
    }
}
?>
