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

//Active page logic
$activePage = isset($activePage) ? $activePage : 'dashboard';

function isActive($page, $current){
    return $page === $current ? 'active' : '';
}

function isCollapsed($group, $current){
    $groups = [
        'christianManagement' => ['dashboard', 'members'],
        'finance' => ['donations'],
        'churchManagement' => ['ministries', 'events', 'attendance'],
        'userManagement' => ['settings-profile']
    ];

    if(isset($groups[$group]) && in_array($current, $groups[$group])){
        return 'show';
    }
    return '';
}

?>
<!-- Sidebar -->
<div class="sidebar sidebar-menu-groups" id="sidebar">
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
    <div class="sidebar-menu-groups" >
        <!--Christian Management -->
        <div class="menu-group">
            <div class="menu-group-header" data-bs-toggle="collapse" data-bs-target="#christianManagement" aria-expanded="<?= isCollapsed('christianManagement', $activePage) ? 'true' : 'false' ?>">
                <i class="bi bi-chevron-down"></i>
                <span>Christian Management</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('christianManagement', $activePage) ?>" id="christianManagement">
        <li><a href="<?php echo BASE_URL; ?>/app/views/dashboard.php" class="<?= isActive('dashboard', $activePage)?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/members.php" class="<?= isActive('members', $activePage) ?> ">
            <i class="bi bi-people"></i> Members
        </a></li>
            </ul>
</div>
<!-- Church Management -->
 <div class="menu-group">
            <div class="menu-group-header" data-bs-toggle="collapse" data-bs-target="#churchManagement" aria-expanded="<?= isCollapsed('churchManagement', $activePage) ? 'true' : 'false' ?>">
                <i class="bi bi-chevron-down"></i>
                <span>Church Management</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('churchManagement', $activePage) ?>" id="churchManagement">
        <li><a href="<?php echo BASE_URL; ?>/app/views/attendance.php" class="<?= isActive('attendance', $activePage) ?>">
            <i class="bi bi-clipboard-check"></i> Attendance
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/events.php" class="<?= isActive('events', $activePage) ?> ">
            <i class="bi bi-calendar-event"></i> Events
        </a></li>
        <li><a href="<?php echo BASE_URL; ?>/app/views/ministries.php" class="<?= isActive('ministries', $activePage) ?>">
            <i class="bi bi-diagram-3"></i> Ministries
        </a></li>
</ul>
 </div>
 <!-- Finances -->
  <div class="menu-group">
            <div class="menu-group-header" data-bs-toggle="collapse" data-bs-target="#finance" aria-expanded="<?= isCollapsed('finance', $activePage) ? 'true' : 'false' ?>">
                <i class="bi bi-chevron-down"></i>
                <span>Finance</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('finance', $activePage) ?>" id="finance">
        <li><a href="<?php echo BASE_URL; ?>/app/views/donations.php" class="<?= isActive('donations', $activePage) ?>">
            <i class="bi bi-cash-coin"></i> Donations
        </a></li>
            </ul>
  </div>
    <div class="menu-group">
        <div class="menu-group-header" data-bs-toggle="collapse" data-bs-target="#settings">
            <i class="bi bi-chevron-down"></i>
             <span>General Settings</span>
        </div>
        <ul class="sidebar-menu collapse <?= isCollapsed('settings', $activePage) ?>" id="settings">
        <li>
            <a href="<?php echo BASE_URL; ?>/app/views/settings.php" class="menu-link <?= isActive('settings', $activePage) ?>">
             <i class="bi bi-gear"></i>Settings</a>
        </li>
        <li><a href="<?php echo BASE_URL; ?>/app/controllers/logout.php" class="menu-link logout-link">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a></li>
    </ul>
    </div>

</div>
</div>
