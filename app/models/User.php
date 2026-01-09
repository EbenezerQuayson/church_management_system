<?php
// User Model

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register($data) {
        $sql = "INSERT INTO {$this->table} (email, password, first_name, last_name, role_id, profile_photo) 
                VALUES (:email, :password, :first_name, :last_name, :role_id, :profile_photo)"; // Added profile_photo column
        
        $stmt = $this->db->prepare($sql);
        //Preparing variables for insertion
        $email = $data['email'];
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $firstName = $data['first_name'];
        $lastName = $data['last_name'];
        $roleId = $data['role_id'] ?? 4;
        $profilePhoto = $data['profile_photo'] ?? null; // New profile photo variable   


        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':role_id', $roleId);
        $stmt->bindParam(':profile_photo', $profilePhoto); // Bind profile photo
        
        return $stmt->execute();
    }

    public function findByEmail($email) {
        $sql = "SELECT u.*, r.name as role_name FROM {$this->table} u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email AND u.is_active = true";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT u.*, r.name as role_name FROM {$this->table} u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id AND u.is_active = true";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function update($id, $data)
{
    $sql = "UPDATE users SET
                first_name = :first_name,
                last_name  = :last_name,
                email      = :email,
                role_id    = :role_id,
                is_active  = :status,
                updated_at = NOW()
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':first_name', $data['first_name']);
    $stmt->bindValue(':last_name', $data['last_name']);
    $stmt->bindValue(':email', $data['email']);
    $stmt->bindValue(':role_id', $data['role_id'], PDO::PARAM_INT);
    $stmt->bindValue(':status', $data['status'], PDO::PARAM_INT);

    return $stmt->execute();
}


    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT u.*, r.name as role_name FROM {$this->table} u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.is_active = true
                ORDER BY u.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function getAllUsers() {
        $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.is_active as status,
                       r.name as role_name, u.role_id
                FROM {$this->table} u
                LEFT JOIN roles r ON u.role_id = r.id
                ORDER BY u.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function deactivate($id) {
    $sql = "UPDATE users SET is_active = 0 WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

}
?>
