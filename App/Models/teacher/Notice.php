<?php
include_once '../include/connect.php';
include_once '../include/session.php';

requireRole('teacher');

$user = getCurrentUser($pdo);
$msg = "";

class TeacherNotice {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Create new notice
    public function createNotice($data) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO notices 
                (title, content, notice_image, created_by, created_at) 
                VALUES (?, ?, ?, ?, NOW())");
            
            $result = $stmt->execute([
                $data['title'], 
                $data['content'], 
                $data['notice_image'], 
                $_SESSION['user_id']
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Notice creation error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get teacher's notices
    public function getTeacherNotices($teacher_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.first_name, u.last_name
                FROM notices n
                JOIN users u ON n.created_by = u.id
                WHERE n.created_by = ? AND n.is_active = 1
                ORDER BY n.created_at DESC
            ");
            $stmt->execute([$teacher_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get teacher notices error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get notice statistics
    public function getNoticeStats($teacher_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notices WHERE created_by = ? AND is_active = 1");
            $stmt->execute([$teacher_id]);
            $total_notices = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notices WHERE created_by = ? AND is_active = 1 AND DATE(created_at) = CURDATE()");
            $stmt->execute([$teacher_id]);
            $today_notices = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return [
                'total_notices' => $total_notices,
                'today_notices' => $today_notices
            ];
        } catch (PDOException $e) {
            error_log("Notice stats error: " . $e->getMessage());
            return [
                'total_notices' => 0,
                'today_notices' => 0
            ];
        }
    }
    
    // Delete notice
    public function deleteNotice($notice_id, $teacher_id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE notices SET is_active = 0 WHERE id = ? AND created_by = ?");
            return $stmt->execute([$notice_id, $teacher_id]);
        } catch (PDOException $e) {
            error_log("Delete notice error: " . $e->getMessage());
            return false;
        }
    }
}

// Handle form submissions
$teacher_notice = new TeacherNotice($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_notice':
                // Handle file upload
                $notice_image = null;
                if (isset($_FILES['notice_image']) && $_FILES['notice_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../uploads/notices/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['notice_image']['name'], PATHINFO_EXTENSION);
                    $filename = 'notice_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['notice_image']['tmp_name'], $upload_path)) {
                        $notice_image = 'uploads/notices/' . $filename;
                    }
                }
                
                $notice_data = [
                    'title' => $_POST['title'],
                    'content' => $_POST['content'],
                    'notice_image' => $notice_image
                ];
                
                $result = $teacher_notice->createNotice($notice_data);
                
                if ($result) {
                    $msg = '<div class="alert alert-success">Notice created successfully!</div>';
                } else {
                    $msg = '<div class="alert alert-danger">Failed to create notice. Please try again.</div>';
                }
                break;
                
            case 'delete_notice':
                $result = $teacher_notice->deleteNotice($_POST['notice_id'], $_SESSION['user_id']);
                
                if ($result) {
                    $msg = '<div class="alert alert-success">Notice deleted successfully!</div>';
                } else {
                    $msg = '<div class="alert alert-danger">Failed to delete notice.</div>';
                }
                break;
        }
    }
}

// Get notices and stats
$notices = $teacher_notice->getTeacherNotices($_SESSION['user_id']);
$stats = $teacher_notice->getNoticeStats($_SESSION['user_id']);
?>
