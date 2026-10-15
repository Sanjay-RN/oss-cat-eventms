<?php
/*
 * db.php - MongoDB connection
 * Requires: php_mongodb extension + mongodb/mongodb library (vendor folder)
 */
require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Kolkata');

if (!extension_loaded('mongodb')) {
    die("<h3>MongoDB PHP extension is not enabled.</h3><p>Copy php_mongodb.dll to C:\\xampp\\php\\ext, add <code>extension=mongodb</code> to php.ini and restart Apache. See README.md.</p>");
}

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->selectDatabase('eventms');          // Database name

    // Collections
    $users         = $db->users;
    $events        = $db->events;
    $registrations = $db->registrations;
} catch (Exception $e) {
    die("<h3>Database connection failed.</h3><p>Make sure MongoDB is running on localhost:27017.</p>");
}

/* Convert a string id into a MongoDB ObjectId safely (returns null if invalid) */
function oid($id) {
    try {
        return new MongoDB\BSON\ObjectId((string)$id);
    } catch (Exception $e) {
        return null;
    }
}

/* Escape output to prevent XSS */
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
