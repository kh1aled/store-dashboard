<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_clients');
$active = 'clients';
$id = (int)($_GET['id'] ?? 0);
$row = ['name' => '', 'email' => '', 'phone' => '', 'address' => '', 'gender' => 'other', 'age' => '', 'status' => 'active', 'password' => ''];
$userId = 0;
if ($id) {
    $st = $pdo->prepare("SELECT c.*,u.name,u.email,u.status user_status FROM clients c JOIN users u ON u.id=c.user_id WHERE c.id=?");
    $st->execute([$id]);
    $found = $st->fetch();
    if (!$found) redirect('/admin/clients/index.php');
    $row = $found;
    $userId = (int)$found['user_id'];
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Name and valid email are required.';
    else try {
        $pdo->beginTransaction();
        if ($id) {
            $sql = "UPDATE users SET name=?,email=?,status=? WHERE id=?";
            $pdo->prepare($sql)->execute([$name, $email, $_POST['status'] ?? 'active', $userId]);
            if ($password !== '') $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);
            $pdo->prepare("UPDATE clients SET phone=?,address=?,gender=?,age=?,status=? WHERE id=?")->execute([trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), $_POST['gender'] ?? 'other', ($_POST['age'] ?? '') !== '' ? (int)$_POST['age'] : null, $_POST['status'] ?? 'active', $id]);
        } else {
            $role = (int)$pdo->query("SELECT id FROM roles WHERE name='client'")->fetchColumn();
            $pdo->prepare("INSERT INTO users(name,email,password,role_id,status) VALUES(?,?,?,?,?)")->execute([$name, $email, password_hash($password ?: 'password', PASSWORD_DEFAULT), $role, 'active']);
            $userId = (int)$pdo->lastInsertId();
            $pdo->prepare("INSERT INTO clients(user_id,phone,address,gender,age) VALUES(?,?,?,?,?)")->execute([$userId, trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), $_POST['gender'] ?? 'other', ($_POST['age'] ?? '') !== '' ? (int)$_POST['age'] : null]);
        }
        $pdo->commit();
        flash('success', 'Client saved.');
        redirect('/admin/clients/index.php');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = 'Could not save client. Email may already exist.';
    }
}
$pageTitle = $id ? 'Edit Client' : 'Add Client';
include __DIR__ . '/../../includes/header.php'; ?>
<div class="mb-4">
    <h1 class="page-title mb-0"><?= $id ? 'Edit' : 'Add' ?> Client</h1>
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Client Details</h3>
    </div>
    <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="clientName" name="name" placeholder="Client name" value="<?= e($row['name']) ?>" required>
                        <label for="clientName">Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="clientEmail" name="email" placeholder="Email" value="<?= e($row['email']) ?>" required>
                        <label for="clientEmail">Email</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="clientPhone" name="phone" placeholder="Phone" value="<?= e($row['phone']) ?>">
                        <label for="clientPhone">Phone</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="clientAddress" name="address" placeholder="Address" value="<?= e($row['address']) ?>">
                        <label for="clientAddress">Address</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="clientGender" name="gender">
                            <option value="male" <?= $row['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= $row['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= $row['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                        <label for="clientGender">Gender</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" min="1" max="120" class="form-control" id="clientAge" name="age" placeholder="Age" value="<?= e($row['age']) ?>">
                        <label for="clientAge">Age</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="clientStatus" name="status">
                            <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <label for="clientStatus">Account Status</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="password" class="form-control" id="clientPassword" name="password" placeholder="Password" <?= $id ? '' : 'required' ?>>
                        <label for="clientPassword">Password <?= $id ? '(optional)' : '' ?></label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Save Client</button>
                <a class="btn btn-sm btn-outline-secondary" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>