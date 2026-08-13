<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login($pdo);
$role = user_role($pdo);
if ($role === 'client') redirect('/client/dashboard.php');
$pageTitle = 'Dashboard';
$active = 'dashboard';
$stats = [
    'products' => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn(),
    'clients' => (int)$pdo->query("SELECT COUNT(*) FROM clients WHERE status='active'")->fetchColumn(),
    'orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'revenue' => (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status<>'cancelled'")->fetchColumn(),
    'low_stock' => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock<=5 AND status='active'")->fetchColumn(),
];
$recent = $pdo->query("SELECT o.id,o.order_number,o.total_amount,o.status,o.created_at,u.name client_name FROM orders o JOIN clients c ON c.id=o.client_id JOIN users u ON u.id=c.user_id ORDER BY o.id DESC LIMIT 8")->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<!--begin::App Content-->

<div class="row g-3 mb-4">
    <!-- <?php foreach ([['products', 'Products', 'bi-box-seam'], ['clients', 'Clients', 'bi-people'], ['orders', 'Orders', 'bi-receipt'], ['revenue', 'Revenue', 'bi-cash-stack']] as $i => $x): ?>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi <?= $x[2] ?>"></i></div>
                <div class="text-secondary small"><?= $x[1] ?></div>
                <div class="stat-value"><?= ($x[0] === 'revenue' ? money($stats[$x[0]]) : number_format($stats[$x[0]])) ?></div>
            </div>
        </div>
    <?php endforeach; ?> -->
    <?php foreach ([['products', 'Products', 'bi-box-seam', 'products', 'primary'], ['clients', 'Clients', 'bi-people', 'clients', 'success'], ['orders', 'Orders', 'bi-receipt', 'orders', 'warning'], ['revenue', 'Revenue', 'bi-cash-stack', 'reports', 'danger']] as $i => $x): ?>
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 1-->
            <div class="small-box text-bg-<?= $x[4] ?>">
                <div class="inner">
                    <h3><?= ($x[0] === 'revenue' ? money($stats[$x[0]]) : number_format($stats[$x[0]])) ?></h3>

                    <p><?= $x[1] ?></p>
                </div>
                <div class="small-box-icon d-flex justify-content-center align-items-center"><i class="bi <?= $x[2] ?>"></i></div>

                <a
                    href="<?= BASE_URL . '/admin' . '/' . $x[3]  . '/index.php' ?>"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    More info <i class="bi bi-link-45deg"></i>
                </a>
            </div>
            <!--end::Small Box Widget 1-->
        </div>
        <!--end::Col-->
    <?php endforeach; ?>

</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h3 class="card-title">Recent Orders</h3>
                    </div>
                    <div class="col-auto">
                        <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-sm btn-outline-secondary">View all</a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Client</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent as $o): ?><tr>
                                    <td><span class="fw-medium">#<?= e($o['order_number']) ?></span></td>
                                    <td><?= e($o['client_name']) ?></td>
                                    <td><?= money($o['total_amount']) ?></td>
                                    <td><?= status_badge($o['status']) ?></td>
                                    <td class="text-body-secondary"><?= e(date('M d, Y', strtotime($o['created_at']))) ?></td>
                                </tr><?php endforeach;
                                    if (!$recent): ?><tr>
                                    <td colspan="5" class="text-center py-5 text-body-secondary">No orders yet.</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card mb-4 h-100">
            <div class="card-header">
                <h3 class="card-title">Quick Health</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Low stock products</span>
                        <strong><?= number_format($stats['low_stock']) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Database</span>
                        <span class="badge text-bg-success">Connected</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Role system</span>
                        <span class="badge text-bg-success">Active</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Version</span>
                        <strong><?= e(APP_VERSION) ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>