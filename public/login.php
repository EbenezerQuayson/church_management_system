<?php
// Login Page

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {

    $role = $_SESSION['user_role'] ?? 'Member';

    if ($role === 'Treasurer') {
        header('Location: ../app/views/overview.php');
    } elseif ($role === 'Leader') {
        header('Location: ../app/views/members.php');
    } else {
        header('Location: ../app/views/dashboard.php');
    }

    exit;
}


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
    $result = $auth->login();
    
   if ($result['success']) {

    $role = $_SESSION['user_role'] ?? 'Member';

    switch ($role) {
        case 'Treasurer':
            header('Location: ../app/views/overview.php');
            break;

        case 'Leader':
            header('Location: ../app/views/members.php');
            break;

        case 'Admin':
            header('Location: ../app/views/dashboard.php');
            break;

        default:
            header('Location: ../app/views/dashboard.php');
    }

    exit;
    } else {
        $message = $result['message'];
        $message_type = 'error';
    }
}

if (isset($_GET['expired'])) {
    $message = 'Your session has expired. Please login again.';
    $message_type = 'warning';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $church_name  ?> - Login</title>
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
        }

        .login-container {
            max-width: 450px;
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

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 61, 165, 0.3);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container mx-auto">
            <div class="card">
                <div class="card-body">
                    <!-- Logo Section -->
                    <div class="logo-section">
                        <img src="<?= $church_logo ?> "alt="<?= $church_name ?> Logo">
                        <h1><?= $church_name ?></h1>
                        <p>Church Management System</p>
                    </div>

                    <!-- Message Display -->
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'warning'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form method="POST" action="" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: none; border: 2px solid #e9ecef; border-right: none;">
                                    <i class="fas fa-envelope" style="color: var(--primary);"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: none; border: 2px solid #e9ecef; border-right: none;">
                                    <i class="fas fa-lock" style="color: var(--primary);"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login btn-primary w-100 text-white mb-3">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>

                    <!-- Forgot Password and Registration Links -->
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot your password?</a> | <a href="register.php">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
