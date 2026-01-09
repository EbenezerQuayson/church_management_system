<?php
// Member Model

class Member {
    private $db;
    private $table = 'members';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function update($id, $data, $ministries = []) {
    // Update member info
    $sql = "UPDATE {$this->table} 
            SET first_name = :first_name,
                last_name  = :last_name,
                email      = :email,
                phone      = :phone,
                date_of_birth = :date_of_birth,
                gender     = :gender,
                join_date  = :join_date,
                address    = :address,
                city       = :city,
                region     = :region,
                area       = :area,
                landmark   = :landmark,
                gps        = :gps,
                emergency_contact_name = :emergency_contact_name,
                emergency_phone = :emergency_phone
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);

    $stmt->bindParam(':first_name', $data['first_name']);
    $stmt->bindParam(':last_name', $data['last_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
    $stmt->bindParam(':gender', $data['gender']);
    $stmt->bindParam(':join_date', $data['join_date']);
    $stmt->bindParam(':address', $data['address']);
    $stmt->bindParam(':city', $data['city']);
    $stmt->bindParam(':region', $data['region']);
    $stmt->bindParam(':area', $data['area']);
    $stmt->bindParam(':landmark', $data['landmark']);
    $stmt->bindParam(':gps', $data['gps']);
    $stmt->bindParam(':emergency_contact_name', $data['emergency_contact_name']);
    $stmt->bindParam(':emergency_phone', $data['emergency_phone']);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    $updated = $stmt->execute();

    if (!$updated) return false;

    //Handle image update if needed
    if (!empty($data['member_img'])) {
        $imgSql = "UPDATE {$this->table} SET member_img = :member_img WHERE id = :id";
        $imgStmt = $this->db->prepare($imgSql);
        $imgStmt->bindParam(':member_img', $data['member_img']);
        $imgStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $imgStmt->execute();
    }

    //Update ministries
    // Delete previous entries
    $deleteSql = "DELETE FROM ministry_members WHERE member_id = :member_id";
    $deleteStmt = $this->db->prepare($deleteSql);
    $deleteStmt->bindParam(':member_id', $id, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Insert new selections
    if (!empty($ministries) && is_array($ministries)) {
        $insertSql = "INSERT INTO ministry_members (member_id, ministry_id, role, joined_date, created_at)
                      VALUES (:member_id, :ministry_id, :role, :joined_date, NOW())";
        $insertStmt = $this->db->prepare($insertSql);

        foreach ($ministries as $ministryId) {
            $role = 'Member'; // default role
            $joinedDate = $data['join_date'] ?? date('Y-m-d');

            $insertStmt->bindParam(':member_id', $id);
            $insertStmt->bindParam(':ministry_id', $ministryId);
            $insertStmt->bindParam(':role', $role);
            $insertStmt->bindParam(':joined_date', $joinedDate);

            $insertStmt->execute();
        }
    }

    return true;
}


public function create($data)
{

    // Generate a unique member code
    $memberCode = 'HEB-' . strtoupper(bin2hex(random_bytes(4))); // Example: HEB-A1B2C3D4

    // Check if email already exists
    $checkSql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
    $checkStmt = $this->db->prepare($checkSql);
    $checkStmt->bindParam(':email', $data['email']);
    $checkStmt->execute();

    if ($checkStmt->fetchColumn() > 0) {
        return false;
    }

    $sql = "INSERT INTO {$this->table} (
        member_code,
        first_name,
        last_name,
        email,
        phone,
        date_of_birth,
        gender,
        join_date,
        address,
        city,
        region,
        area,
        landmark,
        gps,
        emergency_contact_name,
        emergency_phone,
        member_img
    ) VALUES (
        :member_code,
        :first_name,
        :last_name,
        :email,
        :phone,
        :date_of_birth,
        :gender,
        :join_date,
        :address,
        :city,
        :region,
        :area,
        :landmark,
        :gps,
        :emergency_contact_name,
        :emergency_phone,
        :member_img
    )";

    $stmt = $this->db->prepare($sql);

    $stmt->bindParam(':member_code', $memberCode);
    $stmt->bindParam(':first_name', $data['first_name']);
    $stmt->bindParam(':last_name', $data['last_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
    $stmt->bindParam(':gender', $data['gender']);
    $stmt->bindParam(':join_date', $data['join_date']);
    $stmt->bindParam(':address', $data['address']);
    $stmt->bindParam(':city', $data['city']);
    $stmt->bindParam(':region', $data['region']);
    $stmt->bindParam(':area', $data['area']);
    $stmt->bindParam(':landmark', $data['landmark']);
    $stmt->bindParam(':gps', $data['gps']);
    $stmt->bindParam(':emergency_contact_name', $data['emergency_contact_name']);
    $stmt->bindParam(':emergency_phone', $data['emergency_phone']);
    $stmt->bindParam(':member_img', $data['member_img']);

    if ($stmt->execute()) {
        return $this->db->lastInsertId();
        return $memberCode;
    }

    return false;
}


    public function assignMinistry($memberId, $ministryId)
{
    $stmt = $this->db->prepare("
        INSERT INTO ministry_member (member_id, ministry_id)
        VALUES (?, ?)
    ");

    return $stmt->execute([$memberId, $ministryId]);
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


    public function getMemberMinistries($memberId) {
    $sql = "SELECT m.name 
            FROM ministries m
            JOIN ministry_members mm ON mm.ministry_id = m.id
            WHERE mm.member_id = :member_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':member_id', $memberId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

public function getMinistries($memberId) {
    $sql = "SELECT ministry_id FROM ministry_members WHERE member_id = :member_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // Returns array of ministry IDs
}


public function updateMinistries($memberId, $ministries = []) {
    // Remove old memberships
    $sql = "DELETE FROM ministry_members WHERE member_id = :member_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
    $stmt->execute();

    // Add new memberships
    if (!empty($ministries)) {
        $insertSql = "INSERT INTO ministry_members (member_id, ministry_id, role, joined_date, created_at)
                      VALUES (:member_id, :ministry_id, :role, :joined_date, NOW())";
        $stmt = $this->db->prepare($insertSql);

        foreach ($ministries as $minId) {
            $role = 'Member';
            $joinedDate = date('Y-m-d');
            $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->bindParam(':ministry_id', $minId, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':joined_date', $joinedDate);
            $stmt->execute();
        }
    }
}

public function getAllWithMinistries() {
    $sql = "
        SELECT 
            m.*,
            GROUP_CONCAT(min.name ORDER BY min.name SEPARATOR ', ') AS ministries
        FROM members m
        LEFT JOIN ministry_members mm ON mm.member_id = m.id
        LEFT JOIN ministries min ON min.id = mm.ministry_id
        GROUP BY m.id
        ORDER BY m.first_name, m.last_name
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public function getMembersGroupedByMinistry() {
    $sql = "
        SELECT 
            m.*,
            min.id AS ministry_id,
            min.name AS ministry_name
        FROM members m
        LEFT JOIN ministry_members mm ON mm.member_id = m.id
        LEFT JOIN ministries min ON min.id = mm.ministry_id
        ORDER BY min.name, m.first_name
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped = [];

    foreach ($rows as $row) {
        $key = $row['ministry_name'] ?? 'No Ministry';
        $grouped[$key][] = $row;
    }

    return $grouped;
}


public function getByIdWithMinistries($id)
{
    $stmt = $this->db->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $this->db->prepare(
        "SELECT ministry_id FROM ministry_members WHERE member_id = ?"
    );
    $stmt->execute([$id]);

    $member['ministries'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $member;
}


public function getAllForExport()
{
    $sql = "
        SELECT 
            m.id,
            m.first_name,
            m.last_name,
            m.email,
            m.phone,
            m.gender,
            m.date_of_birth,
            m.join_date,
            m.region,
            m.city,
            m.area,
            m.address,
            m.emergency_contact_name,
            m.emergency_phone,
            GROUP_CONCAT(min.name SEPARATOR ', ') AS ministries
        FROM members m
        LEFT JOIN ministry_members mm ON mm.member_id = m.id
        LEFT JOIN ministries min ON min.id = mm.ministry_id
        GROUP BY m.id
        ORDER BY m.first_name ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function exists($firstName, $lastName, $email = null, $phone = null) {
    $sql = "SELECT id FROM members WHERE first_name = :first_name AND last_name = :last_name";
    $params = [':first_name' => $firstName, ':last_name' => $lastName];

    if ($email) {
        $sql .= " AND email = :email";
        $params[':email'] = $email;
    }
    if ($phone) {
        $sql .= " AND phone = :phone";
        $params[':phone'] = $phone;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : false;
}

public function existsByMemberCode(string $code): bool
{
    $sql = "SELECT COUNT(*) FROM members WHERE member_code = :code";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':code' => $code]);
    return $stmt->fetchColumn() > 0;
}


public function getByCode($code)
{
    $sql = "SELECT * FROM {$this->table} WHERE member_code = :code";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


} 
?>