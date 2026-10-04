<?php
require_once __DIR__ . '/config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$id ?: 0]); $product = $stmt->fetch();
if (!$product) { http_response_code(404); $pageTitle = 'Product not found'; require __DIR__ . '/partials/header.php'; echo '<section class="container section"><div class="empty-state"><h1>Product not found</h1><a class="btn btn-primary" href="catalog.php">Back to catalogue</a></div></section>'; require __DIR__ . '/partials/footer.php'; exit; }
$pageTitle = $product['name'] . ' — MediTrade';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); require_login();
    if (current_user()['role'] !== 'buyer') { set_flash('Buyer accounts are required to add products to cart.', 'error'); header('Location: product.php?id=' . $id); exit; }
    $qty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $qty = max(1, min(500, (int)$qty));
    if ((int)$product['stock'] < $qty) { set_flash('Requested quantity is higher than available stock.', 'error'); }
    else { $_SESSION['cart'] = $_SESSION['cart'] ?? []; $_SESSION['cart'][$id] = min(500, (int)($_SESSION['cart'][$id] ?? 0) + $qty); set_flash('Product added to your cart.'); }
    header('Location: product.php?id=' . $id); exit;
}
require __DIR__ . '/partials/header.php';
?>
<section class="section container">
  <a class="back-link" href="catalog.php">← Back to catalogue</a>
  <div class="product-detail">
    <div class="product-detail-art art-<?= (int)$product['id'] % 4 ?>"><span class="detail-plus">✚</span><span class="detail-label">MEDICAL<br>SUPPLIES</span></div>
    <div class="product-detail-copy"><span class="eyebrow"><?= e($product['category']) ?></span><h1><?= e($product['name']) ?></h1><p class="detail-brand"><?= e($product['brand']) ?></p><p><?= e($product['description']) ?></p><div class="detail-price"><?= money($product['price']) ?> <small>/ <?= e($product['pack_size']) ?></small></div><div class="detail-stock <?= $product['stock'] > 0 ? 'stock-good' : 'stock-out' ?>"><?= $product['stock'] > 0 ? (int)$product['stock'] . ' units available' : 'Currently out of stock' ?></div><div class="detail-facts"><div><small>Product code</small><strong><?= e($product['product_code']) ?></strong></div><div><small>Pack size</small><strong><?= e($product['pack_size']) ?></strong></div></div>
    <?php if ($product['stock'] > 0): ?>
      <?php if (current_user() && current_user()['role'] === 'buyer'): ?>
        <form method="post" class="add-cart-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="1" max="<?= (int)$product['stock'] ?>" value="1" required><button class="btn btn-primary btn-large" type="submit">Add to cart →</button></form>
      <?php elseif (!current_user()): ?><a class="btn btn-primary btn-large" href="login.php">Log in to order →</a>
      <?php else: ?><p class="muted">Admin accounts manage products and stock.</p><?php endif; ?>
    <?php endif; ?>
    <p class="demo-note">Portfolio demo product. Verify all product, regulatory and supply information before any real-world use.</p>
    </div>
  </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
