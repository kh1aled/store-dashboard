<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in($pdo)) redirect('/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $type = $_POST['account_type'] ?? 'client';
    if ($type !== 'client') $type = 'employee';
    if (strlen($name) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6 || $pass !== $confirm) $error = 'Please provide valid details and matching passwords (6+ characters).';
    else {
        try {
            $pdo->beginTransaction();
            $role = $type === 'employee' ? 'employee' : 'client';
            $st = $pdo->prepare("SELECT id FROM roles WHERE name=?");
            $st->execute([$role]);
            $roleId = (int)$st->fetchColumn();
            $status = $type === 'employee' ? 'pending' : 'active';
            $st = $pdo->prepare("INSERT INTO users(name,email,password,role_id,status) VALUES(?,?,?,?,?)");
            $st->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $roleId, $status]);
            $uid = (int)$pdo->lastInsertId();
            if ($type === 'client') {
                $st = $pdo->prepare("INSERT INTO clients(user_id) VALUES(?)");
                $st->execute([$uid]);
            } else {
                $st = $pdo->prepare("INSERT INTO employees(user_id,status) VALUES(?,?)");
                $st->execute([$uid, 'inactive']);
            }
            $pdo->commit();
            if ($status === 'active') {
                login_user(['id' => $uid]);
                redirect('/index.php');
            }
            flash('success', 'Employee account created and is waiting for admin approval.');
            redirect('/login.php');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'Email may already be registered.';
        }
    }
}
$pageTitle = 'Register';
$bodyClass = 'auth-page';
include __DIR__ . '/includes/header.php';
?>

<main class="login-box m-auto">
    <h1 class="login-logo">
        <a href="<?= BASE_URL ?>/index.php"><b>STARE</b></a>
    </h1>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Create Account</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <?= csrf_field() ?>

                <label class="visually-hidden" for="registerName">Full name</label>
                <div class="input-group mb-3">
                    <input
                        id="registerName"
                        type="text"
                        class="form-control"
                        name="name"
                        placeholder="Full name"
                        required>
                    <div class="input-group-text">
                        <span class="bi bi-person-fill"></span>
                    </div>
                </div>

                <label class="visually-hidden" for="registerEmail">Email</label>
                <div class="input-group mb-3">
                    <input
                        id="registerEmail"
                        type="email"
                        class="form-control"
                        name="email"
                        placeholder="Email"
                        required>
                    <div class="input-group-text">
                        <span class="bi bi-envelope"></span>
                    </div>
                </div>

                <label class="visually-hidden" for="registerPassword">Password</label>
                <div class="input-group mb-3">
                    <input
                        id="registerPassword"
                        type="password"
                        class="form-control"
                        name="password"
                        placeholder="Password"
                        minlength="6"
                        required>
                    <div class="input-group-text">
                        <span class="bi bi-lock-fill"></span>
                    </div>
                </div>

                <label class="visually-hidden" for="confirmPassword">
                    Confirm password
                </label>
                <div class="input-group mb-3">
                    <input
                        id="confirmPassword"
                        type="password"
                        class="form-control"
                        name="confirm_password"
                        placeholder="Confirm password"
                        required>
                    <div class="input-group-text">
                        <span class="bi bi-shield-lock-fill"></span>
                    </div>
                </div>

                <label class="visually-hidden" for="accountType">
                    Account Type
                </label>
                <div class="input-group mb-3">
                    <select
                        id="accountType"
                        class="form-select"
                        name="account_type">
                        <option value="client">Client</option>
                        <option value="employee">
                            Employee (admin approval)
                        </option>
                    </select>
                    <div class="input-group-text">
                        <span class="bi bi-person-badge-fill"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Create Account
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <p class="mb-0 text-center mt-3">
                Already have an account?
                <a href="<?= BASE_URL ?>/login.php">Login</a>
            </p>
        </div>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>