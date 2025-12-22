<?php
// Header Include File - Used in all pages
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance()->getConnection();

$userId = $_SESSION['user_id'] ?? null;

$user = null;

if($userId) {
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_photo FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// Get church settings
$db = Database::getInstance();
$settings = $db->fetchAll("SELECT * FROM settings");
$church_name = 'The Methodist Church Ghana';
$primary_color = '#003DA5';
$secondary_color = '#CC0000';
$accent_color = '#F4C43F';

// Override with database values if available
foreach ($settings as $setting) {
    if ($setting['setting_key'] === 'church_name') $church_name = $setting['setting_value'];
    if ($setting['setting_key'] === 'primary_color') $primary_color = $setting['setting_value'];
    if ($setting['setting_key'] === 'secondary_color') $secondary_color = $setting['setting_value'];
    if ($setting['setting_key'] === 'accent_color') $accent_color = $setting['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($church_name); ?> - Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
            --accent-color: <?php echo $accent_color; ?>;
        }
    </style>
</head>
<body>
    
<?php
include __DIR__ . '/navbar.php';
?>