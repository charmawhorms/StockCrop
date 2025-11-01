<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['id'];

// Fetch user info from users + customers
$query = mysqli_prepare($conn, "
    SELECT u.email, c.firstName, c.lastName, c.phoneNumber, c.address1, c.address2, c.parish
    FROM users u
    LEFT JOIN customers c ON u.id = c.userId
    WHERE u.id = ?
");
mysqli_stmt_bind_param($query, "i", $userId);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($query);

// Sanitize address data (handle potential nulls from LEFT JOIN)
$firstName = htmlspecialchars($user['firstName'] ?? 'Customer');
$lastName = htmlspecialchars($user['lastName'] ?? '');
$fullName = $firstName . ' ' . $lastName;
$email = htmlspecialchars($user['email'] ?? 'N/A');
$phone = htmlspecialchars($user['phoneNumber'] ?? 'N/A');
$address = implode(', ', array_filter([
    htmlspecialchars($user['address1'] ?? ''),
    htmlspecialchars($user['address2'] ?? ''),
    htmlspecialchars($user['parish'] ?? '')
]));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockCrop | Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="assets/icon.png">
    <style>
        :root {
            /* Keep primary success color but use it sparingly */
            --bs-stockcrop-green: #198754; 
            /* Use a very light, neutral background */
            --bs-background-light: #f4f6f9; 
            /* Subtle border color */
            --bs-border-subtle: #e0e4eb;
        }
        body { 
            background-color: var(--bs-background-light); 
        }
        .dashboard-header { 
            background-color: var(--bs-white); 
            border-bottom: 1px solid var(--bs-border-subtle); 
            padding: 1.5rem 0; 
        }
        /* Style the tabs to be subtle */
        .nav-link.dash-tab {
            color: var(--bs-gray-600);
            border: none;
            border-bottom: 2px solid transparent;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-link.dash-tab.active, .nav-link.dash-tab:hover {
            color: var(--bs-stockcrop-green);
            border-bottom: 2px solid var(--bs-stockcrop-green);
            background-color: transparent;
        }
        .action-card {
            border-radius: 0.5rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: 1px solid var(--bs-border-subtle);
            height: 100%;
            background-color: var(--bs-white);
        }
        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
        }
        .detail-card {
             /* Use subtle border left instead of strong color block */
             border-left: 4px solid var(--bs-stockcrop-green);
        }
        .stat-icon {
            color: var(--bs-gray-500); /* Neutral icon color */
        }

        /* Define Green and Orange colors for better consistency */
    :root {
        --stockcrop-green: #388E3C; 
        --stockcrop-orange: #FF8F00; 
    }

    .dashboard-header {
        /* Apply the gradient background */
        background-image: linear-gradient(to right, var(--stockcrop-green), var(--stockcrop-orange));
        background-size: 100% 100%;
        padding: 1.5rem 0; 
        border: none;
    }

    /* Ensure text has high contrast */
    .dashboard-header .container h1 {
        color: #ffffff !important; 
    }
    
    .dashboard-header .container p.lead {
        color: #e0e0e0 !important; 
    }
    
    /* Optional: Small addition to improve image visibility/position */
    .hero-img {
        /* Add a little margin to separate it from the text */
        margin-left: 1rem; 
        /* Ensure it doesn't shrink too much on small screens */
        flex-shrink: 0;
    }

    .opacity-control {
        /* Set opacity to 50% (You can adjust this value: 0.8 is subtle, 0.3 is very faint) */
        opacity: 0.2;
        
        /* Optional: Add a smooth transition effect when hovering, although not needed here */
        /* transition: opacity 0.3s ease; */ 
    }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="dashboard-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <h1 class="display-6 fw-bold text-dark mb-1">Hello, <?= $firstName ?></h1>
                <p class="lead text-muted mb-0">Manage your profile and track your orders.</p>
            </div>

            <div class="d-none d-sm-block">
                 <img src="assets/produceOutline.png" alt="Dashboard Hero" class="img-fluid hero-img opacity-control" style="max-height: 150px;">
            </div>
            
        </div>
    </div>
</div>


<div class="container py-5">
    
    <ul class="nav nav-tabs mb-4 border-0" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link dash-tab active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                <span class="material-symbols-outlined align-middle me-1" style="font-size: 20px;">home</span> Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link dash-tab" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
                <span class="material-symbols-outlined align-middle me-1" style="font-size: 20px;">package</span> Orders
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link dash-tab" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                <span class="material-symbols-outlined align-middle me-1" style="font-size: 20px;">person</span> Profile
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link dash-tab" id="wishlist-tab" data-bs-toggle="tab" data-bs-target="#wishlist" type="button" role="tab" aria-controls="wishlist" aria-selected="false">
                <span class="material-symbols-outlined align-middle me-1" style="font-size: 20px;">favorite</span> Wishlist
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="row g-4">
                
                <div class="col-lg-4 col-md-6">
                    <div class="card action-card p-4 text-center shadow-sm">
                        <span class="material-symbols-outlined display-3 stat-icon mb-2">local_shipping</span>
                        <h5 class="fw-bold mb-1">0 Orders in Progress</h5>
                        <p class="text-muted small">Check the status of your deliveries.</p>
                        <button class="btn btn-sm btn-outline-secondary mt-2 w-75 mx-auto" data-bs-toggle="tab" data-bs-target="#orders">View Orders</button>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card action-card p-4 text-center shadow-sm">
                        <span class="material-symbols-outlined display-3 stat-icon mb-2">account_box</span>
                        <h5 class="fw-bold mb-1">Update Account Info</h5>
                        <p class="text-muted small">Manage your name, phone, and address.</p>
                        <a href="editProfile.php" class="btn btn-sm btn-success mt-2 w-75 mx-auto">Edit Profile</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card action-card p-4 shadow-sm">
                        <h5 class="fw-bold mb-3 d-flex align-items-center text-dark">
                             <span class="material-symbols-outlined me-2 stat-icon">update</span> Latest Activity
                        </h5>
                        <p class="mb-1"><strong>Last Order:</strong> N/A</p>
                        <p class="mb-1 text-muted small">Your order history is currently empty.</p>
                        <div class="mt-3 pt-3 border-top">
                            <a href="shop.php" class="btn btn-sm btn-outline-secondary w-100">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
            <h3 class="mb-3 fw-bold">My Orders</h3>
            <div class="alert alert-info text-center bg-white border-0 shadow-sm">
                <span class="material-symbols-outlined display-6 d-block mb-2 text-primary">list_alt</span>
                <p class="mb-0">You have no active or past orders on record.</p>
                <a href="shop.php" class="alert-link fw-semibold">Start your first order now.</a>
            </div>
        </div>

        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <h3 class="mb-3 fw-bold">Account Details</h3>

            <div class="card p-4 shadow-sm detail-card bg-white">
                <h5 class="fw-bold mb-3 text-dark">Personal Information & Address</h5>
                
                <div class="row g-3 text-muted">
                    <div class="col-md-6"><span class="fw-semibold text-dark">Name:</span> <?= $fullName ?></div>
                    <div class="col-md-6"><span class="fw-semibold text-dark">Email:</span> <?= $email ?></div>
                    <div class="col-md-6"><span class="fw-semibold text-dark">Phone:</span> <?= $phone ?></div>
                    <div class="col-md-6"><span class="fw-semibold text-dark">Address:</span> <?= $address ? $address : 'Not set' ?></div>
                </div>

                <div class="mt-4 pt-3 border-top">
                     <a href="editProfile.php" class="btn btn-success me-2">
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">edit</span> Edit Profile
                    </a>
                    <a href="changePassword.php" class="btn btn-outline-secondary">
                        Change Password
                    </a>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="wishlist" role="tabpanel" aria-labelledby="wishlist-tab">
            <h3 class="mb-3 fw-bold">My Wishlist</h3>
             <div class="alert alert-warning text-center bg-white border-0 shadow-sm">
                <span class="material-symbols-outlined display-6 d-block mb-2 text-warning">favorite</span>
                <p class="mb-0">Your wishlist is currently empty.</p>
                <a href="shop.php" class="alert-link fw-semibold">Browse Products</a>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>