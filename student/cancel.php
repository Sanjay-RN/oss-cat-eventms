<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_student();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = oid($_SESSION['user_id']);
    $eid = oid($_POST['event_id'] ?? '');

    // DELETE: student can only remove their own registration
    $result = $eid ? $registrations->deleteOne(['user_id' => $uid, 'event_id' => $eid]) : null;
    if ($result && $result->getDeletedCount() > 0) {
        flash('success', 'Your registration has been cancelled.');
    } else {
        flash('danger', 'Registration not found.');
    }
}
$back = ($_POST['back'] ?? '') === 'mine' ? 'my_registrations.php' : 'dashboard.php';
header('Location: ' . $back);
exit;
