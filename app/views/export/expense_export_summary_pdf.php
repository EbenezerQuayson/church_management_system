<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../models/Expense.php';
require_once __DIR__ . '/../../models/ExpenseCategory.php';
require_once __DIR__ .  '../../../../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo = Database::getInstance()->getConnection();

$expense = new Expense($pdo);
$expenseCategory = new ExpenseCategory($pdo);
$expenses = $expense->getAll();
$total = $expense->getTotalAmount();
$monthTotal = $expense->getTotalByMonth();

ob_start();
?>
<html>
<head>
<style>
/*  PDF styling */
    /* Base reset */
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        color: #333;
        margin: 0;
        padding: 0;
    }

    /* Header */
    .header {
        background-color: #1f4fd8; /* match primary color */
        color: #ffffff;
        padding: 18px 20px;
        text-align: center;
    }

    .header h2 {
        margin: 0;
        font-size: 20px;
        letter-spacing: 0.5px;
    }

    .header p {
        margin: 4px 0 0;
        font-size: 11px;
        opacity: 0.9;
    }

    /* Summary cards */
    .summary {
        margin: 20px;
        border: 1px solid #e3e3e3;
        border-radius: 6px;
        padding: 15px;
        background-color: #f9fafc;
    }

    .summary-item {
        margin-bottom: 8px;
        font-size: 13px;
    }

    .summary-item strong {
        color: #1f4fd8;
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px;
    }

    thead {
        background-color: #1f4fd8;
        color: #ffffff;
    }

    th {
        padding: 10px 8px;
        text-align: left;
        font-size: 12px;
        border: 1px solid #d0d7e5;
    }

    td {
        padding: 9px 8px;
        border: 1px solid #e0e0e0;
        font-size: 11.5px;
    }

    tbody tr:nth-child(even) {
        background-color: #f4f6fb;
    }

    tbody tr:hover {
        background-color: #eef2ff;
    }

    /* Amount emphasis */
    .amount {
        font-weight: bold;
        color: #ff0000ff;
    }

    /* Footer */
    .footer {
        text-align: center;
        margin-top: 30px;
        font-size: 10px;
        color: #777;
    }


</style>
</head>
<body>
<div class="header">
<h2>Expense Summary Report</h2>
<p>Generated on: <?php echo date('F d, Y'); ?></p>
</div>
<div class="summary">
    <div class="summary-item">
        <strong>Total Expenses: </strong>¢<?php echo number_format($total['total_amount'], 2); ?>
  
    </div>
     <div class="summary-item">
        <strong>This Month: </strong>¢<?php echo number_format($monthTotal['total_expense'], 2); ?>
    </div>
</div>

<
<table border="1" cellspacing="0" cellpadding="8">
<thead>
    <tr>
        <th>Category</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Date</th>
    </tr>
</thead>
<tbody>
<?php foreach ($expenses as $e): ?>
<tr>
    <td class="member-name">
       <?php echo $e['category_name']; ?>
    </td>
    <td class="amount">¢<?php echo number_format($e['amount'],2); ?></td>
    <td><?php echo $e['description']; ?></td>
    <td><?php echo date('M d, Y', strtotime($e['expense_date'])); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>

</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream('expense_summary_' . date('Y-m-d') . '.pdf', ['Attachment' => false]);

?>