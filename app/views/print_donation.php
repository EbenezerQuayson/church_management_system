<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Donation.php';
require_once __DIR__ . '/../models/Member.php';

if (!isset($_GET['id'])) {
    die("Invalid receipt request.");
}

$donation = new Donation();
$memberModel = new Member();

$data = $donation->getById($_GET['id']);

if (!$data) {
    die("Donation not found.");
}

// Determine contributor label
if ($data['member_id'] === null && $data['notes'] === 'service_total') {
    $memberName = "Service Total";
} elseif (!empty($data['first_name'])) {
    $memberName = $data['first_name'] . " " . $data['last_name'];
} else {
    $memberName = "Anonymous";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Donation Receipt</title>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 30px;
        background: #f7f7f7;
    }
    .receipt-container {
        background: white;
        padding: 25px;
        width: 700px;
        margin: auto;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    .header {
        text-align: center;
        margin-bottom: 25px;
    }
    .header img {
        width: 90px;
        margin-bottom: 10px;
    }
    h2 {
        margin: 5px 0;
        color: #003da5; /* Methodist blue */
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 16px;
    }
    table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .label {
        font-weight: bold;
        width: 200px;
        color: #003da5;
    }
    .footer {
        margin-top: 30px;
        text-align: center;
        font-size: 14px;
        color: #555;
    }
    .print-btn {
        display: block;
        margin: 20px auto;
        padding: 10px 25px;
        background: #003da5;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }
    @media print {
        .print-btn { display: none; }
        body { background: white; }
    }
</style>
</head>

<body>

<div class="receipt-container">

    <div class="header">
        <!-- Replace with your logo path -->
        <img src="/church_management_system/public/images/methodist_logo.png">
        <h2>Donation Receipt</h2>
        <p style="margin: 0; font-size:14px;">The Methodist Church</p>
    </div>

    <table>
        <tr>
            <td class="label">Receipt ID:</td>
            <td>#<?php echo $data['id']; ?></td>
        </tr>
        <tr>
            <td class="label">Contributor:</td>
            <td><?php echo htmlspecialchars($memberName); ?></td>
        </tr>
        <tr>
            <td class="label">Donation Type:</td>
            <td><?php echo htmlspecialchars($data['donation_type']); ?></td>
        </tr>
        <tr>
            <td class="label">Amount:</td>
            <td><strong>¢<?php echo number_format($data['amount'], 2); ?></strong></td>
        </tr>
        <tr>
            <td class="label">Date:</td>
            <td><?php echo date('M d, Y', strtotime($data['donation_date'])); ?></td>
        </tr>
        <tr>
            <td class="label">Notes:</td>
            <td><?php echo !empty($data['notes']) ? htmlspecialchars($data['notes']) : "—"; ?></td>
        </tr>
    </table>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>

    <div class="footer">
        Thank you for your contribution. God bless you richly.
    </div>

</div>

<script>
    // Auto-print when loaded
    window.onload = () => {
        setTimeout(() => { window.print(); }, 500);
    };
</script>

</body>
</html>
