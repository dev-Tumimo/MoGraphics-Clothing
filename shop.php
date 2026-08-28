<?php
$pageTitle = 'Shop | MoGraphics';
$bodyClass = 'shop-page';
include 'includes/header.php';

$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = trim($_GET['q'] ?? '');

$sql = "
    SELECT
        p.product_id,
        p.name,
        p.description,
        p.image,
        c.category_name,
        MIN(pv.price) AS price
    FROM products p
    JOIN categories c ON c.category_id = p.category_id
    LEFT JOIN product_variants pv ON pv.product_id = p.product_id
    WHERE p.is_active = 1
";

$params = [];

if ($category > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category;
}

if ($search !== '') {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " GROUP BY p.product_id ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("
    SELECT category_id, category_name
    FROM categories
    WHERE is_active = 1
    ORDER BY category_name
")->fetchAll();
?>

<section class="shop-hero">
    <p class="eyebrow">MOGRAPHICS COLLECTION</p>
    <h1>SHOP</h1>
</section>

<section class="shop-page">
    <form method="get" class="shop-filters">
        <input
            type="search"
            name="q"
            placeholder="Search products..."
            value="<?= e($search) ?>"
        >

        <select name="category">
            <option value="0">All categories</option>

            <?php foreach ($categories as $c): ?>
                <option
                    value="<?= $c['category_id'] ?>"
                    <?= $category === (int)$c['category_id'] ? 'selected' : '' ?>
                >
                    <?= e($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="pill lime" type="submit">Filter</button>
    </form>

    <div class="shop-product-grid">
        <?php foreach ($products as $product): ?>
            <a class="shop-product-card" href="product.php?id=<?= (int)$product['product_id'] ?>">
                <div class="shop-product-image">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
                    <?php else: ?>
                        <span>MoGraphics</span>
                    <?php endif; ?>
                </div>

                <div class="shop-product-info">
                    <div>
                        <small><?= e($product['category_name']) ?></small>
                        <h3><?= e($product['name']) ?></h3>
                    </div>

                    <strong>
                        R <?= number_format((float)$product['price'], 2) ?>
                    </strong>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
