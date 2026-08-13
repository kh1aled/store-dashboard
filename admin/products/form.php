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
<div class="mb-4">
    <h1 class="page-title mb-0"><?=$id?'Edit':'Add'?> Product</h1>
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Product Details</h3>
    </div>
    <div class="card-body">
        <?php if($error):?><div class="alert alert-danger"><?=e($error)?></div><?php endif;?>
        <form method="post">
            <?=csrf_field()?>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="productSku" name="sku" placeholder="SKU" value="<?=e($row['sku'])?>" required>
                        <label for="productSku">SKU</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="productName" name="name" placeholder="Product name" value="<?=e($row['name'])?>" required>
                        <label for="productName">Name</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control" id="productDescription" name="description" placeholder="Description" style="height: 6rem"><?=e($row['description'])?></textarea>
                        <label for="productDescription">Description</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" step=".01" min="0" class="form-control" id="productCostPrice" name="cost_price" placeholder="Cost price" value="<?=e($row['cost_price'])?>">
                        <label for="productCostPrice">Cost Price</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" step=".01" min="0" class="form-control" id="productSellingPrice" name="selling_price" placeholder="Selling price" value="<?=e($row['selling_price'])?>" required>
                        <label for="productSellingPrice">Selling Price</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" step=".01" min="0" max="100" class="form-control" id="productDiscount" name="discount" placeholder="Discount %" value="<?=e($row['discount'])?>">
                        <label for="productDiscount">Discount %</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" min="0" class="form-control" id="productStock" name="stock" placeholder="Stock" value="<?=e($row['stock'])?>" required>
                        <label for="productStock">Stock</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="productCategory" name="category_id">
                            <option value="">—</option>
                            <?php foreach($cats as $x):?><option value="<?=$x['id']?>" <?=$row['category_id']==$x['id']?'selected':''?>><?=e($x['name'])?></option><?php endforeach;?>
                        </select>
                        <label for="productCategory">Category</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="productBrand" name="brand_id">
                            <option value="">—</option>
                            <?php foreach($brands as $x):?><option value="<?=$x['id']?>" <?=$row['brand_id']==$x['id']?'selected':''?>><?=e($x['name'])?></option><?php endforeach;?>
                        </select>
                        <label for="productBrand">Brand</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="productPartner" name="partner_id">
                            <option value="">—</option>
                            <?php foreach($partners as $x):?><option value="<?=$x['id']?>" <?=$row['partner_id']==$x['id']?'selected':''?>><?=e($x['name'])?></option><?php endforeach;?>
                        </select>
                        <label for="productPartner">Partner</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="productImage" name="image" placeholder="Image URL" value="<?=e($row['image'])?>">
                        <label for="productImage">Image URL</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="productStatus" name="status">
                            <option value="active" <?=$row['status']==='active'?'selected':''?>>Active</option>
                            <option value="inactive" <?=$row['status']==='inactive'?'selected':''?>>Inactive</option>
                        </select>
                        <label for="productStatus">Status</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Save Product</button>
                <a class="btn btn-sm btn-outline-secondary" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__.'/../../includes/footer.php';?>