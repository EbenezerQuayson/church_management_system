<?php
// Session Configuration and Helpers
require_once __DIR__ . '/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout (30 minutes)
$session_timeout = 1800;

// Check session timeout
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $session_timeout) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/public/login.php?expired=1');
        exit;
    }
}
$_SESSION['last_activity'] = time();

// Helper functions for session management
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

function hasRole($roles) {
    $user_role = getUserRole();
    if (is_array($roles)) {
        return in_array($user_role, $roles);
    }
    return $user_role === $roles;
}
?>
