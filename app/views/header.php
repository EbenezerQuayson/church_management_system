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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="top-nav-left">
            <button class="btn sidebar-toggle d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-none d-md-flex align-items-center">
                <img src="<?php echo BASE_URL;?>/assets/images/methodist-logo.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                <span class="fw-bold text-dark"><?php echo htmlspecialchars($church_name); ?></span>
            </div>
        </div>
        <div class="top-nav-right">
         <div class="dropdown">
            <button class="top-nav-icon-btn notification-btn" data-bs-toggle="dropdown">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">New member registered</a></li>
                    <li><a class="dropdown-item" href="#">Event today at 10 AM</a></li>
                    <li><a class="dropdown-item" href="#">Donation received</a></li>
                </ul>
         </div>
            <div class="dropdown user-dropdown ms-2">
                <button class="user-profile-btn dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="<?php echo BASE_URL; echo '/'; echo htmlspecialchars($user['profile_photo'] ?? '/assets/images/placeholder-user.jpg'); ?>" alt="User" class="user-thumbnail">
                    <span class="d-none d-md-inline user-name-top"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL;?>/app/views/settings.php#profile">
                        <i class="fas fa-user"></i> Profile
                    </a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL;?>/app/views/settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL;?>/app/controllers/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
