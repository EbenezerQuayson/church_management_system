
<?php
$activePage= 'overview';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';


// Pull data from your database later — for now placeholders:
$totalIncome = 12000;
$totalExpenses = 8500;
$currentBalance = $totalIncome - $totalExpenses;

$monthlyIncome = 2500;
$monthlyExpenses = 1900;
?>

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
                    <p class="text-success mb-0"><small>↑ This Year</small></p>
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
                    <p class="text-danger mb-0"><small>↓ This Year</small></p>
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
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx1 = document.getElementById('incomeExpenseChart');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
        datasets: [
            {
                label: "Income",
                data: [1200, 1500, 1800, 1700, 2000, 2300, 2500, 2400, 2100, 2600, 2800, 3000],
                borderWidth: 2,
            },
            {
                label: "Expenses",
                data: [900, 1100, 1000, 1300, 1500, 1400, 1600, 1700, 1800, 1750, 1900, 2000],
                borderWidth: 2,
            }
        ]
    }
});

const ctx2 = document.getElementById('expenseBreakdownChart');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ["Utilities", "Maintenance", "Outreach", "Welfare", "Admin"],
        datasets: [{
            data: [1200, 750, 900, 500, 300],
        }]
    }
});
</script>


<?php include 'footer.php'; ?>

