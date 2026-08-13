<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'view_reports');
$active = 'reports';
$pageTitle = 'Reports';
$revenue = (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status NOT IN('cancelled')")->fetchColumn();
$avg = (float) $pdo->query("SELECT COALESCE(AVG(total_amount),0) FROM orders WHERE status NOT IN('cancelled')")->fetchColumn();
$top = $pdo->query("SELECT oi.product_name,SUM(oi.quantity) qty,SUM(oi.subtotal) revenue FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status<>'cancelled' GROUP BY oi.product_id,oi.product_name ORDER BY qty DESC LIMIT 10")->fetchAll();
$monthly = $pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') month,COUNT(*) orders,SUM(total_amount) revenue FROM orders WHERE status<>'cancelled' GROUP BY month ORDER BY month DESC LIMIT 6")->fetchAll();
include __DIR__ . '/../../includes/header.php'; ?>

<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Reports</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reports</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="mb-4">
    <h1 class="page-title mb-0">Reports</h1>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-body-secondary">Revenue</div>
                <div class="fs-3 fw-semibold"><?= money($revenue) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-body-secondary">Average Order</div>
                <div class="fs-3 fw-semibold"><?= money($avg) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-body-secondary">Products Sold</div>
                <div class="fs-3 fw-semibold"><?= number_format((int) $pdo->query("SELECT COALESCE(SUM(quantity),0) FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status<>'cancelled'")->fetchColumn()) ?></div>
            </div>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Top Products</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top as $r): ?><tr>
                                    <td><?= e($r['product_name']) ?></td>
                                    <td><?= $r['qty'] ?></td>
                                    <td><?= money($r['revenue']) ?></td>
                                </tr><?php endforeach; ?>
                            <?php if (!$top): ?><tr>
                                    <td colspan="3" class="text-center py-5 text-body-secondary">No data yet.</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Monthly Summary</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthly as $r): ?><tr>
                                    <td><?= e($r['month']) ?></td>
                                    <td><?= $r['orders'] ?></td>
                                    <td><?= money($r['revenue']) ?></td>
                                </tr><?php endforeach; ?>
                            <?php if (!$monthly): ?><tr>
                                    <td colspan="3" class="text-center py-5 text-body-secondary">No data yet.</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>