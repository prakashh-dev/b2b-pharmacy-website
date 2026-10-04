<?php
require_once __DIR__ . '/config.php'; require_login();
if (current_user()['role'] !== 'buyer') { header('Location: admin.php'); exit; }
$cart = $_SESSION['cart'] ?? []; if (!$cart) { set_flash('Your cart is empty.', 'error'); header('Location: cart.php'); exit; }
$pageTitle = 'Checkout — MediTrade'; $error = '';
$ids = array_map('intval', array_keys($cart)); $marks = implode(',', array_fill(0, count($ids), '?'));
$st = $pdo->prepare("SELECT id,name,price,stock,is_active FROM products WHERE id IN ($marks)"); $st->execute($ids); $products = $st->fetchAll();
$total = 0; foreach ($products as $p) { if (!(int)$p['is_active'] || (int)$p['stock'] < (int)$cart[$p['id']]) $error = 'A product is no longer available in the requested quantity. Return to your cart and update it.'; $total += (float)$p['price'] * (int)$cart[$p['id']]; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $notes = trim((string)($_POST['notes'] ?? ''));
    if (mb_strlen($notes) > 500) $error = 'Notes must be 500 characters or fewer.';
    if (!$error) {
        try {
            $pdo->beginTransaction();
            $fresh = $pdo->prepare('SELECT id,name,price,stock,is_active FROM products WHERE id = ? FOR UPDATE');
            $verified = []; $grandTotal = 0;
            foreach ($cart as $id => $qty) {
                $fresh->execute([(int)$id]); $p = $fresh->fetch();
                if (!$p || !(int)$p['is_active'] || (int)$p['stock'] < (int)$qty) throw new RuntimeException('Stock changed. Please review your cart.');
                $p['qty'] = (int)$qty; $p['line_total'] = (float)$p['price'] * (int)$qty; $grandTotal += $p['line_total']; $verified[] = $p;
            }
            $orderStmt = $pdo->prepare("INSERT INTO orders (user_id,total_amount,status,notes) VALUES (?,?, 'Pending', ?)");
            $orderStmt->execute([current_user()['id'], $grandTotal, $notes ?: null]); $orderId = (int)$pdo->lastInsertId();
            $lineStmt = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity,line_total) VALUES (?,?,?,?,?,?)');
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
            foreach ($verified as $p) {
                $lineStmt->execute([$orderId,$p['id'],$p['name'],$p['price'],$p['qty'],$p['line_total']]);
                $stockStmt->execute([$p['qty'],$p['id']]);
            }
            $pdo->commit(); $_SESSION['cart'] = []; set_flash('Your demo order has been placed.'); header('Location: order.php?id=' . $orderId); exit;
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $ex instanceof RuntimeException ? $ex->getMessage() : 'Could not place the order. Please try again.';
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">FINAL STEP</span><h1>Review your order</h1><p>Submit a demo order request. No payment will be taken.</p></div></section>
<section class="section container checkout-layout"><div class="auth-card checkout-card"><h2>Order details</h2><?php if ($error): ?><div class="form-errors"><p><?= e($error) ?></p></div><?php endif; ?><form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Buyer / business<input value="<?= e(current_user()['business_name'] ?? current_user()['name']) ?>" disabled></label><label>Order notes (optional)<textarea name="notes" rows="4" maxlength="500" placeholder="Delivery preferences or order notes..."><?= e((string)($_POST['notes'] ?? '')) ?></textarea></label><button class="btn btn-primary btn-large btn-block" type="submit" <?= $error ? 'disabled' : '' ?>>Place demo order →</button></form><p class="muted small-text">Order placement reduces demo inventory. This is not a real purchase.</p></div><aside class="order-summary"><span class="eyebrow">SUMMARY</span><h2>Items</h2><?php foreach ($products as $p): ?><div class="summary-row"><span><?= e($p['name']) ?> × <?= (int)$cart[$p['id']] ?></span><strong><?= money((float)$p['price'] * (int)$cart[$p['id']]) ?></strong></div><?php endforeach; ?><div class="summary-total"><span>Total</span><strong><?= money($total) ?></strong></div><a class="text-link" href="cart.php">← Back to cart</a></aside></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
