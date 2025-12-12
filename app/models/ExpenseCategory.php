<?php
class ExpenseCategory
{
    private $conn;
    private $table = "expense_categories";

    public function __construct($db)
    {
        $this->conn = $db;
    }

//Check if category exists
    public function getByName($name){
        $stmt = $this->conn->prepare("SELECT * FROM expense_categories WHERE name = :name");
    $stmt->execute(['name' => $name]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // returns false if not found

    }

    /**
 * Create a new expense category and return its ID
 */
public function create($name, $description = null)
{
    $sql = "INSERT INTO {$this->table} (name, description)
            VALUES (:name, :description)";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);

    if ($stmt->execute()) {
        // Return the ID of the newly created category
        return $this->conn->lastInsertId();
    }

    return false;
}


    /**
     * Get all categories
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Get category by ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update a category
     */
    public function update($id, $name, $description = null)
    {
        $sql = "UPDATE {$this->table}
                SET name = :name,
                    description = :description
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Delete a category
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
