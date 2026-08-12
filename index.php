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


<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><span class="eyebrow">OVERVIEW</span>
        <h1 class="page-title mb-1">Good to see you, <?= e($user['name'])  ?></h1>
        <p class="text-secondary mb-0">Live operational summary from MySQL.</p>
    </div><span class="role-pill"><?= e(ucfirst($role)) ?></span>
</div>
<div class="row g-3 mb-4">
    <?php foreach ([['products', 'Products', 'bi-box-seam'], ['clients', 'Clients', 'bi-people'], ['orders', 'Orders', 'bi-receipt'], ['revenue', 'Revenue', 'bi-cash-stack']] as $i => $x): ?>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi <?= $x[2] ?>"></i></div>
                <div class="text-secondary small"><?= $x[1] ?></div>
                <div class="stat-value"><?= ($x[0] === 'revenue' ? money($stats[$x[0]]) : number_format($stats[$x[0]])) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="panel">
            <div class="panel-head">
                <h2 class="h5 mb-0">Recent Orders</h2><a href="<?= BASE_URL ?>/admin/orders/index.php">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle stare-table mb-0">
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
                                <td><strong>#<?= e($o['order_number']) ?></strong></td>
                                <td><?= e($o['client_name']) ?></td>
                                <td><?= money($o['total_amount']) ?></td>
                                <td><?= status_badge($o['status']) ?></td>
                                <td class="text-secondary"><?= e(date('M d, Y', strtotime($o['created_at']))) ?></td>
                            </tr><?php endforeach;
                                if (!$recent): ?><tr>
                                <td colspan="5" class="text-center py-5 text-secondary">No orders yet.</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="h5 mb-0">Quick Health</h2>
            </div>
            <div class="health-row"><span>Low stock products</span><strong><?= number_format($stats['low_stock']) ?></strong></div>
            <div class="health-row"><span>Database</span><span class="badge text-bg-success">Connected</span></div>
            <div class="health-row"><span>Role system</span><span class="badge text-bg-success">Active</span></div>
            <div class="health-row"><span>Version</span><strong><?= e(APP_VERSION) ?></strong></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>