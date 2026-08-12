<?php
require_once __DIR__.'/config/database.php'; require_once __DIR__.'/includes/functions.php';
if(is_logged_in($pdo)) redirect('/index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $pass=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
 $type=$_POST['account_type']??'client';
 if($type!=='client') $type='employee';
 if(strlen($name)<3 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($pass)<6 || $pass!==$confirm) $error='Please provide valid details and matching passwords (6+ characters).';
 else {
  try {
   $pdo->beginTransaction();
   $role=$type==='employee'?'employee':'client';
   $st=$pdo->prepare("SELECT id FROM roles WHERE name=?"); $st->execute([$role]); $roleId=(int)$st->fetchColumn();
   $status=$type==='employee'?'pending':'active';
   $st=$pdo->prepare("INSERT INTO users(name,email,password,role_id,status) VALUES(?,?,?,?,?)");
   $st->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$roleId,$status]); $uid=(int)$pdo->lastInsertId();
   if($type==='client') { $st=$pdo->prepare("INSERT INTO clients(user_id) VALUES(?)"); $st->execute([$uid]); }
   else { $st=$pdo->prepare("INSERT INTO employees(user_id,status) VALUES(?,?)"); $st->execute([$uid,'inactive']); }
   $pdo->commit();
   if($status==='active'){ login_user(['id'=>$uid]); redirect('/index.php'); }
   flash('success','Employee account created and is waiting for admin approval.'); redirect('/login.php');
  } catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); $error='Email may already be registered.'; }
 }
}
$pageTitle='Register'; $bodyClass='auth-page'; include __DIR__.'/includes/header.php';
?>
<div class="auth-card">
 <div class="text-center mb-4"><div class="brand-mark large">S</div><h1 class="h3 mt-3">Create Account</h1><p class="text-secondary">Choose the account type you need.</p></div>
 <?php if($error): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
 <form method="post"><?=csrf_field()?>
  <input class="form-control mb-3" name="name" placeholder="Full name" required>
  <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
  <input class="form-control mb-3" type="password" name="password" placeholder="Password" minlength="6" required>
  <input class="form-control mb-3" type="password" name="confirm_password" placeholder="Confirm password" required>
  <label class="form-label">Account Type</label>
  <select class="form-select mb-4" name="account_type"><option value="client">Client</option><option value="employee">Employee (admin approval)</option></select>
  <button class="btn btn-stare w-100">Create Account</button>
 </form>
 <div class="text-center mt-4 small"><a href="<?=BASE_URL?>/login.php">Back to login</a></div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
