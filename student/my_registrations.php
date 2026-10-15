<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_student();

$uid = oid($_SESSION['user_id']);
$today = date('Y-m-d');

// READ: this student's registrations joined with event details
$list = $registrations->aggregate([
    ['$match'  => ['user_id' => $uid]],
    ['$lookup' => ['from' => 'events', 'localField' => 'event_id', 'foreignField' => '_id', 'as' => 'event']],
    ['$unwind' => '$event'],
    ['$sort'   => ['event.date' => 1]],
]);

$pageTitle = 'My Registrations';
include '../includes/header.php';
?>
<h3 class="page-title mb-4"><i class="bi bi-ticket-perforated me-2"></i>My Registrations</h3>
<div class="card"><div class="card-body table-responsive">
<table class="table table-hover">
    <thead><tr><th>#</th><th>Event</th><th>Date</th><th>Venue</th><th>Registered On</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php $i = 0; foreach ($list as $r): $i++;
        $ev = $r['event'];
        $upcoming = $ev['date'] >= $today; ?>
        <tr>
            <td><?= $i ?></td>
            <td class="fw-semibold"><?= e($ev['title']) ?></td>
            <td><?= e(date('d M Y', strtotime($ev['date']))) ?></td>
            <td><?= e($ev['venue']) ?></td>
            <td><?= e($r['reg_date']->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('d M Y, h:i A')) ?></td>
            <td>
                <?php if (!empty($r['attended'])): ?><span class="badge bg-success">Attended</span>
                <?php elseif ($upcoming): ?><span class="badge bg-primary">Upcoming</span>
                <?php else: ?><span class="badge bg-secondary">Completed</span><?php endif; ?>
            </td>
            <td class="text-end">
                <?php if ($upcoming): ?>
                <form method="post" action="cancel.php" class="confirm-action" data-message="Cancel this registration?">
                    <input type="hidden" name="event_id" value="<?= $ev['_id'] ?>">
                    <input type="hidden" name="back" value="mine">
                    <button class="btn btn-sm btn-outline-danger">Cancel</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($i === 0): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">You have not registered for any event. <a href="dashboard.php">Browse events</a>.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div></div>
<?php include '../includes/footer.php'; ?>
