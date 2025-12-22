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

$sheet->setTitle('Financial Summary');
$sheet ->setCellValue('A1', 'Financial Summary Report');
$sheet ->mergeCells('A1:D1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

$sheet->setCellValue('A2', 'Total Donations:');
$sheet->setCellValue('B2', '¢'. number_format($total['total'], 2));

$sheet->setCellValue('A3', 'This Month:');
$sheet->setCellValue('B3', '¢'. number_format($monthTotal['total'], 2));


$sheet->setCellValue('A4', 'Member');
$sheet->setCellValue('B4', 'Amount');
$sheet->setCellValue('C4', 'Type');
$sheet->setCellValue('D4', 'Date');

$sheet->getStyle('A6:D6')->getFont()->setBold(true);

$row = 7; //Subject to change(responsible for the data starting at row 7)
foreach ($donations as $d) {
   $member = $d['member_id'] ? ($d['first_name']." ".$d['last_name']) : "Anonymous";
   $sheet->setCellValue('A'.$row, $member);
   $sheet->setCellValue('B'.$row, '¢'. number_format($d['amount'],2));
   $sheet->setCellValue('C'.$row, $d['donation_type']);
   $sheet->setCellValue('D'.$row, date('M d, Y', strtotime($d['donation_date'])));
   $row++;
}

//Auto size
foreach (range('A', 'D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="financial_summary.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer->save('php://output');  
exit;

?>