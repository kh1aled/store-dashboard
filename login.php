<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in($pdo)) redirect('/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $st = $pdo->prepare("SELECT u.*,r.name role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=? LIMIT 1");
    $st->execute([$email]);
    $u = $st->fetch();
    if (!$u || !password_verify($password, $u['password']) || $u['status'] !== 'active') $error = 'Invalid credentials or inactive account.';
    else {
        login_user($u);
        redirect('/index.php');
    }
}
$pageTitle = 'Login';
$bodyClass = 'auth-page';
include __DIR__ . '/includes/header.php';
?>
<main class="login-box m-auto">
    <h1 class="login-logo">
        <a href="<?= BASE_URL ?>/index.php"><b>STARE</b></a>
    </h1>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to continue</p>

            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

            <form method="post">
                <?= csrf_field() ?>
                <label class="visually-hidden" for="loginEmail">Email</label>
                <div class="input-group mb-3">
                    <input id="loginEmail" type="email" class="form-control" name="email" placeholder="Email" required>
                    <div class="input-group-text">
                        <span class="bi bi-envelope"></span>
                    </div>
                </div>
                <label class="visually-hidden" for="loginPassword">Password</label>
                <div class="input-group mb-3">
                    <input id="loginPassword" type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-text">
                        <span class="bi bi-lock-fill"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </div>
            </form>

            <p class="mb-1 text-center mt-3">
                New client? <a href="<?= BASE_URL ?>/register.php">Create account</a>
            </p>

            <div class="callout callout-info mt-4 mb-0">
                <strong>Demo accounts</strong><br>
                Admin: admin@stare.local / password<br>
                Employee: employee@stare.local / password<br>
                Client: client@stare.local / password
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>