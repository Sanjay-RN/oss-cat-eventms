<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_student();

$uid = oid($_SESSION['user_id']);
$today = date('Y-m-d');

// READ: upcoming events
$list = $events->find(['date' => ['$gte' => $today]], ['sort' => ['date' => 1]]);

// Registration counts per event
$counts = [];
foreach ($registrations->aggregate([['$group' => ['_id' => '$event_id', 'n' => ['$sum' => 1]]]]) as $c) {
    $counts[(string)$c['_id']] = $c['n'];
}
// Events this student already registered for
$mine = [];
foreach ($registrations->find(['user_id' => $uid]) as $r) {
    $mine[(string)$r['event_id']] = true;
}

$pageTitle = 'Events';
include '../includes/header.php';
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
    <h3 class="page-title mb-0"><i class="bi bi-grid me-2"></i>Upcoming Events</h3>
    <input class="form-control w-auto" placeholder="Search events..." data-search=".event-col">
</div>

<div class="row g-4">
<?php $i = 0; foreach ($list as $ev): $i++;
    $id = (string)$ev['_id'];
    $taken = $counts[$id] ?? 0;
    $max = max(1, (int)$ev['max_seats']);
    $left = $max - $taken;
    $pct = min(100, round($taken / $max * 100)); ?>
    <div class="col-md-6 col-lg-4 event-col">
        <div class="card event-card h-100">
            <div class="card-header-band"></div>
            <div class="card-body d-flex flex-column">
                <span class="badge bg-primary-subtle text-primary align-self-start mb-2"><?= e($ev['category'] ?? 'General') ?></span>
                <h5><?= e($ev['title']) ?></h5>
                <p class="text-muted small flex-grow-1"><?= e($ev['description']) ?></p>
                <div class="event-meta small mb-3">
                    <div><i class="bi bi-calendar3"></i> <?= e(date('D, d M Y', strtotime($ev['date']))) ?> <?= $ev['time'] ? '&middot; ' . e(date('h:i A', strtotime($ev['time']))) : '' ?></div>
                    <div><i class="bi bi-geo-alt"></i> <?= e($ev['venue']) ?></div>
                    <div><i class="bi bi-people"></i> <?= $left ?> of <?= $max ?> seats left</div>
                </div>
                <div class="progress mb-3"><div class="progress-bar <?= $pct >= 90 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= $pct ?>%"></div></div>

                <?php if (isset($mine[$id])): ?>
                    <form method="post" action="cancel.php" class="confirm-action" data-message="Cancel your registration?">
                        <input type="hidden" name="event_id" value="<?= $id ?>">
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-1"></i>Cancel Registration</button>
                    </form>
                    <small class="text-success text-center mt-1"><i class="bi bi-check-circle-fill"></i> You are registered</small>
                <?php elseif ($left <= 0): ?>
                    <button class="btn btn-secondary w-100" disabled>Event Full</button>
                <?php else: ?>
                    <form method="post" action="register_event.php">
                        <input type="hidden" name="event_id" value="<?= $id ?>">
                        <button class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1"></i>Register Now</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if ($i === 0): ?>
    <div class="col-12"><div class="alert alert-info">No upcoming events right now. Check back later!</div></div>
<?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
