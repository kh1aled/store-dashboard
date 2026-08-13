<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_partners');
$active = 'partners';
$id = (int)($_GET['id'] ?? 0);
$row = ['name' => '', 'company' => '', 'email' => '', 'phone' => '', 'address' => '', 'status' => 'active'];
if ($id) {
    $st = $pdo->prepare("SELECT * FROM partners WHERE id=?");
    $st->execute([$id]);
    $row = $st->fetch() ?: $row;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $args = [];
    $args[] = trim($_POST["name"] ?? "");
    $args[] = trim($_POST["company"] ?? "");
    $args[] = trim($_POST["email"] ?? "");
    $args[] = trim($_POST["phone"] ?? "");
    $args[] = trim($_POST["address"] ?? "");
    $status = $_POST['status'] ?? 'active';
    $args[] = $status;
    try {
        if ($id) {
            $st = $pdo->prepare("UPDATE partners SET name=?, company=?, email=?, phone=?, address=?,status=? WHERE id=?");
            $args[] = $id;
        } else {
            $st = $pdo->prepare("INSERT INTO partners(name, company, email, phone, address,status) VALUES(?, ?, ?, ?, ?, ?)");
        }
        $st->execute($args);
        flash('success', 'Partners saved.');
        redirect('/admin/partners/index.php');
    } catch (Throwable $e) {
        $error = 'Could not save. Check required fields and unique values.';
    }
}
$pageTitle = $id ? 'Edit Partners' : 'Add Partners';
include __DIR__ . '/../../includes/header.php'; ?>
<div class="mb-4">
    <h1 class="page-title mb-0"><?= $id ? 'Edit' : 'Add' ?> Partner</h1>
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Partner Details</h3>
    </div>
    <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="partnerName" name="name" placeholder="Partner name" value="<?= e($row['name']) ?>">
                        <label for="partnerName">Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="partnerCompany" name="company" placeholder="Company" value="<?= e($row['company']) ?>">
                        <label for="partnerCompany">Company</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="partnerEmail" name="email" placeholder="Email" value="<?= e($row['email']) ?>">
                        <label for="partnerEmail">Email</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="partnerPhone" name="phone" placeholder="Phone" value="<?= e($row['phone']) ?>">
                        <label for="partnerPhone">Phone</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="partnerAddress" name="address" placeholder="Address" value="<?= e($row['address']) ?>">
                        <label for="partnerAddress">Address</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="partnerStatus" name="status">
                            <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <label for="partnerStatus">Status</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                <a class="btn btn-sm btn-outline-secondary" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>