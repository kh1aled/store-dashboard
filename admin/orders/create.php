<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../includes/functions.php';
require_permission($pdo,'create_orders');
$active='orders'; $pageTitle='Create Order'; $error='';

$clients=$pdo->query("SELECT c.id,u.name,u.email,c.address FROM clients c JOIN users u ON u.id=c.user_id WHERE c.status='active' AND u.status='active' ORDER BY u.name")->fetchAll();
$products=$pdo->query("SELECT id,name,sku,stock,selling_price,discount FROM products WHERE status='active' AND stock>0 ORDER BY name")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $clientId=(int)($_POST['client_id']??0);
    $shipping=trim($_POST['shipping_address']??'');
    $postedItems=$_POST['items']??[];
    if($clientId<1 || $shipping==='') $error='Client and shipping address are required.';
    else {
      try{
        $pdo->beginTransaction();
        $st=$pdo->prepare("SELECT c.id,c.address FROM clients c JOIN users u ON u.id=c.user_id WHERE c.id=? AND c.status='active' AND u.status='active' FOR UPDATE");
        $st->execute([$clientId]); $client=$st->fetch();
        if(!$client) throw new RuntimeException('Invalid client.');
        $clean=[];
        foreach($postedItems as $pid=>$qty){
            $qty=(int)$qty; $pid=(int)$pid;
            if($qty>0) $clean[$pid]=$qty;
        }
        if(!$clean) throw new RuntimeException('Add at least one product.');
        $ids=array_keys($clean);
        $ph=implode(',',array_fill(0,count($ids),'?'));
        $st=$pdo->prepare("SELECT id,name,stock,selling_price,discount FROM products WHERE id IN ($ph) AND status='active' FOR UPDATE");
        $st->execute($ids); $rows=$st->fetchAll(); $byId=[];
        foreach($rows as $r) $byId[(int)$r['id']]=$r;
        $subtotal=0;
        foreach($clean as $pid=>$qty){
            if(!isset($byId[$pid])) throw new RuntimeException('Invalid product selected.');
            if((int)$byId[$pid]['stock']<$qty) throw new RuntimeException('Not enough stock for '.$byId[$pid]['name']);
            $subtotal+=product_price($byId[$pid])*$qty;
        }
        $orderNo='ST-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
        $employeeId=employee_id_for_user($pdo,(int)current_user($pdo)['id']);
        $ins=$pdo->prepare("INSERT INTO orders(order_number,client_id,employee_id,subtotal,total_amount,shipping_address,status) VALUES(?,?,?,?,?,?, 'confirmed')");
        $ins->execute([$orderNo,$clientId,$employeeId,$subtotal,$subtotal,$shipping]);
        $orderId=(int)$pdo->lastInsertId();
        $oi=$pdo->prepare("INSERT INTO order_items(order_id,product_id,product_name,quantity,unit_price,discount,subtotal) VALUES(?,?,?,?,?,?,?)");
        $up=$pdo->prepare("UPDATE products SET stock=stock-? WHERE id=?");
        foreach($clean as $pid=>$qty){
            $p=$byId[$pid]; $price=product_price($p);
            $oi->execute([$orderId,$pid,$p['name'],$qty,$price,$p['discount'],$price*$qty]);
            $up->execute([$qty,$pid]);
        }
        $pdo->commit();
        flash('success','Order '.$orderNo.' created successfully.');
        redirect('/admin/orders/view.php?id='.$orderId);
      }catch(Throwable $e){
        if($pdo->inTransaction()) $pdo->rollBack();
        $error=$e->getMessage()?:'Could not create order.';
      }
    }
}
include __DIR__.'/../../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Create Order</h1>
        <p class="text-secondary mb-0">Create an order for a client and reserve stock immediately.</p>
    </div>
    <a class="btn btn-sm btn-outline-secondary" href="index.php">Back to Orders</a>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Client</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Select client</option>
                                    <?php foreach ($clients as $c): ?><option value="<?= $c['id'] ?>" data-address="<?= e($c['address'] ?? '') ?>" <?= ((int)($_POST['client_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?> — <?= e($c['email']) ?></option><?php endforeach; ?>
                                </select>
                                <label for="client_id">Client</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="shipping_address" name="shipping_address" placeholder="Shipping address" style="height: 8rem" required><?= e($_POST['shipping_address'] ?? '') ?></textarea>
                                <label for="shipping_address">Shipping Address</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-6">
                            <h3 class="card-title">Products</h3>
                        </div>
                        <div class="col-12 col-md-6 text-md-end">
                            <span class="text-body-secondary small">Available stock</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th width="120">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): $old = (int)($_POST['items'][$p['id']] ?? 0); ?><tr>
                                        <td>
                                            <span class="fw-medium"><?= e($p['name']) ?></span>
                                            <div class="small text-body-secondary"><?= e($p['sku']) ?></div>
                                        </td>
                                        <td><?= money(product_price($p)) ?></td>
                                        <td><?= $p['stock'] ?><?php if ($p['stock'] <= 5): ?> <span class="badge text-bg-warning">Low</span><?php endif; ?></td>
                                        <td><input class="form-control form-control-sm" type="number" name="items[<?= $p['id'] ?>]" min="0" max="<?= $p['stock'] ?>" value="<?= $old ?>"></td>
                                    </tr><?php endforeach;
                                    if (!$products): ?><tr>
                                        <td colspan="4" class="text-center py-5 text-body-secondary">No products available.</td>
                                    </tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check2-circle"></i> Create Order</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
document.getElementById('client_id')?.addEventListener('change', function(){
  const o=this.options[this.selectedIndex], a=document.getElementById('shipping_address');
  if(a && !a.value) a.value=o.dataset.address||'';
});
</script>
<?php include __DIR__.'/../../includes/footer.php'; ?>