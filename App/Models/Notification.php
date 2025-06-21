<?php
class NotificationSystem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Create notification record
    public function createNotification($user_id, $title, $message, $type = 'info', $data = null) {
        try {
            // Debug: Log the attempt
            error_log("Creating notification for user_id: $user_id, title: $title");
            
            $stmt = $this->pdo->prepare("INSERT INTO notifications 
                                        (user_id, title, message, type, data, created_at) 
                                        VALUES (?, ?, ?, ?, ?, NOW())");
            
            $dataJson = $data ? json_encode($data) : null;
            $result = $stmt->execute([$user_id, $title, $message, $type, $dataJson]);
            
            if (!$result) {
                error_log("Failed to insert notification: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            $notification_id = $this->pdo->lastInsertId();
            error_log("Notification created with ID: $notification_id");
            
            // Try to send real-time notification
            $this->sendRealTimeNotification($user_id, [
                'id' => $notification_id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'timestamp' => time()
            ]);
            
            return $notification_id;
        } catch (PDOException $e) {
            error_log("Notification creation error: " . $e->getMessage());
            return false;
        }
    }
    
    // Send exam reminder notifications
    public function sendExamReminders() {
        try {
            // Get exams happening in next 24 hours
            $stmt = $this->pdo->prepare("
                SELECT e.*, c.class_name, c.section,
                       GROUP_CONCAT(s.subject_name SEPARATOR ', ') as subjects
                FROM exams e
                JOIN classes c ON e.class_id = c.id
                LEFT JOIN exam_subjects es ON e.id = es.exam_id
                LEFT JOIN subjects s ON es.subject_id = s.id
                WHERE e.status = 'scheduled' 
                AND DATEDIFF(e.exam_date_start, CURDATE()) = 1
                GROUP BY e.id
            ");
            $stmt->execute();
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($exams as $exam) {
                // Get all students in this class
                $stmt = $this->pdo->prepare("
                    SELECT DISTINCT u.id, u.first_name, u.last_name, u.email
                    FROM students st
                    JOIN users u ON st.user_id = u.id
                    JOIN student_classes sc ON st.id = sc.student_id
                    WHERE sc.class_id = ? AND sc.status = 'enrolled'
                ");
                $stmt->execute([$exam['class_id']]);
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($students as $student) {
                    $this->createNotification(
                        $student['id'],
                        "📝 Exam Tomorrow!",
                        "Your {$exam['exam_name']} exam is scheduled for tomorrow. Subjects: {$exam['subjects']}",
                        'exam_reminder',
                        [
                            'exam_id' => $exam['id'],
                            'exam_name' => $exam['exam_name'],
                            'exam_date' => $exam['exam_date_start'],
                            'class' => $exam['class_name'] . ' ' . $exam['section']
                        ]
                    );
                }
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Exam reminder error: " . $e->getMessage());
            return false;
        }
    }
    
    // Send real-time notification using Server-Sent Events
    private function sendRealTimeNotification($user_id, $notification) {
        // Store in session for real-time delivery
        if (!isset($_SESSION['pending_notifications'])) {
            $_SESSION['pending_notifications'] = [];
        }
        
        if (!isset($_SESSION['pending_notifications'][$user_id])) {
            $_SESSION['pending_notifications'][$user_id] = [];
        }
        
        $_SESSION['pending_notifications'][$user_id][] = $notification;
        error_log("Real-time notification queued for user: $user_id");
    }
    
    // Get user notifications
    public function getUserNotifications($user_id, $limit = 20) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM notifications 
                                        WHERE user_id = ? 
                                        ORDER BY created_at DESC 
                                        LIMIT ?");
            $stmt->execute([$user_id, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get notifications error: " . $e->getMessage());
            return [];
        }
    }
    
    // Mark notification as read
    public function markAsRead($notification_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE notifications 
                                        SET is_read = 1, read_at = NOW() 
                                        WHERE id = ? AND user_id = ?");
            return $stmt->execute([$notification_id, $user_id]);
        } catch (PDOException $e) {
            error_log("Mark notification read error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get unread notification count
    public function getUnreadCount($user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications 
                                        WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Get unread count error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
