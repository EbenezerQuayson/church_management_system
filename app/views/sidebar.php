<?php
// Sidebar Include File - Used in all dashboard pages
require_once __DIR__ . '/../../config/config.php';
include_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

$pdo = Database::getInstance()->getConnection();

$userId = $_SESSION['user_id'] ?? null;

$user = null;

if($userId) {
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_photo FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} 

?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="church-logo-placeholder">
            <i class="fas fa-cross"></i>
        </div>
        <h4><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
    </div>

    <!-- User Card -->
    <div class="sidebar-user-card">
        <div class="user-avatar">
            <img src="<?php echo BASE_URL; echo '/'; echo htmlspecialchars($user['profile_photo'] ?? '/assets/images/placeholder-user.jpg'); ?>" alt="User">
        </div>
        <div class="user-info">
            <h6><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h6>
            <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role']); ?></span>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu" >
        <li><a href="<?php echo BASE_URL; ?>/app/views/dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/members.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'members.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Members
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/attendance.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'attendance.php') ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-list"></i> Attendance
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/events.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'events.php') ? 'active' : ''; ?>">
            <i class="fas fa-calendar"></i> Events
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/ministries.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'ministries.php') ? 'active' : ''; ?>">
            <i class="fas fa-handshake"></i> Ministries
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/donations.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'donations.php') ? 'active' : ''; ?>">
            <i class="fas fa-hand-holding-heart"></i> Donations
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/settings.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'settings.php') ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/controllers/logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a></li>
    </ul>
</div>
