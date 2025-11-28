<?php
// Authentication Controller

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Email and password are required'];
            }

            $user = $this->user->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role_name'];
                $_SESSION['user_role_id'] = $user['role_id'];

                return ['success' => true, 'message' => 'Login successful'];
            }

            return ['success' => false, 'message' => 'Invalid email or password'];
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'password_confirm' => trim($_POST['password_confirm'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'role_id' => trim($_POST['role_id'] ?? 4), // Added role_id with default member role
            ];

            // Validation
            if (empty($data['email']) || empty($data['password']) || empty($data['first_name']) || empty($data['last_name'])) {
                return ['success' => false, 'message' => 'All fields are required'];
            }

            if ($data['password'] !== $data['password_confirm']) {
                return ['success' => false, 'message' => 'Passwords do not match'];
            }

            if (strlen($data['password']) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters'];
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email address'];
            }

            // Check if user exists
            if ($this->user->findByEmail($data['email'])) {
                return ['success' => false, 'message' => 'Email already registered'];
            }

            $profilePhoto = null;
            if (!empty($_FILES['profile_photo']['name'])) {
                $uploadDir = __DIR__ . '/../../assets/uploads/profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = uniqid() . '_' . basename($_FILES['profile_photo']['name']);
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadPath)) {
                    $profilePhoto = '/assets/uploads/profiles/' . $fileName;
                }
            }

            $data['profile_photo'] = $profilePhoto;

            if ($this->user->register($data)) {
                return ['success' => true, 'message' => 'Registration successful! Please login.'];
            }

            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ../public/login.php');
        exit;
    }
}
?>
