<?php
/* header.php - common top section with Bootstrap navbar
 * Set $pageTitle before including this file. */
$pageTitle = $pageTitle ?? 'College Event Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | EventMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= base_url('index.php') ?>">
            <i class="bi bi-calendar-event-fill me-2"></i>EventMS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
            <?php if (is_logged_in() && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/dashboard.php') ?>"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/events.php') ?>"><i class="bi bi-list-ul me-1"></i>Manage Events</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/event_add.php') ?>"><i class="bi bi-plus-circle me-1"></i>Add Event</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/students.php') ?>"><i class="bi bi-people me-1"></i>Students</a></li>
            <?php elseif (is_logged_in()): ?>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('student/dashboard.php') ?>"><i class="bi bi-grid me-1"></i>Events</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('student/my_registrations.php') ?>"><i class="bi bi-ticket-perforated me-1"></i>My Registrations</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('student/profile.php') ?>"><i class="bi bi-person-circle me-1"></i>Profile</a></li>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
                <li class="nav-item ms-lg-3">
                    <span class="badge rounded-pill bg-light text-dark me-2"><?= e($_SESSION['name']) ?> (<?= e($_SESSION['role']) ?>)</span>
                </li>
                <li class="nav-item"><a class="btn btn-sm btn-outline-light" href="<?= base_url('logout.php') ?>"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('login.php') ?>">Login</a></li>
                <li class="nav-item"><a class="btn btn-sm btn-light ms-lg-2" href="<?= base_url('register.php') ?>">Sign Up</a></li>
            <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="py-4">
<div class="container">
<?php show_flash(); ?>
