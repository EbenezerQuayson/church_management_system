<?php

require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/Member.php';
require_once __DIR__ . '/../../models/Ministry.php';
require_once __DIR__ . '/../../../vendor/autoload.php';


require_once __DIR__ . '/../../models/Notifications.php';

$notification = new Notification();

$db = Database::getInstance();
$admins = $db->fetchAll("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Admin'");


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

    if (count($rows) < 2) {
        throw new Exception('Empty file');
    }

    /* ===== NORMALIZE HEADERS ===== */
    $rawHeaders = array_shift($rows);

    $headers = array_map(function ($h) {
        return strtolower(
            str_replace([' ', '-'], '_', trim($h))
        );
    }, $rawHeaders);

    /* ===== REQUIRED FIELDS ===== */
    $requiredFields = ['first_name', 'last_name', 'phone_number', 'gender'];

    $importedCount = 0;
    

    foreach ($rows as $index => $row) {

        $rowData = array_combine($headers, $row);

        if ($rowData === false) continue;

        // Trim values
        $rowData = array_map(fn($v) => trim((string)$v), $rowData);

        /* ===== VALIDATION ===== */
        $missing = [];
        foreach ($requiredFields as $field) {
            if (empty($rowData[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            // skip invalid row (optionally log)
            continue;
        }

        /* ===== BUILD DATA ===== */
        $data = [
            'first_name' => $rowData['first_name'],
            'last_name'  => $rowData['last_name'],
            'email'      => $rowData['email'] ?? null,
            'phone'      => $rowData['phone_number'],
            'gender'     => ucfirst(strtolower($rowData['gender'])),
            'date_of_birth' => excelDateToYmd($rowData['date_of_birth'] ?? null),
            'join_date'  => excelDateToYmd($rowData['join_date'] ?? null) ?? date('Y-m-d'),
            'region'     => $rowData['region'] ?? null,
            'city'       => $rowData['city'] ?? null,
            'area'       => $rowData['area'] ?? null,
            'address'    => $rowData['address'] ?? null,
            'emergency_contact_name' => $rowData['emergency_contact'] ?? null,
            'emergency_phone'        => $rowData['emergency_phone'] ?? null,
            'member_img' => null
        ];

        /* ===== DUPLICATE CHECK ===== */
        $existingId = $member->exists(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone']
        );

        $memberId = $existingId ?: $member->create($data);
        if (!$memberId) continue;

        /* ===== MINISTRIES ===== */
        $ministries = explode(',', $rowData['ministries'] ?? '');

        foreach ($ministries as $ministryName) {
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

    /* ===== NOTIFICATIONS ===== */
    foreach ($admins as $admin) {
        $notification->create(
            $admin['id'],
            'Members Imported',
            "$importedCount members were successfully imported.",
            'members.php'
        );
    }

    header("Location:" . BASE_URL . "/app/views/members.php?msg=imported&count=$importedCount");
    exit;

} catch (Exception $e) {
    header("Location:" . BASE_URL . "/app/views/members.php?msg=import_failed");
    exit;
}
