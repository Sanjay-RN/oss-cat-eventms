<?php
/*
 * auth.php - session helpers and access control
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ' . base_url('student/dashboard.php'));
        exit;
    }
}

function require_student() {
    require_login();
    if ($_SESSION['role'] !== 'student') {
        header('Location: ' . base_url('admin/dashboard.php'));
        exit;
    }
}

/* Build URLs relative to the project root, works from any sub-folder */
function base_url($path = '') {
    $root = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    // If we are inside /admin or /student, go one level up
    if (preg_match('#/(admin|student)$#', $root)) {
        $root = dirname($root);
    }
    return rtrim($root, '/') . '/' . ltrim($path, '/');
}

/* One-time flash messages (success / error alerts) */
function flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show" role="alert">'
            . e($f['message'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}
