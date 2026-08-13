<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'view_employees');
$active = 'employees';
$pageTitle = 'Employees';
$rows = $pdo->query("SELECT e.*,u.name,u.email,u.status user_status,r.name role_name FROM employees e JOIN users u ON u.id=e.user_id JOIN roles r ON r.id=u.role_id ORDER BY e.id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php'; ?>


<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Employees</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Employees</li>
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
        <h1 class="page-title mb-0">Employees</h1>
    </div><?php if (has_permission($pdo, 'manage_employees')): ?><a href="form.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Add Employee</a><?php endif; ?>
</div>
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?><tr>
                            <td>
                                <span class="fw-medium"><?= e($r['name']) ?></span>
                                <div class="text-body-secondary small"><?= e($r['email']) ?></div>
                            </td>
                            <td><?= e($r['department']) ?></td>
                            <td><?= e($r['position']) ?></td>
                            <td><?= money($r['salary']) ?></td>
                            <td><?= status_badge($r['user_status']) ?></td>
                            <td><?= e(ucfirst($r['role_name'])) ?></td>
                            <td class="text-end"><?php if (has_permission($pdo, 'manage_employees')): ?>
                                    <a class="btn btn-sm btn-outline-secondary" href="form.php?id=<?= $r['id'] ?>" aria-label="Edit <?= e($r['name']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr><?php endforeach; ?>
                    <?php if (!$rows): ?><tr>
                            <td colspan="7" class="text-center py-5 text-body-secondary">No employees found.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>