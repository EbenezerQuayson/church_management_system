<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../models/Donation.php';
require_once __DIR__ .  '../../../../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;



$donation = new Donation();
$donations = $donation->getAll();
$total = $donation->getTotalAmount();
$monthTotal = $donation->getTotalByMonth();

ob_start();
?>
<html>
<head>
<style>
/*  PDF styling */
</style>
</head>
<body>

<h2>Financial Summary Report</h2>

<p><strong>Total Donations: </strong>¢<?php echo number_format($total['total'], 2); ?></p>
<p><strong>This Month: </strong>¢<?php echo number_format($monthTotal['total'], 2); ?></p>

<table border="1" cellspacing="0" cellpadding="8">
<thead>
    <tr>
        <th>Member</th>
        <th>Amount</th>
        <th>Type</th>
        <th>Date</th>
    </tr>
</thead>
<tbody>
<?php foreach ($donations as $d): ?>
<tr>
    <td>
        <?php 
            if ($d['member_id'] === null) echo "Anonymous";
            else echo htmlspecialchars($d['first_name']." ".$d['last_name']);
        ?>
    </td>
    <td>¢<?php echo number_format($d['amount'],2); ?></td>
    <td><?php echo $d['donation_type']; ?></td>
    <td><?php echo date('M d, Y', strtotime($d['donation_date'])); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream('financial_summary.pdf', ['Attachment' => false]);

?>

