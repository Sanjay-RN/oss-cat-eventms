<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Logged-in users go straight to their dashboard
if (is_logged_in()) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
    exit;
}

// READ: show the next few upcoming events on the home page
$today = date('Y-m-d');
$upcoming = $events->find(['date' => ['$gte' => $today]], ['sort' => ['date' => 1], 'limit' => 3]);

$pageTitle = 'Home';
include 'includes/header.php';
?>
<section class="hero mb-5">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <span class="badge bg-warning text-dark mb-3">Open Source Systems Lab Project</span>
            <h1 class="display-5 mb-3">College Event Management System</h1>
            <p class="lead mb-4">Discover campus events, register in one click and let the admin manage everything from a single dashboard. Built with PHP, Bootstrap 5 and MongoDB.</p>
            <a href="register.php" class="btn btn-warning btn-lg me-2"><i class="bi bi-person-plus me-1"></i>Get Started</a>
            <a href="login.php" class="btn btn-outline-light btn-lg"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        </div>
        <div class="col-lg-5 text-center d-none d-lg-block">
            <i class="bi bi-calendar2-week" style="font-size: 11rem; opacity: .9;"></i>
        </div>
    </div>
</section>

<div class="row g-4 mb-5 text-center">
    <div class="col-md-4"><div class="card p-4 h-100"><i class="bi bi-plus-square fs-1 text-primary"></i><h5 class="mt-3">Create Events</h5><p class="text-muted mb-0">Admin adds events with venue, date and seat limit.</p></div></div>
    <div class="col-md-4"><div class="card p-4 h-100"><i class="bi bi-check2-square fs-1 text-success"></i><h5 class="mt-3">Register Online</h5><p class="text-muted mb-0">Students register or cancel with a single click.</p></div></div>
    <div class="col-md-4"><div class="card p-4 h-100"><i class="bi bi-bar-chart fs-1 text-warning"></i><h5 class="mt-3">Track Participation</h5><p class="text-muted mb-0">Live seat counts and participant lists per event.</p></div></div>
</div>

<h4 class="page-title mb-3"><i class="bi bi-stars me-2"></i>Upcoming Events</h4>
<div class="row g-4">
<?php $found = false; foreach ($upcoming as $ev): $found = true; ?>
    <div class="col-md-4">
        <div class="card event-card h-100">
            <div class="card-header-band"></div>
            <div class="card-body">
                <span class="badge bg-primary-subtle text-primary mb-2"><?= e($ev['category'] ?? 'General') ?></span>
                <h5><?= e($ev['title']) ?></h5>
                <div class="event-meta small text-muted">
                    <div><i class="bi bi-calendar3"></i> <?= e(date('d M Y', strtotime($ev['date']))) ?></div>
                    <div><i class="bi bi-geo-alt"></i> <?= e($ev['venue']) ?></div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if (!$found): ?>
    <div class="col-12"><div class="alert alert-info">No upcoming events yet. Login as admin to add one.</div></div>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
