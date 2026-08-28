<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        ci.quantity,
        pv.variant_id,
        pv.size,
        pv.colour,
        pv.price,
        pv.stock_quantity,
        p.name
    FROM carts c
    JOIN cart_items ci ON ci.cart_id = c.cart_id
    JOIN product_variants pv ON pv.variant_id = ci.variant_id
    JOIN products p ON p.product_id = pv.product_id
    WHERE c.customer_id = ?
");

$stmt->execute([$_SESSION['customer_id']]);
$items = $stmt->fetchAll();

if (!$items) {
    header('Location: cart.php');
    exit;
}

$total = 0;
foreach ($items as $item) {
    $total += (float)$item['price'] * (int)$item['quantity'];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'Bank Transfer';

    if ($shippingAddress === '') {
        $error = 'Please enter your shipping address.';
    } else {
        try {
            $pdo->beginTransaction();

            foreach ($items as $item) {
                if ((int)$item['quantity'] > (int)$item['stock_quantity']) {
                    throw new Exception('One or more products no longer have enough stock.');
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO orders (
                    customer_id,
                    status,
                    total_amount,
                    shipping_address
                )
                VALUES (?, 'Pending', ?, ?)
            ");

            $stmt->execute([
                $_SESSION['customer_id'],
                $total,
                $shippingAddress
            ]);

            $orderId = (int)$pdo->lastInsertId();

            $insertItem = $pdo->prepare("
                INSERT INTO order_items (
                    order_id,
                    variant_id,
                    quantity,
                    unit_price
                )
                VALUES (?, ?, ?, ?)
            ");

            $updateStock = $pdo->prepare("
                UPDATE product_variants
                SET stock_quantity = stock_quantity - ?
                WHERE variant_id = ?
                  AND stock_quantity >= ?
            ");

            foreach ($items as $item) {
                $insertItem->execute([
                    $orderId,
                    $item['variant_id'],
                    $item['quantity'],
                    $item['price']
                ]);

                $updateStock->execute([
                    $item['quantity'],
                    $item['variant_id'],
                    $item['quantity']
                ]);

                if ($updateStock->rowCount() !== 1) {
                    throw new Exception('Stock changed while checking out.');
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO payments (
                    order_id,
                    amount,
                    payment_method,
                    payment_status
                )
                VALUES (?, ?, ?, 'Pending')
            ");

            $stmt->execute([
                $orderId,
                $total,
                $paymentMethod
            ]);

            $cartId = getOrCreateCart($pdo, (int)$_SESSION['customer_id']);

            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $stmt->execute([$cartId]);

            $pdo->commit();

            header("Location: orders.php?success=1");
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = $e->getMessage();
        }
    }
}

$pageTitle = 'Checkout | MoGraphics';
include 'includes/header.php';
?>

<section class="checkout-page">
    <div class="checkout-form-card">
        <p class="eyebrow">CHECKOUT</p>
        <h1>Complete your order</h1>

        <?php if ($error): ?>
            <div class="form-message error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="stack-form">
            <div>
                <label>Shipping address</label>
                <textarea name="shipping_address" rows="5" required></textarea>
            </div>

            <div>
                <label>Payment method</label>
                <select name="payment_method">
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                </select>
            </div>

            <button class="pill lime form-button" type="submit">
                Place order
            </button>
        </form>
    </div>

    <aside class="checkout-summary">
        <h2>Order summary</h2>

        <?php foreach ($items as $item): ?>
            <div class="summary-row">
                <span>
                    <?= e($item['name']) ?>
                    (<?= e($item['size']) ?> / <?= e($item['colour']) ?>)
                    × <?= (int)$item['quantity'] ?>
                </span>

                <strong>
                    R <?= number_format((float)$item['price'] * (int)$item['quantity'], 2) ?>
                </strong>
            </div>
        <?php endforeach; ?>

        <div class="summary-total">
            <span>Total</span>
            <strong>R <?= number_format($total, 2) ?></strong>
        </div>
    </aside>
</section>

<?php include 'includes/footer.php'; ?>
