<?php
/**
 * AJAX endpoint to retrieve donation details by ID.
 * Expects 'id' as a GET parameter and returns donation data in JSON format.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Donation.php';

if (!isset($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'data' => null,
        'message' => 'Missing ID'
    ]);
    exit;
}

$donation = new Donation();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    echo json_encode([
        'status' => 'error',
        'data' => null,
        'message' => 'Invalid ID'
    ]);
    exit;
}

// Initialize $data to null
$data = null;

// Fetch donation data by ID
$data = $donation->getById($id);

if (!$data) {
    echo json_encode([
        'status' => 'error',
        'data' => null,
        'message' => 'Donation not found'
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'data' => $data
]);
exit;
?>
