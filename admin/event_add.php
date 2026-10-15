<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/event_form.php';
require_admin();

$ev = ['category' => 'Technical'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ev, $errors] = validate_event($_POST);
    if (!$errors) {
        // CREATE: insert a new event document
        $ev['created_at'] = new MongoDB\BSON\UTCDateTime();
        $events->insertOne($ev);
        flash('success', 'Event "' . $ev['title'] . '" added successfully.');
        header('Location: events.php');
        exit;
    }
}

$pageTitle = 'Add Event';
include '../includes/header.php';
?>
<div class="card"><div class="card-body p-4">
    <h3 class="page-title mb-4"><i class="bi bi-plus-circle me-2"></i>Add New Event</h3>
    <?php render_event_form($ev, $errors, 'Save Event'); ?>
</div></div>
<?php include '../includes/footer.php'; ?>
