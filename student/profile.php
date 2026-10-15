<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_student();

$uid = oid($_SESSION['user_id']);
$user = $users->findOne(['_id' => $uid]);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $dept  = trim($_POST['department'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $newPw = $_POST['new_password'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) $errors[] = 'Phone must be 10 digits.';
    if ($newPw !== '' && strlen($newPw) < 6) $errors[] = 'New password must be at least 6 characters.';

    if (!$errors) {
        $set = ['name' => $name, 'department' => $dept, 'phone' => $phone];
        if ($newPw !== '') $set['password'] = password_hash($newPw, PASSWORD_DEFAULT);

        // UPDATE: student edits own profile
        $users->updateOne(['_id' => $uid], ['$set' => $set]);
        $_SESSION['name'] = $name;
        flash('success', 'Profile updated.');
        header('Location: profile.php');
        exit;
    }
}

$pageTitle = 'My Profile';
include '../includes/header.php';
?>
<div class="auth-wrapper" style="max-width:600px">
<div class="card"><div class="card-body p-4">
    <h3 class="page-title mb-4"><i class="bi bi-person-circle me-2"></i>My Profile</h3>
    <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $er) echo '<li>' . e($er) . '</li>'; ?></ul></div><?php endif; ?>
    <form method="post">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input name="name" class="form-control" value="<?= e($user['name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Register Number</label><input class="form-control" value="<?= e($user['regno'] ?? '') ?>" disabled></div>
            <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="<?= e($user['email']) ?>" disabled></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" maxlength="10" value="<?= e($user['phone'] ?? '') ?>"></div>
            <div class="col-12">
                <label class="form-label">Department</label>
                <select name="department" class="form-select">
                    <?php foreach (['Computer Science','Computer Applications','Information Technology','Commerce','Mathematics','Physics','Other'] as $d): ?>
                        <option <?= (($user['department'] ?? '') === $d) ? 'selected' : '' ?>><?= e($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12"><label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label><input type="password" name="new_password" class="form-control"></div>
        </div>
        <button class="btn btn-primary mt-4"><i class="bi bi-save me-1"></i>Save Changes</button>
    </form>
</div></div>
</div>
<?php include '../includes/footer.php'; ?>
