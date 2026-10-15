<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $regno    = strtoupper(trim($_POST['regno'] ?? ''));
    $dept     = trim($_POST['department'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // Server-side validation
    if ($name === '')                                   $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'Enter a valid email.';
    if ($regno === '')                                  $errors[] = 'Register number is required.';
    if ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) $errors[] = 'Phone must be 10 digits.';
    if (strlen($password) < 6)                          $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)                         $errors[] = 'Passwords do not match.';
    if ($users->findOne(['email' => $email]))           $errors[] = 'This email is already registered.';

    if (!$errors) {
        // CREATE: insert a new student document
        $users->insertOne([
            'name'       => $name,
            'email'      => $email,
            'regno'      => $regno,
            'department' => $dept,
            'phone'      => $phone,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'role'       => 'student',
            'created_at' => new MongoDB\BSON\UTCDateTime(),
        ]);
        flash('success', 'Account created successfully. Please login.');
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Student Sign Up';
include 'includes/header.php';
?>
<div class="auth-wrapper" style="max-width:560px">
    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <div class="auth-icon mb-2"><i class="bi bi-person-plus"></i></div>
                <h3 class="page-title">Student Sign Up</h3>
            </div>
            <?php if ($errors): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $er) echo '<li>' . e($er) . '</li>'; ?></ul></div>
            <?php endif; ?>
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Full Name</label><input name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Register Number</label><input name="regno" class="form-control" required value="<?= e($_POST['regno'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" maxlength="10" value="<?= e($_POST['phone'] ?? '') ?>"></div>
                    <div class="col-12">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select">
                            <?php foreach (['Computer Science','Computer Applications','Information Technology','Commerce','Mathematics','Physics','Other'] as $d): ?>
                                <option <?= (($_POST['department'] ?? '') === $d) ? 'selected' : '' ?>><?= e($d) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                    <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="confirm" class="form-control" required></div>
                </div>
                <button class="btn btn-primary w-100 mt-4"><i class="bi bi-check2-circle me-1"></i>Create Account</button>
            </form>
            <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
