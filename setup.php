<?php
/*
 * setup.php - Run ONCE in the browser: http://localhost/eventms/setup.php
 * Creates the default admin account, indexes and some sample events.
 */
require_once 'includes/db.php';

$messages = [];

// Unique indexes (prevents duplicate emails and duplicate registrations)
$users->createIndex(['email' => 1], ['unique' => true]);
$registrations->createIndex(['user_id' => 1, 'event_id' => 1], ['unique' => true]);
$messages[] = 'Indexes created.';

// Default admin
if (!$users->findOne(['email' => 'admin@college.edu'])) {
    $users->insertOne([
        'name'       => 'Administrator',
        'email'      => 'admin@college.edu',
        'password'   => password_hash('admin123', PASSWORD_DEFAULT),
        'role'       => 'admin',
        'created_at' => new MongoDB\BSON\UTCDateTime(),
    ]);
    $messages[] = 'Admin created: admin@college.edu / admin123';
} else {
    $messages[] = 'Admin already exists.';
}

// Sample events (only if the collection is empty)
if ($events->countDocuments() === 0) {
    $d = function ($days) { return date('Y-m-d', strtotime("+$days days")); };
    $events->insertMany([
        ['title' => 'Tech Quiz 2026', 'category' => 'Technical', 'description' => 'Inter-department technical quiz on programming, networks and databases.', 'date' => $d(7),  'time' => '10:00', 'venue' => 'Seminar Hall A', 'max_seats' => 60,  'created_at' => new MongoDB\BSON\UTCDateTime()],
        ['title' => 'Hackathon: Code for Campus', 'category' => 'Technical', 'description' => '24-hour hackathon to build solutions for campus problems.', 'date' => $d(14), 'time' => '09:00', 'venue' => 'Computer Lab 3', 'max_seats' => 40,  'created_at' => new MongoDB\BSON\UTCDateTime()],
        ['title' => 'Open Source Workshop', 'category' => 'Workshop', 'description' => 'Hands-on session on Git, GitHub and contributing to open source projects.', 'date' => $d(10), 'time' => '14:00', 'venue' => 'Main Auditorium', 'max_seats' => 120, 'created_at' => new MongoDB\BSON\UTCDateTime()],
        ['title' => 'Cultural Fest - Rhythm', 'category' => 'Cultural', 'description' => 'Dance, music and drama competitions across departments.', 'date' => $d(21), 'time' => '11:00', 'venue' => 'Open Air Theatre', 'max_seats' => 200, 'created_at' => new MongoDB\BSON\UTCDateTime()],
        ['title' => 'Inter-College Cricket', 'category' => 'Sports', 'description' => 'T20 cricket tournament. Team captains register on behalf of the team.', 'date' => $d(28), 'time' => '08:00', 'venue' => 'College Ground', 'max_seats' => 22,  'created_at' => new MongoDB\BSON\UTCDateTime()],
    ]);
    $messages[] = '5 sample events inserted.';
} else {
    $messages[] = 'Events already exist; sample data skipped.';
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Setup</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container py-5" style="max-width:600px">
<div class="card shadow-sm"><div class="card-body">
<h3>EventMS Setup Complete</h3>
<ul class="list-group my-3"><?php foreach ($messages as $m) echo '<li class="list-group-item">' . e($m) . '</li>'; ?></ul>
<a href="login.php" class="btn btn-primary">Go to Login</a>
</div></div></div></body></html>
