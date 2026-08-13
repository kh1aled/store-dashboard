<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_categories');
$active = 'categories';
$id = (int)($_GET['id'] ?? 0);
$row = ['name' => '', 'description' => '', 'image' => '', 'status' => 'active'];
if ($id) {
    $st = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $st->execute([$id]);
    $row = $st->fetch() ?: $row;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    if (strlen($name) < 2) $error = 'Name is required.';
    else {
        $st = $id ? $pdo->prepare("UPDATE categories SET name=?,description=?,image=?,status=? WHERE id=?") : $pdo->prepare("INSERT INTO categories(name,description,image,status) VALUES(?,?,?,?)");
        $args = [$name, trim($_POST['description'] ?? ''), trim($_POST['image'] ?? ''), $_POST['status'] ?? 'active'];
        if ($id) $args[] = $id;
        $st->execute($args);
        flash('success', $id ? 'Category updated.' : 'Category created.');
        redirect('/admin/categories/index.php');
    }
}
$pageTitle = $id ? 'Edit Category' : 'Add Category';
include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-4"><span class="eyebrow">CATALOG</span>
    <h1 class="page-title"><?= $id ? 'Edit' : 'Add' ?> Category</h1>
</div>
<div class="panel form-panel"><?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="<?= e($row['name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Image URL</label><input class="form-control" name="image" value="<?= e($row['image']) ?>"></div>
            <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4"><?= e($row['description']) ?></textarea></div>
            <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status">
                    <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select></div>
        </div>
        <div class="mt-4"><button class="btn btn-stare">Save</button> <a class="btn btn-outline-secondary" href="index.php">Cancel</a></div>
    </form>

<div class="mb-4">
    <h1 class="page-title mb-0"><?= $id ? 'Edit' : 'Add' ?> Category</h1>
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Category Details</h3>
    </div>
    <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="categoryName" name="name" placeholder="Category name" value="<?= e($row['name']) ?>" required>
                        <label for="categoryName">Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="categoryImage" name="image" placeholder="Image URL" value="<?= e($row['image']) ?>">
                        <label for="categoryImage">Image URL</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control" id="categoryDescription" name="description" placeholder="Description" style="height: 6rem"><?= e($row['description']) ?></textarea>
                        <label for="categoryDescription">Description</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="categoryStatus" name="status">
                            <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <label for="categoryStatus">Status</label>
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