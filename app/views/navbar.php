<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
$user_id = $_SESSION['user_id'];
$unread_count = $db->fetch("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0", [$user_id]);


?>
<style>
.notifications-dropdown {
    width: 250px;
    max-height: 320px;
    overflow-y: auto;
    overflow-x: hidden ;
}
.notifications-dropdown .dropdown-item {
    background-color: #f5f6f8;     
    margin: 6px 8px;
    padding: 10px 12px;
    border-radius: 8px;
    white-space: normal;         
    line-height: 1.4;
    font-size: 0.9rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    transition: background-color 0.2s ease;
}
.notifications-dropdown .dropdown-item:hover {
    background-color: #e9ecef;
}

.notifications-dropdown .dropdown-item.unread {
    background-color: #eef2ff;    
    font-weight: 500;
}

.notifications-dropdown.show {
    display: block;
}

@media (max-width: 768px) {
    .notifications-dropdown {
        max-height: 260px ;
    }
}
</style>
<!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="top-nav-left">
            <button class="btn sidebar-toggle d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
             <a href="dashboard.php" class="btn sidebar-toggle d-md-none">
                <i class="bi bi-speedometer2"></i>
</a>
            <div class="d-none d-md-flex align-items-center">
                <img src="<?php echo BASE_URL;?>/assets/images/methodist-logo.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                <span class="fw-bold text-dark"><?php echo htmlspecialchars($church_name); ?></span>
            </div>
        </div>
        <div class="top-nav-right">
         <div class="dropdown">
            <button class="top-nav-icon-btn notification-btn" data-bs-toggle="dropdown">
                <i class="fas fa-bell"></i>
                <span class="notification-badge"><?= $unread_count['unread_count'] ?? 0 ?></span>
            </button>
                <ul class="dropdown-menu dropdown-menu-end notifications-dropdown">
                    <?php
$notifications = $db->fetchAll("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC", [$user_id]);

if (!empty($notifications)) {
    foreach ($notifications as $notification):
    ?>
    <li>
        <a class="dropdown-item unread" href="<?php echo BASE_URL . '/app/views/notification.php'?>">
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
                </ul>
         </div>
            <div class="dropdown user-dropdown ms-2">
                <button class="user-profile-btn dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="<?php echo BASE_URL; echo '/'; echo htmlspecialchars($user['profile_photo'] ?? '/assets/images/avatar-placeholder.png'); ?>" alt="User" class="user-thumbnail">
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