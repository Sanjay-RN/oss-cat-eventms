<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // READ: find the user by email
    $user = $users->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (string)$user['_id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        flash('success', 'Welcome back, ' . $user['name'] . '!');
        header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
        exit;
    }
    $error = 'Invalid email or password.';
}

$pageTitle = 'Login';
include 'includes/header.php';
?>
<div class="auth-wrapper">
    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <div class="auth-icon mb-2"><i class="bi bi-shield-lock"></i></div>
                <h3 class="page-title">Login</h3>
                <p class="text-muted">Admin and students login here</p>
            </div>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Login</button>
            </form>
            <p class="text-center mt-3 mb-0">New student? <a href="register.php">Create an account</a></p>
            <hr>
            <p class="small text-muted mb-0 text-center">Demo admin: <code>admin@college.edu</code> / <code>admin123</code></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
