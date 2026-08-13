<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'view_products');
$active = 'products';
$pageTitle = 'Products';
$q = trim($_GET['q'] ?? '');
$sql = "SELECT p.*,c.name category_name,b.name brand_name,pa.name partner_name FROM products p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN brands b ON b.id=p.brand_id LEFT JOIN partners pa ON pa.id=p.partner_id";
$params = [];
if ($q !== '') {
    $sql .= " WHERE p.name LIKE ? OR p.sku LIKE ?";
    $params = ["%$q%", "%$q%"];
}
$sql .= " ORDER BY p.id DESC";
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><span class="eyebrow">CATALOG</span>
        <h1 class="page-title">Product</h1>
    </div><?php if (has_permission($pdo, 'manage_products')): ?><a class="btn btn-stare" href="form.php"><i class="bi bi-plus-lg"></i> Add Product</a><?php endif; ?>
</div>
<div class="panel mb-4">
    <form class="row g-2">
        <div class="col-md-8"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search by name or SKU"></div>
        <div class="col-auto"><button class="btn btn-outline-secondary">Search</button></div>
    </form>
</div>
<div class="panel">
    <div class="table-responsive">
        <table class="table stare-table align-middle">
            <thead>
                <tr>
                    <th>Products</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?><tr>
                        <td>
                            <div class="d-flex align-items-center gap-2"><?php if ($r['image']): ?><img src="<?= e($r['image']) ?>" class="thumb" alt=""><?php endif; ?><strong><?= e($r['name']) ?></strong></div>
                        </td>
                        <td class="text-secondary"><?= e($r['sku']) ?></td>
                        <td><?= e($r['category_name'] ?? '—') ?></td>
                        <td><?= e($r['brand_name'] ?? '—') ?></td>
                        <td><?= money(product_price($r)) ?></td>
                        <td><?= $r['stock'] ?><?php if ($r['stock'] <= 5): ?> <span class="badge text-bg-warning">Low</span><?php endif; ?></td>
                        <td><?= status_badge($r['status']) ?></td>
                        <td><?php if (has_permission($pdo, 'manage_products')): ?><a href="form.php?id=<?= $r['id'] ?>" class="action-link">Edit</a>
                                <form class="d-inline" method="post" action="delete.php" onsubmit="return confirmDelete('product')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-link text-danger p-0 ms-2">Delete</button></form><?php endif; ?>
                        </td>
                    </tr><?php endforeach;
                        if (!$rows): ?><tr>
                        <td colspan="8" class="text-center py-5 text-secondary">No products found.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div><?php include __DIR__ . '/../../includes/footer.php'; ?>