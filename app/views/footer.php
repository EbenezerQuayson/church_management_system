<?php
// Footer Include File
require_once __DIR__ . '/../../config/config.php';
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <!-- Bootstrap JS (make sure this is included first) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ----- 1. Activate tab based on URL hash -----
    const hash = window.location.hash;
    if (hash) {
        const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }

    // ----- 2. Responsive sidebar toggle -----
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active'); // toggles sidebar visibility
        });
    }
});


</script>

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
            fetch('<?php echo BASE_URL; ?>/app/ajax/get_donation.php?id=' + id)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    let d = res.data;

                    // Handle service total
                    let member = "Anonymous";
                    if (d.member_id === null && d.notes === 'service_total') {
                        member = "Service Total";
                    } else if (d.first_name) {
                        member = d.first_name + " " + d.last_name;
                    }

                    document.getElementById("donationDetailsContent").innerHTML = `
                        <table class="table table-bordered">
                            <tr><th>Member</th><td>${member}</td></tr>
                            <tr><th>Amount</th><td>¢${parseFloat(d.amount).toFixed(2)}</td></tr>
                            <tr><th>Type</th><td>${d.donation_type}</td></tr>
                            <tr><th>Date</th><td>${d.donation_date}</td></tr>
                            <tr><th>Notes</th><td>${d.notes || "-"}</td></tr>
                        </table>
                    `;

                    // Attach actions
                    document.getElementById("editDonationBtn").onclick = () => {
                        window.location.href = "edit_donation.php?id=" + d.id;
                    };

                    document.getElementById("deleteDonationBtn").onclick = () => {
                        if (confirm("Are you sure you want to delete this donation?")) {
                            window.location.href = "delete_donation.php?id=" + d.id;
                        }
                    };

                    document.getElementById("printDonationBtn").onclick = () => {
                        window.open("print_donation.php?id=" + d.id, "_blank");
                    };

                } else {
                    document.getElementById("donationDetailsContent").innerHTML =
                        '<p class="text-danger">Error loading donation details.</p>';
                }
            });

        });
    });

});
</script>


</body>
</html>
    
