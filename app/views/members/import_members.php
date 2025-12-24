<?php

require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/Member.php';
require_once __DIR__ . '/../../models/Ministry.php';
require_once __DIR__ . '/../../../vendor/autoload.php';


require_once __DIR__ . '/../../models/Notifications.php';

$notification = new Notification();



use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\IOFactory;

requireLogin();

function excelDateToYmd($value) {
    if (empty($value)) return null;

    // If Excel numeric date
    if (is_numeric($value)) {
        return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
    }

    // If already a valid string date
    $timestamp = strtotime($value);
    return $timestamp ? date('Y-m-d', $timestamp) : null;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['import_members'])) {
    header("Location: ". BASE_URL . "/app/views/members.php");
    exit;
}

$db = Database::getInstance()->getConnection();
$member = new Member();
$ministryModel = new Ministry();

/* ===== FILE CHECK ===== */
if (empty($_FILES['import_file']['name'])) {
    header("Location:" . BASE_URL . "/app/views/members.php?msg=import_failed_no_file");
    exit;
}

$fileTmpPath = $_FILES['import_file']['tmp_name'];
$fileExtension = strtolower(pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION));

if (!in_array($fileExtension, ['xlsx', 'xls', 'csv'])) {
    header("Location:" . BASE_URL .  "/app/views/members.php?msg=import_failed_type");
    exit;
}

/* ===== IMPORT ===== */
try {
    $rows = IOFactory::load($fileTmpPath)->getActiveSheet()->toArray();
    array_shift($rows); // remove header

    $importedCount = 0;

    foreach ($rows as $row) {

        $data = [
            'first_name' => trim($row[1] ?? ''),
            'last_name'  => trim($row[2] ?? ''),
            'email'      => trim($row[3] ?? ''),
            'phone'      => trim($row[4] ?? ''),
            'gender'     => ucfirst(trim($row[5] ?? '')),
           'date_of_birth' => excelDateToYmd($row[6] ?? null),
            'join_date'   => excelDateToYmd($row[7] ?? null) ?? date('Y-m-d'),
            'address'    => $row[10] ?? '',
            'city'       => $row[11] ?? '',
            'region'     => $row[9] ?? '',
            'area'       => $row[12] ?? '',
            'emergency_contact_name' => $row[13] ?? '',
            'emergency_phone' => $row[14] ?? '',
            'member_img' => null
        ];

        if (!$data['first_name'] || !$data['last_name']) continue;

        $existingId = $member->exists(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone']
        );

        $memberId = $existingId ?: $member->create($data);
        if (!$memberId) continue;

        // Ministries
        foreach (explode(',', $row[8] ?? '') as $ministryName) {
            $ministryName = trim($ministryName);
            if (!$ministryName) continue;

            $ministryId = $ministryModel->getIdByName($ministryName)
                          ?? $ministryModel->create($ministryName);

            $stmt = $db->prepare("
                INSERT IGNORE INTO ministry_members
                (member_id, ministry_id, role, joined_date, created_at)
                VALUES (?, ?, 'Member', ?, NOW())
            ");
            $stmt->execute([$memberId, $ministryId, $data['join_date']]);
        }

        $importedCount++;
    }
    $notification->create(
    $_SESSION['user_id'],
    'Members Imported',
    "$importedCount members were successfully imported.",
    'members.php'
);


    header("Location:" . BASE_URL . "/app/views/members.php?msg=imported&count=$importedCount");
    exit;

} catch (Exception $e) {
    header("Location:" . BASE_URL . "/app/views/members.php?msg=import_failed");
    exit;
}

