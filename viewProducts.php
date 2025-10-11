<?php
include 'session.php';
include 'config.php';

// Only allow farmers
redirectIfNotLoggedIn();
if ($_SESSION['roleId'] != 2) {
    header("Location: index.php");
    exit();
}

// Get farmerId
$userId = $_SESSION['id'];
$farmerQuery = mysqli_prepare($conn, "SELECT id FROM farmers WHERE userId = ?");
mysqli_stmt_bind_param($farmerQuery, "i", $userId);
mysqli_stmt_execute($farmerQuery);
$farmerResult = mysqli_stmt_get_result($farmerQuery);

if ($farmerResult && mysqli_num_rows($farmerResult) > 0) {
    $farmerRow = mysqli_fetch_assoc($farmerResult);
    $farmerId = $farmerRow['id'];
} else {
    die("Error: Farmer record not found.");
}

// Fetch products
$stmt = mysqli_prepare($conn, "
    SELECT p.id, p.productName, p.description, p.price, p.unitOfSale, p.stockQuantity, p.isAvailable, p.imagePath, c.categoryName
    FROM products p
    JOIN categories c ON p.categoryId = c.id
    WHERE p.farmerId = ?
    ORDER BY p.id DESC
");
mysqli_stmt_bind_param($stmt, "i", $farmerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Products | StockCrop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/icon.png">
<style>
body { display: flex; min-height: 100vh; }
.sidebar { width: 220px; background-color: #198754; color: white; flex-shrink: 0; display: flex; flex-direction: column; padding-top: 20px; }
.sidebar a { color: white; text-decoration: none; padding: 12px 20px; display: block; }
.sidebar a:hover, .sidebar a.active { background-color: #146c43; }
.content { flex-grow: 1; padding: 20px; }
img.product-image { width: 80px; height: auto; }
</style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center fw-bold">🌾 StockCrop</h4>
    <hr style="border-color: rgba(255,255,255,0.3);">
    <a href="farmerDashboard.php">Dashboard</a>
    <a href="addProduct.php">Add Product</a>
    <a href="viewProducts.php" class="active">My Products</a>
    <a href="viewOrders.php">View Orders</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h2 class="fw-bold text-success">My Products</h2>
    <p class="lead">Manage your products: edit or remove items as needed.</p>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">You have not added any products yet. <a href="addProduct.php">Add a product now</a>.</div>
    <?php else: ?>
        <table class="table table-striped table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price (JMD)</th>
                    <th>Unit</th>
                    <th>Stock</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr id="product-<?php echo $row['id']; ?>">
                    <td>
                        <?php if ($row['imagePath'] && file_exists($row['imagePath'])): ?>
                            <img src="<?php echo $row['imagePath']; ?>" class="product-image" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['productName']); ?></td>
                    <td><?php echo htmlspecialchars($row['categoryName']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['unitOfSale']); ?></td>
                    <td><?php echo $row['stockQuantity']; ?></td>
                    <td><?php echo $row['isAvailable'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="editProduct.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary mb-1">Edit</a>
                        <button class="btn btn-sm btn-danger mb-1" onclick="deleteProduct(<?php echo $row['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function deleteProduct(productId) {
    if (confirm("Are you sure you want to delete this product?")) {
        fetch('deleteProductAjax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + productId
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                const row = document.getElementById('product-' + productId);
                if (row) row.remove();
                alert("Product deleted successfully.");
            } else {
                alert("Error: " + data);
            }
        })
        .catch(error => alert("Error: " + error));
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
