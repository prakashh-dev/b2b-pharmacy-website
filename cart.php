<?php
require_once __DIR__ . '/config.php'; require_login();
if (current_user()['role'] !== 'buyer') { header('Location: admin.php'); exit; }
$pageTitle = 'Your Cart — MediTrade';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $action = (string)($_POST['action'] ?? '');
    if ($action === 'remove') { $id = (int)($_POST['product_id'] ?? 0); unset($_SESSION['cart'][$id]); set_flash('Item removed from cart.'); header('Location: cart.php'); exit; }
    if ($action === 'update') {
        foreach (($_POST['qty'] ?? []) as $id => $qty) {
            $id = (int)$id; $qty = filter_var($qty, FILTER_VALIDATE_INT);
            if (!$qty || $qty < 1) { unset($_SESSION['cart'][$id]); continue; }
            $st = $pdo->prepare('SELECT stock FROM products WHERE id = ? AND is_active = 1'); $st->execute([$id]); $stock = $st->fetchColumn();
            if ($stock !== false) $_SESSION['cart'][$id] = min((int)$stock, min(500, (int)$qty)); else unset($_SESSION['cart'][$id]);
        }
        set_flash('Cart updated.'); header('Location: cart.php'); exit;
    }
    if ($action === 'checkout') { header('Location: checkout.php'); exit; }
}
$cart = $_SESSION['cart'] ?? []; $items = []; $total = 0;
if ($cart) {
    $ids = array_map('intval', array_keys($cart)); $marks = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare("SELECT id,name,brand,pack_size,price,stock,is_active FROM products WHERE id IN ($marks)"); $st->execute($ids);
    foreach ($st->fetchAll() as $p) {
        if (!(int)$p['is_active'] || (int)$p['stock'] < 1) { unset($_SESSION['cart'][$p['id']]); continue; }
        $qty = min((int)$cart[$p['id']], (int)$p['stock']); $_SESSION['cart'][$p['id']] = $qty;
        $p['qty'] = $qty; $p['line_total'] = $qty * (float)$p['price']; $total += $p['line_total']; $items[] = $p;
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">YOUR ORDER</span><h1>Shopping cart</h1><p>Review quantities before you submit your demo order.</p></div></section>
<section class="section container">
<?php if (!$items): ?><div class="empty-state"><span>▱</span><h2>Your cart is waiting</h2><p>Explore the catalogue and add products to get started.</p><a class="btn btn-primary" href="catalog.php">Browse medicines</a></div>
<?php else: ?><div class="cart-layout"><div class="cart-items"><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><?php foreach ($items as $item): ?><article class="cart-item"><div class="cart-product-icon">✚</div><div class="cart-product-info"><strong><?= e($item['name']) ?></strong><span><?= e($item['brand']) ?> · <?= e($item['pack_size']) ?></span><small><?= money($item['price']) ?> each · <?= (int)$item['stock'] ?> available</small></div><label class="qty-label">Qty<input type="number" name="qty[<?= (int)$item['id'] ?>]" min="1" max="<?= (int)$item['stock'] ?>" value="<?= (int)$item['qty'] ?>"></label><strong class="cart-line-total"><?= money($item['line_total']) ?></strong><button class="remove-btn" type="submit" name="action" value="remove" formaction="cart.php" onclick="this.form.product_id.value='<?= (int)$item['id'] ?>'">Remove</button><input type="hidden" name="product_id" value=""></article><?php endforeach; ?><button class="btn btn-outline" name="action" value="update">Update quantities</button></form></div><aside class="order-summary"><span class="eyebrow">ORDER SUMMARY</span><h2>Summary</h2><div class="summary-row"><span>Items</span><strong><?= count($items) ?></strong></div><div class="summary-row"><span>Subtotal</span><strong><?= money($total) ?></strong></div><div class="summary-row"><span>Shipping</span><strong>Not included</strong></div><div class="summary-total"><span>Estimated total</span><strong><?= money($total) ?></strong></div><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn btn-primary btn-large btn-block" name="action" value="checkout">Continue to checkout →</button></form><p class="muted small-text">Demo checkout only. No payment is collected.</p></aside></div><?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
