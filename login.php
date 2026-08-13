<?php
require_once __DIR__.'/config/database.php'; require_once __DIR__.'/includes/functions.php';
if(is_logged_in($pdo)) redirect('/index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $email=trim($_POST['email']??''); $password=$_POST['password']??'';
 $st=$pdo->prepare("SELECT u.*,r.name role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=? LIMIT 1"); $st->execute([$email]); $u=$st->fetch();
 if(!$u || !password_verify($password,$u['password']) || $u['status']!=='active') $error='Invalid credentials or inactive account.';
 else { login_user($u); redirect('/index.php'); }
}
$pageTitle='Login'; $bodyClass='auth-page'; include __DIR__.'/includes/header.php';
?>
<div class="auth-card">
 <div class="text-center mb-4"><div class="brand-mark large">S</div><h1 class="h3 mt-3 text-info">Welcome to STARE</h1><p class="text-secondary">Sign in to continue</p></div>
 <?php if($error): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
 <form method="post"><?=csrf_field()?>
  <label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required>
  <label class="form-label">Password</label><input class="form-control mb-4" type="password" name="password" required>
  <button class="btn btn-stare w-100">Login</button>
 </form>
 <div class="text-center mt-4 small">New client? <a href="<?=BASE_URL?>/register.php">Create account</a></div>
 <div class="demo-box mt-4"><strong>Demo accounts</strong><br>Admin: admin@stare.local / password<br>Employee: employee@stare.local / password<br>Client: client@stare.local / password</div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
