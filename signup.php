<?php
require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['auth_csrf'])) {
    $_SESSION['auth_csrf'] = bin2hex(random_bytes(32));
}

function auth_email_domain_exists(string $email): bool
{
    $at = strrpos($email, '@');

    if ($at === false || !function_exists('checkdnsrr')) {
        return false;
    }

    $domain = substr($email, $at + 1);

    return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
}

$errors = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
];
$formError = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf'] ?? '');

    if (!hash_equals($_SESSION['auth_csrf'], $token)) {
        $formError = 'The form expired. Please refresh and try again.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        $nameLength = function_exists('mb_strlen')
            ? mb_strlen($name, 'UTF-8')
            : strlen($name);

        if ($name === '') {
            $errors['name'] = 'Enter your name.';
        } elseif ($nameLength < 2 || $nameLength > 120) {
            $errors['name'] = 'Your name must be between 2 and 120 characters.';
        } elseif (preg_match('/[\x00-\x1F\x7F]/', $name)) {
            $errors['name'] = 'Your name contains invalid characters.';
        }

        if ($email === '') {
            $errors['email'] = 'Enter your email address.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter an email in the format name@example.com.';
        } elseif (!auth_email_domain_exists($email)) {
            $errors['email'] = 'That email domain could not be found. Check its spelling.';
        }

        if ($password === '') {
            $errors['password'] = 'Create a password.';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Your password must be at least 8 characters.';
        } elseif (strlen($password) > 128) {
            $errors['password'] = 'Your password must be no more than 128 characters.';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Confirm your password.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'The passwords do not match.';
        }

        $hasErrors = false;
        foreach ($errors as $message) {
            if ($message !== '') {
                $hasErrors = true;
                break;
            }
        }

        if (!$hasErrors) {
            $pdo = db();
            $check = $pdo->prepare(
                'SELECT id FROM users WHERE email = ? LIMIT 1'
            );
            $check->execute([$email]);

            if ($check->fetch()) {
                $errors['email'] = 'An account with this email already exists. Try logging in.';
            } else {
                try {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $insert = $pdo->prepare(
                        'INSERT INTO users (name, email, password_hash)
                         VALUES (?, ?, ?)'
                    );
                    $insert->execute([$name, $email, $passwordHash]);

                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)$pdo->lastInsertId();
                    $_SESSION['user_name'] = $name;

                    header('Location: index.php');
                    exit;
                } catch (PDOException $exception) {
                    if ($exception->getCode() === '23000') {
                        $errors['email'] = 'An account with this email already exists. Try logging in.';
                    } else {
                        throw $exception;
                    }
                }
            }
        }
    }
}

$pageTitle = 'Sign up';
$active = '';
require __DIR__ . '/includes/header.php';
?>

<section class="section auth-page auth-signup">
  <aside class="auth-visual">
    <span class="auth-kicker">A DAY AROUND GAMPOLA</span>

    <div class="auth-visual-copy">
      <h2>Find your<br>own kind of<br><em>beautiful.</em></h2>
      <p>Discover quiet corners, local stories, and places worth slowing down for.</p>
    </div>

    <span class="auth-visual-note">YOUR LOCAL GUIDE · CENTRAL SRI LANKA</span>
  </aside>

  <div class="auth-card">
    <div class="eyebrow eyebrow-dark">JOIN US</div>
    <h1>Create an account</h1>
    <p>Sign up to get started with your Gampola day plan.</p>

    <?php if ($formError !== ''): ?>
      <p class="auth-error" role="alert"><?= e($formError) ?></p>
    <?php endif; ?>

    <form class="auth-form auth-signup-form" method="post" action="signup.php">
      <input type="hidden" name="csrf" value="<?= e($_SESSION['auth_csrf']) ?>">

      <div class="auth-field">
        <label for="name">Name</label>
        <input
          id="name"
          name="name"
          type="text"
          minlength="2"
          maxlength="120"
          value="<?= e($name) ?>"
          required
          autocomplete="name"
          aria-invalid="<?= $errors['name'] !== '' ? 'true' : 'false' ?>"
          aria-describedby="name-error"
        >
        <?php if ($errors['name'] !== ''): ?>
          <small class="field-error" id="name-error" role="alert">
            <?= e($errors['name']) ?>
          </small>
        <?php endif; ?>
      </div>

      <div class="auth-field">
        <label for="email">Email</label>
        <input
          id="email"
          name="email"
          type="email"
          value="<?= e($email) ?>"
          required
          autocomplete="email"
          aria-invalid="<?= $errors['email'] !== '' ? 'true' : 'false' ?>"
          aria-describedby="email-error"
        >
        <?php if ($errors['email'] !== ''): ?>
          <small class="field-error" id="email-error" role="alert">
            <?= e($errors['email']) ?>
          </small>
        <?php endif; ?>
      </div>

      <div class="auth-field">
        <label for="password">Password</label>
        <input
          id="password"
          name="password"
          type="password"
          minlength="8"
          maxlength="128"
          required
          autocomplete="new-password"
          aria-invalid="<?= $errors['password'] !== '' ? 'true' : 'false' ?>"
          aria-describedby="password-error"
        >
        <?php if ($errors['password'] !== ''): ?>
          <small class="field-error" id="password-error" role="alert">
            <?= e($errors['password']) ?>
          </small>
        <?php endif; ?>
      </div>

      <div class="auth-field">
        <label for="confirm_password">Confirm password</label>
        <input
          id="confirm_password"
          name="confirm_password"
          type="password"
          minlength="8"
          maxlength="128"
          required
          autocomplete="new-password"
          aria-invalid="<?= $errors['confirm_password'] !== '' ? 'true' : 'false' ?>"
          aria-describedby="confirm-password-error"
        >
        <?php if ($errors['confirm_password'] !== ''): ?>
          <small class="field-error" id="confirm-password-error" role="alert">
            <?= e($errors['confirm_password']) ?>
          </small>
        <?php endif; ?>
      </div>

      <button class="button button-dark button-full" type="submit">
        Create account
      </button>
    </form>

    <p class="auth-switch">
      Already have an account? <a href="login.php">Log in</a>
    </p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>