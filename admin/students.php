<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_admin();

// DELETE a student and their registrations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($sid = oid($_POST['id'] ?? ''))) {
    $registrations->deleteMany(['user_id' => $sid]);
    $users->deleteOne(['_id' => $sid, 'role' => 'student']);
    flash('success', 'Student removed.');
    header('Location: students.php');
    exit;
}

// READ: students with their registration count
$list = $users->aggregate([
    ['$match'  => ['role' => 'student']],
    ['$sort'   => ['name' => 1]],
    ['$lookup' => ['from' => 'registrations', 'localField' => '_id', 'foreignField' => 'user_id', 'as' => 'regs']],
    ['$addFields' => ['regCount' => ['$size' => '$regs']]],
]);

$pageTitle = 'Students';
include '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title mb-0"><i class="bi bi-people me-2"></i>Registered Students</h3>
    <input class="form-control w-auto" placeholder="Search students..." data-search="#sTable tbody tr">
</div>
<div class="card"><div class="card-body table-responsive">
<table class="table table-hover" id="sTable">
    <thead><tr><th>#</th><th>Name</th><th>Reg. No</th><th>Department</th><th>Email</th><th>Phone</th><th>Events</th><th></th></tr></thead>
    <tbody>
    <?php $i = 0; foreach ($list as $s): $i++; ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= e($s['name']) ?></td>
            <td><?= e($s['regno'] ?? '-') ?></td>
            <td><?= e($s['department'] ?? '-') ?></td>
            <td><?= e($s['email']) ?></td>
            <td><?= e($s['phone'] ?? '-') ?></td>
            <td><span class="badge bg-primary"><?= (int)$s['regCount'] ?></span></td>
            <td class="text-end">
                <form method="post" class="confirm-action" data-message="Delete this student and all their registrations?">
                    <input type="hidden" name="id" value="<?= $s['_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($i === 0): ?><tr><td colspan="8" class="text-center text-muted py-4">No students have signed up yet.</td></tr><?php endif; ?>
    </tbody>
</table>
</div></div>
<?php include '../includes/footer.php'; ?>
