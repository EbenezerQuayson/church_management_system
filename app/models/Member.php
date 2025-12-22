<?php
// Member Model

class Member {
    private $db;
    private $table = 'members';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET first_name = :first_name, last_name = :last_name, 
                email = :email, phone = :phone, date_of_birth = :date_of_birth, 
                gender = :gender, address = :address, city = :city, state = :state, 
                zip_code = :zip_code, updated_at = NOW() WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $first_name =  $data['first_name'];
        $last_name =  $data['last_name'];  
        $email =  $data['email'];
        $phone =  $data['phone'];
        $date_of_birth =  $data['date_of_birth'];
        $gender =  $data['gender'];
        $address =  $data['address'];
        $city =  $data['city'];
        $state =  $data['state'];
        $zip_code =  $data['zip_code'];

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':zip_code', $zip_code);
        
        return $stmt->execute();
    }


    public function create($data) {
        //Check if  email already exists
        $checkSql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bindParam(':email', $data['email']);
        $checkStmt->execute();
        $emailExists = $checkStmt->fetchColumn();

        if ($emailExists > 0) {
            return false; // Email already exists
        }


        $sql = "INSERT INTO {$this->table} (first_name, last_name, email, phone, date_of_birth, gender, join_date, address, city, state, zip_code)
                VALUES (:first_name, :last_name, :email, :phone, :date_of_birth, :gender, :join_date, :address, :city, :state, :zip_code)";
        
        $stmt = $this->db->prepare($sql);
        $first_name =  $data['first_name'];
        $last_name =  $data['last_name'];  
        $email =  $data['email'];
        $phone =  $data['phone'];
        $date_of_birth =  $data['date_of_birth'];
        $gender =  $data['gender'];
        $join_date =  $data['join_date'];
        $address =  $data['address'];
        $city =  $data['city'];
        $state =  $data['state'];
        $zip_code =  $data['zip_code'];


        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':join_date', $join_date);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':zip_code', $zip_code);
        
        return $stmt->execute();
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC";
        
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

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET status = 'inactive', updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function permanentDelete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function restore($id) {
       $sql = "UPDATE {$this->table} SET status = 'active', updated_at = NOW() WHERE id = :id";
       $stmt = $this->db->prepare($sql);
       $stmt->bindParam(':id', $id);
       return $stmt->execute();

    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getMaleCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE gender = 'male' AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getFemaleCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE gender = 'female' AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getRecentMembers($limit = 5) {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (first_name LIKE :keyword OR last_name LIKE :keyword OR email LIKE :keyword) 
                AND status = 'active' 
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $likeKeyword = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $likeKeyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
