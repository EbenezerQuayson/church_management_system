<?php
require_once __DIR__ . '/../../config/database.php';

class HomepageMinistry {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Get all homepage ministries with ministry details
    public function getAll() {
        $stmt = $this->pdo->prepare("
          SELECT 
    hm.id as homepage_id, 
    hm.ministry_id, 
    hm.image_path, 
    hm.link_url,
    hm.icon_class,       -- fetch from homepage_ministries
    hm.is_active,
    m.name, 
    m.description
FROM homepage_ministries hm
JOIN ministries m ON m.id = hm.ministry_id
ORDER BY hm.id ASC

        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function getAllForHomepage() {
        $stmt = $this->pdo->prepare("
          SELECT 
    hm.id AS homepage_id, 
    hm.ministry_id, 
    hm.image_path, 
    hm.link_url,
    hm.icon_class,       -- fetch from homepage_ministries
    hm.is_active,
    m.name, 
    m.description
FROM homepage_ministries hm
JOIN ministries m ON m.id = hm.ministry_id
WHERE hm.is_active = 1
ORDER BY hm.id ASC;

        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if a ministry is already on homepage
    public function exists($ministry_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM homepage_ministries WHERE ministry_id = :mid");
        $stmt->execute([':mid' => $ministry_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Add a ministry to homepage
    public function add($ministry_id, $image_path = null, $link_url = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO homepage_ministries (ministry_id, image_path, link_url)
            VALUES (:mid, :img, :link)
        ");
        return $stmt->execute([
            ':mid' => $ministry_id,
            ':img' => $image_path,
            ':link' => $link_url
        ]);
    }

    // Update homepage ministry details
    public function update($id, $image_path = null, $link_url = null) {
        $stmt = $this->pdo->prepare("
            UPDATE homepage_ministries
            SET image_path = :img, link_url = :link
            WHERE id = :id
        ");
        return $stmt->execute([
            ':img' => $image_path,
            ':link' => $link_url,
            ':id' => $id
        ]);
    }

    // Remove a ministry from homepage
    public function remove($id) {
        $stmt = $this->pdo->prepare("DELETE FROM homepage_ministries WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
