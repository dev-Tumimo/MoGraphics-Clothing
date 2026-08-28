<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$quantities = $_POST['qty'] ?? [];

foreach ($quantities as $cartItemId => $quantity) {
    $cartItemId = (int)$cartItemId;
    $quantity = (int)$quantity;

    if ($quantity <= 0) {
        $stmt = $pdo->prepare("
            DELETE ci
            FROM cart_items ci
            JOIN carts c ON c.cart_id = ci.cart_id
            WHERE ci.cart_item_id = ?
              AND c.customer_id = ?
        ");

        $stmt->execute([$cartItemId, $_SESSION['customer_id']]);
    } else {
        $stmt = $pdo->prepare("
            SELECT pv.stock_quantity
            FROM cart_items ci
            JOIN carts c ON c.cart_id = ci.cart_id
            JOIN product_variants pv ON pv.variant_id = ci.variant_id
            WHERE ci.cart_item_id = ?
              AND c.customer_id = ?
        ");

        $stmt->execute([$cartItemId, $_SESSION['customer_id']]);
        $stock = (int)$stmt->fetchColumn();

        $quantity = min($quantity, $stock);

        $stmt = $pdo->prepare("
            UPDATE cart_items ci
            JOIN carts c ON c.cart_id = ci.cart_id
            SET ci.quantity = ?
            WHERE ci.cart_item_id = ?
              AND c.customer_id = ?
        ");

        $stmt->execute([
            $quantity,
            $cartItemId,
            $_SESSION['customer_id']
        ]);
    }
}

header('Location: cart.php');
exit;
?>