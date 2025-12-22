<?php

class Overview
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Get recent donations and expenses for overview table
     */
    public function getRecentTransactions(int $limit = 10): array
    {
        $sql = "
            SELECT 
                d.donation_date AS trans_date,
                'Donation' AS type,
                d.donation_type AS category,
                d.amount,
                d.notes AS description
            FROM donations d

            UNION ALL

            SELECT
                e.expense_date AS trans_date,
                'Expense' AS type,
                ec.name AS category,
                e.amount,
                e.description
            FROM expenses e
            LEFT JOIN expense_categories ec 
                ON e.category_id = ec.id

            ORDER BY trans_date DESC
            LIMIT :limit
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getMonthlyIncomeExpense(int $year): array
{
    $sql = "
        SELECT month, 
               SUM(income) AS income,
               SUM(expense) AS expense
        FROM (
            SELECT 
                MONTH(donation_date) AS month,
                amount AS income,
                0 AS expense
            FROM donations
            WHERE YEAR(donation_date) = :year

            UNION ALL

            SELECT 
                MONTH(expense_date) AS month,
                0 AS income,
                amount AS expense
            FROM expenses
            WHERE YEAR(expense_date) = :year
        ) t
        GROUP BY month
        ORDER BY month
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['year' => $year]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getExpenseBreakdown(): array
{
    $sql = "
        SELECT ec.name AS category, SUM(e.amount) AS total
        FROM expenses e
        JOIN expense_categories ec ON e.category_id = ec.id
        GROUP BY ec.id
        ORDER BY total DESC
    ";

    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getYearTotals(int $year): array
{
    $sql = "
        SELECT
            (SELECT COALESCE(SUM(amount), 0)
             FROM donations
             WHERE YEAR(donation_date) = :year) AS total_income,

            (SELECT COALESCE(SUM(amount), 0)
             FROM expenses
             WHERE YEAR(expense_date) = :year) AS total_expenses
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['year' => $year]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}




}
