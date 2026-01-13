<?php
class Ministry {
    private $db;
    private $table = 'ministries';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Fetch all active ministries
    //=======EXISTING METHODS (for import members) ==============
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


    //=====================NEW METHODS FOR MINISTRIES PAGE ======
     public function getAllDetails(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

     public function createFull(array $data): int {
        $sql = "INSERT INTO {$this->table} 
                (name, description, leader_email, meeting_day, meeting_time, location, status)
                VALUES 
                (:name, :description, :leader_email, :meeting_day, :meeting_time, :location, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':leader_email' => $data['leader_email'] ?? null,
            ':meeting_day' => $data['meeting_day'] ?? null,
            ':meeting_time' => $data['meeting_time'] ?? null,
            ':location' => $data['location'] ?? null,
            ':status' => $data['status'] ?? 'active'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} SET
                    name = :name,
                    description = :description,
                    meeting_day = :meeting_day,
                    meeting_time = :meeting_time,
                    location = :location,
                    leader_email = :leader_email,
                    status = :status
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'         => $data['name'],
            ':description'  => $data['description'] ?? null,
            ':meeting_day'  => $data['meeting_day'] ?? null,
            ':meeting_time' => $data['meeting_time'] ?? null,
            ':location'     => $data['location'] ?? null,
            ':leader_email' => $data['leader_email'] ?? null,
            ':status'       => $data['status'] ?? 'active',
            ':id'           => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
?>
