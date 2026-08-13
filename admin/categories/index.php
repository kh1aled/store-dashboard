<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'view_categories');
$active = 'categories';
$pageTitle = 'Categories';
$rows = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><span class="eyebrow">CATALOG</span>
        <h1 class="page-title">Categories</h1>
    </div><?php if (has_permission($pdo, 'manage_categories')): ?><a class="btn btn-stare" href="form.php"><i class="bi bi-plus-lg"></i> Add Category</a><?php endif; ?>
</div>
<div class="panel">
    <div class="table-responsive">
        <table class="table stare-table align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Products</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): $st = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id=?");
                    $st->execute([$r['id']]);
                    $count = $st->fetchColumn(); ?><tr>
                        <td>#<?= $r['id'] ?></td>
                        <td><strong><?= e($r['name']) ?></strong>
                            <div class="text-secondary small"><?= e($r['description']) ?></div>
                        </td>
                        <td><?= status_badge($r['status']) ?></td>
                        <td><?= $count ?></td>
                        <td><?php if (has_permission($pdo, 'manage_categories')): ?><a class="action-link" href="form.php?id=<?= $r['id'] ?>">Edit</a>
                                <form class="d-inline" method="post" action="delete.php" onsubmit="return confirmDelete('category')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-link text-danger p-0 ms-2">Delete</button></form><?php endif; ?>
                        </td>
                    </tr><?php endforeach;
                        if (!$rows): ?><tr>
                        <td colspan="5" class="text-center py-5 text-secondary">No categories.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>