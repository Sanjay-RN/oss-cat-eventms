<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_admin();

// Optional category filter
$filter = [];
$cat = $_GET['category'] ?? '';
if ($cat !== '') $filter['category'] = $cat;

// READ: all events, newest date first
$list = $events->find($filter, ['sort' => ['date' => -1]]);

// Registration count per event (one aggregation instead of a query per row)
$counts = [];
foreach ($registrations->aggregate([['$group' => ['_id' => '$event_id', 'n' => ['$sum' => 1]]]]) as $c) {
    $counts[(string)$c['_id']] = $c['n'];
}
$today = date('Y-m-d');

$pageTitle = 'Manage Events';
include '../includes/header.php';
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <h3 class="page-title mb-0"><i class="bi bi-list-ul me-2"></i>Manage Events</h3>
    <div class="d-flex gap-2">
        <input class="form-control" placeholder="Search events..." data-search="#eventsTable tbody tr">
        <form method="get">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">All categories</option>
                <?php foreach (['Technical','Workshop','Cultural','Sports','Seminar','Other'] as $c): ?>
                    <option <?= $cat === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="event_add.php" class="btn btn-primary text-nowrap"><i class="bi bi-plus-lg"></i> Add</a>
    </div>
</div>

<div class="card"><div class="card-body table-responsive">
<table class="table table-hover" id="eventsTable">
    <thead><tr><th>#</th><th>Title</th><th>Category</th><th>Date</th><th>Venue</th><th>Seats</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php $i = 0; foreach ($list as $ev): $i++;
        $id = (string)$ev['_id'];
        $reg = $counts[$id] ?? 0;
        $upcoming = $ev['date'] >= $today; ?>
        <tr>
            <td><?= $i ?></td>
            <td class="fw-semibold"><?= e($ev['title']) ?></td>
            <td><span class="badge bg-primary-subtle text-primary"><?= e($ev['category'] ?? '-') ?></span></td>
            <td><?= e(date('d M Y', strtotime($ev['date']))) ?> <small class="text-muted"><?= e($ev['time'] ?? '') ?></small></td>
            <td><?= e($ev['venue']) ?></td>
            <td><?= $reg ?> / <?= (int)$ev['max_seats'] ?></td>
            <td><?= $upcoming ? '<span class="badge bg-success">Upcoming</span>' : '<span class="badge bg-secondary">Completed</span>' ?></td>
            <td class="text-end text-nowrap">
                <a href="participants.php?id=<?= $id ?>" class="btn btn-sm btn-outline-info" title="Participants"><i class="bi bi-people"></i></a>
                <a href="event_edit.php?id=<?= $id ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form method="post" action="event_delete.php" class="d-inline confirm-action" data-message="Delete this event and all its registrations?">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($i === 0): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No events found. <a href="event_add.php">Add one</a>.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div></div>
<?php include '../includes/footer.php'; ?>
