<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
requireLogin();

$db = Database::getInstance();
$user_id = $_SESSION['user_id'];

$db->query(
    "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
    [$user_id]
);
?>