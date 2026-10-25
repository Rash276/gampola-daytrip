<?php
require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['auth_csrf'])) {
    $_SESSION['auth_csrf'] = bin2hex(random_bytes(32));
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf'] ?? '');

    if (!hash_equals($_SESSION['auth_csrf'], $token)) {
        $error = 'The form expired. Please try again.';
    } else {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $error = 'Enter your email and password.';
        } else {
            $stmt = db()->prepare(
                'SELECT id, name, email, password_hash
                 FROM users
                 WHERE email = ?
                 LIMIT 1'
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'The email or password is incorrect.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_name'] = $user['name'];

                header('Location: index.php');
                exit;
            }
        }
    }
}

$pageTitle = 'Log in';
$active = '';
require __DIR__ . '/includes/header.php';
?>

<section class="section auth-page">

  <aside class="auth-visual">
    <span class="auth-kicker">A DAY AROUND GAMPOLA</span>

    <div class="auth-visual-copy">
      <h2>Find your<br>own kind of<br><em>beautiful.</em></h2>
      <p>Discover quiet corners, local stories, and places worth slowing down for.</p>
    </div>

    <span class="auth-visual-note">YOUR LOCAL GUIDE · CENTRAL SRI LANKA</span>
  </aside>

  <div class="auth-card">
    <div class="eyebrow eyebrow-dark">WELCOME BACK</div>
    <h1>Log in</h1>
    <p>Log in to continue planning your day.</p>

    <?php if ($error !== ''): ?>
      <p class="auth-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="auth-form" method="post" action="login.php">
      <input
        type="hidden"
        name="csrf"
        value="<?= e($_SESSION['auth_csrf']) ?>"
      >

      <label for="email">Email</label>
      <input
        id="email"
        name="email"
        type="email"
        value="<?= e($email) ?>"
        required
        autocomplete="email"
      >

      <label for="password">Password</label>
      <input
        id="password"
        name="password"
        type="password"
        required
        autocomplete="current-password"
      >

      <button class="button button-dark button-full" type="submit">
        Log in
      </button>
    </form>

    <p class="auth-switch">
      Don’t have an account? <a href="signup.php">Sign up</a>
    </p>
  </div>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>