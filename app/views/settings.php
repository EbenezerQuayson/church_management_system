<?php
// Settings Page
$activePage = 'settings';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Ministry.php';
require_once __DIR__ . '/../models/HomepageMinistry.php';

function redirectSelf($anchor = '') {
    $url = $_SERVER['PHP_SELF'];
    
    if ($anchor) {
        $url .= '#' . ltrim($anchor, '#');
    }

    header("Location: " . $url);
    exit;
}




requireLogin();
$pdo = Database::getInstance()->getConnection();
$ministry = new Ministry();
$homepageMinistry = new HomepageMinistry();

$homepageMinistries = $homepageMinistry->getAll();

//Fetch logged-in user details
$userId = $_SESSION['user_id'] ?? null;
$user = null;
if($userId) {
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_photo FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

$db = Database::getInstance();
$ministries = $db->fetchAll("SELECT * FROM ministries WHERE status = 'active' ORDER BY name");


$db = Database::getInstance();
$message = '';
$message_type = '';

// Get all settings
$settings = $db->fetchAll("SELECT * FROM settings");
$settings_array = [];
foreach ($settings as $setting) {
    $settings_array[$setting['setting_key']] = $setting['setting_value'];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $success = true;

    // --- General Settings ---
    if (isset($_POST['update_general'])) {
        $updates = [
            'church_name' => trim($_POST['church_name'] ?? ''),
            'church_address' => trim($_POST['church_address'] ?? ''),
            'church_phone' => trim($_POST['church_phone'] ?? ''),
            'church_email' => trim($_POST['church_email'] ?? '')
        ];

        foreach ($updates as $key => $value) {
            // Preserve old value if empty
            if ($value === '') {
                $value = $settings_array[$key] ?? '';
            }

            $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                    ON DUPLICATE KEY UPDATE setting_value = :value";
            $stmt = $db->getConnection()->prepare($sql);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
            if (!$stmt->execute()) {
                $success = false;
            }
        }

        $message = $success ? 'General settings updated successfully!' : 'Failed to update general settings';
        $message_type = $success ? 'success' : 'error';
    }

    // --- Branding Settings ---
    if (isset($_POST['update_branding'])) {
        $updates = [
            'primary_color' => $_POST['primary_color'] ?? $settings_array['primary_color'] ?? '#003DA5',
            'secondary_color' => $_POST['secondary_color'] ?? $settings_array['secondary_color'] ?? '#CC0000',
            'accent_color' => $_POST['accent_color'] ?? $settings_array['accent_color'] ?? '#F4C43F'
        ];

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

        $message = $success ? 'Branding settings updated successfully!' : 'Failed to update branding settings';
        $message_type = $success ? 'success' : 'error';
    }

    // --- Profile Settings (e.g., password change) ---
     if (isset($_POST['update_profile'])) {
        $success = true;

        $fullName = trim($_POST['name'] ?? '');
        $firstName = $lastName = '';

        if ($fullName !== '') {
            $parts = explode(' ', $fullName, 2); // Split into max 2 parts
            $firstName = $parts[0];
            $lastName = $parts[1] ?? ''; // If no last name, empty string
        } else {
            $firstName = $user['first_name'];
            $lastName = $user['last_name'];
        }
        $name = $firstName .' ' . $lastName;
        $email = trim($_POST['email'] ?? '');

        // Update name and email if not empty
        if ($name !== '' || $email !== '') {
            $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id";
            $stmt = $db->getConnection()->prepare($sql);
            $stmt->bindValue(':first_name', $firstName);
            $stmt->bindValue(':last_name', $lastName);
            $stmt->bindValue(':email', $email ?: $user['email']);
            $stmt->bindValue(':id', $userId);
            if (!$stmt->execute()) {
                $success = false;
            }
        }

        // Handle profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/profile_photos/';
            $filename = uniqid() . '_' . basename($_FILES['profile_photo']['name']);
            $targetPath = $uploadDir . $filename;

            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetPath)) {
                // Update in database
                $sql = "UPDATE users SET profile_photo = :photo WHERE id = :id";
                $stmt = $db->getConnection()->prepare($sql);
                $stmt->bindValue(':photo', 'uploads/profile_photos/' . $filename);
                $stmt->bindValue(':id', $userId);
                if (!$stmt->execute()) {
                    $success = false;
                } else {
                    $user['profile_photo'] = 'uploads/profile_photos/' . $filename;
                }
            } else {
                $success = false;
            }
        }

        $message = $success ? 'Profile updated successfully!' : 'Failed to update profile';
        $message_type = $success ? 'success' : 'error';

        // Refresh session data if needed
        $_SESSION['user_name'] = $name ?: $_SESSION['user_name'];
        $_SESSION['user_email'] = $email ?: $_SESSION['user_email'];
    }
    if (isset($_POST['change_password'])) {
       $success = true;

    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1. Fetch current hashed password from DB
    $stmt = $db->getConnection()->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->bindValue(':id', $userId);
    $stmt->execute();
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_user || !password_verify($old_password, $current_user['password'])) {
        $success = false;
        $message = "Current password is incorrect!";
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $success = false;
        $message = "New password and confirmation do not match!";
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) { // Optional: minimum length check
        $success = false;
        $message = "New password must be at least 6 characters!";
        $message_type = 'error';
    } else {
        // 2. Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // 3. Update in database
        $stmt = $db->getConnection()->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindValue(':password', $hashed_password);
        $stmt->bindValue(':id', $userId);
        if ($stmt->execute()) {
            $message = "Password changed successfully!";
            $message_type = 'success';
        } else {
            $message = "Failed to update password!";
            $message_type = 'error';
        }
    }
    }

    

    // Refresh settings array after any update
    $settings = $db->fetchAll("SELECT * FROM settings");
    $settings_array = [];
    foreach ($settings as $setting) {
        $settings_array[$setting['setting_key']] = $setting['setting_value'];
    }


    //HomepageSettings
    if (isset($_POST['update_homepage'])) {
    $updates = [
        'church_motto' => $_POST['church_motto'] ?? '',
        'church_tagline' => $_POST['church_tagline'] ?? '',
        'homepage_about_text' => $_POST['about_text'] ?? '',
        'homepage_social_facebook' => $_POST['social_facebook'] ?? '',
        'homepage_social_instagram' => $_POST['social_instagram'] ?? '',
        'homepage_social_tiktok' => $_POST['social_tiktok'] ?? '',
        'homepage_social_youtube' => $_POST['social_youtube'] ?? '',
        'homepage_social_x' => $_POST['social_x'] ?? '',
    ];

    // Handle file uploads
    $upload_dir = __DIR__ . '/../../assets/images/uploads/homepage/';
    if (!empty($_FILES['church_logo']['name'])) {
        $target = $upload_dir . basename($_FILES['church_logo']['name']);
        move_uploaded_file($_FILES['church_logo']['tmp_name'], $target);
        $updates['church_logo'] = 'uploads/homepage/' . basename($_FILES['church_logo']['name']);
    }
    if (!empty($_FILES['about_image']['name'])) {
        $target = $upload_dir . basename($_FILES['about_image']['name']);
        move_uploaded_file($_FILES['about_image']['tmp_name'], $target);
        $updates['homepage_about_image'] = 'uploads/homepage/' . basename($_FILES['about_image']['name']);
    }

    foreach ($updates as $key => $value) {
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                ON DUPLICATE KEY UPDATE setting_value = :value";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    }

    $message = "Homepage settings updated successfully!";
    $message_type = 'success';

    // Reload updated settings
    $settings = $db->fetchAll("SELECT * FROM settings");
    $settings_array = [];
    foreach ($settings as $setting) {
        $settings_array[$setting['setting_key']] = $setting['setting_value'];
    }
}
if (isset($_POST['add_homepage_ministry'])) {
    $ministry_id = $_POST['ministry_id'] ?? '';
    $link_url    = $_POST['link_url'] ?? '';
    $icon_class  = $_POST['icon_class'] ?? '';

    $upload_dir = __DIR__ . '/../../assets/images/uploads/homepage/';
    $image_path = '';

    if (!empty($_FILES['image_path']['name']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $filename = uniqid() . '_' . basename($_FILES['image_path']['name']);
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'uploads/homepage/' . $filename;
        }
    }

    $stmt = $db->getConnection()->prepare("
        INSERT INTO homepage_ministries (ministry_id, image_path, link_url, icon_class)
        VALUES (:ministry_id, :image_path, :link_url, :icon_class)
    ");

    $stmt->execute([
        ':ministry_id' => $ministry_id,
        ':image_path' => $image_path,
        ':link_url'   => $link_url,
        ':icon_class' => $icon_class
    ]);

    $_SESSION['flash_message'] = "Homepage ministry added successfully!";
    $_SESSION['flash_type'] = "success";

    redirectSelf('homepage-ministries');
}




if (isset($_POST['edit_homepage_ministry'])) {
    $homepage_id = $_POST['homepage_id'];
    $ministry_id = $_POST['ministry_id'];
    $link_url    = $_POST['link_url'];
    $icon_class  = $_POST['icon_class'];

    $upload_dir = __DIR__ . '/../../assets/images/uploads/homepage/';
    $image_path = null;

    if (!empty($_FILES['image_path']['name']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $filename = uniqid() . '_' . basename($_FILES['image_path']['name']);
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'uploads/homepage/' . $filename;
        }
    }

    $sql = "UPDATE homepage_ministries
            SET ministry_id = :ministry_id,
                link_url = :link_url,
                icon_class = :icon_class";

    if ($image_path !== null) {
        $sql .= ", image_path = :image_path";
    }

    $sql .= " WHERE id = :id";

    $stmt = $db->getConnection()->prepare($sql);
    $stmt->bindValue(':ministry_id', $ministry_id);
    $stmt->bindValue(':link_url', $link_url);
    $stmt->bindValue(':icon_class', $icon_class);
    if ($image_path !== null) $stmt->bindValue(':image_path', $image_path);
    $stmt->bindValue(':id', $homepage_id);
    $stmt->execute();

    $_SESSION['flash_message'] = "Homepage ministry updated successfully!";
    $_SESSION['flash_type'] = "success";

    redirectSelf('homepage-ministries');
}


if (isset($_POST['remove_homepage_ministry'])) {
    $homepage_id = $_POST['homepage_id'];

    $stmt = $db->getConnection()->prepare(
        "DELETE FROM homepage_ministries WHERE id = :id"
    );
    $stmt->execute([':id' => $homepage_id]);

    $_SESSION['flash_message'] = "Homepage ministry removed successfully!";
    $_SESSION['flash_type'] = "success";

    redirectSelf('homepage-ministries');
}




if (isset($_POST['toggle_homepage_ministry'])) {
    $homepage_id = $_POST['homepage_id'];

    $stmt = $db->getConnection()->prepare("
        UPDATE homepage_ministries
        SET is_active = IF(is_active = 1, 0, 1)
        WHERE id = :id
    ");
    $stmt->execute([':id' => $homepage_id]);

    $_SESSION['flash_message'] = "Homepage ministry visibility updated.";
    $_SESSION['flash_type'] = "success";

    redirectSelf('homepage-ministries');
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
        <?php if (!empty($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type']; ?>">
                <?= htmlspecialchars($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            
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
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="homepage-tab" data-bs-toggle="tab" data-bs-target="#homepage" type="button" >Homepage</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#homepage-programs" disabled>
                    Homepage Programs
                </button>
            </li>
             <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#homepage-ministries" >
                    Homepage Organisations
                </button>
            </li>


        </ul>

<div class="tab-content">

    <!-- --- General Settings --- -->
    <div class="tab-pane fade show active" id="general" role="tabpanel">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>General Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="church_name" class="form-label fw-bold">Church Name</label>
                        <input type="text" name="church_name" class="form-control" value="<?php echo htmlspecialchars($settings_array['church_name'] ?? ''); ?>" placeholder="Enter church name">
                    </div>
                    <div class="mb-3">
                        <label for="church_address" class="form-label fw-bold">Church Address</label>
                        <input type="text" name="church_address" class="form-control" value="<?php echo htmlspecialchars($settings_array['church_address'] ?? ''); ?>" placeholder="Enter church address">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="church_phone" class="form-label fw-bold">Church Phone</label>
                            <input type="tel" name="church_phone" class="form-control" value="<?php echo htmlspecialchars($settings_array['church_phone'] ?? ''); ?>" placeholder="Enter phone number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="church_email" class="form-label fw-bold">Church Email</label>
                            <input type="email" name="church_email" class="form-control" value="<?php echo htmlspecialchars($settings_array['church_email'] ?? ''); ?>" placeholder="Enter email">
                        </div>
                    </div>
                    <button type="submit" name="update_general" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <!-- --- Branding Settings --- -->
    <div class="tab-pane fade" id="branding" role="tabpanel">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-paint-brush me-2"></i>Branding Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Primary Color</label>
                            <div class="input-group">
                                <input type="color" name="primary_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($settings_array['primary_color'] ?? '#003DA5'); ?>">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings_array['primary_color'] ?? '#003DA5'); ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Secondary Color</label>
                            <div class="input-group">
                                <input type="color" name="secondary_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($settings_array['secondary_color'] ?? '#CC0000'); ?>">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings_array['secondary_color'] ?? '#CC0000'); ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Accent Color</label>
                            <div class="input-group">
                                <input type="color" name="accent_color" class="form-control form-control-color" value="<?php echo htmlspecialchars($settings_array['accent_color'] ?? '#F4C43F'); ?>">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings_array['accent_color'] ?? '#F4C43F'); ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_branding" class="btn btn-success mt-3"><i class="fas fa-save me-1"></i> Save Branding</button>
                </form>
            </div>
        </div>
    </div>

    <!-- --- Profile Settings --- -->
    <div class="tab-pane fade" id="profile" role="tabpanel">
        <!-- Profile Info Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row align-items-center mb-3">
                        <!-- Profile Photo -->
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img id="profilePreview" src="<?php echo BASE_URL . '/' . htmlspecialchars($user['profile_photo'] ?? 'assets/images/avatar-placeholder.png'); ?>" class="img-fluid rounded-square border shadow-sm mb-2" style="max-width:150px;" alt="Profile Photo">
                            <input type="file" name="profile_photo" class="form-control form-control-sm mt-2" onchange="previewProfile(this)">
                        </div>

                        <!-- Name & Email -->
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" placeholder="Enter your full name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" placeholder="Enter your email">
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Change Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="old_password" class="form-label fw-bold">Current Password</label>
                        <input type="password" class="form-control" name="old_password" required placeholder="Enter current password">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-bold">New Password</label>
                        <input type="password" class="form-control" name="new_password" required placeholder="Enter new password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" required placeholder="Confirm new password">
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning"><i class="fas fa-lock me-1"></i> Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="homepage" role="tabpanel">
    <form method="POST" enctype="multipart/form-data">
        <h5 class="mb-3">Homepage Settings  <small class="text-gray"><a href="<?= BASE_URL ?> /public/home.php" class="text-decoration-none">(Visit homepage)</a></small></h5>
       
        <br>
        <div class="mb-3">
            <label class="form-label">Church Name</label>
            <input type="text" name="hero_title" disabled class="form-control" value="<?php echo htmlspecialchars($settings_array['church_name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Church Motto</label>
            <input type="text" name="church_motto" class="form-control" value="<?php echo htmlspecialchars($settings_array['church_motto'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Church Tagline</label>
            <input type="text" name="church_tagline" class="form-control" value="<?php echo htmlspecialchars($settings_array['church_tagline'] ?? ''); ?>">
        </div>
        <div class="mb-3 text-center">
            <label class="form-label">Church Logo Image</label>
            <input type="file" name="church_logo" class="form-control mb-2" onchange="previewImage(this, 'heroPreview')">
            <?php if(!empty($settings_array['church_logo'])): ?>
                <img id="heroPreview" src="<?php echo BASE_URL . '/assets/images/' . htmlspecialchars($settings_array['church_logo']); ?>" class="img-fluid rounded mb-2" style="max-height:200px;">
            <?php else: ?>
                <img id="heroPreview" src="" class="img-fluid rounded mb-2" style="display:none; max-height:200px;">
            <?php endif; ?>
        </div>

        <hr>

        <!-- <h5 class="mb-3">Hero CTA Buttons</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">CTA 1 Text</label>
                <input type="text" name="hero_cta1_text" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_hero_cta1_text'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">CTA 1 Link</label>
                <input type="text" name="hero_cta1_link" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_hero_cta1_link'] ?? ''); ?>">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">CTA 2 Text</label>
                <input type="text" name="hero_cta2_text" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_hero_cta2_text'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">CTA 2 Link</label>
                <input type="text" name="hero_cta2_link" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_hero_cta2_link'] ?? ''); ?>">
            </div>
        </div> -->

        <hr>

        <h5 class="mb-3">About Section</h5>
        <div class="mb-3">
            <label class="form-label">About Text</label>
            <textarea name="about_text" class="form-control" rows="4"><?php echo htmlspecialchars($settings_array['homepage_about_text'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3 text-center">
            <label class="form-label">About Image</label>
            <input type="file" name="about_image" class="form-control mb-2" onchange="previewImage(this, 'aboutPreview')">
            <?php if(!empty($settings_array['homepage_about_image'])): ?>
                <img id="aboutPreview" src="<?php echo BASE_URL . '/assets/images/' . htmlspecialchars($settings_array['homepage_about_image']); ?>" class="img-fluid rounded mb-2" style="max-height:200px;">
            <?php else: ?>
                <img id="aboutPreview" src="" class="img-fluid rounded mb-2" style="display:none; max-height:200px;">
            <?php endif; ?>
        </div>

        <hr>

        <h5 class="mb-3">Social Links (Footer)</h5>
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Facebook</label>
                <input type="text" name="social_facebook" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_social_facebook'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Instagram</label>
                <input type="text" name="social_instagram" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_social_instagram'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">TikTok</label>
                <input type="text" name="social_tiktok" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_social_tiktok'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">YouTube</label>
                <input type="text" name="social_youtube" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_social_youtube'] ?? ''); ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">X / Twitter</label>
                <input type="text" name="social_x" class="form-control" value="<?php echo htmlspecialchars($settings_array['homepage_social_x'] ?? ''); ?>">
            </div>
        </div>

        <button type="submit" name="update_homepage" class="btn btn-primary mt-3">Save Homepage Settings</button>
    </form>
    </div>

<div class="tab-pane fade" id="homepage-programs" role="tabpanel">
    <h4>Homepage Programs</h4>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProgramModal">Add Program</button>
    
    <div class="row">
        <?php foreach($programs as $program): ?>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5><i class="<?php echo $program['icon_class']; ?>"></i> <?php echo htmlspecialchars($program['title']); ?></h5>
                        <div>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProgramModal<?php echo $program['id']; ?>">Edit</button>
                            <a href="delete_program.php?id=<?php echo $program['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    </div>
                    <p class="text-muted"><?php echo htmlspecialchars($program['description']); ?></p>
                    <?php if($program['day_time']): ?>
                        <small class="text-secondary"><?php echo htmlspecialchars($program['day_time']); ?></small>
                    <?php endif; ?>
                    <?php if($program['image_path']): ?>
                        <img src="<?php echo BASE_URL.'/assets/images/'.$program['image_path']; ?>" class="img-fluid mt-2" alt="">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="tab-pane fade" id="homepage-ministries" role="tabpanel">
    <h4>Homepage Ministries</h4>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addMinistryModal">Add Ministry</button>

  <div class="d-flex gap-3 overflow-auto pb-2">
    <?php foreach($homepageMinistries as $hm): ?>
        <div style="min-width:280px; max-width:280px; flex-shrink:0;">
            <div class="card h-100">
                 <?php
                        $defaultImage = BASE_URL . '/assets/images/church_sanctuary.jpg';
                        $imageSrc = !empty($hm['image_path'])
                            ? BASE_URL . '/assets/images/' . htmlspecialchars($hm['image_path'])
                            : $defaultImage;
                        ?>
                            <img 
                                src="<?= $imageSrc; ?>"
                                class="img-fluid"
                                alt="<?= htmlspecialchars($hm['name']); ?>"
                                onerror="this.src='<?= $defaultImage; ?>';"
                            >

                <div class="card-body">
                    <h5>
                        <i class="<?= $hm['icon_class']; ?>"></i>
                        <?= htmlspecialchars($hm['name']); ?>
                    </h5>

                    <p class="text-muted">
                        <?= htmlspecialchars($hm['description']); ?>
                    </p>

                    <?php if($hm['link_url']): ?>
                        <a href="<?= htmlspecialchars($hm['link_url']); ?>" class="btn btn-sm btn-primary">
                            Learn More
                        </a>
                    <?php endif; ?>

                    <button class="btn btn-sm btn-primary"
                        onclick='openEditModal(
                            <?= $hm["homepage_id"] ?>,
                            <?= $hm["ministry_id"] ?>,
                            "<?= htmlspecialchars($hm["link_url"] ?? "", ENT_QUOTES) ?>",
                            "<?= htmlspecialchars($hm["icon_class"] ?? "", ENT_QUOTES) ?>",
                            "<?= htmlspecialchars($hm["image_path"] ?? "", ENT_QUOTES) ?>")'>
                        Edit
                    </button>

                    <button class="btn btn-sm btn-danger"
                        onclick="openRemoveModal(<?= $hm['homepage_id']; ?>)">
                        Remove
                    </button>

                    <form method="POST" class="d-inline">
                        <input type="hidden" name="homepage_id" value="<?= $hm['homepage_id']; ?>">
                        <input type="hidden" name="toggle_homepage_ministry" value="1">
                        <button class="btn btn-sm <?= $hm['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                            <?= $hm['is_active'] ? 'Hide' : 'Show'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>



</div>

<!-- Add modal for adding a homepage ministry -->
<div class="modal fade" id="addMinistryModal" tabindex="-1" aria-labelledby="addMinistryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addMinistryModalLabel">Add Homepage Ministry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
          <!-- Select Ministry -->
          <div class="mb-3">
            <label class="form-label">Ministry</label>
            <select name="ministry_id" class="form-control" required>
              <option value="">Select a ministry</option>
              <?php foreach($ministries as $m): ?>
                <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Icon Class -->
           <div class="mb-3">
    <label class="form-label">Choose an Icon</label>
    <select id="iconSelect" class="form-select" name="icon_class">
        <option value="">-- Select an icon --</option>
        <option value="fas fa-church">Church <i class="fas fa-church"></i></option>
        <option value="fas fa-users">Users</option>
        <option value="fas fa-hand-holding-heart">Ministry</option>
        <option value="fas fa-book">Bible</option>
        <option value="fas fa-heart">Love</option>
        <option value="fas fa-praying-hands">Prayer</option>
        <option value="fas fa-bible">Bible</option>
        <option value="fas fa-hands-helping">Charity/Outreach</option>
        <option value="fas fa-users">Community</option>
        <option value="fas fa-music">Music / Choir</option>
        <option value="fas fa-graduation-cap">Teaching / Education</option>
        <option value="fas fa-heart">Youth / Care</option>
        <option value="fas fa-child">Children’s Ministry</option>
        <option value="fas fa-hand-holding-heart">Support / Counseling</option>
        <option value="fas fa-bullhorn">Announcements / Evangelism</option>
    </select>
    <div class="mt-2">
        <span>Preview: </span>
        <i id="iconPreview" class=""></i>
    </div>
</div>
    

          <!-- Image Upload -->
          <div class="mb-3">
            <label class="form-label">Custom Image</label>
            <input type="file" name="image_path" class="form-control">
          </div>

          <!-- Link URL -->
          <div class="mb-3">
            <label class="form-label">Link URL</label>
            <input type="text" name="link_url" class="form-control" placeholder="https://example.com">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_homepage_ministry" class="btn btn-success">Add Ministry</button>
        </div>
      </div>
    </form>
  </div>
</div>
  <!-- <div class="mb-3">
    <label class="form-label">Icon Class (FontAwesome)</label>
    <input type="text" id="iconInput" name="icon_class" class="form-control" placeholder="e.g., fas fa-church">
    <div class="mt-2">
        <span>Preview: </span>
        <i id="iconPreview" class=""></i>
    </div>
</div> -->

<!-- Edit modal -->
 <div class="modal fade" id="editMinistryModal" tabindex="-1" aria-labelledby="editMinistryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="homepage_id" id="editHomepageId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editMinistryModalLabel">Edit Homepage Ministry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
          <!-- Select Ministry -->
          <div class="mb-3">
            <label class="form-label">Ministry</label>
            <select name="ministry_id" id="editMinistrySelect" class="form-control" required>
              <?php foreach($ministries as $m): ?>
                <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Icon Class -->
          <div class="mb-3">
            <label class="form-label">Choose an Icon</label>
            <select name="icon_class" id="editIconSelect" class="form-select">
              <option value="">-- Select an icon --</option>
              <option value="fas fa-church">Church</option>
              <option value="fas fa-users">Users</option>
              <option value="fas fa-hand-holding-heart">Ministry</option>
            <option value="fas fa-book">Bible</option>
            <option value="fas fa-heart">Love</option>
            <option value="fas fa-praying-hands">Prayer</option>
            <option value="fas fa-bible">Bible</option>
            <option value="fas fa-hands-helping">Charity/Outreach</option>
            <option value="fas fa-users">Community</option>
            <option value="fas fa-music">Music / Choir</option>
            <option value="fas fa-graduation-cap">Teaching / Education</option>
            <option value="fas fa-heart">Youth / Care</option>
            <option value="fas fa-child">Children’s Ministry</option>
            <option value="fas fa-hand-holding-heart">Support / Counseling</option>
            <option value="fas fa-bullhorn">Announcements / Evangelism</option>
            </select>
            <div class="mt-2">
                <span>Preview: </span>
                <i id="editIconPreview" class=""></i>
            </div>
          </div>

          <!-- Image Upload -->
          <div class="mb-3">
            <label class="form-label">Custom Image</label>
            <input type="file" name="image_path" class="form-control">
            <img 
  id="editImagePreview"
  class="img-fluid rounded mt-2"
  style="display:none; max-height:150px;"
>
            <small class="text-muted">Leave empty to keep current image.</small>
          </div>

          <!-- Link URL -->
          <div class="mb-3">
            <label class="form-label">Link URL</label>
            <input type="text" name="link_url" id="editLinkUrl" class="form-control">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="edit_homepage_ministry" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Remove Modal -->
 <div class="modal fade" id="removeMinistryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST">
      <input type="hidden" name="homepage_id" id="removeHomepageId">
      <input type="hidden" name="remove_homepage_ministry" value="1">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger">Remove Homepage Ministry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <p class="mb-0">
            Are you sure you want to remove this ministry from the homepage?
          </p>
          <small class="text-muted">
            This action cannot be undone.
          </small>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-danger">
            Yes, Remove
          </button>
        </div>
      </div>
    </form>
  </div>
</div>




<!-- Live Preview Script -->
<script>
function previewProfile(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}

  const iconSelect = document.getElementById('iconSelect');
    const iconPreview = document.getElementById('iconPreview');

    iconSelect.addEventListener('change', () => {
        iconPreview.className = iconSelect.value; // Updates the preview
    });

const editIconSelect = document.getElementById('editIconSelect');
const editIconPreview = document.getElementById('editIconPreview');

editIconSelect.addEventListener('change', function () {
    editIconPreview.className = this.value;
});



function openEditModal(id, ministryId, linkUrl, iconClass, imagePath) {
  document.getElementById('editHomepageId').value = id;
  document.getElementById('editMinistrySelect').value = ministryId;
  document.getElementById('editLinkUrl').value = linkUrl;
 const iconSelect = document.getElementById('editIconSelect');

[...iconSelect.options].forEach(opt => {
    opt.selected = opt.value === iconClass;
});

  const iconPreview = document.getElementById('editIconPreview');
  iconPreview.className = iconClass;

  const img = document.getElementById('editImagePreview');
  if (img && imagePath) {
    img.src = "<?= BASE_URL ?>/assets/images/" + imagePath;
    img.style.display = 'block';
  }

  const modalEl = document.getElementById('editMinistryModal');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
}

function openRemoveModal(id) {
    document.getElementById('removeHomepageId').value = id;

    const modal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('removeMinistryModal')
    );
    modal.show();
}






</script>



<?php include 'footer.php'; ?>
