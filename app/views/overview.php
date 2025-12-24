
<?php
$activePage= 'overview';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/Donation.php';
require_once __DIR__ . '/../models/ExpenseCategory.php';
require_once __DIR__ . '/../models/Overview.php';

requireLogin();

$user_id = $_SESSION['user_id'];


$db = Database::getInstance();
$pdo = Database::getInstance() -> getConnection();


//Model
$expenseModel = new Expense($pdo);
$donationModel = new Donation();
$expenseCategoryModel = new ExpenseCategory($pdo);
$overviewModel = new Overview($pdo);



// Pull data from your database 
$totalIncome = $donationModel->getTotalAmount();
$totalIncome = $totalIncome['total'];
$totalExpenses = $expenseModel->getTotalAmount();
$totalExpenses = $totalExpenses['total_amount'];
$currentBalance = $totalIncome - $totalExpenses;

$monthlyIncome = $donationModel->getTotalByMonth();
$monthlyIncome = $monthlyIncome['total'];
$monthlyExpenses = $expenseModel->getTotalByMonth(date('Y'), date('m'));
$monthlyExpenses = $monthlyExpenses['total_expense'];

//Overview
$transactions = $overviewModel->getRecentTransactions(5);
$monthlyData = $overviewModel->getMonthlyIncomeExpense(date('Y'));
$expenseBreakdown = $overviewModel->getExpenseBreakdown();
$currentYear = date('Y');
$lastYear = $currentYear - 1;
$current = $overviewModel->getYearTotals($currentYear);
$previous = $overviewModel->getYearTotals($lastYear);
$currentIncome = $current['total_income'];
$currentExpense = $current['total_expenses'];
$previousExpense = $previous['total_expenses'];
$previousIncome = $previous['total_income'];

//Determining direction of arrow for expense
if($previousExpense > 0){
    $expenseChange = (($currentExpense - $previousExpense)/$previousExpense)*100;
} else{
    $expenseChange = 0;
}

//Determining arrow direction for income
if($previousIncome > 0){
    $incomeChange = (($currentIncome - $previousIncome)/$previousIncome)*100;
} else{
    $incomeChange = 0;
}

$isUpExpense = $expenseChange > 0;
$isDownExpense = $expenseChange < 0;

$isUpIncome = $incomeChange > 0;
$isDownIncome = $incomeChange < 0;

//Arrays for chart
$labels = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

$income = array_fill(0, 12, 0);
$expenses = array_fill(0, 12, 0);

foreach ($monthlyData as $data) {
    $month = $data['month'] - 1; // Adjust month index (0-based)
    $income[$month] = $data['income'];
    $expenses[$month] = $data['expense'];
}

$expenseLabels = array_column($expenseBreakdown, 'category');
$expenseTotals = array_column($expenseBreakdown, 'total');



?>
<!--  -->
<?php include 'header.php'; ?>
<div class="main-content">
<?php include 'sidebar.php'; ?>

<div class="container-fluid">

    <h2 class="mt-4">Finance Overview</h2>
    <p class="text-muted">Summary of financial activities, donations, and expenses.</p>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">

        <!-- Total Income -->
        <div class="col-md-3">
            <div class="card shadow border-0">
                <div class="card-body text-center">
                    <i class="fa fa-hand-holding-heart text-primary fs-1"></i>
                    <h5 class="mt-3">Total Income</h5>
                    <h3 class="fw-bold">GHS <?= number_format($totalIncome); ?></h3>
                    <p class="<?= $isUpIncome ? 'text-success' : 'text-danger' ?> mb-0"><small>
                        <?= $isUpIncome ? '↑' : ($isDownIncome ? '↓' : '—') ?>
                        <?= abs(round($incomeChange, 1)) ?>%
                        This Year
                    </small></p>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-md-3">
            <div class="card shadow border-0">
                <div class="card-body text-center">
                    <i class="fa fa-money-bill-wave text-danger fs-1"></i>
                    <h5 class="mt-3">Total Expenses</h5>
                    <h3 class="fw-bold">GHS <?= number_format($totalExpenses); ?></h3>
                    <p class="<?= $isUpExpense ? 'text-danger' : 'text-success' ?> mb-0"><small>
                        <?= $isUpExpense ? '↑' : ($isDownExpense ? '↓' : '—') ?>
                        <?= abs(round($expenseChange, 1)) ?>%
                        This Year
                    </small></p>
                </div>
            </div>
        </div>

        <!-- Current Balance -->
        <div class="col-md-3">
            <div class="card shadow border-0">
                <div class="card-body text-center">
                    <i class="fa fa-wallet text-success fs-1"></i>
                    <h5 class="mt-3">Current Balance</h5>
                    <h3 class="fw-bold">GHS <?= number_format($currentBalance); ?></h3>
                    <p class="text-muted mb-0"><small>Available Funds</small></p>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="col-md-3">
            <div class="card shadow border-0">
                <div class="card-body text-center">
                    <i class="fa fa-calendar text-info fs-1"></i>
                    <h5 class="mt-3">This Month</h5>
                    <h6 class="fw-bold text-success">Income: GHS <?= number_format($monthlyIncome); ?></h6>
                    <h6 class="fw-bold text-danger">Expenses: GHS <?= number_format($monthlyExpenses); ?></h6>
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
                            <a href="<?php echo BASE_URL; ?>/app/views/donations.php" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Add Income
                            </a>
                            <a href="<?php echo BASE_URL; ?>/app/views/expenses.php" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Add Expense
                            </a>
                            <!-- <a href="<?php echo BASE_URL; ?>/app/views/donations.php" class="btn btn-outline-primary">
                                <i class="fas fa-gift"></i> Record Donation
                            </a>
                            <a href="<?php echo BASE_URL; ?>/app/views/attendance.php" class="btn btn-outline-primary">
                                <i class="fas fa-check-circle"></i> Mark Attendance
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">

        <!-- Income vs Expenses Chart -->
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Income vs Expenses (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="incomeExpenseChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        <div class="col-lg-4">
            <div class="card shadow border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Expense Breakdown</h5>
                </div>
                <div class="card-body">
                    <canvas id="expenseBreakdownChart" height="250"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Transactions -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Transactions</h5>
        </div>

        <div class="card-body p-0">
            <table class="table mb-0 table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Amount (GHS)</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($transactions as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['trans_date']) ?></td>

        <td>
            <span class="badge <?= $row['type'] === 'Donation' ? 'bg-success' : 'bg-danger' ?>">
                <?= $row['type'] ?>
            </span>
        </td>

        <td><?= htmlspecialchars($row['category'] ?? '—') ?></td>

        <td><?= number_format($row['amount'], 2) ?></td>

        <td><?= htmlspecialchars($row['description'] ?? '') ?></td>
    </tr>
<?php endforeach; ?>
</tbody>


                <!-- <tbody>
                    <tr>
                        <td>2025-02-01</td>
                        <td><span class="badge bg-success">Donation</span></td>
                        <td>Tithe</td>
                        <td>300</td>
                        <td>Sunday Service</td>
                    </tr>

                    <tr>
                        <td>2025-02-03</td>
                        <td><span class="badge bg-danger">Expense</span></td>
                        <td>Maintenance</td>
                        <td>120</td>
                        <td>Generator Fuel</td>
                    </tr>

                    <tr>
                        <td>2025-02-05</td>
                        <td><span class="badge bg-success">Donation</span></td>
                        <td>Offering</td>
                        <td>150</td>
                        <td>Youth Meeting</td>
                    </tr>
                </tbody> -->
            </table>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const incomeData = <?=  json_encode($income) ?>;
    const expenseData = <?= json_encode($expenses) ?>;
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    const ctx1 = document.getElementById('expenseBreakdownChart').getContext('2d');

    new Chart(ctx,{
        type: 'line',
        data:{
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: "Income",
                    data: incomeData,
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: "Expenses",
                    data: expenseData,
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        }
    });

    new Chart(ctx1,{
        type: 'doughnut',
        data:{
            labels: <?= json_encode($expenseLabels) ?>,
            datasets: [{
                data: <?= json_encode($expenseTotals) ?>
            }]
        }
    });


// const ctx1 = document.getElementById('incomeExpenseChart');
// new Chart(ctx1, {
//     type: 'line',
//     data: {
//         labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
//         datasets: [
//             {
//                 label: "Income",
//                 data: [1200, 1500, 1800, 1700, 2000, 2300, 2500, 2400, 2100, 2600, 2800, 3000],
//                 borderWidth: 2,
//             },
//             {
//                 label: "Expenses",
//                 data: [900, 1100, 1000, 1300, 1500, 1400, 1600, 1700, 1800, 1750, 1900, 2000],
//                 borderWidth: 2,
//             }
//         ]
//     }
// });

// const ctx2 = document.getElementById('expenseBreakdownChart');
// new Chart(ctx2, {
//     type: 'doughnut',
//     data: {
//         labels: ["Utilities", "Maintenance", "Outreach", "Welfare", "Admin"],
//         datasets: [{
//             data: [1200, 750, 900, 500, 300],
//         }]
//     }
// });
</script>


<?php include 'footer.php'; ?>

