<?php
$activePage = 'expenses';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/Notifications.php';
require_once __DIR__ . '/../models/ExpenseCategory.php';

requireLogin();

$user_id = $_SESSION['user_id'];

$pdo = Database::getInstance()->getConnection();

// MODELS
$expenseModel = new Expense($pdo);
$categoryModel = new ExpenseCategory($pdo);
$notification = new Notification();
// Expenses
$expenses = $expenseModel->getAll();
$categories = $categoryModel->getAll();
$monthly_total = $expenseModel->getTotalByMonth(date('Y'), date('m'));
$total_amount = $expenseModel->getTotalAmount();

//Notification Admins
$db = Database::getInstance();
$admins = $db->fetchAll("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Admin'");




// Message handling 
$message = '';
$message_type = '';
if (isset($_GET['success'])) {
    $message = 'Expense saved successfully.';
    $message_type = 'success';
} elseif (isset($_GET['updated'])) {
    $message = 'Expense updated successfully.';
    $message_type = 'success';
} elseif (isset($_GET['deleted'])) {
    $message = 'Expense deleted successfully.';
    $message_type = 'success';
}


// -------------------------
// HANDLE UPDATE EXPENSE
// -------------------------
if (isset($_POST['update_expense'])) {
    $id = (int) ($_POST['expense_id'] ?? 0);
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $date_spent = $_POST['date_spent'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');

    // preserve old receipt if none uploaded
    $receipt_path = $_POST['old_receipt'] ?? null;

    // If new file uploaded
    if (!empty($_FILES['receipt']['name'])) {
        $targetDir = __DIR__ . '/../../assets/uploads/receipts/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($_FILES['receipt']['name']));
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
            $receipt_path = 'assets/uploads/receipts/' . $fileName;
        }
    }

    $expenseModel->update(
        $id,
        $date_spent,      // expense_date
        $category_id,     // category_id
        $amount,          // amount
        $description,     // description
        $receipt_path     // receipt_path
    );
    foreach($admins as $admin){
        $notification->create(
        $admin['id'],
        'Expense Updated',
        'An expense of ¢' . number_format($amount, 2) . ' was updated.',
        'expenses.php'
    );
    }
    header("Location: expenses.php?updated=1");
    exit;
}

// -------------------------
// HANDLE DELETE EXPENSE
// -------------------------
if (isset($_POST['delete_expense'])) {
    $id = (int) ($_POST['expense_id'] ?? 0);
    // Optionally remove receipt file from disk before deleting (not implemented)
    $expenseModel->delete($id);
    foreach($admins as $admin){
        $notification->create(
        $admin['id'],
        'Expense Deleted',
        'An expense record was deleted.',
        'expenses.php'
    ); }
    header("Location: expenses.php?deleted=1");
    exit;
}

// FETCH DATA
$categories = $categoryModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
$expenses = $expenseModel->getAll()->fetchAll(PDO::FETCH_ASSOC);


//Adding custom Category
if (isset($_POST['create_expense'])) {
    $categoryName = trim($_POST['category_input']); // from datalist input

    // 1. Check if category exists
    $existingCategory = $categoryModel->getByName($categoryName);

    if ($existingCategory) {
        $categoryId = $existingCategory['id'];
    } else {
        // 2. Create new category and get its ID
        $categoryId = $categoryModel->create($categoryName);
        if (!$categoryId) {
            die("Failed to create category.");
        }
    }

    // 3. Insert expense
    $amount = (float) ($_POST['amount'] ?? 0);
    $date_spent = $_POST['date_spent'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');

    $receipt_path = null;
    if (!empty($_FILES['receipt']['name'])) {
        $targetDir = __DIR__ . '/../../assets/uploads/receipts/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($_FILES['receipt']['name']));
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
            $receipt_path = 'assets/uploads/receipts/' . $fileName;
        }
    }

    $expenseModel->create($date_spent, $categoryId, $amount, $description, $receipt_path);
    foreach($admins as $admin){
        $notification->create(
        $admin['id'],
        'Expense Added',
        'A new expense of ¢' . number_format($amount, 2) . ' was added.',
        'expenses.php'
    ); }
    header("Location: expenses.php?success=1");
    exit;
}


?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Expenses</h2>
            <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportSummaryModal">
                <i class="fas fa-file-export"></i> Export Summary
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fas fa-plus"></i> Add Expense
            </button>
</div>
        </div>

    <!-- Summary Cards -->
      <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card stat-card-green">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="bis bi-cash-stack"></i>
                        </div>
                        <p class="stat-value">¢<?php echo number_format($monthly_total['total_expense'], 2); ?></p>
                        <p class="stat-label">This Month (<?php echo date('F Y'); ?>)</p>
                    </div>
                </div>
            </div>
             <div class="col-md-4">
                <div class="card stat-card stat-card-orange">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="bis bi-credit-card"></i>
                        </div>
                        <p class="stat-value">¢<?php echo number_format($total_amount['total_amount'], 2); ?></p>
                        <p class="stat-label">Total Expenses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card stat-card-blue">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <p class="stat-value"><?php echo count($expenses); ?></p>
                        <p class="stat-label">Total Expenses</p>
                    </div>
                </div>
            </div>
        </div>

        
<!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle table-mobile-friendly">
                    <thead style="background-color: var(--primary-color); color: #fff;">
                        <tr>
                            <th class="col-essential">#</th>
                            <th class="col-essential">Category</th>
                            <th class="col-essential">Amount (¢)</th>
                            <th class="col-hide-mobile">Date</th>
                            <th class="col-hide-mobile">Description</th>
                            <th class="col-hide-mobile">Receipt</th>
                            <th class="col-essential text-end" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No expenses recorded yet.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($expenses as $i => $row): ?>
                            <tr>
                                <td class="col-essential"><?= $i + 1 ?></td>
                                <td class="col-essential"><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
                                <td class="col-essential"><strong>¢<?= number_format($row['amount'], 2) ?></strong></td>
                                <td class="col-hide-mobile"><?= date('M d, Y', strtotime($row['expense_date'])) ?></td>
                                <td class="col-hide-mobile"><?= nl2br(htmlspecialchars($row['description'] ?? '')) ?></td>
                                <td class="col-hide-mobile">
                                    <?php if (!empty($row['receipt_path'])): ?>
                                        <!-- <a href="<?= BASE_URL . '/assets/uploads/receipts/1765547204_Screenshot_2025-11-06_024117.png' ?>" target="_blank">View</a> -->
                                        <a href="<?= BASE_URL .'/'. htmlspecialchars($row['receipt_path']) ?>" target="_blank">View</a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td class="col-essential text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editExpenseModal<?= $row['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteExpenseModal<?= $row['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- EDIT MODAL -->
                            <div class="modal fade" id="editExpenseModal<?= $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="modal-header" style="background:var(--primary-color); color:white;">
                                                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Expense</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <input type="hidden" name="expense_id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="old_receipt" value="<?= htmlspecialchars($row['receipt_path'] ?? '') ?>">

                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select name="category_id" class="form-select" required>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $row['category_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($cat['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Amount</label>
                                                    <input type="number" name="amount" class="form-control" step="0.01" value="<?= htmlspecialchars($row['amount']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Date</label>
                                                    <input type="date" name="date_spent" class="form-control" value="<?= htmlspecialchars($row['expense_date']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Replace Receipt (optional)</label>
                                                    <input type="file" name="receipt" class="form-control">
                                                    <?php if (!empty($row['receipt_path'])): ?>
                                                        <small class="text-muted">Current: <a href="<?= BASE_URL .'/'. htmlspecialchars($row['receipt_path']) ?>" target="_blank">View</a></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="update_expense" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- DELETE MODAL -->
                            <div class="modal fade" id="deleteExpenseModal<?= $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title"><i class="fas fa-trash"></i> Delete Expense</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this expense record?</p>
                                                <input type="hidden" name="expense_id" value="<?= $row['id'] ?>">
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="delete_expense" class="btn btn-danger">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD EXPENSE MODAL -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addExpenseForm" method="post" enctype="multipart/form-data">
                <div class="modal-header" style="background:var(--primary-color); color:white;">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add Expense</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Category as datalist input -->
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input 
                            list="categoryList" 
                            name="category_input" 
                            class="form-control" 
                            placeholder="Select or type a category" 
                            required
                        >
                        <datalist id="categoryList">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['name']) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date_spent" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Receipt (optional)</label>
                        <input type="file" name="receipt" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_expense" class="btn btn-primary">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

        
    </div>
</div>
<!-- EXPORT SUMMARY MODAL -->
 <div class="modal fade" id="exportSummaryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Export Expenses Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Select the format you want to export the summary in:</p>

        <div class="d-grid gap-2">
          <button class="btn btn-success" id="exportExcelBtn">Export as Excel</button>
          <!-- <button class="btn btn-warning" id="exportCsvBtn">Export as CSV</button> -->
          <button class="btn btn-secondary" id="exportPdfBtn">Export as PDF</button>
        </div>
      </div>

    </div>
  </div>
</div>


<script>
    document.getElementById("exportPdfBtn").addEventListener("click", function() {
    window.location.href = "export/expense_export_summary_pdf.php";
});

document.getElementById("exportExcelBtn").addEventListener("click", function() {
    window.location.href = "export/expense_export_summary_excel.php";
});

</script>

<?php include 'footer.php'; ?>
