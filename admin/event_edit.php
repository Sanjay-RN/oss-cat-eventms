<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/event_form.php';
require_admin();

$id = oid($_GET['id'] ?? '');
$existing = $id ? $events->findOne(['_id' => $id]) : null;
if (!$existing) {
    flash('danger', 'Event not found.');
    header('Location: events.php');
    exit;
}

$ev = (array)$existing->getArrayCopy();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ev, $errors] = validate_event($_POST);

    // Do not allow seats lower than the number already registered
    $registered = $registrations->countDocuments(['event_id' => $id]);
    if ($ev['max_seats'] < $registered) {
        $errors[] = "Seats cannot be less than the $registered students already registered.";
    }

    if (!$errors) {
        // UPDATE: modify the existing event document
        $ev['updated_at'] = new MongoDB\BSON\UTCDateTime();
        $events->updateOne(['_id' => $id], ['$set' => $ev]);
        flash('success', 'Event "' . $ev['title'] . '" updated successfully.');
        header('Location: events.php');
        exit;
    }
}

$pageTitle = 'Edit Event';
include '../includes/header.php';
?>
<div class="card"><div class="card-body p-4">
    <h3 class="page-title mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Event</h3>
    <?php render_event_form($ev, $errors, 'Update Event'); ?>
</div></div>
<?php include '../includes/footer.php'; ?>
