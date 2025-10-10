<?php
include 'config.php';
include 'session.php';
redirectIfNotLoggedIn();

if ($_SESSION['roleId'] != 2) {
    echo "Unauthorized";
    exit();
}

if (isset($_POST['id'])) {
    $productId = intval($_POST['id']);

    // Get farmerId from users table
    $userId = $_SESSION['id'];
    $farmerQuery = mysqli_prepare($conn, "SELECT id FROM farmers WHERE userId = ?");
    mysqli_stmt_bind_param($farmerQuery, "i", $userId);
    mysqli_stmt_execute($farmerQuery);
    $farmerResult = mysqli_stmt_get_result($farmerQuery);

    if ($farmerResult && mysqli_num_rows($farmerResult) > 0) {
        $farmerRow = mysqli_fetch_assoc($farmerResult);
        $farmerId = $farmerRow['id'];

        // Delete product
        $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ? AND farmerId = ?");
        mysqli_stmt_bind_param($stmt, "ii", $productId, $farmerId);

        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "DB error: " . mysqli_error($conn);
        }
    } else {
        echo "Farmer not found.";
    }
} else {
    echo "No product ID provided.";
}
?>
