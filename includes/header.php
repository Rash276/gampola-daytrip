<?php
$pageTitle = $pageTitle ?? APP_NAME;
$active = $active ?? '';
$notice = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f7f5ef">
  <title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/gampola-daytrip/assets/css/style.css?v=4">
</head>
<body>

<header class="site-header">
  <div class="nav-wrap">

    <a class="brand" href="index.php">
      <span class="brand-mark">G</span>
      <span>Gampola<span class="brand-light"> Daytrip</span></span>
    </a>

    <button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>

    <nav class="main-nav" aria-label="Main navigation">
      <a class="<?= $active === 'explore' ? 'active' : '' ?>" href="index.php">
        Explore
      </a>

      <a class="<?= $active === 'planner' ? 'active' : '' ?>" href="planner.php">
        My day plan
        <span class="nav-count"><?= count($_SESSION['plan'] ?? []) ?></span>
      </a>

      <a class="nav-login" href="login.php">Log in</a>
      <a class="nav-signup" href="signup.php">Sign up</a>

      <a class="nav-admin" href="admin.php">
        <?= is_admin() ? 'Dashboard' : 'Admin' ?>
      </a>
    </nav>

  </div>
</header>

<main>
  <?php if ($notice): ?>
    <div class="toast toast-<?= e($notice[1]) ?>" role="status">
      <?= e($notice[0]) ?>
    </div>
  <?php endif; ?>