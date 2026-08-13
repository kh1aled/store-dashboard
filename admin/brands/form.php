<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_brands');
$active = 'brands';
$id = (int)($_GET['id'] ?? 0);
$row = ['name' => '', 'logo' => '', 'description' => '', 'status' => 'active'];
if ($id) {
    $st = $pdo->prepare("SELECT * FROM brands WHERE id=?");
    $st->execute([$id]);
    $row = $st->fetch() ?: $row;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $args = [];
    $args[] = trim($_POST["name"] ?? "");
    $args[] = trim($_POST["logo"] ?? "");
    $args[] = trim($_POST["description"] ?? "");
    $status = $_POST['status'] ?? 'active';
    $args[] = $status;
    try {
        if ($id) {
            $st = $pdo->prepare("UPDATE brands SET name=?, logo=?, description=?,status=? WHERE id=?");
            $args[] = $id;
        } else {
            $st = $pdo->prepare("INSERT INTO brands(name, logo, description,status) VALUES(?, ?, ?, ?)");
        }
        $st->execute($args);
        flash('success', 'Brands saved.');
        redirect('/admin/brands/index.php');
    } catch (Throwable $e) {
        $error = 'Could not save. Check required fields and unique values.';
    }
}
$pageTitle = $id ? 'Edit Brands' : 'Add Brands';
include __DIR__ . '/../../includes/header.php';
?>
<div class="mb-4">
    <h1 class="page-title mb-0"><?= $id ? 'Edit' : 'Add' ?> Brand</h1>
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Brand Details</h3>
    </div>
    <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="brandName" name="name" placeholder="Brand name" value="<?= e($row['name']) ?>">
                        <label for="brandName">Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="url" class="form-control" id="brandLogo" name="logo" placeholder="Logo URL" value="<?= e($row['logo']) ?>">
                        <label for="brandLogo">Logo URL</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control" id="brandDescription" name="description" placeholder="Description" style="height: 6rem"><?= e($row['description']) ?></textarea>
                        <label for="brandDescription">Description</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="brandStatus" name="status">
                            <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <label for="brandStatus">Status</label>
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