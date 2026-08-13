<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'view_brands');
$active = 'brands';
$pageTitle = 'Brands';
$rows = $pdo->query("SELECT * FROM brands ORDER BY id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div><span class="eyebrow">MANAGEMENT</span>
        <h1 class="page-title">Brands</h1>
    </div><?php if (has_permission($pdo, 'manage_brands')): ?><a class="btn btn-stare" href="form.php"><i class="bi bi-plus-lg"></i> Add</a><?php endif; ?>
</div>
<div class="panel">
    <div class="table-responsive">
        <table class="table stare-table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <tuh>Logo url </tuh>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?><tr>
                        <td>#<?= $r['id'] ?></td>
                        <td><?= e($r["name"]) ?></td>
                        <td><?= e($r["logo"]) ?></td>
                        <td><?= e($r["description"]) ?></td>
                        <td><?= status_badge($r['status']) ?></td>
                        <td><?php if (has_permission($pdo, 'manage_brands')): ?><a href="form.php?id=<?= $r['id'] ?>" class="action-link">Edit</a>
                                <form class="d-inline" method="post" action="delete.php" onsubmit="return confirmDelete('record')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-link text-danger p-0 ms-2">Delete</button></form><?php endif; ?>
                        </td>
                    </tr><?php endforeach;
                        if (!$rows): ?><tr>
                        <td colspan="6" class="text-center py-5 text-secondary">No records.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>


<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Brands</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Brands</li>
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
        <h1 class="page-title mb-0">Brands</h1>
    </div><?php if (has_permission($pdo, 'manage_brands')): ?><a class="btn btn-sm btn-primary" href="form.php"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Add</a><?php endif; ?>
</div>
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Logo URL</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?><tr>
                            <td>#<?= $r['id'] ?></td>
                            <td><span class="fw-medium"><?= e($r["name"]) ?></span></td>
                            <td><?= e($r["logo"]) ?></td>
                            <td><?= e($r["description"]) ?></td>
                            <td><?= status_badge($r['status']) ?></td>
                            <td class="text-end"><?php if (has_permission($pdo, 'manage_brands')): ?>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a class="btn btn-sm btn-outline-secondary" href="form.php?id=<?= $r['id'] ?>" aria-label="Edit <?= e($r['name']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                        <form method="post" action="delete.php" onsubmit="return confirmDelete('record')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Delete <?= e($r['name']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </div><?php endif; ?>
                            </td>
                        </tr><?php endforeach;
                            if (!$rows): ?><tr>
                            <td colspan="6" class="text-center py-5 text-body-secondary">No records.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div><?php include __DIR__ . '/../../includes/footer.php'; ?>