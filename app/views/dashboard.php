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
$totalMales = $db->fetch("SELECT COUNT(*) AS total FROM members WHERE status ='active' AND gender='Male'");
$totalFemales = $db->fetch("SELECT COUNT(*) AS total FROM members WHERE status ='active' AND gender='Female'");
// Get recent activities
$recent_members = $db->fetchAll("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
$recent_events = $db->fetchAll("SELECT * FROM events ORDER BY created_at DESC LIMIT 5");

//Chart Data
$monthlyDonations = $db->fetchAll("SELECT MONTH(donation_date) AS month, COUNT(*) AS total FROM donations GROUP BY MONTH(donation_date)");
$monthlyTotals = array_fill(0, 12, 0);
foreach ($monthlyDonations as $d){
    $monthlyTotals[$d['month'] - 1] = (int)$d['total'];
}

//Convert to JSON for JS
$jsDonationData = json_encode($monthlyTotals);
?>
<?php include 'header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Dashboard</h2>
            <span class="text-muted dashboard-greetings">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
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
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <p class="stat-value">¢<?php echo number_format($donations_total['total'], 0); ?></p>
                        <p class="stat-label">Total Amount</p>
                    </div>
                </div>
            </div>

            <!-- Total Males -->
             <div class="col-md-6 col-lg-3">
            <div class="card stat-card stat-card-teal">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-male"></i>
                    </div>
                    <h3 class="stat-value" id="totalMaleMembers"><?php echo $totalMales['total']; ?></h3>
                    <p class="stat-label">Total Male Christians</p>
                </div>
            </div>
        </div>

        <!-- Total Females -->
             <div class="col-md-6 col-lg-3">
            <div class="card stat-card stat-card-pink">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-female"></i>
                    </div>
                    <h3 class="stat-value" id="totalFemaleMembers"><?php echo $totalFemales['total'];?></h3>
                    <p class="stat-label">Total Female</p>
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

        <!-- Charts Row -->
          <div class="row g-4">
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="mb-3"><i class="bi bi-bar-chart"></i> Church Expenses Chart</h5>
                <canvas id="donationsChart"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-container">
                <h5 class="mb-3"><i class="bi bi-pie-chart"></i> Church Population Chart</h5>
                <canvas id="ministryChart">

                </canvas>
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

<script>
    const donationData =<?php echo $jsDonationData;?>;
    const ctx = document.getElementById("donationsChart");
    const ctx1 = document.getElementById("ministryChart");
    const totalMales = <?php echo $totalMales['total']?>;
    const totalFemales = <?php echo $totalFemales['total']?>;


    console.log(donationData);

    // Chart for Donations
    new Chart(ctx, {
        type: "line",
        data: {
            labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            datasets: [{
                label: "Monthly Donations",
                data: donationData,
                backgroundColor: [
              "rgba(74, 144, 226, 0.8)",
              "rgba(123, 104, 238, 0.8)",
              "rgba(240, 147, 251, 0.8)",
              "rgba(67, 233, 123, 0.8)",
              "rgba(79, 172, 254, 0.8)",
              "rgba(255, 193, 7, 0.8)",
            ],
                borderWidth: 2
            }]
        },

       options: {
         responsive: true,
         maintainAspectRatio: true,
         scales: {
           y: {
             beginAtZero: true,
              ticks: {
                precision: 0,
            //    stepSize: 5,
              },
           },
         },
         plugins: {
           legend: { position: "bottom" },
         },
       },
});

//Chart for Gender
new Chart(ctx1,{
    type:'doughnut',
    data:{
        labels:['Male', 'Female'],
        datasets:[{
            data:[totalMales, totalFemales],
            borderWidth:1
        }]
    }
});

</script>

<?php include 'footer.php'; ?>
