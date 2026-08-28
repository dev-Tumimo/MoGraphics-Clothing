<?php
$pageTitle = 'Product | MoGraphics';
include 'includes/header.php';

$productId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT
        p.product_id,
        p.name,
        p.description,
        p.image,
        c.category_name
    FROM products p
    JOIN categories c ON c.category_id = p.category_id
    WHERE p.product_id = ?
      AND p.is_active = 1
    LIMIT 1
");

$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    ?>
    <section class="generic-page">
        <h1>Product not found</h1>
        <a class="pill lime" href="shop.php">Back to shop</a>
    </section>
    <?php
    include 'includes/footer.php';
    exit;
}

$vstmt = $pdo->prepare("
    SELECT
        variant_id,
        size,
        colour,
        price,
        stock_quantity,
        sku
    FROM product_variants
    WHERE product_id = ?
    ORDER BY price ASC, size ASC
");

$vstmt->execute([$productId]);
$variants = $vstmt->fetchAll();

$minPrice = null;
foreach ($variants as $variant) {
    if ($minPrice === null || $variant['price'] < $minPrice) {
        $minPrice = $variant['price'];
    }
}
?>

<section class="product-detail-page">
    <div class="detail-photo">
        <?php if (!empty($product['image'])): ?>
            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
        <?php else: ?>
            <span>MoGraphics</span>
        <?php endif; ?>
    </div>

    <div class="detail-copy">
        <p class="eyebrow"><?= e($product['category_name']) ?></p>

        <h1><?= e($product['name']) ?></h1>

        <?php if ($minPrice !== null): ?>
            <div class="detail-price">
                R <?= number_format((float)$minPrice, 2) ?>
            </div>
        <?php endif; ?>

        <p class="detail-description">
            <?= nl2br(e($product['description'])) ?>
        </p>

        <?php if ($variants): ?>
            <form action="cart_add.php" method="post" class="add-cart-form">
                <input type="hidden" name="product_id" value="<?= $productId ?>">

                <div>
                    <label for="variant_id">Choose size / colour</label>

                    <select name="variant_id" id="variant_id" required>
                        <?php foreach ($variants as $variant): ?>
                            <option
                                value="<?= $variant['variant_id'] ?>"
                                <?= $variant['stock_quantity'] <= 0 ? 'disabled' : '' ?>
                            >
                                <?= e($variant['size']) ?>
                                /
                                <?= e($variant['colour']) ?>
                                —
                                R <?= number_format((float)$variant['price'], 2) ?>
                                —
                                <?= (int)$variant['stock_quantity'] ?> in stock
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="quantity">Quantity</label>
                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        min="1"
                        value="1"
                        required
                    >
                </div>

                <button class="pill lime detail-add-button" type="submit">
                    <i class="bi bi-bag-plus"></i>
                    Add to cart
                </button>
            </form>
        <?php else: ?>
            <div class="form-message error">
                This product does not have any variants yet.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
