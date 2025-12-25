<?php
class Ministry {
    private $db;
    private $table = 'ministries';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Fetch all active ministries
    public function getAllActive() {
        $sql = "SELECT id, name FROM {$this->table} WHERE status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get ministry ID by name, return null if not found
    public function getIdByName(string $name): ?int {
        $sql = "SELECT id FROM {$this->table} WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id'] : null;
    }

    // Create a new ministry and return its ID
    public function create(string $name): int {
        $sql = "INSERT INTO {$this->table} (name, status) VALUES (:name, 'active')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name]);
        return (int)$this->db->lastInsertId();
    }
}
?>
