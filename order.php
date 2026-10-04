<?php
require_once __DIR__ . '/config.php'; require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (current_user()['role'] === 'admin') { $st = $pdo->prepare('SELECT o.*,u.name AS buyer_name,u.business_name,u.email FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id=?'); $st->execute([$id ?: 0]); }
else { $st = $pdo->prepare('SELECT o.*,u.name AS buyer_name,u.business_name,u.email FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id=? AND o.user_id=?'); $st->execute([$id ?: 0,current_user()['id']]); }
$order = $st->fetch();
if (!$order) { http_response_code(404); $pageTitle = 'Order not found'; require __DIR__ . '/partials/header.php'; echo '<section class="container section"><div class="empty-state"><h1>Order not found</h1><a class="btn btn-primary" href="orders.php">Back to orders</a></div></section>'; require __DIR__ . '/partials/footer.php'; exit; }
$st = $pdo->prepare('SELECT product_name,unit_price,quantity,line_total FROM order_items WHERE order_id=?'); $st->execute([$order['id']]); $items = $st->fetchAll();
$pageTitle = 'Order #MT-' . $order['id'] . ' — MediTrade';
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">ORDER CONFIRMATION</span><h1>Order #MT-<?= (int)$order['id'] ?></h1><p>Submitted <?= e(date('d M Y, g:i A', strtotime($order['created_at']))) ?></p></div></section>
<section class="section container"><div class="order-detail-top"><div><span class="muted">Order status</span><p><span class="status status-<?= strtolower(e($order['status'])) ?>"><?= e($order['status']) ?></span></p></div><div><span class="muted">Buyer</span><p><strong><?= e($order['business_name'] ?: $order['buyer_name']) ?></strong></p></div><div><span class="muted">Order total</span><p class="detail-price"><?= money($order['total_amount']) ?></p></div></div>
<div class="table-card"><div class="table-scroll"><table><thead><tr><th>Product</th><th>Unit price</th><th>Quantity</th><th>Total</th></tr></thead><tbody><?php foreach ($items as $item): ?><tr><td><?= e($item['product_name']) ?></td><td><?= money($item['unit_price']) ?></td><td><?= (int)$item['quantity'] ?></td><td><?= money($item['line_total']) ?></td></tr><?php endforeach; ?></tbody><tfoot><tr><th colspan="3">Total</th><th><?= money($order['total_amount']) ?></th></tr></tfoot></table></div></div>
<?php if ($order['notes']): ?><div class="notes-box"><strong>Order notes</strong><p><?= nl2br(e($order['notes'])) ?></p></div><?php endif; ?><div class="page-actions"><a class="btn btn-outline" href="orders.php">← My orders</a><button class="btn btn-primary" type="button" onclick="window.print()">Print order</button></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
