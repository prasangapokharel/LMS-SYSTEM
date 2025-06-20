<?php
namespace App\Models\headoffice;

require_once __DIR__ . '/../../../config/database.php';

class Student {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function getAllStudents() {
        $sql = "SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.address 
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                WHERE u.is_active = 1 
                ORDER BY s.id DESC";
        $result = $this->conn->query($sql);
        
        $students = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }
        return $students;
    }
    
    public function getStudentById($id) {
        $sql = "SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.address, u.username 
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    public function getStudentsByClassId($class_id) {
        $sql = "SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.address, u.username, 
                sc.enrollment_date, sc.status as enrollment_status
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                JOIN student_classes sc ON s.id = sc.student_id
                WHERE sc.class_id = ? AND u.is_active = 1
                ORDER BY u.first_name, u.last_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Calculate attendance percentage
                $attendance_sql = "SELECT 
                    COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count,
                    COUNT(*) as total_count
                    FROM attendance 
                    WHERE student_id = ? AND class_id = ?";
                $att_stmt = $this->conn->prepare($attendance_sql);
                $att_stmt->bind_param("ii", $row['id'], $class_id);
                $att_stmt->execute();
                $att_result = $att_stmt->get_result();
                $att_data = $att_result->fetch_assoc();
                
                if ($att_data['total_count'] > 0) {
                    $row['attendance_percentage'] = round(($att_data['present_count'] / $att_data['total_count']) * 100);
                } else {
                    $row['attendance_percentage'] = 0;
                }
                
                $students[] = $row;
            }
        }
        return $students;
    }
    
    public function countStudentsByClassId($class_id) {
        $sql = "SELECT COUNT(*) as count FROM student_classes WHERE class_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['count'];
    }
    
    public function addStudentToClass($student_id, $class_id, $academic_year_id) {
        // Check if student is already in this class
        $check_sql = "SELECT id FROM student_classes WHERE student_id = ? AND class_id = ? AND academic_year_id = ?";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->bind_param("iii", $student_id, $class_id, $academic_year_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            return false; // Student already in class
        }
        
        $sql = "INSERT INTO student_classes (student_id, class_id, academic_year_id, enrollment_date) 
                VALUES (?, ?, ?, CURDATE())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $student_id, $class_id, $academic_year_id);
        return $stmt->execute();
    }
    
    public function removeStudentFromClass($student_id, $class_id) {
        $sql = "DELETE FROM student_classes WHERE student_id = ? AND class_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $student_id, $class_id);
        return $stmt->execute();
    }
    
    public function getAvailableStudentsForClass($class_id, $academic_year_id) {
        $sql = "SELECT s.*, u.first_name, u.last_name 
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id NOT IN (
                    SELECT student_id FROM student_classes 
                    WHERE class_id = ? AND academic_year_id = ?
                ) 
                AND u.is_active = 1
                ORDER BY u.first_name, u.last_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $class_id, $academic_year_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }
        return $students;
    }
}
