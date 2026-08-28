<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        order_id,
        order_date,
        status,
        total_amount,
        shipping_address
    FROM orders
    WHERE customer_id = ?
    ORDER BY order_date DESC
");

$stmt->execute([$_SESSION['customer_id']]);
$orders = $stmt->fetchAll();

$videoAuthPage = true;
$pageTitle = 'My Orders | MoGraphics';
include 'includes/header.php';
?>

<section class="video-bg-page orders-video-page">
    <video class="page-bg-video" autoplay muted loop playsinline>
        <source src="assets/video/hero.mp4" type="video/mp4">
    </video>
    <div class="page-bg-overlay orders-overlay"></div>

    <div class="orders-video-content">
    <div class="account-heading">
        <div>
            <p class="eyebrow">MY ACCOUNT</p>
            <h1>Hello, <?= e($_SESSION['customer_name'] ?? 'Customer') ?></h1>
        </div>

        <a class="pill" href="logout.php">Log out</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="form-message success">
            Your order has been placed successfully.
        </div>
    <?php endif; ?>

    <?php if (!$orders): ?>
        <div class="empty-cart">
            <h2>No orders yet.</h2>
            <p>Your completed orders will appear here.</p>
            <a class="pill lime" href="shop.php">Start shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Shipping</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['order_id'] ?></td>
                            <td><?= e($order['order_date']) ?></td>
                            <td><?= e($order['status']) ?></td>
                            <td><?= e($order['shipping_address']) ?></td>
                            <td>R <?= number_format((float)$order['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
