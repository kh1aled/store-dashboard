<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_users');
$active = 'users';
$pageTitle = 'Users';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    if (in_array($status, ['active', 'pending', 'blocked'], true)) {
        if ($id === (int) current_user($pdo)['id'] && $status !== 'active') {
            flash('danger', 'You cannot block your own account.');
        } else {
            $pdo->prepare("UPDATE users SET status=? WHERE id=?")->execute([$status, $id]);
            flash('success', 'User status updated.');
        }
    }
    redirect('/admin/users/index.php');
}
$rows = $pdo->query("SELECT u.id,u.name,u.email,u.status,u.created_at,r.name role_name FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.id DESC")->fetchAll();
include __DIR__ . '/../../includes/header.php'; ?>
<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Users</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-0">Users</h1>
        <p class="text-body-secondary mb-0">Manage account status and review assigned roles.</p>
    </div>
</div>
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $u): ?><tr>
                            <td>
                                <span class="fw-medium"><?= e($u['name']) ?></span>
                                <div class="small text-body-secondary"><?= e($u['email']) ?></div>
                            </td>
                            <td><?= e(ucfirst($u['role_name'])) ?></td>
                            <td><?= status_badge($u['status']) ?></td>
                            <td class="text-body-secondary"><?= e(date('Y-m-d', strtotime($u['created_at']))) ?></td>
                            <td class="text-end">
                                <form method="post" class="d-flex justify-content-end gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <select class="form-select form-select-sm w-auto" name="status" aria-label="Status for <?= e($u['name']) ?>">
                                        <option value="active" <?= $u['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="pending" <?= $u['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="blocked" <?= $u['status'] === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Save</button>
                                </form>
                            </td>
                        </tr><?php endforeach; ?>
                    <?php if (!$rows): ?><tr>
                            <td colspan="5" class="text-center py-5 text-body-secondary">No users found.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>