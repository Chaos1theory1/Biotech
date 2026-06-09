<?php
session_start();

// Default admin credentials - change these immediately after first login
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'admin123';

// Project root (one level up from admin/)
$BASE_DIR = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR;

// Files that are allowed to be edited from the admin panel (relative to project root)
$ALLOWED_FILES = [
    'index.html',
    'services.html',
    'contact.html',
    'a-propos.html'
];

function is_logged_in() {
    return !empty($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function sanitize_filename($file) {
    // Only keep basename to avoid directory traversal
    return basename($file);
}

function allowed_file($file) {
    global $ALLOWED_FILES;
    return in_array($file, $ALLOWED_FILES, true);
}

function file_path($file) {
    global $BASE_DIR;
    return $BASE_DIR . $file;
}

// Simple CSRF token helpers
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

function csrf_token() {
    return $_SESSION['csrf_token'];
}

function check_csrf() {
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
        die('Invalid CSRF token');
    }
}

?>
