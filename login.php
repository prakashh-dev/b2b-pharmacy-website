<?php
require_once __DIR__ . '/config.php';
if (current_user()) { header('Location: ' . (current_user()['role'] === 'admin' ? 'admin.php' : 'catalog.php')); exit; }
$pageTitle = 'Log in — MediTrade'; $error = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $email = strtolower(trim((string)($_POST['email'] ?? ''))); $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id,name,email,password_hash,role,business_name FROM users WHERE email = ? LIMIT 1'); $stmt->execute([$email]); $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        unset($user['password_hash']); $_SESSION['user'] = $user;
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        header('Location: ' . ($user['role'] === 'admin' ? 'admin.php' : 'catalog.php')); exit;
    } else { $error = 'Email or password is incorrect.'; }
}
require __DIR__ . '/partials/header.php';
?>
<section class="auth-section container"><div class="auth-aside"><span class="auth-symbol">✚</span><span class="eyebrow eyebrow-light">WELCOME BACK</span><h1>Good supply starts with a clear view.</h1><p>Sign in to browse products, manage your cart and review demo order updates.</p><div class="auth-check">✓ One organised catalogue</div><div class="auth-check">✓ Transparent stock information</div><div class="auth-check">✓ Order status visibility</div></div>
<div class="auth-card"><span class="eyebrow">ACCOUNT ACCESS</span><h2>Log in to MediTrade</h2><p class="muted">Enter your account details below.</p>
<?php if ($error): ?><div class="form-errors"><p><?= e($error) ?></p></div><?php endif; ?>
<form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email address<input type="email" name="email" autocomplete="username" value="<?= e($email) ?>" required></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button class="btn btn-primary btn-large btn-block" type="submit">Log in →</button></form><div class="demo-login"><strong>Demo admin account</strong><span>Email: admin@meditrade.local</span><span>Password: Admin@12345</span></div><p class="auth-bottom">New to MediTrade? <a href="register.php">Create a buyer account</a></p></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
