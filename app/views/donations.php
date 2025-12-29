<?php
// Donations Page
$activePage = 'income';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Donation.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Notifications.php';


requireLogin();

//Models
$donation = new Donation();
$member_model = new Member();
$notification = new Notification();
$donations = $donation->getAll();
$members = $member_model->getAll();
$monthly_total = $donation->getTotalByMonth();
$total_amount = $donation->getTotalAmount();

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();
$admins = $db->fetchAll("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Admin'");





$message = '';
$message_type = '';

//Logic to prevent resubmission after refresh
if(isset($_GET['msg'])){
    switch($_GET['msg']){
        case 'added':
            $message = 'Income recorded successfully!';
            $message_type = 'success';
            break;
        case 'add_failed':
            $message = 'Failed to record income';
            $message_type = 'error';
            break;
        case 'updated':
            $message = 'Income updated successfully!';
            $message_type = 'success';
            break;
        case 'update_failed':
            $message = 'Failed to update income';
            $message_type = 'error';
            break;
        case 'deleted':
            $message = 'income deleted successfully!';
            $message_type = 'success';
            break;
        case 'delete_failed':
            $message = 'Failed to delete income';
            $message_type = 'error';
            break;
        default:
            $message = '';
            $message_type = '';
            break;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'create';

   


$income_source = 'anonymous' ;
   if(!empty($_POST['member_id']) && $_POST['member_id'] !== 'service_total'){
    $member_id = (int) $_POST['member_id'];
    $income_source = 'member';
   } elseif($_POST['member_id'] === 'service_total'){
    $member_id = null;
    $income_source = 'service_total';
   } else{
    $member_id = null;
    $income_source = 'anonymous';
   }

    // Shared Data
    $commonData = [
        'member_id'      => $member_id,
        'amount'         => $_POST['amount'] ?? 0,
        'donation_type'  => $_POST['donation_type'] ?? 'General',
        'donation_date'  => $_POST['donation_date'] ?? date('Y-m-d'),
        'notes'          => trim($_POST['notes'] ?? ''),
        'income_source' => $income_source
    ];

    /* ------------------------------
       CREATE DONATION
    ------------------------------ */
    if ($action === 'create') {

        if ($commonData['amount'] <= 0) {
            $message = 'Amount must be greater than 0';
            $message_type = 'error';
        } else {
            if ($donation->create($commonData)) {
                foreach($admins as $admin){
                     $notification->create(
                    $admin['id'],
                    'New Income Recorded',
                    'An income of ¢' . number_format($commonData['amount'], 2) . ' was recorded.',
                    'donations.php');
                }
               
                 header("Location: donations.php?msg=added");
                 exit();
                $donations = $donation->getAll();
            } else {
                // $message = 'Failed to record donation';
                // $message_type = 'error';
                header("Location: donations.php?msg=add_failed");
                exit();
            }
        }
    }

    /* ------------------------------
       EDIT DONATION
    ------------------------------ */
    if ($action === 'edit') {

        $id = (int) $_POST['id'];

        if ($commonData['amount'] <= 0) {
            $message = 'Amount must be greater than 0';
            $message_type = 'error';
        } else {
            if ($donation->update($id, $commonData)) {
                    foreach($admins as $admin){
                        $notification->create(
                        $admin['id'],
                        'Income Updated',
                        'An income of ¢' . number_format($commonData['amount'], 2) . ' was updated.',
                        'donations.php');
                    }
                header("Location: donations.php?msg=updated");
                exit();
                $donations = $donation->getAll();
            } else {
                // $message = 'Failed to update donation';
                // $message_type = 'error';
                 header("Location: donations.php?msg=update_failed");
                 exit();
            }
        }
    }

    /* ------------------------------
       DELETE DONATION
    ------------------------------ */
    if ($action === 'delete' && isset($_POST['id'])) {

        $id = (int) $_POST['id'];

        if ($donation->delete($id)) {
           foreach($admins as $admin){
                $notification->create(
                $admin['id'],
                'Income Deleted',
                'An income record was deleted.',
                'donations.php');
            }
             header("Location: donations.php?msg=deleted");
             exit();
            $donations = $donation->getAll();
        } else {
             header("Location: donations.php?msg=delete_failed");
             exit();
        }
    }
}

$count = 1;

?>
<?php include 'header.php'; ?>
<style>
    /* Mobile optimization */
@media (max-width: 576px) {

    .btn {
        padding: 0.35rem 0.6rem;
        font-size: 0.8rem;
    }

    .btn i {
        font-size: 0.8rem;         /* smaller icons */
        margin-right: 4px;
    }

    h2 {
        font-size: 1.25rem;        /* reduce page title size */
    }

    .btn span {
        display: none;
    }

    }

</style>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid mt-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Income</h2>
            <div>
             <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportSummaryModal">
                <i class="fas fa-file-export"></i> <span>Export Summary</span>
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                <i class="fas fa-plus"></i> <span>Add Income</span>
            </button>
</div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card stat-card-green">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <p class="stat-value">¢<?php echo number_format($monthly_total['total'], 2); ?></p>
                        <p class="stat-label">This Month (<?php echo date('F Y'); ?>)</p>
                    </div>
                </div>
            </div>
             <div class="col-md-4">
                <div class="card stat-card stat-card-orange">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <p class="stat-value">¢<?php echo number_format($total_amount['total'], 2); ?></p>
                        <p class="stat-label">Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card stat-card-blue">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <p class="stat-value"><?php echo count($donations); ?></p>
                        <p class="stat-label">Total Income</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Donations Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-mobile-friendly">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th class="col-hide-mobile">#</th>
                                <th class="col-essential">Member</th>
                                <th class="col-essential">Amount (¢)</th>
                                <th class="col-hide-mobile">Type</th>
                                <th class="col-hide-mobile">Date</th>
                                <th class="col-essential text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($donations)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No income recorded yet.</td>
                            </tr>
                        <?php endif; ?>

                            <?php foreach ($donations as $d): ?>
                                <tr>
                                    <td class="col-hide-mobile"><?= $count++ ?></td>
                                    <td class="col-essential"><?php 
                                    if($d['income_source'] === 'service_total'){
                                        echo 'Service Total';
                                    } elseif ($d['income_source'] === 'member' && !empty($d['first_name'])) {
                                        echo htmlspecialchars($d['first_name'] . ' ' . $d['last_name']);
                                    } else {
                                        echo 'Anonymous';
                                    }
 ?></td>
     <td class="col-essential"><strong>¢<?php echo number_format($d['amount'], 2); ?></strong></td>
     <td class="col-hide-mobile"><?php echo ucfirst($d['donation_type']); ?></td>
     <td class="col-hide-mobile"><?php echo date('M d, Y', strtotime($d['donation_date'])); ?></td>
     <td class="col-essential text-end">
    <button class="btn btn-sm btn-outline-primary viewDonationBtn" data-donation-id="<?= $d['id']; ?>"
    data-member-id="<?= $d['member_id'] ?? '' ?>" 
   data-member="<?php
    if ($d['income_source'] === 'service_total') echo 'Service Total';
    elseif ($d['income_source'] === 'member' && $d['first_name'])
        echo $d['first_name'] . ' ' . $d['last_name'];
    else echo 'Anonymous';
?>"
data-source="<?= $d['income_source']; ?>"
    data-amount="<?= $d['amount']; ?>"
    data-type="<?= $d['donation_type']; ?>"
    data-date="<?= $d['donation_date']; ?>"
    data-notes="<?= htmlspecialchars($d['notes']); ?>"
    data-bs-target="#donationDetails"
    data-bs-toggle="modal">
    <i class="fas fa-eye"></i>
</button>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Donation Modal -->
<div class="modal fade" id="addDonationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="fas fa-gift"></i> Record Income</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="member_id" class="form-label">Member (Optional)</label>
                        <select class="form-select" name="member_id">
                            <option value="">Anonymous</option>
                            <option value="service_total">Service Total</option>
                            <?php foreach ($members as $m): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">¢</span>
                            <input type="number" class="form-control" name="amount" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="donation_type" class="form-label">Type</label>
                        <select class="form-select" name="donation_type">
                            <option value="General">General Offering</option>
                            <option value="Service Offering">Service Offering</option>
                            <option value="Service Tithe">Service Tithe</option>
                            <option value="Tithe">Tithe</option>
                            <option value="Building Fund">Building Fund</option>
                            <option value="Missions">Missions</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="donation_date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="donation_date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Income</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Donation Details Modal -->
<div class="modal fade" tabindex="-1" id="donationDetails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background-color: var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-gift"></i> Income Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="donationDetailsContent">
                    <!-- <p class="text-center text-muted">Loading...</p> -->
   
    <!-- Hidden details -->
     <table class="table table-bordered" id="donationDetailsTable">
    <tr><th>Member</th><td id="detail_member"></td></tr>
    <tr><th>Amount</th><td id="detail_amount"></td></tr>
    <tr><th>Type</th><td id="detail_type"></td></tr>
    <tr><th>Date</th><td id="detail_date"></td></tr>
    <tr><th>Notes</th><td id="detail_notes"></td></tr>
</table>   
            </div>

            </div>

            <div class="modal-footer d-flex justify-content-between">

                <div>
                    <button id="editDonationBtn" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</button>
                    <button id="deleteDonationBtn" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDonationModal"><i class="fas fa-trash"></i> Delete</button>
                </div>

                <button id="printDonationBtn" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>

        </div>
    </div>
</div>
<!-- Edit Donation Modal -->
<div class="modal fade"  id="editDonationModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="editDonationForm">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_donation_id">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Income</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Member (Optional)</label>
                    <select class="form-select" name="member_id" id="edit_member_id">
                        <option value="anonymous">Anonymous</option>
                        <option value="service_total">Service Total</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" class="form-control" name="amount" id="edit_amount" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="donation_type" id="edit_type">
                        <option value="General">General Offering</option>
                        <option value="Service Offering">Service Offering</option>
                        <option value="Service Tithe">Service Tithe</option>
                        <option value="Tithe">Tithe</option>
                        <option value="Building Fund">Building Fund</option>
                        <option value="Missions">Missions</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="donation_date" id="edit_date">
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" id="edit_notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<!-- Delete Modal -->
<div class="modal fade" id="deleteDonationModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="deleteDonationForm">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="delete_donation_id">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Delete Income</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this income?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>



<!-- Export Summary Modal -->
 <div class="modal fade" id="exportSummaryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Export Income Summary</h5>
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
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".viewDonationBtn").forEach(btn => {
        btn.addEventListener("click", function () {

            const donationId = this.dataset.donationId;

            document.getElementById("detail_member").textContent = this.dataset.member;
            document.getElementById("detail_amount").textContent = "¢" + parseFloat(this.dataset.amount).toFixed(2);
            document.getElementById("detail_type").textContent = this.dataset.type;
            document.getElementById("detail_date").textContent = this.dataset.date;
            document.getElementById("detail_notes").textContent = this.dataset.notes || "-";

            // Buttons
            // document.getElementById("editDonationBtn").onclick = () => {
            //     window.location.href = "edit_donation.php?id=" + this.dataset.donationId;
            // };

            // document.getElementById("deleteDonationBtn").onclick = () => {
            //     if (confirm("Are you sure?")) {
            //         window.location.href = "delete_donation.php?id=" + this.dataset.donationId;
            //     }
            // };

            // Edit modal
        const editMemberSelect = document.getElementById("edit_member_id");
        document.getElementById("edit_donation_id").value = donationId;
        if (this.dataset.source === 'service_total') {
            editMemberSelect.value = 'service_total';
        } else if( this.dataset.source === 'anonymous') {
            editMemberSelect.value = 'anonymous';
        }  else {
            editMemberSelect.value = this.dataset.memberId;
        }

        document.getElementById("edit_amount").value = this.dataset.amount;
        document.getElementById("edit_type").value = this.dataset.type;
        document.getElementById("edit_date").value = this.dataset.date;
        document.getElementById("edit_notes").value = this.dataset.notes || "";

            // Delete modal
        document.getElementById("delete_donation_id").value = donationId;

//Print button
            document.getElementById("printDonationBtn").onclick = () => {
                window.open("receipt.php?id=" + donationId, "_blank");
            };

        });
    });

});

// Export Buttons
// document.getElementById("exportCsvBtn").addEventListener("click", function() {
//     window.location.href = "export/income_export_summary_csv.php";
// });

document.getElementById("exportPdfBtn").addEventListener("click", function() {
    window.location.href = "export/income_export_summary_pdf.php";
});

document.getElementById("exportExcelBtn").addEventListener("click", function() {
    window.location.href = "export/income_export_summary_excel.php";
});


//Prevent edit model to be opened on top of details model
document.getElementById("editDonationBtn").addEventListener("click", () => {
    const detailsModal = bootstrap.Modal.getInstance(
        document.getElementById("donationDetails")
    );
    detailsModal.hide();

    const editModal = new bootstrap.Modal(
        document.getElementById("editDonationModal")
    );
    editModal.show();
});



</script>



</body>
</html>


<?php include 'footer.php'; ?>
