<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/../../models/Expense.php';
require_once __DIR__ . '/../../models/ExpenseCategory.php';
require_once __DIR__ .  '../../../../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo = Database::getInstance()->getConnection();


$expense = new Expense($pdo);
$expenseCategory = new ExpenseCategory($pdo);
$expenses = $expense->getAll();
$total = $expense->getTotalAmount();
$monthTotal = $expense->getTotalByMonth();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Expense Summary');
$sheet->setCellValue('A1', 'Expense Summary Report');
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
$sheet->mergeCells('A1:D1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

$sheet->setCellValue('A2', 'Total Expenses:');
$sheet->setCellValue('B2', '¢'. number_format($total['total_amount'], 2));

$sheet->setCellValue('A3', 'This Month(' . date('F Y') . '):');
$sheet->setCellValue('B3', '¢'. number_format($monthTotal['total_expense'], 2));


$sheet->setCellValue('A4', 'Category');
$sheet->setCellValue('B4', 'Amount');
$sheet->setCellValue('C4', 'Description');
$sheet->setCellValue('D4', 'Date');


$sheet->getStyle('A4:D4')->getFont()->setBold(true);

$row = 5; //(responsible for the data starting at row 5)
foreach ($expenses as $e) {
   $category = $e['category_name'];
   $sheet->setCellValue('A'.$row, $category);
   $sheet->setCellValue('B'.$row, '¢'. number_format($e['amount'],2));
   $sheet->setCellValue('C'.$row, $e['description']);
   $sheet->setCellValue('D'.$row, date('M d, Y', strtotime($e['expense_date'])));
   $row++;
}

//Auto size
foreach (range('A', 'D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="expense_summary_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer->save('php://output');  
exit;

?>