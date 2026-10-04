<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Medicine Catalogue — MediTrade';
$q = trim((string)($_GET['q'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE is_active = 1 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$sql = "SELECT id, name, brand, category, pack_size, price, stock FROM products WHERE is_active = 1";
$params = [];
if ($q !== '') { $sql .= " AND (name LIKE :q OR brand LIKE :q OR product_code LIKE :q)"; $params['q'] = '%' . $q . '%'; }
if ($category !== '') { $sql .= " AND category = :category"; $params['category'] = $category; }
$sql .= " ORDER BY name ASC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $products = $stmt->fetchAll();
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">THE CATALOGUE</span><h1>Medicine & healthcare supplies</h1><p>Find a product, check pack information and build your order.</p></div></section>
<section class="section container">
  <form class="filter-bar" method="get">
    <label class="search-box"><span>⌕</span><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search product, brand or code..." aria-label="Search products"></label>
    <select name="category" aria-label="Filter by category"><option value="">All categories</option><?php foreach ($categories as $cat): ?><option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select>
    <button class="btn btn-primary" type="submit">Search</button>
    <?php if ($q !== '' || $category !== ''): ?><a class="btn btn-outline" href="catalog.php">Clear</a><?php endif; ?>
  </form>
  <div class="catalog-meta"><span><strong><?= count($products) ?></strong> products found</span><span>Prices shown for demonstration</span></div>
  <?php if (!$products): ?><div class="empty-state"><span>⌕</span><h2>No products found</h2><p>Try another search term or category.</p><a class="btn btn-primary" href="catalog.php">Clear filters</a></div>
  <?php else: ?><div class="product-grid">
    <?php foreach ($products as $p): ?>
      <article class="product-card">
        <div class="product-art art-<?= (int)$p['id'] % 4 ?>"><span class="product-symbol">✚</span><span class="product-category"><?= e($p['category']) ?></span></div>
        <div class="product-info"><div class="product-brand"><?= e($p['brand']) ?></div><h3><?= e($p['name']) ?></h3><p><?= e($p['pack_size']) ?> · SKU <?= (int)$p['id'] ?></p><div class="product-bottom"><strong><?= money($p['price']) ?></strong><span class="<?= $p['stock'] > 0 ? 'stock-good' : 'stock-out' ?>"><?= $p['stock'] > 0 ? (int)$p['stock'] . ' in stock' : 'Out of stock' ?></span></div><a class="btn btn-outline btn-block" href="product.php?id=<?= (int)$p['id'] ?>">View details</a></div>
      </article>
    <?php endforeach; ?>
  </div><?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
