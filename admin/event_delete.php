<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_admin();

// Only accept POST so a delete can't happen from a simple link click
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit;
}

$id = oid($_POST['id'] ?? '');
$event = $id ? $events->findOne(['_id' => $id]) : null;

if ($event) {
    // DELETE: remove the event and all its registrations
    $registrations->deleteMany(['event_id' => $id]);
    $events->deleteOne(['_id' => $id]);
    flash('success', 'Event "' . $event['title'] . '" deleted.');
} else {
    flash('danger', 'Event not found.');
}
header('Location: events.php');
exit;
