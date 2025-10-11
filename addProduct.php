<?php
include 'session.php';
include 'config.php';

// Only allow farmers
redirectIfNotLoggedIn();
if ($_SESSION['roleId'] != 2) {
    header("Location: index.php");
    exit();
}

// Get farmerId from farmers table
$userId = $_SESSION['id']; // users.id
$farmerQuery = mysqli_prepare($conn, "SELECT id FROM farmers WHERE userId = ?");
mysqli_stmt_bind_param($farmerQuery, "i", $userId);
mysqli_stmt_execute($farmerQuery);
$farmerResult = mysqli_stmt_get_result($farmerQuery);

if ($farmerResult && mysqli_num_rows($farmerResult) > 0) {
    $farmerRow = mysqli_fetch_assoc($farmerResult);
    $farmerId = $farmerRow['id']; // correct foreign key
} else {
    die("Error: Farmer record not found. Please contact support.");
}

$successMessage = '';
$errorMessage = '';

// Fetch categories
$catQuery = mysqli_query($conn, "SELECT id, categoryName FROM categories ORDER BY categoryName ASC");
$categories = [];
while ($row = mysqli_fetch_assoc($catQuery)) {
    $categories[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $productName = htmlspecialchars(trim($_POST["productName"]));
    $description = htmlspecialchars(trim($_POST["description"]));
    $categoryId = intval($_POST["category"]);
    $price = floatval($_POST["price"]);
    $unitOfSale = htmlspecialchars(trim($_POST["unitOfSale"]));
    $stockQuantity = intval($_POST["stockQuantity"]);
    $isAvailable = isset($_POST["isAvailable"]) ? 1 : 0;

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES["image"]["type"], $allowedTypes)) {
            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $imagePath = "uploads/" . uniqid() . '.' . $ext;
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
        } else {
            $errorMessage = "Only JPG, PNG, and GIF files are allowed.";
        }
    }

    if ($errorMessage == '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO products (farmerId, categoryId, productName, description, price, unitOfSale, stockQuantity, imagePath, isAvailable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iissdsisi", $farmerId, $categoryId, $productName, $description, $price, $unitOfSale, $stockQuantity, $imagePath, $isAvailable);

        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "Product added successfully!";
        } else {
            $errorMessage = "Error adding product. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product | StockCrop</title>
<link rel="icon" type="image/png" href="assets/icon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
    body { display: flex; min-height: 100vh; }
    .sidebar { width: 220px; background-color: #198754; color: white; flex-shrink: 0; display: flex; flex-direction: column; padding-top: 20px; }
    .sidebar a { color: white; text-decoration: none; padding: 12px 20px; display: block; }
    .sidebar a:hover, .sidebar a.active { background-color: #146c43; }
    .content { flex-grow: 1; padding: 20px; }
</style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center fw-bold">🌾 StockCrop</h4>
    <hr style="border-color: rgba(255,255,255,0.3);">
    <a href="farmerDashboard.php">Dashboard</a>
    <a href="addProduct.php" class="active">Add Product</a>
    <a href="viewProducts.php">My Products</a>
    <a href="viewOrders.php">View Orders</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h2 class="fw-bold text-success">Add New Product</h2>
    <p class="lead">Fill in the details below to add a new product.</p>

    <?php if ($successMessage != ''): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    <?php if ($errorMessage != ''): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form action="addProduct.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="productName" class="form-label">Product Name:</label>
            <input type="text" id="productName" name="productName" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category:</label>
            <select id="category" name="category" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['categoryName']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (JMD):</label>
            <input type="number" step="0.01" id="price" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="unitOfSale" class="form-label">Unit of Sale (e.g., lb, dozen, head):</label>
            <input type="text" id="unitOfSale" name="unitOfSale" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="stockQuantity" class="form-label">Stock Quantity:</label>
            <input type="number" id="stockQuantity" name="stockQuantity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Product Image:</label>
            <input type="file" id="image" name="image" class="form-control">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" id="isAvailable" name="isAvailable" class="form-check-input" checked>
            <label for="isAvailable" class="form-check-label">Available for Sale</label>
        </div>

        <button type="submit" name="submit" class="btn btn-success">Add Product</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
