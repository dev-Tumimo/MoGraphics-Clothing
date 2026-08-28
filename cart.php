<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'cart.php';
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        ci.cart_item_id,
        ci.quantity,
        pv.variant_id,
        pv.size,
        pv.colour,
        pv.price,
        pv.stock_quantity,
        p.product_id,
        p.name,
        p.image
    FROM carts c
    JOIN cart_items ci ON ci.cart_id = c.cart_id
    JOIN product_variants pv ON pv.variant_id = ci.variant_id
    JOIN products p ON p.product_id = pv.product_id
    WHERE c.customer_id = ?
    ORDER BY ci.cart_item_id DESC
");

$stmt->execute([$_SESSION['customer_id']]);
$items = $stmt->fetchAll();

$total = 0;

$pageTitle = 'Cart | MoGraphics';
include 'includes/header.php';
?>

<section class="video-bg-page cart-video-page">
    <video class="page-bg-video" autoplay muted loop playsinline>
        <source src="assets/video/hero.mp4" type="video/mp4">
    </video>
    <div class="page-bg-overlay cart-overlay"></div>

    <div class="video-page-content cart-video-content">

    <div class="cart-title-row">
        <div>
            <p class="eyebrow">YOUR SELECTION</p>
            <h1>Shopping cart</h1>
        </div>

        <a href="shop.php">Continue shopping ↗</a>
    </div>

    <?php if (!$items): ?>
        <div class="empty-cart">
            <h2>Your cart is empty.</h2>
            <p>Add a few MoGraphics pieces and come back here.</p>
            <a class="pill lime" href="shop.php">Start shopping</a>
        </div>
    <?php else: ?>
        <form action="cart_update.php" method="post">
            <div class="cart-items">
                <?php foreach ($items as $item): ?>
                    <?php
                    $subtotal = (float)$item['price'] * (int)$item['quantity'];
                    $total += $subtotal;
                    ?>

                    <div class="cart-item">
                        <a href="product.php?id=<?= $item['product_id'] ?>" class="cart-item-photo">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                            <?php else: ?>
                                <span>MoGraphics</span>
                            <?php endif; ?>
                        </a>

                        <div class="cart-item-copy">
                            <h3><?= e($item['name']) ?></h3>
                            <p><?= e($item['size']) ?> / <?= e($item['colour']) ?></p>
                            <p>R <?= number_format((float)$item['price'], 2) ?></p>
                        </div>

                        <div class="cart-quantity">
                            <label>Qty</label>
                            <input
                                type="number"
                                min="0"
                                max="<?= (int)$item['stock_quantity'] ?>"
                                name="qty[<?= $item['cart_item_id'] ?>]"
                                value="<?= (int)$item['quantity'] ?>"
                            >
                            <small>Set to 0 to remove</small>
                        </div>

                        <strong class="cart-subtotal">
                            R <?= number_format($subtotal, 2) ?>
                        </strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-bottom">
                <button class="pill" type="submit">Update cart</button>

                <div class="cart-total-box">
                    <span>Total</span>
                    <strong>R <?= number_format($total, 2) ?></strong>
                    <a class="pill lime" href="checkout.php">Checkout</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
