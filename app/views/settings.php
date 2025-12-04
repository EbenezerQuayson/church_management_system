<?php
// Settings Page
$activePage = 'settings';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
$pdo = Database::getInstance()->getConnection();

//Fetch logged-in user details
$userId = $_SESSION['user_id'] ?? null;
$user = null;
if($userId) {
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_photo FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}



$db = Database::getInstance();
$message = '';
$message_type = '';

// Get all settings
$settings = $db->fetchAll("SELECT * FROM settings");
$settings_array = [];
foreach ($settings as $setting) {
    $settings_array[$setting['setting_key']] = $setting['setting_value'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $updates = [
        'church_name' => trim($_POST['church_name'] ?? ''),
        'church_address' => trim($_POST['church_address'] ?? ''),
        'church_phone' => trim($_POST['church_phone'] ?? ''),
        'church_email' => trim($_POST['church_email'] ?? ''),
        'primary_color' => $_POST['primary_color'] ?? '#003DA5',
        'secondary_color' => $_POST['secondary_color'] ?? '#CC0000',
        'accent_color' => $_POST['accent_color'] ?? '#F4C43F',
    ];

    $success = true;
    foreach ($updates as $key => $value) {
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                ON DUPLICATE KEY UPDATE setting_value = :value";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        if (!$stmt->execute()) {
            $success = false;
        }
    }

    if ($success) {
        $message = 'Settings updated successfully!';
        $message_type = 'success';
        $settings = $db->fetchAll("SELECT * FROM settings");
        $settings_array = [];
        foreach ($settings as $setting) {
            $settings_array[$setting['setting_key']] = $setting['setting_value'];
        }
    } else {
        $message = 'Failed to update settings';
        $message_type = 'error';
    }
}
?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <h2 class="fw-bold mb-4" style="color: var(--primary-color);">Settings</h2>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Settings Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button">Branding</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">Profile</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="church_name" class="form-label">Church Name</label>
                                <input type="text" class="form-control" name="church_name" value="<?php echo htmlspecialchars($settings_array['church_name'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="church_address" class="form-label">Church Address</label>
                                <input type="text" class="form-control" name="church_address" value="<?php echo htmlspecialchars($settings_array['church_address'] ?? ''); ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="church_phone" class="form-label">Church Phone</label>
                                    <input type="tel" class="form-control" name="church_phone" value="<?php echo htmlspecialchars($settings_array['church_phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="church_email" class="form-label">Church Email</label>
                                    <input type="email" class="form-control" name="church_email" value="<?php echo htmlspecialchars($settings_array['church_email'] ?? ''); ?>">
                                </div>
                            </div>
                            <button type="submit" name="update_settings" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Branding Settings -->
            <div class="tab-pane fade" id="branding" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="primary_color" class="form-label">Primary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" name="primary_color" value="<?php echo htmlspecialchars($settings_array['primary_color'] ?? '#003DA5'); ?>">
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings_array['primary_color'] ?? '#003DA5'); ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="secondary_color" class="form-label">Secondary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" name="secondary_color" value="<?php echo htmlspecialchars($settings_array['secondary_color'] ?? '#CC0000'); ?>">
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings_array['secondary_color'] ?? '#CC0000'); ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="accent_color" class="form-label">Accent Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" name="accent_color" value="<?php echo htmlspecialchars($settings_array['accent_color'] ?? '#F4C43F'); ?>">
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings_array['accent_color'] ?? '#F4C43F'); ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="update_settings" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Profile Settings -->
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">My Profile</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <img src="<?php echo BASE_URL; echo '/'; echo htmlspecialchars($user['profile_photo'] ?? '/assets/images/placeholder-user.jpg'); ?>" alt="Profile" class="img-fluid rounded">
                            </div>
                            <div class="col-md-9">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                                <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
