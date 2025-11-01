<?php
// 1. Start the session
session_start();

// 2. Include the database configuration (Crucial for $conn)
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>StockCrop | Home</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styles.css">
        <link rel="icon" type="image/png" href="assets/icon.png">
        <meta name="keywords" content="farmers market, fresh produce, organic fruits, vegetables, local products, sustainable farming, healthy eating, farm-to-table, seasonal produce, community market">
        <meta name="description" content="Discover fresh, organic produce directly from local farmers. Shop seasonal fruits and vegetables, support sustainable farming, and enjoy healthy eating with our farm-to-table market.">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    </head>
    <body>
        <?php
            include 'navbar.php';
        ?>
        
        <section class="hero-section">
            <div class="container hero-container">
                <div class="hero-text">
                    <h1>Fresh From Jamaican Farms, <span>Delivered to Your Door</span></h1>
                    <p>StockCrop connects verified local farmers with consumers, making farm-to-table easier, fresher, and more reliable</p>
                    <a href="shop.php" class="btn-shop d-inline-flex align-items-center mt-3">Shop Now 
                        <span class="material-symbols-outlined ms-2">arrow_forward</span>
                    </a>
                </div>
                <div class="hero-image">
                    <div class="hero-circle"></div>
                    <img src="assets/man-holding-vegetables.png" alt="Farmer holding vegetables">
                </div>
            </div>
        </section>

        <!-- Features + Categories Section -->
        <section class="features-wrap py-5">
        <div class="container text-center mb-4">
            <p class="muted-tag">Feeding Families. Fresh to Your Door.</p>
        </div>

        <div class="container">
            <div class="row g-4 justify-content-center mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card blue">
                <h5 class="feature-title">Finest Produce</h5>
                <p class="feature-desc">Get the freshest fruits, vegetables and ground provisions harvested daily and delivered with care.</p>
                <img src="assets/produceImage.png" alt="Produce" class="feature-illustration">
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card green">
                <h5 class="feature-title">Quality Livestock</h5>
                <p class="feature-desc">Access healthy, ethically-raised animals verified through RADA certification.</p>
                <img src="assets/livestockImage.png" alt="Livestock" class="feature-illustration2">
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card red">
                <h5 class="feature-title">Direct and Fair Trade</h5>
                <p class="feature-desc">Bypass middlemen — buy directly from the grower, ensuring fair prices for you and fair earning for the farmer.</p>
                <img src="assets/farmerImage.png" alt="Farmer" class="feature-illustration">
                </div>
            </div>
            </div>

            <h4 class="text-center categories-title">Categories</h4>

            <div class="categories-row mt-4">
                <div class="category-item">
                    <img src="assets/catFruits.png" alt="Fruits">
                    <p class="cat-label">Fruits</p>
                </div>

                <div class="category-item">
                    <img src="assets/catVegetables.png" alt="Vegetables">
                    <p class="cat-label">Vegetables</p>
                </div>

                <div class="category-item">
                    <img src="assets/catHerbs.png" alt="Herbs">
                    <p class="cat-label">Herbs</p>
                </div>

                <div class="category-item">
                    <img src="assets/catGrains.png" alt="Grains">
                    <p class="cat-label">Grains</p>
                </div>

                <div class="category-item">
                    <img src="assets/catGroundProvision.png" alt="Ground Provision">
                    <p class="cat-label">Ground Provision</p>
                </div>

                <div class="category-item">
                    <img src="assets/catLivestock.png" alt="Livestock">
                    <p class="cat-label">Livestock</p>
                </div>
            </div>
        </div>
        </section>

        <!-- Featured Products Section -->
        <section class="featured-products py-5 position-relative">
            <!-- Background Image -->
            <div class="featured-bg"></div>
            <div class="overlay"></div>

            <div class="container position-relative text-white">
                <h2 class="text-center mb-5 fw-bold text-white">This Week’s Fresh Picks</h2>

                <div class="row g-4">
                    <?php
                        // Fetch 4 products
                        $query = "SELECT id, productName, price, imagePath, unitOfSale FROM products ORDER BY productName DESC LIMIT 4";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card product-card h-100 border-0 shadow-lg">
                            <div class="card-img-container">
                                <img src="<?= htmlspecialchars($row['imagePath']) ?>"  
                                    class="card-img-top" 
                                    alt="<?= htmlspecialchars($row['productName']); ?>">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title text-dark"><?= htmlspecialchars($row['productName']); ?></h5>
                                <p class="text-success fw-bold mb-3">$<?= number_format($row['price'], 2); ?> / <?= htmlspecialchars($row['unitOfSale']); ?></p>
                                <a href="productDetails.php?id=<?= $row['id']; ?>" class="btn btn-success w-100 fw-semibold">View Product</a>
                            </div>
                        </div>
                    </div>
                    <?php 
                            endwhile;
                        else:
                            echo "<p class='text-center text-light'>No featured products available right now.</p>";
                        endif;
                    ?>
                </div>

                <div class="text-center mt-5">
                    <a href="shop.php" class="btn btn-lg btn-warning fw-bold shadow">Browse All Products</a>
                </div>
            </div>
        </section>
        
        <section class="testimonials py-5">
            <div class="container">
                <h2 class="text-center mb-2 fw-bold">Real Stories From Farmers and Shoppers</h2>
                <p class="text-center lead text-muted mb-5">
                    See how StockCrop is helping Jamaicans buy and sell fresh produce directly from the source.
                </p>

                <div class="row g-4">
                    <!-- Shopper -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 p-4 shadow-sm border-0">
                            <div class="card-body text-center"> 
                                <img src="assets/customerProfilePicture.png" 
                                    alt="Lia G. Avatar" 
                                    class="testimonial-avatar mb-3 rounded-circle" height="80" width="80"> 
                                
                                <div class="text-warning mb-3">★★★★★</div> 
                                <p class="card-text fst-italic">
                                    "I love being able to order directly from local farmers. The produce is fresher, and I actually know where my food comes from. StockCrop makes it so convenient!"
                                </p>
                                <footer class="blockquote-footer mt-3">
                                    <cite title="Shopper" class="fw-bold text-dark">Lia G.</cite>, St. Ann
                                </footer>
                            </div>
                        </div>
                    </div>

                    <!-- Farmer -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 p-4 shadow-sm border-0">
                            <div class="card-body text-center"> 
                                <img src="assets/customerProfilePicture.png"
                                    alt="Michael B. Avatar" 
                                    class="testimonial-avatar mb-3 rounded-circle" height="80" width="80"> 

                                <div class="text-warning mb-3">★★★★★</div>
                                <p class="card-text fst-italic">
                                    "As a farmer, StockCrop has helped me reach new customers across the island without needing a middleman. It’s simple, fair, and boosts my earnings."
                                </p>
                                <footer class="blockquote-footer mt-3">
                                    <cite title="Farmer" class="fw-bold text-dark">Michael B.</cite>, Clarendon Farmer
                                </footer>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shopper -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card h-100 p-4 shadow-sm border-0">
                            <div class="card-body text-center"> 
                                <img src="assets/customerProfilePicture.png" 
                                    alt="Sophia R. Avatar" 
                                    class="testimonial-avatar mb-3 rounded-circle" height="80" width="80"> 

                                <div class="text-warning mb-3">★★★★★</div>
                                <p class="card-text fst-italic">
                                    "My family now gets farm-fresh ground provisions delivered weekly. Supporting local farmers while getting quality produce—it’s a win-win!"
                                </p>
                                <footer class="blockquote-footer mt-3">
                                    <cite title="Shopper" class="fw-bold text-dark">Sophia R.</cite>, St. Catherine
                                </footer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="final-cta py-6 position-relative text-white">
            <div class="container text-center">
                <h2 class="fw-bold mb-3 display-5">Taste the Freshness. Get Started Today.</h2>
                <p class="lead mb-5 mx-auto" style="max-width: 750px;">
                    Support your local Jamaican farmers and enjoy the freshest, island-grown ingredients delivered straight to your door. Join the farm-to-table movement.
                </p>
                <a href="shop.php" class="btn btn-warning btn-extra-large fw-bold shadow-lg text-dark px-5 py-3">
                    Start Shopping Now
                </a>
            </div>
        </section>
        <?php
            include 'footer.php';
        ?>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
