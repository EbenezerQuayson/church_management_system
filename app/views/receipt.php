<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Donation.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/config.php';


if (!isset($_GET['id'])) {
    die("Invalid receipt request.");
}

$donation_id = (int) $_GET['id'];

$donation = new Donation();
$member_model = new Member();

// Fetch donation details
$details = $donation->find($donation_id);

if (!$details) {
    die("Donation not found.");
}

// Determine member display
if ($details['member_id'] === null && $details['notes'] === "service_total") {
    $member_name = "Service Total";
} elseif ($details['first_name']) {
    $member_name = $details['first_name'] . " " . $details['last_name'];
} else {
    $member_name = "Anonymous";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Donation Receipt</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            padding: 40px;
            background: #f8f9fa;
        }

        .receipt {
            background: #ffffff;
            border: 3px solid #003da5;
            padding: 25px;
            width: 480px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            color: #003da5;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .sub-header {
            font-size: 15px;
            color: #cc0000;
            margin-top: 5px;
            font-weight: 600;
        }

        .generated {
            font-size: 12px;
            color: #555;
        }

        table {
            width: 100%;
            font-size: 16px;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        tr td:last-child {
            text-align: right;
        }

        th {
            text-align: left;
            padding-top: 10px;
            color: #003da5;
            width: 40%;
        }

        td {
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #555;
            padding-top: 10px;
            border-top: 2px solid #003da5;
        }

        /* Print Optimized */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .receipt {
                box-shadow: none;
                border-radius: 0;
                width: 95%;
            }
        }
    </style>
</head>

<body onload="window.print();">
    <div class="receipt">

        <div class="header">
            <h2>Donation Receipt</h2>
            <div class="sub-header">Methodist Church</div>
            <div class="generated">Generated on <?php echo date('M d, Y'); ?></div>
        </div>

        <table>
            <tr>
                <th>Member:</th>
                <td><?php echo htmlspecialchars($member_name); ?></td>
            </tr>

            <tr>
                <th>Amount:</th>
                <td>¢<?php echo number_format($details['amount'], 2); ?></td>
            </tr>

            <tr>
                <th>Type:</th>
                <td><?php echo htmlspecialchars($details['donation_type']); ?></td>
            </tr>

            <tr>
                <th>Date:</th>
                <td><?php echo date('M d, Y', strtotime($details['donation_date'])); ?></td>
            </tr>

            <tr>
                <th>Notes:</th>
                <td><?php echo $details['notes'] ?: "-"; ?></td>
            </tr>
        </table>

        <div class="footer">
            Thank you for your generous contribution.
        </div>

    </div>
</body>
</html>



<!-- <div class="receipt-wrapper">
    <div class="receipt-header">
        <img src="<?php echo BASE_URL; ?>/assets/methodist-logo.png" alt="Church Logo">
        <h2>Methodist Church - Receipt</h2>
    </div>

    <div class="receipt-details">
        <table>
            <tr>
                <th>Member Name:</th>
                <td><?= $member_name ?></td>
            </tr>

            <tr>
                <th>Type:</th>
                <td><?= $type ?></td>
            </tr>

            <tr>
                <th>Amount:</th>
                <td>₵<?= number_format($amount, 2) ?></td>
            </tr>

            <tr>
                <th>Date:</th>
                <td><?= $date ?></td>
            </tr>

            <tr>
                <th>Recorded By:</th>
                <td><?= $recorded_by ?></td>
            </tr>
        </table>
    </div>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>

    <div class="receipt-footer">
        <p><strong>Thank you for giving!</strong><br>
        Your generosity helps support the ministry.</p>
    </div>
</div> -->
