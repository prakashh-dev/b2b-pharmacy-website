<?php
require_once __DIR__ . '/config.php';
if (current_user()) { header('Location: ' . (current_user()['role'] === 'admin' ? 'admin.php' : 'catalog.php')); exit; }
$pageTitle = 'Create buyer account — MediTrade'; $errors = [];
$name = ''; $email = ''; $business = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? '')); $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $business = trim((string)($_POST['business_name'] ?? '')); $password = (string)($_POST['password'] ?? '');
    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) $errors[] = 'Enter a name between 2 and 100 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (mb_strlen($business) < 2 || mb_strlen($business) > 150) $errors[] = 'Enter your pharmacy/business name.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?'); $check->execute([$email]);
        if ($check->fetch()) $errors[] = 'An account with this email already exists.';
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role,business_name) VALUES (?,?,?,'buyer',?)");
            $stmt->execute([$name,$email,$hash,$business]);
            set_flash('Account created. You can now log in.'); header('Location: login.php'); exit;
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="auth-section container"><div class="auth-aside"><span class="auth-symbol">✚</span><span class="eyebrow eyebrow-light">JOIN MEDITRADE</span><h1>Your pharmacy supply workflow, organised.</h1><p>Create a buyer demo account to explore the catalogue and place test orders.</p><div class="auth-check">✓ Product catalogue access</div><div class="auth-check">✓ Order history in one place</div><div class="auth-check">✓ Simple bulk ordering</div></div>
<div class="auth-card"><span class="eyebrow">BUYER REGISTRATION</span><h2>Create your account</h2><p class="muted">Use your business details for this demo.</p>
<?php if ($errors): ?><div class="form-errors"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Full name<input name="name" autocomplete="name" value="<?= e($name) ?>" required maxlength="100"></label><label>Business / pharmacy name<input name="business_name" value="<?= e($business) ?>" required maxlength="150"></label><label>Email address<input type="email" name="email" autocomplete="email" value="<?= e($email) ?>" required></label><label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required><small>At least 8 characters.</small></label><button class="btn btn-primary btn-large btn-block" type="submit">Create buyer account →</button></form><p class="auth-bottom">Already registered? <a href="login.php">Log in</a></p></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
