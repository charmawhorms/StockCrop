<?php
include 'session.php';
include 'config.php';

// Redirect if not logged in
redirectIfNotLoggedIn();

// Only allow farmers
if ($_SESSION['roleId'] != 2) {
    header("Location: index.php");
    exit();
}

// Get farmer info from farmers table
$userId = $_SESSION['id'];
$query = mysqli_prepare($conn, "
    SELECT firstName, lastName, email 
    FROM farmers 
    WHERE userId = ?
");
mysqli_stmt_bind_param($query, "i", $userId);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

if (mysqli_num_rows($result) > 0) {
    $farm_info = mysqli_fetch_assoc($result);
} else {
    $farm_info = ['firstName' => 'Farmer', 'lastName' => '', 'email' => ''];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard | StockCrop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 220px;
            background-color: #198754;
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #146c43;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center fw-bold">🌾 StockCrop</h4>
    <hr style="border-color: rgba(255,255,255,0.3);">
    <a href="farmerDashboard.php" class="active">Dashboard</a>
    <a href="addProduct.php">Add Product</a>
    <a href="viewProducts.php">My Products</a>
    <a href="viewOrders.php">View Orders</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="fw-bold text-success">
        Welcome, <?php echo htmlspecialchars($farm_info['firstName'] . ' ' . $farm_info['lastName']); ?> 👋
    </h2>
    <p class="lead">Manage your farm products, view orders, and connect with customers directly.</p>

    <div class="row mt-4 g-4">
        <!-- Add Product Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold">🌱 Add New Product</h5>
                    <p class="card-text">List fresh produce and set prices for your customers.</p>
                    <a href="addProduct.php" class="btn btn-success">Add Product</a>
                </div>
            </div>
        </div>

        <!-- View Products Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold">📦 Manage My Products</h5>
                    <p class="card-text">Update, remove, or restock your listed items.</p>
                    <a href="viewProducts.php" class="btn btn-success">View Products</a>
                </div>
            </div>
        </div>

        <!-- View Orders Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold">🧾 View Orders</h5>
                    <p class="card-text">Track customer orders and manage deliveries.</p>
                    <a href="viewOrders.php" class="btn btn-success">View Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
