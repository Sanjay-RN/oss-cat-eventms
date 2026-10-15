<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_admin();

$id = oid($_GET['id'] ?? '');
$event = $id ? $events->findOne(['_id' => $id]) : null;
if (!$event) {
    flash('danger', 'Event not found.');
    header('Location: events.php');
    exit;
}

// Admin can remove a participant (DELETE on registrations)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($rid = oid($_POST['reg_id'] ?? ''))) {
    $registrations->deleteOne(['_id' => $rid, 'event_id' => $id]);
    flash('success', 'Participant removed.');
    header('Location: participants.php?id=' . $id);
    exit;
}

// Mark attendance (UPDATE on registrations)
if (isset($_GET['attend']) && ($rid = oid($_GET['attend']))) {
    $reg = $registrations->findOne(['_id' => $rid]);
    if ($reg) {
        $registrations->updateOne(['_id' => $rid], ['$set' => ['attended' => empty($reg['attended'])]]);
    }
    header('Location: participants.php?id=' . $id);
    exit;
}

// READ: registrations joined with users ($lookup = join)
$list = $registrations->aggregate([
    ['$match'  => ['event_id' => $id]],
    ['$sort'   => ['reg_date' => 1]],
    ['$lookup' => ['from' => 'users', 'localField' => 'user_id', 'foreignField' => '_id', 'as' => 'user']],
    ['$unwind' => '$user'],
]);
$count = $registrations->countDocuments(['event_id' => $id]);

$pageTitle = 'Participants';
include '../includes/header.php';
?>
<a href="events.php" class="btn btn-link px-0 mb-2"><i class="bi bi-arrow-left"></i> Back to events</a>
<div class="card mb-4"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h3 class="page-title mb-1"><?= e($event['title']) ?></h3>
        <div class="text-muted"><i class="bi bi-calendar3"></i> <?= e(date('d M Y', strtotime($event['date']))) ?> &nbsp; <i class="bi bi-geo-alt"></i> <?= e($event['venue']) ?></div>
    </div>
    <div class="text-end">
        <h4 class="mb-0"><?= $count ?> / <?= (int)$event['max_seats'] ?></h4>
        <small class="text-muted">seats filled</small>
    </div>
</div></div>

<div class="d-flex justify-content-between mb-2">
    <input class="form-control w-auto" placeholder="Search participants..." data-search="#pTable tbody tr">
    <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print List</button>
</div>
<div class="card"><div class="card-body table-responsive">
<table class="table table-hover" id="pTable">
    <thead><tr><th>#</th><th>Name</th><th>Reg. No</th><th>Department</th><th>Email</th><th>Phone</th><th>Attendance</th><th></th></tr></thead>
    <tbody>
    <?php $i = 0; foreach ($list as $r): $i++; ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= e($r['user']['name']) ?></td>
            <td><?= e($r['user']['regno'] ?? '-') ?></td>
            <td><?= e($r['user']['department'] ?? '-') ?></td>
            <td><?= e($r['user']['email']) ?></td>
            <td><?= e($r['user']['phone'] ?? '-') ?></td>
            <td>
                <a href="?id=<?= $id ?>&attend=<?= $r['_id'] ?>" class="badge text-decoration-none <?= !empty($r['attended']) ? 'bg-success' : 'bg-secondary' ?>">
                    <?= !empty($r['attended']) ? 'Present' : 'Not marked' ?>
                </a>
            </td>
            <td class="text-end">
                <form method="post" class="confirm-action" data-message="Remove this participant?">
                    <input type="hidden" name="reg_id" value="<?= $r['_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($i === 0): ?><tr><td colspan="8" class="text-center text-muted py-4">No registrations yet.</td></tr><?php endif; ?>
    </tbody>
</table>
</div></div>
<?php include '../includes/footer.php'; ?>
