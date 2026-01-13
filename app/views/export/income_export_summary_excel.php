<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/../../models/Donation.php';
require_once __DIR__ .  '../../../../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$donation = new Donation();
$donations = $donation->getAll();
$total = $donation->getTotalAmount();
$monthTotal = $donation->getTotalByMonth();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Income Summary');
$sheet->setCellValue('A1', 'Income Summary Report');
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
$sheet->mergeCells('A1:E1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

$sheet->setCellValue('A2', 'Total Income:');
$sheet->setCellValue('B2', '¢'. number_format($total['total'], 2));

$sheet->setCellValue('A3', 'This Month(' . date('F Y') . '):');
$sheet->setCellValue('B3', '¢'. number_format($monthTotal['total'], 2));


$sheet->setCellValue('A4', 'Member');
$sheet->setCellValue('B4', 'Amount');
$sheet->setCellValue('C4', 'Type');
$sheet->setCellValue('D4', 'Type');
$sheet->setCellValue('E4', 'Date');

$sheet->getStyle('A4:E4')->getFont()->setBold(true);

$row = 5; //(responsible for the data starting at row 5)
foreach ($donations as $d) {
if ($d['income_source'] === 'service_total') {
    $member = 'Service Total';
} elseif ($d['income_source'] === 'member') {
    $member = trim($d['first_name'] . ' ' . $d['last_name']);
} else {
    $member = 'Anonymous';
}

   $sheet->setCellValue('A'.$row, $member);
   $sheet->setCellValue('B'.$row, '¢'. number_format($d['amount'],2));
   $sheet->setCellValue('C'.$row, $d['donation_type']);
   $sheet->setCellValue('D'.$row, $d['notes']);
   $sheet->setCellValue('E'.$row, date('M d, Y', strtotime($d['donation_date'])));
   $row++;
}

//Auto size
foreach (range('A', 'E') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="income_summary_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer->save('php://output');  
exit;

?>