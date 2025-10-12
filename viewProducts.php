<?php
    include 'session.php';
    include 'config.php';
    include 'sidePanel.php'; // include sidebar early for consistent layout

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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
    :root {
        --sc-primary-green: #028037;
        --sc-dark-green: #01632c;
        --sc-hover-green: #146c43;
        --sc-background: #f5f7fa;
        --sc-light-text: #e0e0e0;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: var(--sc-background);
        margin: 0;
        overflow-x: hidden;
    }
    .content {
        margin-left: 250px;
        padding: 20px 30px;
        padding-top: 80px;
        min-height: 100vh;
    }

        h2 {
            font-weight: 700;
            color: #028037;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .table thead {
            background-color: #028037;
            color: white;
        }

        .btn-primary {
            background-color: #028037;
            border: none;
        }

        .btn-primary:hover {
            background-color: #01632c;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        img.product-image {
            width: 80px;
            height: auto;
            border-radius: 6px;
        }

        @media(max-width: 992px) {
            .content { margin-left: 0; padding: 80px 20px; }
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>My Products</h2>
        <p class="lead">Manage your products: edit or remove items as needed.</p>

        <?php if (mysqli_num_rows($result) == 0): ?>
            <div class="alert alert-info mt-4">
                You have not added any products yet. <a href="addProduct.php" class="text-success fw-bold">Add a product now</a>.
            </div>
        <?php else: ?>
            <div class="table-responsive mt-4">
                <table class="table table-bordered align-middle">
                    <thead>
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
                                <?php if (!empty($row['imagePath']) && file_exists($row['imagePath'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['imagePath']); ?>" class="product-image" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['productName']); ?></td>
                            <td><?php echo htmlspecialchars($row['categoryName']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['unitOfSale']); ?></td>
                            <td><?php echo (int)$row['stockQuantity']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['isAvailable'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $row['isAvailable'] ? 'Yes' : 'No'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="editProduct.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary mb-1">Edit</a>
                                <button class="btn btn-sm btn-danger mb-1" onclick="deleteProduct(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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
                        alert("✅ Product deleted successfully.");
                    } else {
                        alert("❌ Error: " + data);
                    }
                })
                .catch(error => alert("Error: " + error));
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
