<?php
// Attendance Model

class Attendance {
    private $db;
    private $table = 'attendance';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function recordAttendance($data) {
        $sql = "INSERT INTO {$this->table} (member_id, attendance_date, status, notes)
                VALUES (:member_id, :attendance_date, :status, :notes)
                ON DUPLICATE KEY UPDATE status = :status, notes = :notes";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':member_id', $data['member_id']);
        $stmt->bindParam(':attendance_date', $data['attendance_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':notes', $data['notes']);
        
        return $stmt->execute();
    }

    public function getByDate($date) {
        $sql = "SELECT a.*, m.first_name, m.last_name FROM {$this->table} a
                LEFT JOIN members m ON a.member_id = m.id
                WHERE a.attendance_date = :date
                ORDER BY m.first_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMemberAttendance($member_id, $limit = 12) {
        $sql = "SELECT * FROM {$this->table}
                WHERE member_id = :member_id
                ORDER BY attendance_date DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':member_id', $member_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
