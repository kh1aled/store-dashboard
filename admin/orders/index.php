<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'view_orders');
$active = 'orders';
$pageTitle = 'Orders';
$status = $_GET['status'] ?? '';
$sql = "SELECT o.*,u.name client_name,e_u.name employee_name FROM orders o JOIN clients c ON c.id=o.client_id JOIN users u ON u.id=c.user_id LEFT JOIN employees e ON e.id=o.employee_id LEFT JOIN users e_u ON e_u.id=e.user_id";
$params = [];
if (in_array($status, ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'], true)) {
    $sql .= " WHERE o.status=?";
    $params[] = $status;
}
$sql .= " ORDER BY o.id DESC";
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();
include __DIR__ . '/../../includes/header.php'; ?>

<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Orders</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Orders</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<!--begin::App Content-->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title mb-0">Orders</h1>
    </div>
    <a class="btn btn-sm btn-primary" href="create.php"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>New Order</a>
</div>
<div class="card mb-4">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <h3 class="card-title">Order List</h3>
            </div>
            <div class="col-12 col-md-8">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <form>
                        <select class="form-select form-select-sm w-auto" name="status" aria-label="Filter by status" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <?php foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?><option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
                        </select>
                    </form>
                </div>
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
                        <th>Employee</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $o): ?><tr>
                            <td><span class="fw-medium">#<?= e($o['order_number']) ?></span></td>
                            <td><?= e($o['client_name']) ?></td>
                            <td><?= e($o['employee_name'] ?? 'Unassigned') ?></td>
                            <td><?= money($o['total_amount']) ?></td>
                            <td><?= status_badge($o['status']) ?></td>
                            <td class="text-body-secondary"><?= e(date('Y-m-d H:i', strtotime($o['created_at']))) ?></td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="view.php?id=<?= $o['id'] ?>" aria-label="View order #<?= e($o['order_number']) ?>"><i class="bi bi-eye" aria-hidden="true"></i></a></td>
                        </tr><?php endforeach; ?>
                    <?php if (!$rows): ?><tr>
                        <td colspan="7" class="text-center py-5 text-body-secondary">No orders found.</td>
                    </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>