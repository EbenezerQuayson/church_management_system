<?php

class Program
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch all programs (optionally only active ones)
     */
    public function getAll(bool $onlyActive = false): array
    {
        $sql = "SELECT * FROM programs";

        if ($onlyActive) {
            $sql .= " WHERE is_active = 1";
        }

        $sql .= " ORDER BY display_order ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single program by ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM programs WHERE id = :id LIMIT 1"
        );

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Create a new program
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO programs (
                title,
                icon_class,
                schedule_text,
                description,
                is_active,
                display_order
            ) VALUES (
                :title,
                :icon_class,
                :schedule_text,
                :description,
                :is_active,
                :display_order
            )
        ");

        return $stmt->execute([
            ':title'         => $data['title'],
            ':icon_class'    => $data['icon_class'] ?? null,
            ':schedule_text' => $data['schedule_text'],
            ':description'   => $data['description'] ?? null,
            ':is_active'     => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            ':display_order' => $data['display_order'] ?? 0
        ]);
    }

    /**
     * Update an existing program
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE programs SET
                title = :title,
                icon_class = :icon_class,
                schedule_text = :schedule_text,
                description = :description,
                is_active = :is_active,
                display_order = :display_order
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([
            ':title'         => $data['title'],
            ':icon_class'    => $data['icon_class'] ?? null,
            ':schedule_text' => $data['schedule_text'],
            ':description'   => $data['description'] ?? null,
            ':is_active'     => (int)$data['is_active'],
            ':display_order' => $data['display_order'] ?? 0,
            ':id'            => $id
        ]);
    }

    /**
     * Delete a program permanently
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM programs WHERE id = :id LIMIT 1"
        );

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Toggle program visibility (active ↔ inactive)
     */
    public function toggleActive(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE programs
            SET is_active = NOT is_active
            WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get programs for homepage display
     */
    public function getHomepagePrograms(?int $limit = null): array
    {
        $sql = "
            SELECT * FROM programs
            WHERE is_active = 1
            ORDER BY display_order ASC
        ";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
