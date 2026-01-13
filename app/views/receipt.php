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

$details = $donation->find($donation_id);

if (!$details) {
    die("Donation not found.");
}

if ($details['member_id'] === null && $details['notes'] === "service_total") {
    $member_name = "Service Total";
} elseif (!empty($details['first_name'])) {
    $member_name = $details['first_name'] . " " . $details['last_name'];
} else {
    $member_name = "Anonymous";
}

/**
 * Theme defaults (fallback to Methodist palette)
 * If you already have settings table like your homepage,
 * you can optionally fetch and override these.
 */
$church_name  = 'The Methodist Church Ghana';
$church_motto = 'Your Kingdom Come';

try {
    $db = Database::getInstance();
    $settings = $db->fetchAll("SELECT setting_key, setting_value FROM settings");

    foreach ($settings as $setting) {
        if ($setting['setting_key'] === 'church_name') {
            $church_name = $setting['setting_value'];
        }
        if ($setting['setting_key'] === 'church_motto') {
            $church_motto = $setting['setting_value'];
        }
    }
} catch (Exception $e) {
    // fallback to defaults
}



$primary_color   = '#003DA5';
$secondary_color = '#CC0000';
$accent_color    = '#F4C43F';

// Logo: adjust if your logo is stored elsewhere
$church_logo = defined('BASE_URL')
    ? BASE_URL . '/assets/images/methodist-logo.png'
    : '/assets/images/methodist-logo.png';

$receipt_no = 'REC-' . str_pad((string)$donation_id, 6, '0', STR_PAD_LEFT);
$issued_on  = date('M d, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Income Receipt - <?php echo htmlspecialchars($receipt_no); ?></title>
    <style>
        :root{
            --primary: <?php echo $primary_color; ?>;
            --secondary: <?php echo $secondary_color; ?>;
            --accent: <?php echo $accent_color; ?>;
            --ink: #0f172a;
            --muted: #475569;
            --paper: #ffffff;
            --bg: #f5f7fb;
            --line: rgba(15, 23, 42, 0.12);
        }

        * { box-sizing: border-box; }
        body{
            margin: 0;
            padding: 32px 18px;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Noto Sans", "Liberation Sans", sans-serif;
            background: radial-gradient(1200px 600px at 10% 0%, rgba(0,61,165,.08), transparent 60%),
                        radial-gradient(900px 500px at 100% 10%, rgba(204,0,0,.07), transparent 60%),
                        var(--bg);
            color: var(--ink);
        }

        .page{
            max-width: 820px;
            margin: 0 auto;
        }

        .receipt{
            background: var(--paper);
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(2, 8, 23, 0.14);
            overflow: hidden;
            border: 1px solid rgba(2, 8, 23, 0.08);
        }

        /* Top bar */
        .topbar{
            padding: 18px 22px;
            background: linear-gradient(135deg, rgba(0,61,165,0.10), rgba(204,0,0,0.06));
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand{
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand img{
            width: 54px;
            height: 54px;
            object-fit: contain;
        }

        .brand .title{
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .brand .church-name{
            font-weight: 800;
            font-size: 16px;
            letter-spacing: .3px;
            color: var(--primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand .motto{
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .badge{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .3px;
            border: 1px solid rgba(0,61,165,0.25);
            color: var(--primary);
            background: rgba(0,61,165,0.06);
            white-space: nowrap;
        }

        .badge::before{
            content:"";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--accent);
            box-shadow: 0 0 0 3px rgba(244,196,63,0.25);
        }

        /* Body */
        .content{
            padding: 22px;
        }

        .headline{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            padding-bottom: 14px;
            border-bottom: 1px dashed var(--line);
            margin-bottom: 18px;
        }

        .headline h1{
            margin: 0;
            font-size: 20px;
            letter-spacing: .2px;
        }

        .meta{
            text-align: right;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.6;
        }

        .meta strong{
            color: var(--ink);
            font-weight: 700;
        }

        .grid{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .card{
            border: 1px solid rgba(2, 8, 23, 0.08);
            border-radius: 14px;
            padding: 14px 14px;
            background: linear-gradient(180deg, rgba(255,255,255,1), rgba(255,255,255,0.96));
        }

        .label{
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .value{
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
            word-break: break-word;
        }

        .amount{
            font-size: 20px;
            font-weight: 900;
            color: var(--secondary);
            letter-spacing: .2px;
        }

        .wide{
            grid-column: 1 / -1;
        }

        .note{
            font-size: 13px;
            color: var(--muted);
            line-height: 1.5;
        }

        /* Footer */
        .footer{
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            font-size: 12px;
            color: var(--muted);
        }

        .thanks{
            color: var(--ink);
            font-weight: 700;
        }

        .stamp{
            text-align: right;
            font-size: 12px;
        }

        .stamp .line{
            margin-top: 18px;
            border-top: 1px solid rgba(2,8,23,0.2);
            width: 200px;
            margin-left: auto;
        }

        .stamp .sig{
            margin-top: 6px;
            color: var(--muted);
        }

        /* Print */
        @media print{
            body{
                background: #fff;
                padding: 0;
            }
            .receipt{
                box-shadow: none;
                border: none;
                border-radius: 0;
            }
            .page{
                max-width: 100%;
            }
            .topbar{
                border-bottom: 1px solid #ddd;
            }
        }
    </style>
</head>

<body onload="window.print();">
    <div class="page">
        <div class="receipt">
            <div class="topbar">
                <div class="brand">
                    <img src="<?php echo htmlspecialchars($church_logo); ?>" alt="Church Logo">
                    <div class="title">
                        <div class="church-name"><?php echo htmlspecialchars($church_name); ?></div>
                        <div class="motto"><?php echo htmlspecialchars($church_motto); ?></div>
                    </div>
                </div>

                <div class="badge">Income RECEIPT</div>
            </div>

            <div class="content">
                <div class="headline">
                    <div>
                        <h1>Receipt Details</h1>
                        <div class="note">This document confirms a donation received by the church.</div>
                    </div>
                    <div class="meta">
                        <div><strong>Receipt No:</strong> <?php echo htmlspecialchars($receipt_no); ?></div>
                        <div><strong>Issued:</strong> <?php echo htmlspecialchars($issued_on); ?></div>
                    </div>
                </div>

                <div class="grid">
                    <div class="card">
                        <div class="label">Donor / Member</div>
                        <div class="value"><?php echo htmlspecialchars($member_name); ?></div>
                    </div>

                    <div class="card">
                        <div class="label">Income Type</div>
                        <div class="value"><?php echo htmlspecialchars($details['donation_type']); ?></div>
                    </div>

                    <div class="card">
                        <div class="label">Date</div>
                        <div class="value"><?php echo date('M d, Y', strtotime($details['donation_date'])); ?></div>
                    </div>

                    <div class="card">
                        <div class="label">Amount</div>
                        <div class="amount">¢<?php echo number_format((float)$details['amount'], 2); ?></div>
                    </div>

                    <div class="card wide">
                        <div class="label">Notes</div>
                        <div class="value">
                            <?php echo !empty($details['notes']) ? htmlspecialchars($details['notes']) : '<span class="note">—</span>'; ?>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <div>
                        <div class="thanks">Thank you for your generous contribution.</div>
                        <div class="note">Keep this receipt for your records.</div>
                    </div>

                    <div class="stamp">
                        Authorized Signature
                        <div class="line"></div>
                        <div class="sig"><?php echo htmlspecialchars($church_name); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
