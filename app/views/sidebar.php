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
        'finance' => ['donations', 'expenses', 'report'],
        'churchManagement' => ['ministries', 'events', 'attendance', 'service'],
        'notifications' => ['notifications']
    ];

    if(isset($groups[$group]) && in_array($current, $groups[$group])){
        return 'show';
    }
    return '';
}

?>
<!-- Sidebar -->
<nav class="sidebar sidebar-menu-groups" id="sidebar">
    
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


    <!-- Menu Groups -->
    <div class="sidebar-menu-groups">
        <div class="menu-group">
            <div class="menu-group-header"   data-bs-target="#christianManagement" aria-expanded="<?= isCollapsed('christianManagement', $activePage) ? 'true' : 'false' ?>">
                <i class="bi bi-chevron-down"></i>
                <span>Christian Management</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('christianManagement', $activePage) ?>" id="christianManagement">
                <li><a href="dashboard.php" class="<?= isActive('dashboard', $activePage) ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><a href="members.php" class="<?= isActive('members', $activePage) ?>"><i class="bi bi-people"></i> Members</a></li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-group-header"  data-bs-target="#finance" aria-expanded="<?= isCollapsed('finance', $activePage) ? 'true' : 'false' ?>">
                <i class="bi bi-chevron-down"></i>
                <span>Finance</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('finance', $activePage) ?>" id="finance">
                <li><a href="donations.php" class="<?= isActive('donations', $activePage) ?>"><i class="bi bi-cash-coin"></i> Donations</a></li>
                <li><a href="expenses.php" class="<?= isActive('expenses', $activePage) ?>"><i class="bi bi-wallet"></i> Expenses</a></li>
                <li><a href="report.php" class="<?= isActive('report', $activePage) ?>"><i class="bi bi-book"></i> Report</a></li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-group-header"  data-bs-target="#churchManagement" aria-expanded="<?= isCollapsed('churchManagement', $activePage) ? 'true' : 'false' ?>">
                <i class="bi bi-chevron-down"></i>
                <span>Church Management</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('churchManagement', $activePage) ?>" id="churchManagement">
                <li><a href="ministries.php" class="<?= isActive('ministries', $activePage) ?>"><i class="bi bi-diagram-3"></i> Ministries</a></li>
                <li><a href="events.php" class="<?= isActive('events', $activePage) ?>"><i class="bi bi-calendar-event"></i> Events</a></li>
                <li><a href="attendance.php" class="<?= isActive('attendance', $activePage) ?>"><i class="bi bi-clipboard-check"></i> Attendance</a></li>
                <li><a href="service.php" class="<?= isActive('service', $activePage) ?>"><i class="bi bi-calendar2-event"></i> Service Info</a></li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-group-header"  data-bs-target="#notifications">
                <i class="bi bi-chevron-down"></i>
                <span>Notifications</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('notifications', $activePage) ?>" id="notifications">
                <li><a href="notification.php" class="<?= isActive('notifications', $activePage) ?>"><i class="bi bi-bell"></i> All Notifications</a></li>
            </ul>
        </div>

        <!-- <div class="menu-group">
            <div class="menu-group-header"  data-bs-target="#sms">
                <i class="bi bi-chevron-down"></i>
                <span>SMS</span>
            </div>
            <ul class="sidebar-menu collapse" id="sms">
                <li><a href="#" class="<?= isActive('sms', $activePage) ?>"><i class="bi bi-chat-dots"></i> Send SMS</a></li>
            </ul>
        </div> -->

        <!-- <div class="menu-group">
            <div class="menu-group-header"  data-bs-target="#userManagement" aria-expanded="<?= isCollapsed('userManagement', $activePage) ? 'true' : 'false' ?>">
                <i class="bi bi-chevron-down"></i>
                <span>User Management</span>
            </div>
            <ul class="sidebar-menu collapse <?= isCollapsed('userManagement', $activePage) ?>" id="userManagement">
                <li><a href="settings-profile.php" class="<?= isActive('settings-profile', $activePage) ?>"><i class="bi bi-person-gear"></i> Users</a></li>
            </ul>
        </div> -->

        <div class="menu-group">
            <div class="menu-group-header">
                <i class="bi bi-gear"></i>
                <span><a href="<?php echo BASE_URL; ?>/app/views/settings.php" class="menu-link <?= isActive('settings', $activePage) ?>">Settings</a></span>
            </div>
        </div>

        <div class="menu-group">
            <div class="menu-group-header">
                <i class="bi bi-box-arrow-right"></i>
                <span><a href="<?php echo BASE_URL; ?>/app/controllers/logout.php" class="menu-link logout-link"> Logout
        </a></span>
            </div>
        </div>
    </div>
</nav>
<script>
document.addEventListener("DOMContentLoaded", () => {

    // Fix chevron rotation on initial page load
    document.querySelectorAll(".sidebar-menu.show").forEach(openMenu => {
        const header = openMenu.previousElementSibling;
        const icon = header.querySelector("i");
        if (icon) icon.classList.add("rotate");
    });

    const headers = document.querySelectorAll(".menu-group-header");

    headers.forEach(header => {
        header.addEventListener("click", () => {
            const targetSelector = header.getAttribute("data-bs-target");
            const target = document.querySelector(targetSelector);
            const icon = header.querySelector("i");

            // Close other groups
            document.querySelectorAll(".sidebar-menu.show").forEach(openMenu => {
                if (openMenu !== target) {
                    new bootstrap.Collapse(openMenu, { toggle: true });
                    openMenu.previousElementSibling.querySelector("i").classList.remove("rotate");
                }
            });

            // Toggle this group
            new bootstrap.Collapse(target, { toggle: true });

            // Toggle chevron rotation
            icon.classList.toggle("rotate");
        });
    });
});

</script>