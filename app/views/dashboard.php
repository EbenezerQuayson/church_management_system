<?php
// Dashboard Page
$activePage = 'dashboard';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/ExpenseCategory.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Donation.php';

requireLogin();

$db = Database::getInstance();
$pdo = Database::getInstance()->getConnection();

//Models
$memberModel = new Member();
$eventModel = new Event();
$donationModel = new Donation();
$expenseModel = new Expense($pdo);
$expenseCategoryModel = new ExpenseCategory($pdo);

//Getting Statistics

//Members
$members_count = $memberModel->getTotalCount();
$male_count = $memberModel->getMaleCount();
$female_count = $memberModel->getFemaleCount();
$recent_members = $memberModel->getRecentMembers();

//Expenses
$expense = $expenseModel->getAll();
$categories = $expenseCategoryModel->getAll();
$monthlyExpenses = $expenseModel->getTotalByMonth(date('Y'), date('m'));
$totalExpenses = $expenseModel->getTotalAmount();

//Events
$events_count = $eventModel->getTotalCount();
$events_scheduled_count = $eventModel->getTotalScheduledCount();
$recent_events = $eventModel->getRecentEvents();

//Income / Donations
$donation_total = $donationModel->getTotalAmount();
$donation_count = $donationModel->getTotalCount();

//Ministries 
$ministries_count = $db->fetch("SELECT COUNT(*) as count FROM ministries WHERE status = 'active'");

//year
$selectedYear = $_GET['year'] ?? date('Y');



// $members_count = $db->fetch("SELECT COUNT(*) as count FROM members WHERE status = 'active'");
// $events_count = $db->fetch("SELECT COUNT(*) as count FROM events WHERE status = 'scheduled'");
// $donations_total = $db->fetch("SELECT COALESCE(SUM(amount), 0) as total FROM donations");
// $totalMales = $db->fetch("SELECT COUNT(*) AS total FROM members WHERE status ='active' AND gender='Male'");
// $totalFemales = $db->fetch("SELECT COUNT(*) AS total FROM members WHERE status ='active' AND gender='Female'");
// Get recent activities
// $recent_members = $db->fetchAll("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
// $recent_events = $db->fetchAll("SELECT * FROM events ORDER BY created_at DESC LIMIT 5");

//Chart Data
$monthlyDonations = $db->fetchAll("
    SELECT 
        MONTH(donation_date) AS month,
        COALESCE(SUM(amount), 0) AS total
    FROM donations
    WHERE YEAR(donation_date) = ?
    GROUP BY MONTH(donation_date)
", [$selectedYear]);

$donationTotals = array_fill(0, 12, 0);
foreach ($monthlyDonations as $d) {
    $donationTotals[$d['month'] - 1] = (float)$d['total'];
}

$monthlyExpenses = $db->fetchAll("
    SELECT 
        MONTH(expense_date) AS month,
        COALESCE(SUM(amount), 0) AS total
    FROM expenses
    WHERE YEAR(expense_date) = ?
    GROUP BY MONTH(expense_date)
", [$selectedYear]);

$expenseTotals = array_fill(0, 12, 0);
foreach ($monthlyExpenses as $e) {
    $expenseTotals[$e['month'] - 1] = (float)$e['total'];
}


$ministryStats = $db->fetchAll("
    SELECT 
        m.name AS ministry_name,
        COUNT(mm.member_id) AS total_members
    FROM ministries m
    LEFT JOIN ministry_members mm 
        ON m.id = mm.ministry_id
    WHERE m.status = 'active'
    GROUP BY m.id
    ORDER BY total_members DESC
");

$ministryLabels = [];
$ministryCounts = [];

foreach ($ministryStats as $row) {
    $ministryLabels[] = $row['ministry_name'];
    $ministryCounts[] = (int)$row['total_members'];
}


//Convert to JSON for JS
$jsDonationData = json_encode($donationTotals);
$jsExpenseData  = json_encode($expenseTotals);

$jsMinistryLabels = json_encode($ministryLabels);
$jsMinistryCounts = json_encode($ministryCounts);
?>
<?php include 'header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .fa-chevron-down {
    transition: transform 0.3s ease;
}
.fa-chevron-down.rotate {
    transform: rotate(180deg);
}

.year-select {
    min-width: 90px;
    padding-right: 2rem; 
}


</style>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Dashboard</h2>
            <span class="text-muted dashboard-greetings">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        </div>

        <!-- Summary Cards -->
         <div class="row mb-2 g-3 justify-content-center">

    <!-- Income Card -->
    <div class="col-md-6 col-lg-5">
        <div class="card stat-card stat-card-lime">
            <div class="card-body">
                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <p class="stat-value">
                    ¢<?php echo number_format($donation_total['total'], 2); ?>
                </p>
                <p class="stat-label">Total Income</p>
            </div>
        </div>
    </div>

    <!-- Expenses Card -->
    <div class="col-md-6 col-lg-5">
        <div class="card stat-card stat-card-pink">
            <div class="card-body">
                <div class="stat-icon">
                    <i class="bi bi-credit-card"></i>
                </div>
                <p class="stat-value">
                    ¢<?php echo number_format($totalExpenses['total_amount'], 2); ?>
                </p>
                <p class="stat-label">Total Expense</p>
            </div>
        </div>
    </div>

</div>
<div class="card mb-4">
    <div class="card-header  text-black d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse"
         data-bs-target="#blueStatsGroup"
         style="cursor:pointer;">
        <span>
            <i class="fas fa-users me-2 text-primary"></i>
            <b>Church Statistics</b>
        </span>
        <i class="fas fa-chevron-down"></i>
    </div>

    <div id="blueStatsGroup" class="collapse">
        <div class="card-body">
            <div class="row g-3">

                <!-- Members -->
                <div class="col-6 col-md-3 col-lg">
                    <div class="card stat-card stat-card-blue h-100">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="stat-value"><?php echo $members_count; ?></p>
                            <p class="stat-label">Active Members</p>
                        </div>
                    </div>
                </div>

                <!-- Events -->
                <div class="col-6 col-md-3 col-lg">
                    <div class="card stat-card stat-card-blue h-100">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <p class="stat-value"><?php echo $events_scheduled_count; ?></p>
                            <p class="stat-label">Upcoming Events</p>
                        </div>
                    </div>
                </div>

                <!-- Ministries -->
                <div class="col-6 col-md-3 col-lg">
                    <div class="card stat-card stat-card-blue h-100">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <p class="stat-value"><?php echo $ministries_count['count']; ?></p>
                            <p class="stat-label">Active Ministries</p>
                        </div>
                    </div>
                </div>

                <!-- Male -->
                <!-- <div class="col-6 col-md-3 col-lg">
                    <div class="card stat-card stat-card-blue h-100">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-male"></i>
                            </div>
                            <p class="stat-value"><?php echo $male_count; ?></p>
                            <p class="stat-label">Male Members</p>
                        </div>
                    </div>
                </div> -->

                <!-- Female -->
                <!-- <div class="col-6 col-md-3 col-lg">
                    <div class="card stat-card stat-card-blue h-100">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-female"></i>
                            </div>
                            <p class="stat-value"><?php echo $female_count; ?></p>
                            <p class="stat-label">Female Members</p>
                        </div>
                    </div>
                </div> -->

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

                              <a href="<?php echo BASE_URL; ?>/app/views/events.php" class="btn btn-outline-primary">
                                <i class="bi bi-building"></i> Add Organization
                            </a>

                            <a href="<?php echo BASE_URL; ?>/app/views/donations.php" class="btn btn-outline-primary">
                                <i class="fas fa-hand-holding-usd"></i> Record Income
                            </a>
                               <a href="<?php echo BASE_URL; ?>/app/views/expenses.php" class="btn btn-outline-primary">
                                <i class="fas fa-cash-register"></i> Record Expense
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
                <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Church Finance Chart</h5>

    <div class="year-selector">
    <form method="GET" class="d-flex align-items-center gap-2">
        <label class="small text-muted mb-0">Year</label>
        <select name="year" class="form-select form-select-sm year-select"
                onchange="this.form.submit()">
            <?php
            $currentYear = date('Y');
            $selectedYear = $_GET['year'] ?? $currentYear;

            for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                $selected = ($selectedYear == $y) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>
    </form>
</div>

</div>

                
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
    const donationData = <?= $jsDonationData ?>;
    const expenseData  = <?= $jsExpenseData ?>;
    const ctx = document.getElementById("donationsChart");
    const ctx1 = document.getElementById("ministryChart");
    // const totalMales = <?php echo $male_count?>;
    // const totalFemales = <?php echo $female_count?>;
    const ministryLabels =<?php echo $jsMinistryLabels;?>;
    const ministryCounts =<?php echo $jsMinistryCounts;?>;


    

    // Chart for Income
new Chart(ctx, {
    type: "line",
    data: {
        labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
        datasets: [
            {
                label: "Income",
                data: donationData,
                borderColor: "rgba(40, 167, 69, 1)",
                backgroundColor: "rgba(40, 167, 69, 0.15)",
                fill: true,
                tension: 0.35,
                borderWidth: 2
            },
            {
                label: "Expenses",
                data: expenseData,
                borderColor: "rgba(220, 53, 69, 1)",
                backgroundColor: "rgba(220, 53, 69, 0.15)",
                fill: true,
                tension: 0.35,
                borderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: "Amount (GHS)"
                },
                ticks: {
                    callback: value => "¢" + value.toLocaleString()
                }
            }
        },
        plugins: {
            legend: {
                position: "bottom"
            },
            tooltip: {
                callbacks: {
                    label: ctx => {
                        return `${ctx.dataset.label}: ¢${ctx.parsed.y.toLocaleString()}`;
                    }
                }
            }
        }
    }
});




//Chart for Ministries
new Chart(ctx1, {
    type: 'doughnut',
    data: {
        labels: ministryLabels,
        datasets: [{
            label: 'Members per Ministry',
            data: ministryCounts,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});


//Chart for Gender
// new Chart(ctx1,{
//     type:'doughnut',
//     data:{
//         labels:['Male', 'Female'],
//         datasets:[{
//             data:[totalMales, totalFemales],
//             borderWidth:1
//         }]
//     }
// });

document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(header => {
    header.addEventListener('click', function () {
        const icon = this.querySelector('.fa-chevron-down');
        icon.classList.toggle('rotate');
    });
});

</script>

<?php include 'footer.php'; ?>
