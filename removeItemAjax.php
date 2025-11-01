<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_POST['productId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit;
}

$productId = intval($_POST['productId']);

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    // Get user's cart ID
    $cartQuery = mysqli_prepare($conn, "SELECT id FROM cart WHERE userId = ?");
    mysqli_stmt_bind_param($cartQuery, "i", $userId);
    mysqli_stmt_execute($cartQuery);
    $cartResult = mysqli_stmt_get_result($cartQuery);

    if ($cartRow = mysqli_fetch_assoc($cartResult)) {
        $cartId = $cartRow['id'];

        // Delete item from cartItems
        $stmt = mysqli_prepare($conn, "DELETE FROM cartItems WHERE cartId = ? AND productId = ?");
        mysqli_stmt_bind_param($stmt, "ii", $cartId, $productId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Get updated count
        $countStmt = mysqli_prepare($conn, "SELECT SUM(quantity) as total FROM cartItems WHERE cartId = ?");
        mysqli_stmt_bind_param($countStmt, "i", $cartId);
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $newCount = intval(mysqli_fetch_assoc($countResult)['total'] ?? 0);
        mysqli_stmt_close($countStmt);
    } else {
        $newCount = 0;
    }

} else {
    // Guest user
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
    $newCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Item removed from cart',
    'newCount' => $newCount
]);
?>