<?php
class Expense
{
    private $conn;
    private $table = "expenses";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /** 
     * Create a new expense record 
     */
    public function create($expense_date, $category_id, $amount, $description = null, $receipt_path = null)
    {
        $sql = "INSERT INTO {$this->table} 
                (expense_date, category_id, amount, description, receipt_path) 
                VALUES (:expense_date, :category_id, :amount, :description, :receipt_path)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':expense_date', $expense_date);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':receipt_path', $receipt_path);

        return $stmt->execute();
    }


     /**
     * Update an expense record
     */
    public function update($id, $expense_date, $category_id, $amount, $description = null, $receipt_path = null)
    {
        $sql = "UPDATE {$this->table}
                SET expense_date = :expense_date,
                    category_id = :category_id,
                    amount = :amount,
                    description = :description,
                    receipt_path = :receipt_path
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':expense_date', $expense_date);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':receipt_path', $receipt_path);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Delete an expense
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    /**
     * Get all expenses
     */
    public function getAll()
    {
        $sql = "SELECT e.*, c.name AS category_name
                FROM {$this->table} e
                JOIN expense_categories c ON e.category_id = c.id
                ORDER BY e.expense_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Get expense by ID
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
     * Get total expenses by month
     */
    public function getTotalByMonth($year, $month)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) AS total_expense
                FROM {$this->table}
                WHERE YEAR(expense_date) = :year
                AND MONTH(expense_date) = :month";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Get total amount of all expenses
     */
    public function getTotalAmount()
    {
        $sql = "SELECT SUM(amount) AS total_amount FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
   
}
?>