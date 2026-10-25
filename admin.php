<?php
require_once __DIR__.'/config.php';
$error='';
if(isset($_GET['logout'])) { $_SESSION=[]; session_destroy(); session_start(); flash('You are now signed out.'); header('Location: admin.php'); exit; }
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['form']??'')==='login') {
  verify_csrf(); $user=trim((string)($_POST['username']??'')); $pass=(string)($_POST['password']??'');
  $q=db()->prepare('SELECT id,username,password_hash FROM admins WHERE username=?');$q->execute([$user]);$admin=$q->fetch();
  if($admin && password_verify($pass,$admin['password_hash'])) { session_regenerate_id(true);$_SESSION['admin_id']=(int)$admin['id'];$_SESSION['admin_name']=$admin['username'];header('Location: admin.php');exit; }
  $error='Those details did not match. Please try again.';
}
if(is_admin() && $_SERVER['REQUEST_METHOD']==='POST' && ($_POST['form']??'')==='delete') {
  verify_csrf();$q=db()->prepare('DELETE FROM places WHERE id=?');$q->execute([(int)($_POST['id']??0)]);flash('Place deleted.');header('Location: admin.php');exit;
}
$pageTitle=is_admin()?'Admin dashboard':'Administrator sign in';
require __DIR__.'/includes/header.php';
if(!is_admin()): ?>
<section class="admin-login-wrap"><div class="admin-login-art"><div class="eyebrow">SITE MAINTENANCE</div><h1>Keep the<br>local guide<br><em>up to date.</em></h1><p>Manage the places and practical information visitors rely on.</p><span class="admin-spark">✳</span></div><div class="login-card"><span class="login-lock">⌑</span><div class="eyebrow eyebrow-dark">ADMINISTRATOR ACCESS</div><h2>Welcome back</h2><p>Sign in to maintain the attraction catalogue.</p><?php if($error): ?><div class="form-error" role="alert"><?= e($error) ?></div><?php endif; ?><form method="post" class="stack-form"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="form" value="login"><label>Username<input name="username" autocomplete="username" required maxlength="80"></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button class="button button-dark button-full">Sign in</button></form><a class="login-back" href="index.php">← Back to the visitor site</a></div></section>
<?php else: $rows=db()->query('SELECT p.id,p.name,p.distance_km,c.name category,p.updated_at FROM places p JOIN categories c ON c.id=p.category_id ORDER BY p.name')->fetchAll(); ?>
<section class="admin-top"><div><div class="eyebrow eyebrow-dark">CONTENT MANAGEMENT</div><h1>Good to see you, <?= e((string)$_SESSION['admin_name']) ?>.</h1><p>Keep the local guide useful, accurate and welcoming.</p></div><div class="admin-actions"><a class="button button-dark" href="place_form.php">＋ Add a place</a><a class="button button-outline" href="admin.php?logout=1">Sign out</a></div></section><section class="section admin-table-section"><div class="admin-table-heading"><div><h2>Your places</h2><p><?= count($rows) ?> attraction records</p></div><a class="text-link" href="index.php">View public site ↗</a></div><div class="table-wrap"><table><thead><tr><th>PLACE</th><th>CATEGORY</th><th>FROM GAMPOLA</th><th>LAST UPDATED</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><strong><?= e($r['name']) ?></strong></td><td><span class="category-pill"><?= e($r['category']) ?></span></td><td><?= number_format((float)$r['distance_km'],1) ?> km</td><td><?= date('d M Y',strtotime($r['updated_at'])) ?></td><td class="table-actions"><a href="place_form.php?id=<?= (int)$r['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this place from the guide?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="form" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="delete-link">Delete</button></form></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php endif; require __DIR__.'/includes/footer.php'; ?>


