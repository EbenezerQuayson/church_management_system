<?php
$activePage = 'expenses';

include 'header.php';
?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid">

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Expenses</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fas fa-wallet"></i> Record Expense
            </button>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">

            <div class="col-md-4">
                <div class="card stat-card stat-card-green">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <p class="stat-value">¢0.00</p>
                        <p class="stat-label">This Month</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card stat-card-orange">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="stat-value">¢0.00</p>
                        <p class="stat-label">Total Expenses</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card stat-card-blue">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <p class="stat-value">0</p>
                        <p class="stat-label">Total Records</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Expenses Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-hover">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Recorded By</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- Dummy Example Row -->
                            <tr>
                                <td>Fuel</td>
                                <td><strong>¢250.00</strong></td>
                                <td>Jan 12, 2025</td>
                                <td>Admin</td>
                                <td>Generator fuel for service</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary viewExpenseBtn" data-bs-toggle="modal" data-bs-target="#expenseDetailsModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>
</div>


<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background-color: var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-wallet"></i> Record Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id">
                            <option value="">Select Category</option>
                            <option value="">Fuel</option>
                            <option value="">Maintenance</option>
                            <option value="">Utilities</option>
                            <option value="">Supplies</option>
                            <option value="">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">¢</span>
                            <input type="number" step="0.01" class="form-control" name="amount" placeholder="0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d'); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="2" name="description"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Record Expense</button>
                </div>

            </form>
        </div>
    </div>
</div>


<!-- Expense Details Modal -->
<div class="modal fade" id="expenseDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background-color: var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Expense Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">
                    <tr><th>Category</th><td>Fuel</td></tr>
                    <tr><th>Amount</th><td>¢250.00</td></tr>
                    <tr><th>Date</th><td>Jan 12, 2025</td></tr>
                    <tr><th>Recorded By</th><td>Admin</td></tr>
                    <tr><th>Description</th><td>Generator fuel for Sunday service</td></tr>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editExpenseModal"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteExpenseModal"><i class="fas fa-trash"></i> Delete</button>
            </div>

        </div>
    </div>
</div>



<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">

            <div class="modal-header" style="background-color: var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Expense</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id">
                        <option>Fuel</option>
                        <option>Maintenance</option>
                        <option>Utilities</option>
                        <option>Supplies</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">¢</span>
                        <input type="number" name="amount" step="0.01" class="form-control" value="250.00">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d'); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="2">Generator fuel for service</textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save Changes</button>
            </div>

        </form>
    </div>
</div>


<!-- Delete Expense Modal -->
<div class="modal fade" id="deleteExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Delete Expense</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete this expense record?</p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger">Delete</button>
            </div>

        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
