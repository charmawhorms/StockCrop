<?php
session_start();
include 'config.php';

// Input validation
if (!isset($_POST['productId'], $_POST['quantity'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$productId = intval($_POST['productId']);
$quantity = intval($_POST['quantity']);
if ($quantity <= 0) $quantity = 1;

// Logged in user
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    // Check if product already exists in cart
    $stmt = mysqli_prepare($conn, "SELECT quantity FROM cart WHERE userId = ? AND productId = ?");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Update quantity
        $newQty = $row['quantity'] + $quantity;
        $updateStmt = mysqli_prepare($conn, "UPDATE cart SET quantity = ? WHERE userId = ? AND productId = ?");
        mysqli_stmt_bind_param($updateStmt, "iii", $newQty, $userId, $productId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    } else {
        // Insert new item
        $insertStmt = mysqli_prepare($conn, "INSERT INTO cart (userId, productId, quantity) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($insertStmt, "iii", $userId, $productId, $quantity);
        mysqli_stmt_execute($insertStmt);
        mysqli_stmt_close($insertStmt);
    }
    mysqli_stmt_close($stmt);

    // Get updated cart count
    $countQuery = mysqli_prepare($conn, "SELECT SUM(quantity) AS total FROM cart WHERE userId = ?");
    mysqli_stmt_bind_param($countQuery, "i", $userId);
    mysqli_stmt_execute($countQuery);
    $countResult = mysqli_stmt_get_result($countQuery);
    $newCount = intval(mysqli_fetch_assoc($countResult)['total'] ?? 0);
    mysqli_stmt_close($countQuery);
} else {
    // GUEST user - store cart in session
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    $newCount = array_sum($_SESSION['cart']);
}

echo json_encode([
    'status' => 'success',
    'message' => 'Product added to cart!',
    'newCount' => $newCount
]);
