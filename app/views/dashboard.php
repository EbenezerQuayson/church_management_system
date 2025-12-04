<?php
// Dashboard Page

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

requireLogin();

$db = Database::getInstance();

// Get statistics
$members_count = $db->fetch("SELECT COUNT(*) as count FROM members WHERE status = 'active'");
$events_count = $db->fetch("SELECT COUNT(*) as count FROM events WHERE status = 'scheduled'");
$ministries_count = $db->fetch("SELECT COUNT(*) as count FROM ministries WHERE status = 'active'");
$donations_total = $db->fetch("SELECT COALESCE(SUM(amount), 0) as total FROM donations");

// Get recent activities
$recent_members = $db->fetchAll("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
$recent_events = $db->fetchAll("SELECT * FROM events ORDER BY created_at DESC LIMIT 5");
?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Dashboard</h2>
            <span class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4 g-4">
            <!-- Members Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card stat-card-blue">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="stat-value"><?php echo $members_count['count']; ?></p>
                        <p class="stat-label">Active Members</p>
                    </div>
                </div>
            </div>

            <!-- Events Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card stat-card-green">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <p class="stat-value"><?php echo $events_count['count']; ?></p>
                        <p class="stat-label">Upcoming Events</p>
                    </div>
                </div>
            </div>

            <!-- Ministries Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card stat-card-purple">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <p class="stat-value"><?php echo $ministries_count['count']; ?></p>
                        <p class="stat-label">Active Ministries</p>
                    </div>
                </div>
            </div>

            <!-- Donations Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card stat-card-orange">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <p class="stat-value">¢<?php echo number_format($donations_total['total'], 0); ?></p>
                        <p class="stat-label">Total Amount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mb-4 g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body ">
                        <h5 class="card-title mb-3 ">Quick Actions</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?php echo BASE_URL; ?>/app/views/members.php" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus"></i> Add Member
                            </a>
                            <a href="<?php echo BASE_URL; ?>/app/views/events.php" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-plus"></i> Create Event
                            </a>
                            <a href="<?php echo BASE_URL; ?>/app/views/donations.php" class="btn btn-outline-primary">
                                <i class="fas fa-gift"></i> Record Donation
                            </a>
                            <a href="<?php echo BASE_URL; ?>/app/views/attendance.php" class="btn btn-outline-primary">
                                <i class="fas fa-check-circle"></i> Mark Attendance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row g-4 mt-2">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5><i class="fas fa-users"></i> Recent Members</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr style="background: var(--primary-color); color: white;">
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_members as $member): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($member['email'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($member['join_date'] ?? $member['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-container">
                    <h5><i class="fas fa-calendar"></i> Recent Events</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr style="background: var(--primary-color); color: white;">
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_events as $event): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                        <td><span class="badge bg-success"><?php echo ucfirst($event['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
