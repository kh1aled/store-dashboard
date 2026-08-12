<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_permission($pdo, 'view_products');
if (user_role($pdo) !== 'client') redirect('/index.php');
$active = 'shop';
$pageTitle = 'Shop';
$cat = (int)($_GET['category'] ?? 0);
$q = trim($_GET['q'] ?? '');
$cats = $pdo->query("SELECT * FROM categories WHERE status='active' ORDER BY name")->fetchAll();
$sql = "SELECT p.*,c.name category_name,b.name brand_name FROM products p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN brands b ON b.id=p.brand_id WHERE p.status='active' AND p.stock>0";
$params = [];
if ($cat) {
    $sql .= " AND p.category_id=?";
    $params[] = $cat;
}
if ($q) {
    $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
$sql .= " ORDER BY p.id DESC";
$st = $pdo->prepare($sql);
$st->execute($params);
$products = $st->fetchAll();
include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><span class="eyebrow">STORE</span>
        <h1 class="page-title">Shop</h1>
    </div><a class="btn btn-stare" href="<?= BASE_URL ?>/client/cart.php"><i class="bi bi-cart3"></i> Cart (<?= cart_count($pdo, client_id_for_user($pdo, (int)current_user($pdo)['id'])) ?>)</a>
</div>
<form class="row g-2 mb-4">
    <div class="col-md-6"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search products"></div>
    <div class="col-md-4"><select class="form-select" name="category">
            <option value="">All categories</option><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
</form>
<div class="row g-4"><?php foreach ($products as $p): ?><div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
            <div class="product-card h-100"><?php if ($p['image']): ?><img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>"><?php endif; ?><div class="p-3 d-flex flex-column h-100">
                    <div class="small text-secondary"><?= e($p['brand_name'] ?? '') ?></div>
                    <h3 class="h5"><?= e($p['name']) ?></h3>
                    <p class="text-secondary small flex-grow-1"><?= e($p['description']) ?></p>
                    <div class="d-flex justify-content-between align-items-center mb-3"><strong><?= money(product_price($p)) ?></strong><?php if ($p['discount'] > 0): ?><span class="badge text-bg-warning"><?= $p['discount'] ?>% off</span><?php endif; ?></div>
                    <form method="post" action="cart_action.php"><?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><button class="btn btn-stare w-100">Add to Cart</button></form>
                </div>
            </div>
        </div><?php endforeach; ?></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>