<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'MediTrade — B2B Pharmacy Wholesale';
$featured = $pdo->query("SELECT id, name, brand, category, pack_size, price, stock FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4")->fetchAll();
require __DIR__ . '/partials/header.php';
?>
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="eyebrow"><span class="eyebrow-dot"></span> A smarter wholesale workflow</span>
      <h1>Better supply.<br><span>Better care.</span></h1>
      <p class="hero-lead">A clearer way for pharmacy teams to discover products, manage bulk orders and keep everyday essentials moving.</p>
      <div class="hero-actions"><a class="btn btn-primary btn-large" href="catalog.php">Explore medicines <span>→</span></a><a class="btn btn-outline btn-large" href="register.php">Join as a buyer</a></div>
      <div class="trust-row"><span><b>✓</b> Organised catalogue</span><span><b>✓</b> Stock visibility</span><span><b>✓</b> Order tracking</span></div>
    </div>
    <div class="hero-visual">
      <div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div>
      <div class="hero-cross">✚</div>
      <div class="floating-card floating-card-top"><span class="mini-icon">✓</span><span><strong>Order placed</strong><small>Order workflow demo</small></span><span class="status-pill">Received</span></div>
      <div class="medicine-art"><div class="bottle-cap"></div><div class="bottle"><div class="bottle-label"><span class="label-cross">✚</span><b>MEDI</b><small>DAILY CARE</small><i></i><i></i></div></div><div class="capsule"><span></span></div></div>
      <div class="floating-card floating-card-bottom"><span class="stock-icon">▦</span><span><strong>Inventory overview</strong><small>Product & stock information</small></span><span class="tiny-bars"><i></i><i></i><i></i><i></i><i></i></span></div>
      <div class="visual-caption">A simpler supply experience</div>
    </div>
  </div>
</section>
<section class="container metric-strip">
  <div><strong>01</strong><span>Find products</span></div><div><strong>02</strong><span>Build your order</span></div><div><strong>03</strong><span>Track fulfilment</span></div><div class="metric-note">One organised workflow for your team.</div>
</section>
<section class="section container">
  <div class="section-heading"><div><span class="eyebrow">PRODUCT CATALOGUE</span><h2>Everyday essentials,<br>easier to find.</h2></div><a class="text-link" href="catalog.php">View all products <span>→</span></a></div>
  <div class="product-grid">
    <?php foreach ($featured as $p): ?>
      <article class="product-card">
        <div class="product-art art-<?= (int)$p['id'] % 4 ?>"><span class="product-symbol">✚</span><span class="product-category"><?= e($p['category']) ?></span></div>
        <div class="product-info"><div class="product-brand"><?= e($p['brand']) ?></div><h3><?= e($p['name']) ?></h3><p><?= e($p['pack_size']) ?></p><div class="product-bottom"><strong><?= money($p['price']) ?></strong><span class="<?= $p['stock'] > 0 ? 'stock-good' : 'stock-out' ?>"><?= $p['stock'] > 0 ? 'In stock' : 'Out of stock' ?></span></div><a class="btn btn-outline btn-block" href="product.php?id=<?= (int)$p['id'] ?>">View product</a></div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<section class="cta-section"><div class="container cta-inner"><div><span class="eyebrow eyebrow-light">FOR PHARMACY BUYERS</span><h2>Make your next order<br>feel more organised.</h2><p>Create a buyer account to browse the catalogue and place demo orders.</p></div><a class="btn btn-light btn-large" href="register.php">Create buyer account <span>→</span></a></div></section>
<section class="section container benefit-grid">
  <div><span class="benefit-icon">⌕</span><h3>Search with clarity</h3><p>Browse by product name, brand or category using a straightforward catalogue.</p></div>
  <div><span class="benefit-icon">▤</span><h3>Know your stock</h3><p>See available stock and pack information before adding products to a cart.</p></div>
  <div><span class="benefit-icon">↗</span><h3>Keep orders visible</h3><p>Review order history and see status updates in one place.</p></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
