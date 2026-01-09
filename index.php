<?php
require_once __DIR__ . '/church_management_system/config/config.php';



$church_name = 'The Methodist Church Ghana';
$church_motto = 'Your Kingdom Come';
$primary_color = '#003DA5';
$secondary_color = '#CC0000';
$accent_color = '#F4C43F';
$church_logo = BASE_URL . '/assets/images/methodist-logo.png';

try {
    require_once __DIR__ . '/church_management_system/config/database.php';
    $db = Database::getInstance();
    $settings = $db->fetchAll("SELECT * FROM settings");

    foreach ($settings as $setting) {
        if ($setting['setting_key'] === 'church_name') $church_name = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_motto') $church_motto = $setting['setting_value'];
        if ($setting['setting_key'] === 'primary_color') $primary_color = $setting['setting_value'];
        if ($setting['setting_key'] === 'secondary_color') $secondary_color = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_logo') {
            if ($setting['setting_value'] != null) {
                $church_logo = BASE_URL . '/assets/images/' . $setting['setting_value'];
            }
        }
    }
} catch (Exception $e) {
    // Database not available, use defaults
}

$redirect_url = BASE_URL . '/public/home.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($church_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/assets/css/homepage.css">
    <style>
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
            --accent-color: <?php echo $accent_color; ?>;
        }

        body.redirect-page {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(0, 61, 165, 0.08), rgba(204, 0, 0, 0.06));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .redirect-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.12);
            padding: 2.5rem 3rem;
            max-width: 540px;
            width: 100%;
        }

        .redirect-logo {
            height: 72px;
            width: auto;
        }

        .redirect-spinner {
            width: 3rem;
            height: 3rem;
            border-width: 0.35rem;
        }
    </style>
    <script>
        window.onload = function () {
            const params = new URLSearchParams(window.location.search);
            if (params.get("preview") === "1") {
                return;
            }
            setTimeout(function () {
                window.location.replace("<?php echo $redirect_url; ?>");
            }, 1800);
        };
    </script>
</head>
<body class="redirect-page">
    <main class="redirect-card text-center">
        <img src="<?php echo $church_logo; ?>" alt="<?php echo htmlspecialchars($church_name); ?> logo" class="redirect-logo mb-3">
        <h1 class="h4 fw-bold mb-2"><?php echo htmlspecialchars($church_name); ?></h1>
        <p class="text-muted mb-4"><?php echo htmlspecialchars($church_motto); ?></p>
        <div class="d-flex flex-column align-items-center gap-3">
            <div class="spinner-border text-primary redirect-spinner" role="status" aria-label="Loading"></div>
            <p class="mb-1">Preparing your experience…</p>
            <small class="text-muted">
                If you are not redirected automatically,
                <a class="fw-semibold" href="<?php echo $redirect_url; ?>">tap here to continue</a>.
            </small>
        </div>
    </main>
</body>
</html>
