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

// Location for site visit/click/share statistics
$STATS_FILE = $BASE_DIR . 'admin' . DIRECTORY_SEPARATOR . 'data.json';

// Image upload settings
$IMAGE_DIR = $BASE_DIR . 'images' . DIRECTORY_SEPARATOR;
$ALLOWED_IMAGE_EXTENSIONS = [ 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' ];
$MAX_UPLOAD_SIZE = 5 * 1024 * 1024; // 5 MB

function stats_path() {
    global $STATS_FILE;
    return $STATS_FILE;
}

function ensure_stats_file() {
    if (!file_exists(stats_path())) {
        $default = [
            'visits' => 0,
            'clicks' => 0,
            'shares' => 0,
            'pageViews' => [],
            'clicksByLabel' => [],
            'sharesByLabel' => [],
            'recentEvents' => []
        ];
        file_put_contents(stats_path(), json_encode($default, JSON_PRETTY_PRINT));
    }
}

function load_stats() {
    ensure_stats_file();
    $json = file_get_contents(stats_path());
    $data = json_decode($json, true);
    if (!is_array($data)) {
        $data = [
            'visits' => 0,
            'clicks' => 0,
            'shares' => 0,
            'pageViews' => [],
            'clicksByLabel' => [],
            'sharesByLabel' => [],
            'recentEvents' => []
        ];
    }
    return $data;
}

function save_stats($data) {
    file_put_contents(stats_path(), json_encode($data, JSON_PRETTY_PRINT));
}

function image_dir_path() {
    global $IMAGE_DIR;
    return $IMAGE_DIR;
}

function allowed_image_extension($name) {
    global $ALLOWED_IMAGE_EXTENSIONS;
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    return in_array($ext, $ALLOWED_IMAGE_EXTENSIONS, true);
}

function sanitize_image_filename($file) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file));
}

function relative_image_url($file) {
    return 'images/' . basename($file);
}

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
