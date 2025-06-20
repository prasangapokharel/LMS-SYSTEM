<?php
/**
 * Academic Analytics Model
 * Handles data retrieval and processing for academic analytics dashboard
 */

// Include necessary files for database connection and session
require_once __DIR__ . '/../../../include/connect.php';
require_once __DIR__ . '/../../../include/session.php';

class Academic {
    private $pdo;
    private $user;
    
    public function __construct($pdo, $user) {
        $this->pdo = $pdo;
        $this->user = $user;
    }
    
    /**
     * Get all active classes for filtering
     */
    public function getClasses() {
        $stmt = $this->pdo->query("SELECT id, class_name, section FROM classes WHERE is_active = 1 ORDER BY class_level, section");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all subjects for filtering
     */
    public function getSubjects() {
        $stmt = $this->pdo->query("SELECT id, subject_name FROM subjects ORDER BY subject_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get overall school statistics
     */
    public function getOverallStats() {
        // Check if teachers table exists
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'teachers'");
        $teachersTableExists = $stmt->rowCount() > 0;
        
        // Build query based on available tables
        $query = "SELECT 
                (SELECT COUNT(*) FROM students WHERE is_active = 1) as total_students,";
        
        if ($teachersTableExists) {
            $query .= "(SELECT COUNT(*) FROM teachers WHERE is_active = 1) as total_teachers,";
        } else {
            $query .= "(SELECT COUNT(*) FROM users WHERE role_id = 2 AND is_active = 1) as total_teachers,";
        }
        
        $query .= "(SELECT COUNT(*) FROM classes WHERE is_active = 1) as total_classes,
                  (SELECT COUNT(*) FROM subjects) as total_subjects";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get attendance statistics for the given period
     */
    public function getAttendanceStats($date_from, $date_to, $class_filter = null) {
        $sql = "SELECT 
              COUNT(*) as total_records,
              SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present,
              SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as total_absent,
              SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as total_late,
              ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as overall_percentage
              FROM attendance a
              JOIN students s ON a.student_id = s.id
              JOIN classes c ON a.class_id = c.id
              WHERE a.attendance_date BETWEEN ? AND ?";
        
        $params = [$date_from, $date_to];
        
        if ($class_filter) {
            $sql .= " AND c.id = ?";
            $params[] = $class_filter;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get class-wise attendance statistics
     */
    public function getClassAttendance($date_from, $date_to) {
        $stmt = $this->pdo->prepare("SELECT c.id, c.class_name, c.section,
              COUNT(DISTINCT s.id) as class_students,
              COUNT(*) as class_records,
              SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as class_present,
              ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as class_percentage
              FROM attendance a
              JOIN students s ON a.student_id = s.id
              JOIN classes c ON a.class_id = c.id
              WHERE a.attendance_date BETWEEN ? AND ?
              GROUP BY c.id, c.class_name, c.section
              ORDER BY class_percentage DESC");
        $stmt->execute([$date_from, $date_to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get subject-wise performance metrics
     */
    public function getSubjectPerformance($date_from, $date_to) {
        // Check if the assignment_submissions table has a score column or grade column
        $stmt = $this->pdo->query("SHOW COLUMNS FROM assignment_submissions LIKE 'score'");
        $hasScoreColumn = $stmt->rowCount() > 0;
        
        $scoreColumn = $hasScoreColumn ? 'score' : 'grade';
        
        $sql = "SELECT s.id, s.subject_name,
              COUNT(DISTINCT a.id) as total_assignments,
              AVG(sa.$scoreColumn) as avg_score,
              MAX(sa.$scoreColumn) as max_score,
              MIN(sa.$scoreColumn) as min_score
              FROM subjects s
              LEFT JOIN assignments a ON s.id = a.subject_id
              LEFT JOIN assignment_submissions sa ON a.id = sa.assignment_id
              WHERE (a.due_date BETWEEN ? AND ? OR a.due_date IS NULL)
              GROUP BY s.id, s.subject_name
              HAVING COUNT(DISTINCT a.id) > 0
              ORDER BY avg_score DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$date_from, $date_to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get teacher performance metrics
     */
    public function getTeacherPerformance($date_from, $date_to) {
        // Check if teachers table exists
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'teachers'");
        $teachersTableExists = $stmt->rowCount() > 0;
        
        // Build query based on available tables
        if ($teachersTableExists) {
            $sql = "SELECT u.id, u.first_name, u.last_name,
                  COUNT(DISTINCT tl.id) as total_logs,
                  COUNT(DISTINCT tl.class_id) as classes_taught,
                  COUNT(DISTINCT tl.subject_id) as subjects_taught,
                  AVG(tl.students_present) as avg_attendance,
                  SUM(tl.lesson_duration) as total_hours
                  FROM users u
                  JOIN teachers t ON u.id = t.user_id
                  LEFT JOIN teacher_logs tl ON u.id = tl.teacher_id
                  WHERE tl.log_date BETWEEN ? AND ?
                  GROUP BY u.id, u.first_name, u.last_name
                  ORDER BY total_logs DESC";
        } else {
            $sql = "SELECT u.id, u.first_name, u.last_name,
                  COUNT(DISTINCT tl.id) as total_logs,
                  COUNT(DISTINCT tl.class_id) as classes_taught,
                  COUNT(DISTINCT tl.subject_id) as subjects_taught,
                  AVG(tl.students_present) as avg_attendance,
                  SUM(tl.lesson_duration) as total_hours
                  FROM users u
                  LEFT JOIN teacher_logs tl ON u.id = tl.teacher_id
                  WHERE u.role_id = 2 AND tl.log_date BETWEEN ? AND ?
                  GROUP BY u.id, u.first_name, u.last_name
                  ORDER BY total_logs DESC";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$date_from, $date_to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get attendance trends over time
     */
    public function getAttendanceTrends($date_from, $date_to, $period = 'monthly', $class_filter = null) {
        $group_by = $period === 'weekly' ? 'YEARWEEK(a.attendance_date, 1)' : 'DATE_FORMAT(a.attendance_date, "%Y-%m")';
        $format = $period === 'weekly' ? 'Week %v, %Y' : '%b %Y';
        
        $sql = "SELECT 
              DATE_FORMAT(a.attendance_date, '$format') as period_label,
              $group_by as period_group,
              COUNT(*) as total_records,
              SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present,
              ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
              FROM attendance a
              JOIN classes c ON a.class_id = c.id
              WHERE a.attendance_date BETWEEN ? AND ?";
        
        $params = [$date_from, $date_to];
        
        if ($class_filter) {
            $sql .= " AND c.id = ?";
            $params[] = $class_filter;
        }
        
        $sql .= " GROUP BY period_group, period_label ORDER BY period_group";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get assignment submission rates by class
     */
    public function getSubmissionRates($date_from, $date_to, $class_filter = null) {
        $sql = "SELECT 
              c.class_name, c.section,
              COUNT(DISTINCT a.id) as total_assignments,
              COUNT(DISTINCT sa.id) as total_submissions,
              ROUND((COUNT(DISTINCT sa.id) / COUNT(DISTINCT a.id)) * 100, 2) as submission_rate
              FROM classes c
              JOIN assignments a ON c.id = a.class_id
              LEFT JOIN assignment_submissions sa ON a.id = sa.assignment_id
              WHERE a.due_date BETWEEN ? AND ?";
        
        $params = [$date_from, $date_to];
        
        if ($class_filter) {
            $sql .= " AND c.id = ?";
            $params[] = $class_filter;
        }
        
        $sql .= " GROUP BY c.id, c.class_name, c.section
                HAVING COUNT(DISTINCT a.id) > 0
                ORDER BY submission_rate DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get top performing students
     */
    public function getTopStudents($date_from, $date_to, $class_filter = null, $limit = 10) {
        // Check if the assignment_submissions table has a score column or grade column
        $stmt = $this->pdo->query("SHOW COLUMNS FROM assignment_submissions LIKE 'score'");
        $hasScoreColumn = $stmt->rowCount() > 0;
        
        $scoreColumn = $hasScoreColumn ? 'score' : 'grade';
        
        $sql = "SELECT 
              u.first_name, u.last_name, c.class_name, c.section,
              COUNT(DISTINCT sa.assignment_id) as assignments_completed,
              AVG(sa.$scoreColumn) as avg_score
              FROM users u
              JOIN students s ON u.id = s.user_id
              JOIN student_classes sc ON s.id = sc.student_id
              JOIN classes c ON sc.class_id = c.id
              JOIN assignment_submissions sa ON s.id = sa.student_id
              JOIN assignments a ON sa.assignment_id = a.id
              WHERE a.due_date BETWEEN ? AND ? AND sc.status = 'enrolled'";
        
        $params = [$date_from, $date_to];
        
        if ($class_filter) {
            $sql .= " AND c.id = ?";
            $params[] = $class_filter;
        }
        
        $sql .= " GROUP BY u.id, u.first_name, u.last_name, c.class_name, c.section
                HAVING COUNT(DISTINCT sa.assignment_id) > 0
                ORDER BY avg_score DESC
                LIMIT $limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Initialize database connection and user
if (!isset($pdo)) {
    global $pdo;
}

if (!isset($user)) {
    $user = getCurrentUser($pdo);
}

// Initialize the model when included
$academic = new Academic($pdo, $user);

// Get filter parameters
$class_filter = $_GET['class_id'] ?? '';
$subject_filter = $_GET['subject_id'] ?? '';
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-3 months'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');
$period = $_GET['period'] ?? 'monthly';

// Get data for the dashboard
$classes = $academic->getClasses();
$subjects = $academic->getSubjects();
$overall_stats = $academic->getOverallStats();
$attendance_stats = $academic->getAttendanceStats($date_from, $date_to, $class_filter);
$class_attendance = $academic->getClassAttendance($date_from, $date_to);
$attendance_trends = $academic->getAttendanceTrends($date_from, $date_to, $period, $class_filter);

// These queries might fail if tables don't exist or have different structure
try {
    $subject_performance = $academic->getSubjectPerformance($date_from, $date_to);
} catch (Exception $e) {
    $subject_performance = [];
}

try {
    $teacher_performance = $academic->getTeacherPerformance($date_from, $date_to);
} catch (Exception $e) {
    $teacher_performance = [];
}

try {
    $submission_rates = $academic->getSubmissionRates($date_from, $date_to, $class_filter);
} catch (Exception $e) {
    $submission_rates = [];
}

try {
    $top_students = $academic->getTopStudents($date_from, $date_to, $class_filter);
} catch (Exception $e) {
    $top_students = [];
}

?>
