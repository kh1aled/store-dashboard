<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_permission($pdo, 'view_cart');
if (user_role($pdo) !== 'client') redirect('/index.php');
$clientId = client_id_for_user($pdo, (int)current_user($pdo)['id']);
$cartId = get_or_create_cart($pdo, $clientId);
$active = 'cart';
$pageTitle = 'My Cart';
$st = $pdo->prepare("SELECT ci.*,p.name,p.image,p.stock,p.selling_price,p.discount FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.cart_id=? ORDER BY ci.id");
$st->execute([$cartId]);
$items = $st->fetchAll();
$total = 0;
foreach ($items as $i) {
    $total += product_price($i) * $i['quantity'];
}
include __DIR__ . '/../includes/header.php'; ?>
<div class="mb-4"><span class="eyebrow">SHOPPING</span>
    <h1 class="page-title">My Cart</h1>
</div>
<?php if (!$items): ?><div class="panel text-center py-5"><i class="bi bi-cart3 display-5 text-secondary"></i>
        <h2 class="h4 mt-3">Your cart is empty</h2><a class="btn btn-stare mt-2" href="products.php">Browse products</a>
    </div><?php else: ?><div class="row g-4">
        <div class="col-lg-8">
            <div class="panel">
                <form method="post" action="cart_action.php"><?= csrf_field() ?><input type="hidden" name="action" value="update">
                    <div class="table-responsive">
                        <table class="table stare-table align-middle">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th width="130">Qty</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody><?php foreach ($items as $i): $price = product_price($i); ?><tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2"><?php if ($i['image']): ?><img src="<?= e($i['image']) ?>" class="thumb"><?php endif; ?><strong><?= e($i['name']) ?></strong></div>
                                        </td>
                                        <td><?= money($price) ?></td>
                                        <td><input class="form-control" type="number" min="0" max="<?= $i['stock'] ?>" name="qty[<?= $i['id'] ?>]" value="<?= $i['quantity'] ?>"></td>
                                        <td><?= money($price * $i['quantity']) ?></td>
                                        <td><button class="btn btn-sm btn-link text-danger" type="submit" name="remove_item" value="<?= $i['id'] ?>">Remove</button></td>
                                    </tr><?php endforeach; ?></tbody>
                        </table>
                    </div><button class="btn btn-outline-secondary">Update Cart</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <h2 class="h5">Summary</h2>
                <div class="d-flex justify-content-between my-3"><span>Total</span><strong class="fs-4"><?= money($total) ?></strong></div><a class="btn btn-stare w-100" href="checkout.php">Checkout</a>
            </div>
        </div>
    </div><?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>