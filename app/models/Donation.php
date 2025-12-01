<?php
// Donation Model

class Donation {
    private $db;
    private $table = 'donations';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $member_id = $data['member_id'] ?? null;

        $sql = "INSERT INTO {$this->table} (member_id, amount, donation_type, donation_date, notes)
                VALUES (:member_id, :amount, :donation_type, :donation_date, :notes)";
        $stmt = $this->db->prepare($sql);
        
        //Preparing variables for insertion
        $amount = $data['amount']; 
        $donation_type = $data['donation_type'];
        $donation_date = $data['donation_date'];
        $notes = $data['notes'] ?? null;

        $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':donation_type', $donation_type);
        $stmt->bindParam(':donation_date', $donation_date);
        $stmt->bindParam(':notes', $notes);
        
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT d.*, m.first_name, m.last_name FROM {$this->table} d
                LEFT JOIN members m ON d.member_id = m.id
                ORDER BY d.donation_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalByMonth($month = null, $year = null) {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');
        
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM {$this->table}
                WHERE MONTH(donation_date) = :month AND YEAR(donation_date) = :year";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalAmount() {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function find($id) {
    $sql = "SELECT d.*, m.first_name, m.last_name 
            FROM donations d 
            LEFT JOIN members m ON d.member_id = m.id
            WHERE d.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
?>
