<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$productId = (int)($_POST['product_id'] ?? 0);
$variantId = (int)($_POST['variant_id'] ?? 0);
$quantity  = max(1, (int)($_POST['quantity'] ?? 1));

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = "product.php?id=$productId";
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT variant_id, stock_quantity
    FROM product_variants
    WHERE variant_id = ?
    LIMIT 1
");

$stmt->execute([$variantId]);
$variant = $stmt->fetch();

if (!$variant || (int)$variant['stock_quantity'] <= 0) {
    header("Location: product.php?id=$productId");
    exit;
}

$quantity = min($quantity, (int)$variant['stock_quantity']);

$cartId = getOrCreateCart($pdo, (int)$_SESSION['customer_id']);

$stmt = $pdo->prepare("
    SELECT cart_item_id, quantity
    FROM cart_items
    WHERE cart_id = ? AND variant_id = ?
    LIMIT 1
");

$stmt->execute([$cartId, $variantId]);
$existing = $stmt->fetch();

if ($existing) {
    $newQuantity = min(
        (int)$variant['stock_quantity'],
        (int)$existing['quantity'] + $quantity
    );

    $stmt = $pdo->prepare("
        UPDATE cart_items
        SET quantity = ?
        WHERE cart_item_id = ?
    ");

    $stmt->execute([$newQuantity, $existing['cart_item_id']]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO cart_items (cart_id, variant_id, quantity)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([$cartId, $variantId, $quantity]);
}

header('Location: cart.php');
exit;
?>