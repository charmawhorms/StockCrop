<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="color-scheme" content="light">
    <link rel="icon" type="image/png" href="assets/icon.png">
    
    <style>
        /* Navbar Link Hover (Green: #028037) */
        .nav-link {
            color: #212529;
            transition: color 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #028037 !important;
        }

        /* Sign Up Button Hover (Darker Red) */
        .btn-signup {
            background-color: #E57373;
            color: white;
            transition: background-color 0.3s ease;
        }

        /* Hover + dropdown open */
        .btn-signup:hover,
        .dropdown.show .btn-signup {
            background-color: #C62828;
            color: white !important;
        }

        /* Log In Button Hover (Darker Orange) */
        .btn-login {
            background-color: #F4A261;
            transition: background-color 0.3s ease;
            color: white;
        }

        .btn-login:hover {
            background-color: #E76F51;
            color: white;
        }

        /* Responsive Fix for Small Screens */
        @media (max-width: 992px) {
            .search-bar {
                margin-top: 10px;
            }
            .nav-link {
                display: block;
                padding: 8px 0;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm py-2 sticky-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/logo.png" alt="StockCrop Logo" height="45" class="me-2">
            </a>

            <!-- Toggler (hamburger icon for small screens) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible content -->
            <div class="collapse navbar-collapse justify-content-between align-items-center" id="navbarNavDropdown">
                <!-- Nav Links -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 d-flex align-items-center gap-lg-3 text-center">
                    <li class="nav-item"><a href="index.php" class="nav-link fw-semibold">Home</a></li>
                    <li class="nav-item"><a href="products.php" class="nav-link fw-semibold">Shop</a></li>
                    <li class="nav-item"><a href="about.php" class="nav-link fw-semibold">About Us</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link fw-semibold">Contact Us</a></li>
                </ul>

                <!-- Search -->
                <form action="shop.php" method="GET" class="d-flex align-items-center bg-light rounded-pill px-3 py-1 mb-2 mb-lg-0 search-bar">
                    <span class="material-symbols-outlined text-secondary me-2">search</span>
                    <input type="text" name="search" class="form-control border-0 bg-light" 
                           placeholder="Search for products..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </form>

                <!-- Action Buttons -->
                <div class="d-flex align-items-center ms-lg-3 gap-2 mt-2 mt-lg-0">
                    <!-- Cart -->
                    <a href="cart.php" 
                       class="d-flex justify-content-center align-items-center position-relative flex-shrink-0"
                       style="background-color: #FFEB3B; width: 42px; height: 42px; border-radius: 50%; text-decoration: none;">
                        <span class="material-symbols-outlined text-dark" style="font-size: 24px;">shopping_cart</span>
                        <?php
                            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            if ($cart_count > 0) {
                                echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'
                                . $cart_count . '</span>';
                            }
                        ?>
                    </a>

                    <!-- Sign Up Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-signup d-flex align-items-center fw-semibold px-3 py-2 rounded-pill dropdown-toggle" 
                                type="button" id="signupDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-symbols-outlined me-1">person</span>Sign Up
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="signupDropdown">
                            <li><a class="dropdown-item" href="registerFarmer.php">Register as a Farmer</a></li>
                            <li><a class="dropdown-item" href="registerCustomer.php">Register as a Customer</a></li>
                        </ul>
                    </div>

                    <!-- Log In -->
                    <a href="login.php" class="btn btn-login fw-semibold px-3 py-2 rounded-pill">
                        Log In
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
