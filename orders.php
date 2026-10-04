<?php
require_once __DIR__ . '/config.php'; require_login();
if (current_user()['role'] === 'admin') { header('Location: admin.php?tab=orders'); exit; }
$pageTitle = 'My Orders — MediTrade';
$st = $pdo->prepare('SELECT id,total_amount,status,created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC'); $st->execute([current_user()['id']]); $orders = $st->fetchAll();
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">BUYER SPACE</span><h1>My orders</h1><p>Track your submitted demo orders and review their current status.</p></div></section>
<section class="section container"><?php if (!$orders): ?><div class="empty-state"><span>▤</span><h2>No orders yet</h2><p>Your submitted orders will appear here.</p><a class="btn btn-primary" href="catalog.php">Explore catalogue</a></div><?php else: ?><div class="table-card"><div class="table-scroll"><table><thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody><?php foreach ($orders as $o): ?><tr><td><strong>#MT-<?= (int)$o['id'] ?></strong></td><td><?= e(date('d M Y, g:i A', strtotime($o['created_at']))) ?></td><td><?= money($o['total_amount']) ?></td><td><span class="status status-<?= strtolower(e($o['status'])) ?>"><?= e($o['status']) ?></span></td><td><a class="text-link" href="order.php?id=<?= (int)$o['id'] ?>">View →</a></td></tr><?php endforeach; ?></tbody></table></div></div><?php endif; ?></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
