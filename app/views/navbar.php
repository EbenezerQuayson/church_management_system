<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
// include  __DIR__ . '/notification.php';
?>
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
                <span class="notification-badge"><?= $unread_count['COUNT(*)'] ?? 0 ?></span>
            </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php
$notifications = $db->fetchAll("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC", [$user_id]);

if (!empty($notifications)) {
    foreach ($notifications as $notification):
    ?>
    <li>
        <a class="dropdown-item" href="<?php echo BASE_URL . '/' . htmlspecialchars($notification['link']); ?>">
            <?php echo htmlspecialchars($notification['message']); ?>
        </a>
    </li>
    <?php
    endforeach;
} else {
    ?>
    <li>
        <a class="dropdown-item text-muted" href="#">No new notifications</a>
    </li>
    <?php
}
?>

                    <!-- <li><a class="dropdown-item" href="#">New member registered</a></li>
                    <li><a class="dropdown-item" href="#">Event today at 10 AM</a></li>
                    <li><a class="dropdown-item" href="#">Donation received</a></li> -->
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