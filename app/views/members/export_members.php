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
    'B1' => 'Member Code',
    'C1' => 'First Name',
    'D1' => 'Last Name',
    'E1' => 'Email',
    'F1' => 'Phone Number',
    'G1' => 'Gender',
    'H1' => 'Date of Birth',
    'I1' => 'Join Date',
    'J1' => 'Ministries',
    'K1' => 'Region',
    'L1' => 'City',
    'M1' => 'Area',
    'N1' => 'Address',
    'O1' => 'Emergency Contact',
    'P1' => 'Emergency Phone',
];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

/* ===== DATA ===== */
$row = 2;
$count = 1;
foreach ($members as $m) {
    $sheet->setCellValue("A$row", $count++);
    $sheet->setCellValue("B$row", $m['member_code']);
    $sheet->setCellValue("C$row", $m['first_name']);
    $sheet->setCellValue("D$row", $m['last_name']);
    $sheet->setCellValue("E$row", $m['email']);
    $sheet->setCellValue("F$row", $m['phone']);
    $sheet->setCellValue("G$row", ucfirst($m['gender']));
    $sheet->setCellValue("H$row", $m['date_of_birth']);
    $sheet->setCellValue("I$row", $m['join_date']);
    $sheet->setCellValue("J$row", $m['ministries'] ?: 'N/A');
    $sheet->setCellValue("K$row", $m['region']);
    $sheet->setCellValue("L$row", $m['city']);
    $sheet->setCellValue("M$row", $m['area']);
    $sheet->setCellValue("N$row", $m['address']);
    $sheet->setCellValue("O$row", $m['emergency_contact_name']);
    $sheet->setCellValue("P$row", $m['emergency_phone']);
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
