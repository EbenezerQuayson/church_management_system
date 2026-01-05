<?php
// Registration Page

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// If already logged in, redirect to dashboard
// if (isLoggedIn()) {
//     header('Location:'. BASE_URL . '/app/views/dashboard.php');
//     exit;
// }



$db = Database::getInstance();
$settings = $db->fetchAll("SELECT * FROM settings");

foreach ($settings as $setting){
 if ($setting['setting_key'] === 'church_name') $church_name = $setting['setting_value'];
  if ($setting['setting_key'] === 'church_logo') {
            if($setting['setting_value'] != null ){
            $church_logo = BASE_URL . '/assets/images/' . $setting['setting_value'];
            } else{
                $church_logo = BASE_URL . '/assets/images/methodist-logo.png';
            }
        }
}


$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    $result = $auth->register();
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
        // Clear form
        $_POST = [];
    } else {
        $message = $result['message'];
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= $church_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #003DA5;
            --secondary: #CC0000;
            --accent: #F4C43F;
        }

        body {
            background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }

        .register-container {
            max-width: 500px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .card-body {
            padding: 3rem 2rem;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section img {
            height: 80px;
            margin-bottom: 1rem;
        }

        .logo-section h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .logo-section p {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 61, 165, 0.15);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 61, 165, 0.3);
            color: white;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container mx-auto">
            <div class="card">
                <div class="card-body">
                    <!-- Logo Section -->
                    <div class="logo-section">
                     <img src="<?= $church_logo ?> "alt="<?= $church_name ?> Logo">
                       <h1><?= $church_name ?></h1>
                        <p>Create Account</p>
                    </div>

                    <!-- Message Display -->
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : ($message_type === 'success' ? 'success' : 'warning'); ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Registration Form -->
                    <form method="POST" action="" novalidate enctype="multipart/form-data"> <!-- Added enctype for file upload -->
                        <div class="form-row">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: none; border: 2px solid #e9ecef; border-right: none;">
                                    <i class="fas fa-envelope" style="color: var(--primary);"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Added role selection dropdown -->
                        <div class="mb-3">
                            <label for="role_id" class="form-label">User Role</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">Select a role</option>
                                <option value="1">Admin</option>
                                 <!-- <option value="4"  >Member</option> -->
                                <option value="3" >Leader</option>
                                <option value="2" >Pastor</option>
                                <option value="2" >Treasurer</option>
                                <option value="2" >Staff</option>
                            </select>
                        </div>

                        <!-- Added profile photo upload -->
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: none; border: 2px solid #e9ecef; border-right: none;">
                                    <i class="fas fa-image" style="color: var(--primary);"></i>
                                </span>
                                <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/png,image/jpeg,image/jpg,image/gif">
                            </div>
                            <small class="text-muted">Optional. Accepted formats: PNG, JPEG, JPG, GIF (Max 5MB)</small>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: none; border: 2px solid #e9ecef; border-right: none;">
                                    <i class="fas fa-lock" style="color: var(--primary);"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="At least 6 characters" required>
                            </div>
                            <small class="text-muted">Password must be at least 6 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: none; border: 2px solid #e9ecef; border-right: none;">
                                    <i class="fas fa-lock" style="color: var(--primary);"></i>
                                </span>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-register w-100 mb-3">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="login-link">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
