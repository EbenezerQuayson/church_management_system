<?php
// Donations Page

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Donation.php';
require_once __DIR__ . '/../models/Member.php';

requireLogin();

$donation = new Donation();
$member_model = new Member();
$donations = $donation->getAll();
$members = $member_model->getAll();
$monthly_total = $donation->getTotalByMonth();
$total_amount = $donation->getTotalAmount();
$message = '';
$message_type = '';

// $member_id = !empty($_POST['member_id']) ? (int)$_POST['member_id'] : null;
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Determine member_id
    if (!empty($_POST['member_id']) && $_POST['member_id'] !== 'service_total') {
        $member_id = (int) $_POST['member_id'];
    } else {
        $member_id = null; // anonymous or service total
    }

    // Build data array
    $data = [
        'member_id'      => $member_id,
        'amount'         => $_POST['amount'] ?? 0,
        'donation_type'  => $_POST['donation_type'] ?? 'General',
        'donation_date'  => $_POST['donation_date'] ?? date('Y-m-d'),
        'notes'          => trim($_POST['notes'] ?? ''),
    ];

    // If Service Total selected, override notes BEFORE saving
    if (!empty($_POST['member_id']) && $_POST['member_id'] === 'service_total') {
        $data['notes'] = 'service_total';
    }

    // Validate
    if (empty($data['amount']) || $data['amount'] <= 0) {
        $message = 'Amount is required and must be greater than 0';
        $message_type = 'error';
    } else {
        if ($donation->create($data)) {
            $message = 'Donation recorded successfully!';
            $message_type = 'success';
            $donations = $donation->getAll();
        } else {
            $message = 'Failed to record donation';
            $message_type = 'error';
        }
    }
}

?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Donations</h2>
            <div>
             <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportSummaryModal">
                <i class="fas fa-file-export"></i> Export Summary
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                <i class="fas fa-gift"></i> Record Donation
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
                        <p class="stat-label">This Month</p>
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
                        <p class="stat-label">Total Donations</p>
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
                    <table class="table table-hover">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th>Member</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $d): ?>
                                <tr>
                                    <td><?php 
if ($d['member_id'] === null && $d['notes'] === 'service_total') {
    echo "Service Total";
} elseif ($d['first_name']) {
    echo htmlspecialchars($d['first_name'] . ' ' . $d['last_name']);
} else {
    echo "Anonymous";
}
 ?></td>
     <td><strong>¢<?php echo number_format($d['amount'], 2); ?></strong></td>
     <td><?php echo ucfirst($d['donation_type']); ?></td>
     <td><?php echo date('M d, Y', strtotime($d['donation_date'])); ?></td>
     <td><button class="btn btn-sm btn-outline-primary viewDonationBtn"data-donation-id="<?= $d['id']; ?>" 
     data-member="<?php 
        if ($d['member_id'] === null && $d['notes'] === 'service_total') echo 'Service Total';
        elseif ($d['first_name']) echo $d['first_name'] . ' ' . $d['last_name'];
        else echo 'Anonymous';
    ?>"
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
                <h5 class="modal-title"><i class="fas fa-gift"></i> Record Donation</h5>
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
                    <button type="submit" class="btn btn-primary">Record Donation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Donation Details Modal -->
<div class="modal fade" data-bs-dismiss="modal" tabindex="-1" id="donationDetails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background-color: var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-gift"></i> Donation Details</h5>
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

    <!-- <div id="details-<?= $don['id'] ?>" class="donation-details hidden">
        <p><strong>Member:</strong> <?= $don['member_id'] ?></p>
        <p><strong>Amount:</strong> <?= $don['amount'] ?></p>
        <p><strong>Date:</strong> <?= $don['donation_date'] ?></p>
        <p><strong>Type:</strong> <?= $don['donation_type'] ?></p>
        <p><strong>Purpose:</strong> <?= $don['notes'] ?></p>
    </div> -->

                </div>

            </div>

            <div class="modal-footer d-flex justify-content-between">

                <div>
                    <button id="editDonationBtn" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</button>
                    <button id="deleteDonationBtn" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                </div>

                <button id="printDonationBtn" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Export Summary Modal -->
 <div class="modal fade" id="exportSummaryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Export Financial Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Select the format you want to export the summary in:</p>

        <div class="d-grid gap-2">
          <button class="btn btn-success" id="exportCsvBtn">Export as CSV</button>
          <button class="btn btn-secondary" id="exportPdfBtn">Export as PDF</button>
          <button class="btn btn-warning" id="exportExcelBtn">Export as Excel</button>
        </div>
      </div>

    </div>
  </div>
</div>



<script>
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".viewDonationBtn").forEach(btn => {
        btn.addEventListener("click", function () {

            document.getElementById("detail_member").textContent = this.dataset.member;
            document.getElementById("detail_amount").textContent = "¢" + parseFloat(this.dataset.amount).toFixed(2);
            document.getElementById("detail_type").textContent = this.dataset.type;
            document.getElementById("detail_date").textContent = this.dataset.date;
            document.getElementById("detail_notes").textContent = this.dataset.notes || "-";

            // Buttons
            document.getElementById("editDonationBtn").onclick = () => {
                window.location.href = "edit_donation.php?id=" + this.dataset.donationId;
            };

            document.getElementById("deleteDonationBtn").onclick = () => {
                if (confirm("Are you sure?")) {
                    window.location.href = "delete_donation.php?id=" + this.dataset.donationId;
                }
            };

            document.getElementById("printDonationBtn").onclick = () => {
                window.open("receipt.php?id=" + this.dataset.donationId, "_blank");
            };

        });
    });

});

// Export Buttons
document.getElementById("exportCsvBtn").addEventListener("click", function() {
    window.location.href = "export/export_summary_csv.php";
});

document.getElementById("exportPdfBtn").addEventListener("click", function() {
    window.location.href = "export/export_summary_pdf.php";
});

document.getElementById("exportExcelBtn").addEventListener("click", function() {
    window.location.href = "export/export_summary_excel.php";
});



</script>




<!-- 
<script>
document.addEventListener("DOMContentLoaded", function () {

    // When eye button is clicked
    document.querySelectorAll(".viewDonationBtn").forEach(btn => {
        btn.addEventListener("click", function () {

            const id = this.dataset.id;

            // Show modal immediately
            const modal = new bootstrap.Modal(document.getElementById('donationDetailsModal'));
            modal.show();

            // Show loading
            document.getElementById("donationDetailsContent").innerHTML =
                '<p class="text-center text-muted">Loading...</p>';

            // Fetch details
            // fetch('<?php echo BASE_URL; ?>/app/ajax/get_donation.php?id=' + id)
            // .then(res => res.json())
            // .then(res => {
            //     if (res.status === 'success') {
            //         let d = res.data;

            //         // Handle service total
            //         let member = "Anonymous";
            //         if (d.member_id === null && d.notes === 'service_total') {
            //             member = "Service Total";
            //         } else if (d.first_name) {
            //             member = d.first_name + " " + d.last_name;
            //         }

            //         document.getElementById("donationDetailsContent").innerHTML = `
            //             <table class="table table-bordered">
            //                 <tr><th>Member</th><td>${member}</td></tr>
            //                 <tr><th>Amount</th><td>¢${parseFloat(d.amount).toFixed(2)}</td></tr>
            //                 <tr><th>Type</th><td>${d.donation_type}</td></tr>
            //                 <tr><th>Date</th><td>${d.donation_date}</td></tr>
            //                 <tr><th>Notes</th><td>${d.notes || "-"}</td></tr>
            //             </table>
            //         `;

            //         // Attach actions
            //         document.getElementById("editDonationBtn").onclick = () => {
            //             window.location.href = "edit_donation.php?id=" + d.id;
            //         };

            //         document.getElementById("deleteDonationBtn").onclick = () => {
            //             if (confirm("Are you sure you want to delete this donation?")) {
            //                 window.location.href = "delete_donation.php?id=" + d.id;
            //             }
            //         };

            //         document.getElementById("printDonationBtn").onclick = () => {
            //             window.open("print_donation.php?id=" + d.id, "_blank");
            //         };

            //     } else {
            //         document.getElementById("donationDetailsContent").innerHTML =
            //             '<p class="text-danger">Error loading donation details.</p>';
            //     }
            // });

        });
    });

});
</script> -->


</body>
</html>


<?php include 'footer.php'; ?>
