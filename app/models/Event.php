<?php
// Event Model

class Event {
    private $db;
    private $table = 'events';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (title, description, event_date, location, capacity, organizer_id, status)
                VALUES (:title, :description, :event_date, :location, :capacity, :organizer_id, :status)";
        
        $stmt = $this->db->prepare($sql);
        //Preparing variables for insertion
        $title = $data['title'];
        $description = $data['description'];
        $event_date = $data['event_date'];
        $location = $data['location'];
        $capacity = $data['capacity'];
        $organizer_id = $data['organizer_id'];
        $status = $data['status'] ?? 'scheduled';


        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':event_date', $event_date);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY event_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function softDelete($id){
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'cancelled' WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
// Permanently delete an event
    public function hardDelete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function edit($id, $data) {
        $sql = "UPDATE {$this->table} SET title = :title, description = :description, 
                event_date = :event_date, location = :location, capacity = :capacity, 
                organizer_id = :organizer_id, status = :status, updated_at=NOW() WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        //Preparing variables for update
        $title = $data['title'];
        $description = $data['description'];
        $event_date = $data['event_date'];
        $location = $data['location'];
        $capacity = $data['capacity'];
        $organizer_id = $data['organizer_id'];
        $status = $data['status'] ?? 'scheduled';


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':event_date', $event_date);
        $stmt->bindParam(':location', $location);   
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalScheduledCount() {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE status = 'scheduled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getRecentEvents($limit = 5) {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'scheduled' ORDER BY event_date DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
