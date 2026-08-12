<?php
require_once __DIR__.'/../../config/database.php';require_once __DIR__.'/../../includes/functions.php';require_permission($pdo,'manage_products');
$active='products';$id=(int)($_GET['id']??0);
$row=['sku'=>'','name'=>'','description'=>'','cost_price'=>'0','selling_price'=>'0','discount'=>'0','stock'=>'0','category_id'=>'','brand_id'=>'','partner_id'=>'','image'=>'','status'=>'active'];
if($id){$st=$pdo->prepare("SELECT * FROM products WHERE id=?");$st->execute([$id]);$row=$st->fetch()?:$row;}
$cats=$pdo->query("SELECT id,name FROM categories WHERE status='active' ORDER BY name")->fetchAll();
$brands=$pdo->query("SELECT id,name FROM brands WHERE status='active' ORDER BY name")->fetchAll();
$partners=$pdo->query("SELECT id,name FROM partners WHERE status='active' ORDER BY name")->fetchAll();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();
 $data=['sku'=>trim($_POST['sku']??''),'name'=>trim($_POST['name']??''),'description'=>trim($_POST['description']??''),'cost_price'=>(float)($_POST['cost_price']??0),'selling_price'=>(float)($_POST['selling_price']??0),'discount'=>(float)($_POST['discount']??0),'stock'=>(int)($_POST['stock']??0),'category_id'=>($_POST['category_id']??'')!==''?(int)$_POST['category_id']:null,'brand_id'=>($_POST['brand_id']??'')!==''?(int)$_POST['brand_id']:null,'partner_id'=>($_POST['partner_id']??'')!==''?(int)$_POST['partner_id']:null,'image'=>trim($_POST['image']??''),'status'=>$_POST['status']??'active'];
 if($data['sku']===''||$data['name']===''||$data['selling_price']<0||$data['stock']<0||$data['discount']<0||$data['discount']>100)$error='SKU, name and valid pricing/stock are required.';
 else {try{$sql=$id?"UPDATE products SET sku=?,name=?,description=?,cost_price=?,selling_price=?,discount=?,stock=?,category_id=?,brand_id=?,partner_id=?,image=?,status=? WHERE id=?":"INSERT INTO products(sku,name,description,cost_price,selling_price,discount,stock,category_id,brand_id,partner_id,image,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";$args=array_values($data);if($id)$args[]=$id;$st=$pdo->prepare($sql);$st->execute($args);flash('success','Product saved.');redirect('/admin/products/index.php');}catch(Throwable $e){$error='Could not save product. SKU must be unique.';}$row=array_merge($row,$data);}}
$pageTitle=$id?'Edit Product':'Add Product';include __DIR__.'/../../includes/header.php';
?>
<div class="mb-4"><span class="eyebrow">CATALOG</span><h1 class="page-title"><?=$id?'Edit':'Add'?> Product</h1></div>
<div class="panel form-panel"><?php if($error):?><div class="alert alert-danger"><?=e($error)?></div><?php endif;?><form method="post"><?=csrf_field()?><div class="row g-3">
<div class="col-md-4"><label class="form-label">SKU</label><input class="form-control" name="sku" value="<?=e($row['sku'])?>" required></div>
<div class="col-md-8"><label class="form-label">Name</label><input class="form-control" name="name" value="<?=e($row['name'])?>" required></div>
<div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"><?=e($row['description'])?></textarea></div>
<div class="col-md-3"><label class="form-label">Cost Price</label><input class="form-control" type="number" step=".01" min="0" name="cost_price" value="<?=e($row['cost_price'])?>"></div>
<div class="col-md-3"><label class="form-label">Selling Price</label><input class="form-control" type="number" step=".01" min="0" name="selling_price" value="<?=e($row['selling_price'])?>" required></div>
<div class="col-md-3"><label class="form-label">Discount %</label><input class="form-control" type="number" step=".01" min="0" max="100" name="discount" value="<?=e($row['discount'])?>"></div>
<div class="col-md-3"><label class="form-label">Stock</label><input class="form-control" type="number" min="0" name="stock" value="<?=e($row['stock'])?>" required></div>
<div class="col-md-4"><label class="form-label">Category</label><select class="form-select" name="category_id"><option value="">—</option><?php foreach($cats as $x):?><option value="<?=$x['id']?>" <?=$row['category_id']==$x['id']?'selected':''?>><?=e($x['name'])?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label">Brand</label><select class="form-select" name="brand_id"><option value="">—</option><?php foreach($brands as $x):?><option value="<?=$x['id']?>" <?=$row['brand_id']==$x['id']?'selected':''?>><?=e($x['name'])?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label">Partner</label><select class="form-select" name="partner_id"><option value="">—</option><?php foreach($partners as $x):?><option value="<?=$x['id']?>" <?=$row['partner_id']==$x['id']?'selected':''?>><?=e($x['name'])?></option><?php endforeach;?></select></div>
<div class="col-md-8"><label class="form-label">Image URL</label><input class="form-control" name="image" value="<?=e($row['image'])?>"></div>
<div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status"><option value="active" <?=$row['status']==='active'?'selected':''?>>Active</option><option value="inactive" <?=$row['status']==='inactive'?'selected':''?>>Inactive</option></select></div>
</div><div class="mt-4"><button class="btn btn-stare">Save Product</button> <a class="btn btn-outline-secondary" href="index.php">Cancel</a></div></form></div>
<?php include __DIR__.'/../../includes/footer.php';?>
