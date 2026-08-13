<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_employees');
$active = 'employees';
$id = (int)($_GET['id'] ?? 0);
$row = ['name' => '', 'email' => '', 'phone' => '', 'address' => '', 'department' => '', 'position' => '', 'salary' => '0', 'hire_date' => '', 'status' => 'active', 'role_id' => ''];
$userId = 0;
if ($id) {
    $st = $pdo->prepare("SELECT e.*,u.name,u.email,u.status user_status,u.role_id FROM employees e JOIN users u ON u.id=e.user_id WHERE e.id=?");
    $st->execute([$id]);
    $found = $st->fetch();
    if (!$found) redirect('/admin/employees/index.php');
    $row = $found;
    $userId = (int)$found['user_id'];
}
$roles = $pdo->query("SELECT id,name FROM roles WHERE name IN('employee','admin') ORDER BY name")->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $roleId = (int)($_POST['role_id'] ?? 0);
    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$roleId) $error = 'Name, email and role are required.';
    else try {
        $pdo->beginTransaction();
        $status = $_POST['status'] ?? 'active';
        if ($id) {
            $pdo->prepare("UPDATE users SET name=?,email=?,role_id=?,status=? WHERE id=?")->execute([$name, $email, $roleId, $status, $userId]);
            if ($password !== '') $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);
            $pdo->prepare("UPDATE employees SET phone=?,address=?,department=?,position=?,salary=?,hire_date=?,status=? WHERE id=?")->execute([trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), trim($_POST['department'] ?? ''), trim($_POST['position'] ?? ''), (float)($_POST['salary'] ?? 0), ($_POST['hire_date'] ?? '') ?: null, $status === 'active' ? 'active' : 'inactive', $id]);
        } else {
            $pdo->prepare("INSERT INTO users(name,email,password,role_id,status) VALUES(?,?,?,?,?)")->execute([$name, $email, password_hash($password ?: 'password', PASSWORD_DEFAULT), $roleId, $status]);
            $userId = (int)$pdo->lastInsertId();
            $pdo->prepare("INSERT INTO employees(user_id,phone,address,department,position,salary,hire_date,status) VALUES(?,?,?,?,?,?,?,?)")->execute([$userId, trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), trim($_POST['department'] ?? ''), trim($_POST['position'] ?? ''), (float)($_POST['salary'] ?? 0), ($_POST['hire_date'] ?? '') ?: null, $status === 'active' ? 'active' : 'inactive']);
        }
        $pdo->commit();
        flash('success', 'Employee saved.');
        redirect('/admin/employees/index.php');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = 'Could not save employee. Email may already exist.';
    }
}
$pageTitle = $id ? 'Edit Employee' : 'Add Employee';
include __DIR__ . '/../../includes/header.php'; ?>
<div class="mb-4">
    <h1 class="page-title mb-0"><?= $id ? 'Edit' : 'Add' ?> Employee</h1>
</div>
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Employee Details</h3>
    </div>
    <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="employeeName" name="name" placeholder="Employee name" value="<?= e($row['name']) ?>" required>
                        <label for="employeeName">Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="employeeEmail" name="email" placeholder="Email" value="<?= e($row['email']) ?>" required>
                        <label for="employeeEmail">Email</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="employeePhone" name="phone" placeholder="Phone" value="<?= e($row['phone']) ?>">
                        <label for="employeePhone">Phone</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="employeeAddress" name="address" placeholder="Address" value="<?= e($row['address']) ?>">
                        <label for="employeeAddress">Address</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="employeeDepartment" name="department" placeholder="Department" value="<?= e($row['department']) ?>">
                        <label for="employeeDepartment">Department</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="employeePosition" name="position" placeholder="Position" value="<?= e($row['position']) ?>">
                        <label for="employeePosition">Position</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" step=".01" min="0" class="form-control" id="employeeSalary" name="salary" placeholder="Salary" value="<?= e($row['salary']) ?>">
                        <label for="employeeSalary">Salary</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="employeeHireDate" name="hire_date" placeholder="Hire date" value="<?= e($row['hire_date']) ?>">
                        <label for="employeeHireDate">Hire Date</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="employeeRole" name="role_id">
                            <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>" <?= $row['role_id'] == $r['id'] ? 'selected' : '' ?>><?= e(ucfirst($r['name'])) ?></option><?php endforeach; ?>
                        </select>
                        <label for="employeeRole">Role</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="employeeStatus" name="status">
                            <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="blocked" <?= $row['status'] === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                        </select>
                        <label for="employeeStatus">Status</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="password" class="form-control" id="employeePassword" name="password" placeholder="Password" <?= $id ? '' : 'required' ?>>
                        <label for="employeePassword">Password <?= $id ? '(optional)' : '' ?></label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Save Employee</button>
                <a class="btn btn-sm btn-outline-secondary" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>