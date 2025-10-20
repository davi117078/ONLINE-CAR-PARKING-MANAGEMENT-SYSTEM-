<?php
/**
 * OCPMS - Authentication & Role Helper
 * ------------------------------------
 * Handles login validation, user sessions, and role-based access.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// --- LOGIN FUNCTION ---
function login_user($email, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Regenerate session ID for security
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        return true;
    }

    return false;
}

// --- LOGOUT FUNCTION ---
function logout_user() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// --- CHECK IF USER IS LOGGED IN ---
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// --- REQUIRE LOGIN (REDIRECT IF NOT LOGGED IN) ---
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// --- CHECK USER ROLE ---
function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("HTTP/1.1 403 Forbidden");
        die("Access denied: insufficient privileges.");
    }
}

// --- GET CURRENT USER DATA ---
function current_user() {
    global $pdo;

    if (!isset($_SESSION['user_id'])) return null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
