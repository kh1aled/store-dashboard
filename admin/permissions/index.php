<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_permissions');
$active = 'permissions';
$pageTitle = 'Role Permissions';

$roles = $pdo->query("SELECT id,name,description FROM roles WHERE name IN ('employee','client') ORDER BY FIELD(name,'employee','client')")->fetchAll();
$permissions = $pdo->query("SELECT id,name,description FROM permissions ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  $roleId = (int) ($_POST['role_id'] ?? 0);
  $allowedRoleIds = array_map('intval', array_column($roles, 'id'));
  if (!in_array($roleId, $allowedRoleIds, true)) {
    http_response_code(400);
    exit('Invalid role.');
  }
  $selected = array_map('intval', $_POST['permissions'] ?? []);
  try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM role_permissions WHERE role_id=?")->execute([$roleId]);
    $ins = $pdo->prepare("INSERT INTO role_permissions(role_id,permission_id) VALUES(?,?)");
    $validIds = array_map('intval', array_column($permissions, 'id'));
    foreach (array_unique($selected) as $pid) {
      if (in_array($pid, $validIds, true)) $ins->execute([$roleId, $pid]);
    }
    $pdo->commit();
    flash('success', 'Permissions updated successfully.');
    redirect('/admin/permissions/index.php');
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('danger', 'Could not update permissions.');
    redirect('/admin/permissions/index.php');
  }
}

$assigned = [];
$st = $pdo->query("SELECT role_id,permission_id FROM role_permissions");
foreach ($st->fetchAll() as $x) $rid = (int) $x['role_id'];
$pid = (int) $x['permission_id'];
$assigned[$rid][$pid] = true;

include __DIR__ . '/../../includes/header.php';
?>

<!--begin::App Content Header-->
<div class="app-content-header" style="padding: 1rem 0rem;">
  <!--begin::Container-->
  <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
      <div class="col-sm-6">
        <h1 class="mb-0 fs-3">Permissions</h1>
      </div>
      <div class="col-sm-6">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Permissions</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end::Row-->
  </div>
  <!--end::Container-->
</div>


<!--begin::App Content-->
<div class="mb-4">
  <h1 class="page-title mb-1">Role Permissions</h1>
  <p class="text-body-secondary mb-0">Control exactly what Employee and Client roles can access. Changes are enforced server-side.</p>
</div>

<?php foreach ($roles as $role): ?>
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h3 class="card-title mb-1"><?= e(ucfirst($role['name'])) ?></h3>
        <div class="text-body-secondary small"><?= e($role['description']) ?></div>
      </div>
      <span class="badge text-bg-dark"><?= count($permissions) ?> permissions available</span>
    </div>
    <div class="card-body">
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
        <div class="row g-3">
          <?php foreach ($permissions as $p): ?>
            <div class="col-md-6 col-xl-4">
              <label class="border rounded-3 p-3 d-flex gap-3 align-items-start h-100">
                <input class="form-check-input mt-1" type="checkbox" name="permissions[]" value="<?= $p['id'] ?>"
                  <?= isset($assigned[$role['id']][$p['id']]) ? 'checked' : '' ?>>
                <span>
                  <strong class="d-block"><?= e(ucwords(str_replace('_', ' ', $p['name']))) ?></strong>
                  <small class="text-body-secondary"><?= e($p['description']) ?></small>
                </span>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-shield-check me-1" aria-hidden="true"></i>Save <?= e(ucfirst($role['name'])) ?> Permissions</button>
        </div>
      </form>
    </div>
  </div>
<?php endforeach; ?>

<div class="card mb-4">
  <div class="card-header">
    <h3 class="card-title">Admin Role</h3>
  </div>
  <div class="card-body">
    <p class="mb-0 text-body-secondary">Admin has full access by design and is not restricted by the role_permissions table.</p>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>