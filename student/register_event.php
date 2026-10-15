<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_student();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$uid = oid($_SESSION['user_id']);
$eid = oid($_POST['event_id'] ?? '');
$event = $eid ? $events->findOne(['_id' => $eid]) : null;

if (!$event) {
    flash('danger', 'Event not found.');
} elseif ($event['date'] < date('Y-m-d')) {
    flash('warning', 'This event has already finished.');
} elseif ($registrations->findOne(['user_id' => $uid, 'event_id' => $eid])) {
    flash('info', 'You are already registered for this event.');
} elseif ($registrations->countDocuments(['event_id' => $eid]) >= (int)$event['max_seats']) {
    flash('warning', 'Sorry, this event is full.');
} else {
    try {
        // CREATE: new registration document
        $registrations->insertOne([
            'user_id'  => $uid,
            'event_id' => $eid,
            'reg_date' => new MongoDB\BSON\UTCDateTime(),
            'attended' => false,
        ]);
        flash('success', 'Registered for "' . $event['title'] . '" successfully!');
    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
        // unique index (user_id + event_id) blocks double registration
        flash('info', 'You are already registered for this event.');
    }
}
header('Location: dashboard.php');
exit;
