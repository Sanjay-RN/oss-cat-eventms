<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_admin();

$today = date('Y-m-d');

// READ: counts for stat tiles
$totalEvents   = $events->countDocuments();
$upcomingCount = $events->countDocuments(['date' => ['$gte' => $today]]);
$totalStudents = $users->countDocuments(['role' => 'student']);
$totalRegs     = $registrations->countDocuments();

// Aggregation: registrations per event (top 5)
$popular = $registrations->aggregate([
    ['$group'  => ['_id' => '$event_id', 'count' => ['$sum' => 1]]],
    ['$sort'   => ['count' => -1]],
    ['$limit'  => 5],
    ['$lookup' => ['from' => 'events', 'localField' => '_id', 'foreignField' => '_id', 'as' => 'event']],
    ['$unwind' => '$event'],
]);

// Recent registrations
$recent = $registrations->aggregate([
    ['$sort'   => ['reg_date' => -1]],
    ['$limit'  => 6],
    ['$lookup' => ['from' => 'users',  'localField' => 'user_id',  'foreignField' => '_id', 'as' => 'user']],
    ['$lookup' => ['from' => 'events', 'localField' => 'event_id', 'foreignField' => '_id', 'as' => 'event']],
    ['$unwind' => '$user'],
    ['$unwind' => '$event'],
]);

$pageTitle = 'Admin Dashboard';
include '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="page-title mb-0"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h3>
    <a href="event_add.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Event</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3"><div class="stat-tile bg-tile-1 d-flex justify-content-between"><div><small>Total Events</small><h2><?= $totalEvents ?></h2></div><i class="bi bi-calendar-event icon"></i></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-tile bg-tile-2 d-flex justify-content-between"><div><small>Upcoming</small><h2><?= $upcomingCount ?></h2></div><i class="bi bi-hourglass-split icon"></i></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-tile bg-tile-3 d-flex justify-content-between"><div><small>Students</small><h2><?= $totalStudents ?></h2></div><i class="bi bi-people icon"></i></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-tile bg-tile-4 d-flex justify-content-between"><div><small>Registrations</small><h2><?= $totalRegs ?></h2></div><i class="bi bi-ticket-perforated icon"></i></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h5 class="mb-3"><i class="bi bi-fire text-danger me-1"></i>Most Popular Events</h5>
            <?php $any = false; foreach ($popular as $p): $any = true;
                $max = max(1, (int)$p['event']['max_seats']);
                $pct = min(100, round($p['count'] / $max * 100)); ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small"><span><?= e($p['event']['title']) ?></span><span><?= $p['count'] ?>/<?= $max ?></span></div>
                    <div class="progress"><div class="progress-bar bg-primary" style="width: <?= $pct ?>%"></div></div>
                </div>
            <?php endforeach; if (!$any) echo '<p class="text-muted">No registrations yet.</p>'; ?>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h5 class="mb-3"><i class="bi bi-clock-history text-primary me-1"></i>Recent Registrations</h5>
            <ul class="list-group list-group-flush">
            <?php $any = false; foreach ($recent as $r): $any = true; ?>
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span><strong><?= e($r['user']['name']) ?></strong> <span class="text-muted">registered for</span> <?= e($r['event']['title']) ?></span>
                    <small class="text-muted"><?= e($r['reg_date']->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('d M, h:i A')) ?></small>
                </li>
            <?php endforeach; if (!$any) echo '<p class="text-muted">No registrations yet.</p>'; ?>
            </ul>
        </div></div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
