<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'view_clients');
$active = 'clients';
$pageTitle = 'Clients';
$rows = $pdo->query("SELECT c.*,u.name,u.email,u.status user_status,(SELECT COUNT(*) FROM orders o WHERE o.client_id=c.id) orders_count FROM clients c JOIN users u ON u.id=c.user_id ORDER BY c.id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php'; ?>

<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Clients</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Clients</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<!--begin::App Content-->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Clients</h1>
    </div><?php if (has_permission($pdo, 'manage_clients')): ?><a href="form.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Add Client</a><?php endif; ?>
</div>
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Orders</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?><tr>
                            <td>
                                <span class="fw-medium"><?= e($r['name']) ?></span>
                                <div class="text-body-secondary small"><?= e($r['email']) ?></div>
                            </td>
                            <td><?= e($r['phone']) ?></td>
                            <td><?= e($r['address']) ?></td>
                            <td><?= $r['orders_count'] ?></td>
                            <td><?= status_badge($r['status']) ?></td>
                            <td class="text-end"><?php if (has_permission($pdo, 'manage_clients')): ?>
                                    <a class="btn btn-sm btn-outline-secondary" href="form.php?id=<?= $r['id'] ?>" aria-label="Edit <?= e($r['name']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr><?php endforeach; ?>
                    <?php if (!$rows): ?><tr>
                            <td colspan="6" class="text-center py-5 text-body-secondary">No clients found.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>