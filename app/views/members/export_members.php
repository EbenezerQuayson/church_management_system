<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../app/models/Member.php';
require_once __DIR__ . '/../../../app/models/Notifications.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$notifcation = new Notification();
$member = new Member();
$members = $member->getAllForExport();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$db = Database::getInstance();
$admins = $db->fetchAll("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Admin'");

/* ===== HEADERS ===== */
$headers = [
    'A1' => 'Serial Number',
    'B1' => 'First Name',
    'C1' => 'Last Name',
    'D1' => 'Email',
    'E1' => 'Phone',
    'F1' => 'Gender',
    'G1' => 'Date of Birth',
    'H1' => 'Join Date',
    'I1' => 'Ministries',
    'J1' => 'Region',
    'K1' => 'City',
    'L1' => 'Area',
    'M1' => 'Address',
    'N1' => 'Emergency Contact',
    'O1' => 'Emergency Phone',
];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

/* ===== DATA ===== */
$row = 2;
$count = 1;
foreach ($members as $m) {
    $sheet->setCellValue("A$row", $count++);
    $sheet->setCellValue("B$row", $m['first_name']);
    $sheet->setCellValue("C$row", $m['last_name']);
    $sheet->setCellValue("D$row", $m['email']);
    $sheet->setCellValue("E$row", $m['phone']);
    $sheet->setCellValue("F$row", ucfirst($m['gender']));
    $sheet->setCellValue("G$row", $m['date_of_birth']);
    $sheet->setCellValue("H$row", $m['join_date']);
    $sheet->setCellValue("I$row", $m['ministries'] ?: 'N/A');
    $sheet->setCellValue("J$row", $m['region']);
    $sheet->setCellValue("K$row", $m['city']);
    $sheet->setCellValue("L$row", $m['area']);
    $sheet->setCellValue("M$row", $m['address']);
    $sheet->setCellValue("N$row", $m['emergency_contact_name']);
    $sheet->setCellValue("O$row", $m['emergency_phone']);
    $row++;
}

/* ===== DOWNLOAD ===== */
$filename = 'members_export_' . date('Y_m_d_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
foreach($admins as $admin){
$notifcation->create(
    $admin['id'],
    'Members Exported',
    'Members data was exported.',
    'members.php'
); }
exit;


?>
