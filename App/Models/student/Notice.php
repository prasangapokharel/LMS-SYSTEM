<?php
include_once '../include/connect.php';
include_once '../include/session.php';

requireRole('student');

$user = getCurrentUser($pdo);

class StudentNotice {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get all active notices
    public function getAllNotices() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.first_name, u.last_name
                FROM notices n
                JOIN users u ON n.created_by = u.id
                WHERE n.is_active = 1
                ORDER BY n.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get notices error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get single notice by ID
    public function getNoticeById($notice_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.first_name, u.last_name
                FROM notices n
                JOIN users u ON n.created_by = u.id
                WHERE n.id = ? AND n.is_active = 1
            ");
            $stmt->execute([$notice_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get notice by ID error: " . $e->getMessage());
            return null;
        }
    }
}

$student_notice = new StudentNotice($pdo);
$notices = $student_notice->getAllNotices();

// Handle AJAX request for single notice
if (isset($_GET['action']) && $_GET['action'] === 'get_notice' && isset($_GET['id'])) {
    $notice = $student_notice->getNoticeById($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($notice);
    exit;
}
?>
