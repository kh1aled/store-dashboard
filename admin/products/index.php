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

<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Products</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title mb-0">Products</h1>
    </div><?php if (has_permission($pdo, 'manage_products')): ?><a class="btn btn-sm btn-primary" href="form.php"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Add Product</a><?php endif; ?>
</div>

<div class="card mb-4">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <h3 class="card-title">Product Catalog</h3>
            </div>
            <div class="col-12 col-md-8">
                <form class="d-flex flex-wrap justify-content-md-end gap-2">
                    <div class="input-group input-group-sm w-auto">
                        <span class="input-group-text">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </span>
                        <input
                            type="search"
                            id="product-search"
                            name="q"
                            class="form-control"
                            placeholder="Search by name or SKU"
                            aria-label="Search products"
                            value="<?= e($q) ?>"
                            style="width: 200px" />
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?><tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($r['image']): ?><img src="<?= e($r['image']) ?>" class="img-size-32 rounded me-2" alt=""><?php endif; ?>
                                    <span class="fw-medium"><?= e($r['name']) ?></span>
                                </div>
                            </td>
                            <td class="text-body-secondary"><?= e($r['sku']) ?></td>
                            <td><?= e($r['category_name'] ?? '—') ?></td>
                            <td><?= e($r['brand_name'] ?? '—') ?></td>
                            <td><?= money(product_price($r)) ?></td>
                            <td><?= $r['stock'] ?><?php if ($r['stock'] <= 5): ?> <span class="badge text-bg-warning">Low</span><?php endif; ?></td>
                            <td><?= status_badge($r['status']) ?></td>
                            <td class="text-end"><?php if (has_permission($pdo, 'manage_products')): ?>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a class="btn btn-sm btn-outline-secondary" href="form.php?id=<?= $r['id'] ?>" aria-label="Edit <?= e($r['name']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                        <form method="post" action="delete.php" onsubmit="return confirmDelete('product')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Delete <?= e($r['name']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </div><?php endif; ?>
                            </td>
                        </tr><?php endforeach;
                            if (!$rows): ?><tr>
                            <td colspan="8" class="text-center py-5 text-body-secondary">No products found.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div><?php include __DIR__ . '/../../includes/footer.php'; ?>