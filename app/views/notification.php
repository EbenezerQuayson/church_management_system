<?php
// Notifications Page
$activePage = 'notifications';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();

$db = Database::getInstance();

$user_id = $_SESSION['user_id'];

// Fetch notifications
$unread = $db->fetchAll("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC", [$user_id]);
$read   = $db->fetchAll("SELECT * FROM notifications WHERE user_id = ? AND is_read = 1 ORDER BY created_at DESC", [$user_id]);

$unread_count = $db->fetch("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0", [$user_id]);

// $notification_id = $_GET['id'];
// $db->query("UPDATE notifications SET is_read = 1 WHERE id = ? ", [$notification_id]);
// $db->query("UPDATE notifications SET is_read = 1 WHERE id = ?", [$user_id]);


$message = $_GET['msg'] ?? null;
$message_type = $_GET['type'] ?? "success";

// function addNotification($user_id, $message, $link = null)
// {
//    global $db;
//     $db->query("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)", [$user_id, $message, $link]);
// }

// addNotification(5, "A new member has registered", "members/view.php?id=22");

?>

<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid">

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Notifications</h2>

            <button class="btn btn-primary btn-sm" id="markAllRead">
                <i class="fas fa-envelope-open-text"></i> Mark All as Read
            </button>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#unreadTab">
                    Unread (<?php echo count($unread); ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#readTab">
                    Read (<?php echo count($read); ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- UNREAD LIST -->
            <div class="tab-pane fade show active" id="unreadTab">
                <?php if (empty($unread)): ?>
                    <p class="text-muted">No unread notifications.</p>
                <?php endif; ?>

                <?php foreach ($unread as $n): ?>
                    <div class="notification-card unread mb-3 p-3 rounded shadow-sm" 
                         onclick="markRead(<?php echo $n['id']; ?>, this)">
                        <div class="d-flex">
                            
                            <!-- Icon -->
                            <div class="notif-icon me-3">
                                <i class="fas fa-bell"></i>
                            </div>

                            <!-- Details -->
                            <div>
                                <p class="fw-bold mb-1"><?php echo htmlspecialchars($n['title']); ?></p>
                                <p class="text-muted mb-1"><?php echo htmlspecialchars($n['message']); ?></p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> 
                                    <?php echo date("M d, Y h:i A", strtotime($n['created_at'])); ?>
                                </small>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- READ LIST -->
            <div class="tab-pane fade" id="readTab">
                <?php if (empty($read)): ?>
                    <p class="text-muted">No read notifications.</p>
                <?php endif; ?>

                <?php foreach ($read as $n): ?>
                    <div class="notification-card mb-3 p-3 rounded border">
                        <div class="d-flex">

                            <div class="notif-icon me-3 read">
                                <i class="fas fa-envelope-open"></i>
                            </div>

                            <div>
                                <p class="fw-bold mb-1"><?php echo htmlspecialchars($n['title']); ?></p>
                                <p class="text-muted mb-1"><?php echo htmlspecialchars($n['message']); ?></p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> 
                                    <?php echo date("M d, Y h:i A", strtotime($n['created_at'])); ?>
                                </small>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

    </div>
</div>
<?php include 'mark_all_read.php'; ?>
<!-- JS -->
<script>
function markRead(id, element) {
    fetch("mark_read.php?id=" + id)
        .then(() => {
            element.classList.remove('unread');
            element.querySelector('.notif-icon').classList.add('read');
        });
}

// Individual unread notification click
document.querySelectorAll('.notification-card.unread').forEach(card => {
    card.addEventListener('click', () => markRead(card.dataset.id, card));
});

// Mark all as read
document.getElementById("markAllRead").addEventListener("click", () => {
    fetch("mark_all_read.php")
        .then(() => {
            // Update UI
            document.querySelectorAll('.notification-card.unread').forEach(card => {
                card.classList.remove('unread');
                card.querySelector('.notif-icon').classList.add('read');
            });
            // Optional: reload page if you want everything refreshed
            location.reload();
        })
        .catch(err => console.error("Failed to mark all as read:", err));
});
</script>
<style>
.notification-card {
    cursor: pointer;
    border-left: 4px solid transparent;
    transition: 0.2s;
    background: #fff;
}

.notification-card.unread {
    background: #f0f6ff;
    border-left-color: var(--primary-color);
}

.notification-card:hover {
    background: #eaf2fb;
}

.notif-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #e6f2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: var(--primary-color);
}

.notif-icon.read {
    background: #eaeaea;
    color: gray;
}
</style>

<?php include 'footer.php'; ?>
