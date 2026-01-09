<?php
require_once __DIR__ . '/../../models/Donation.php';
require_once __DIR__ .  '../../../../config/database.php';

$donation = new Donation();
$records = $donation->getAll();

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=income_summary_".date('Y-m-d').".csv");

$output = fopen("php://output", "w");
fputcsv($output, ["Date", "Amount", "Type", "Notes"]);

foreach ($records as $r) {
    fputcsv($output, [$r['donation_date'], $r['amount'], $r['donation_type'], $r['notes']]);
}

?>