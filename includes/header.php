<?php
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/functions.php';
$user=$pdo?current_user($pdo):null;
$active=$active??'';
$flashes=get_flashes();
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=e($pageTitle??APP_NAME)?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?=BASE_URL?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="<?=e($bodyClass??'')?>">
<nav class="navbar navbar-expand-lg navbar-dark stare-navbar sticky-top">
 <div class="container-fluid px-3 px-lg-4">
  <a class="navbar-brand fw-bold" href="<?=BASE_URL?>/index.php"><span class="brand-mark">S</span> STARE</a>
  <?php if($user): ?>
  <button class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#sidebar"><span class="navbar-toggler-icon"></span></button>
  <div class="d-none d-lg-flex align-items-center ms-auto gap-3">
    <span class="small text-white-50"><?=e($user['name'])?> · <?=e(ucfirst($user['role_name']))?></span>
    <a class="btn btn-sm btn-outline-light" href="<?=BASE_URL?>/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
  <?php endif; ?>
 </div>
</nav>
<?php if($user): ?>
<div class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="sidebar">
 <div class="offcanvas-header d-lg-none"><h5 class="offcanvas-title">STARE</h5><button class="btn-close" data-bs-dismiss="offcanvas"></button></div>
 <div class="offcanvas-body p-0">
  <div class="sidebar-inner">
   <div class="sidebar-user d-lg-none"><strong><?=e($user['name'])?></strong><span><?=e(ucfirst($user['role_name']))?></span></div>
   <nav class="nav flex-column gap-1">
    <?php if(has_permission($pdo,'view_dashboard')): ?><a class="nav-link <?=($active==='dashboard'?'active':'')?>" href="<?=BASE_URL?>/index.php"><i class="bi bi-grid-1x2"></i> Dashboard</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_categories')): ?><a class="nav-link <?=($active==='categories'?'active':'')?>" href="<?=BASE_URL?>/admin/categories/index.php"><i class="bi bi-tags"></i> Categories</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_brands')): ?><a class="nav-link <?=($active==='brands'?'active':'')?>" href="<?=BASE_URL?>/admin/brands/index.php"><i class="bi bi-bookmark-star"></i> Brands</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_products')): ?><a class="nav-link <?=($active==='products'?'active':'')?>" href="<?=BASE_URL?>/admin/products/index.php"><i class="bi bi-box-seam"></i> Products</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_partners')): ?><a class="nav-link <?=($active==='partners'?'active':'')?>" href="<?=BASE_URL?>/admin/partners/index.php"><i class="bi bi-building"></i> Partners</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_clients')): ?><a class="nav-link <?=($active==='clients'?'active':'')?>" href="<?=BASE_URL?>/admin/clients/index.php"><i class="bi bi-people"></i> Clients</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_employees')): ?><a class="nav-link <?=($active==='employees'?'active':'')?>" href="<?=BASE_URL?>/admin/employees/index.php"><i class="bi bi-person-badge"></i> Employees</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_orders')): ?><a class="nav-link <?=($active==='orders'?'active':'')?>" href="<?=BASE_URL?>/admin/orders/index.php"><i class="bi bi-receipt"></i> Orders</a><?php endif; ?>
    <?php if(has_permission($pdo,'create_orders') && user_role($pdo)!=='client'): ?><a class="nav-link" href="<?=BASE_URL?>/admin/orders/create.php"><i class="bi bi-plus-circle"></i> New Order</a><?php endif; ?>
    <?php if(has_permission($pdo,'view_reports')): ?><a class="nav-link <?=($active==='reports'?'active':'')?>" href="<?=BASE_URL?>/admin/reports/index.php"><i class="bi bi-bar-chart"></i> Reports</a><?php endif; ?>
    <?php if(has_permission($pdo,'manage_users')): ?><a class="nav-link <?=($active==='users'?'active':'')?>" href="<?=BASE_URL?>/admin/users/index.php"><i class="bi bi-person-gear"></i> Users</a><?php endif; ?>
    <?php if(has_permission($pdo,'manage_permissions')): ?><a class="nav-link <?=($active==='permissions'?'active':'')?>" href="<?=BASE_URL?>/admin/permissions/index.php"><i class="bi bi-shield-lock"></i> Permissions</a><?php endif; ?>
    <?php if(user_role($pdo)==='client'): ?><a class="nav-link <?=($active==='shop'?'active':'')?>" href="<?=BASE_URL?>/client/products.php"><i class="bi bi-shop"></i> Shop</a><?php endif; ?>
    <?php if(user_role($pdo)==='client'): ?><a class="nav-link <?=($active==='cart'?'active':'')?>" href="<?=BASE_URL?>/client/cart.php"><i class="bi bi-cart3"></i> Cart <span class="badge rounded-pill bg-light text-dark ms-auto"><?=cart_count($pdo,(int)client_id_for_user($pdo,(int)$user['id']))?></span></a><?php endif; ?>
    <?php if(user_role($pdo)==='client'): ?><a class="nav-link <?=($active==='myorders'?'active':'')?>" href="<?=BASE_URL?>/client/orders.php"><i class="bi bi-bag-check"></i> My Orders</a><?php endif; ?>
   </nav>
   <div class="sidebar-bottom d-lg-none"><a href="<?=BASE_URL?>/logout.php" class="btn btn-outline-light w-100">Logout</a></div>
  </div>
 </div>
</div>
<main class="main-content">
 <div class="container-fluid px-3 px-lg-4 py-4">
 <?php foreach($flashes as [$type,$msg]): ?><div class="alert alert-<?=e($type)?> alert-dismissible fade show"><?=e($msg)?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endforeach; ?>
<?php else: ?>
<main class="auth-main">
 <div class="container py-5">
<?php endif; ?>
