<?php
/*
 * event_form.php - shared form + validation for Add Event and Edit Event
 */
const EVENT_CATEGORIES = ['Technical', 'Workshop', 'Cultural', 'Sports', 'Seminar', 'Other'];

/* Validate POST data, return [data, errors] */
function validate_event($post) {
    $data = [
        'title'       => trim($post['title'] ?? ''),
        'category'    => trim($post['category'] ?? ''),
        'description' => trim($post['description'] ?? ''),
        'date'        => trim($post['date'] ?? ''),
        'time'        => trim($post['time'] ?? ''),
        'venue'       => trim($post['venue'] ?? ''),
        'max_seats'   => (int)($post['max_seats'] ?? 0),
    ];
    $errors = [];
    if ($data['title'] === '')                                   $errors[] = 'Title is required.';
    if (!in_array($data['category'], EVENT_CATEGORIES, true))    $errors[] = 'Choose a valid category.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date']))     $errors[] = 'Choose a valid date.';
    if ($data['time'] !== '' && !preg_match('/^\d{2}:\d{2}$/', $data['time'])) $errors[] = 'Choose a valid time.';
    if ($data['venue'] === '')                                   $errors[] = 'Venue is required.';
    if ($data['max_seats'] < 1)                                  $errors[] = 'Seats must be at least 1.';
    return [$data, $errors];
}

/* Render the form ($ev = current values) */
function render_event_form($ev, $errors, $buttonText) {
    if ($errors) {
        echo '<div class="alert alert-danger"><ul class="mb-0">';
        foreach ($errors as $er) echo '<li>' . e($er) . '</li>';
        echo '</ul></div>';
    }
    ?>
    <form method="post">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Event Title</label>
                <input name="title" class="form-control" required value="<?= e($ev['title'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <?php foreach (EVENT_CATEGORIES as $c): ?>
                        <option <?= (($ev['category'] ?? '') === $c) ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control"><?= e($ev['description'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required value="<?= e($ev['date'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Time</label>
                <input type="time" name="time" class="form-control" value="<?= e($ev['time'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Maximum Seats</label>
                <input type="number" min="1" name="max_seats" class="form-control" required value="<?= e($ev['max_seats'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Venue</label>
                <input name="venue" class="form-control" required value="<?= e($ev['venue'] ?? '') ?>">
            </div>
        </div>
        <div class="mt-4">
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i><?= e($buttonText) ?></button>
            <a href="events.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    <?php
}
