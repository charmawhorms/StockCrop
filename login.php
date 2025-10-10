<!DOCTYPE html>
<html lang="en">
    <head>
        <title>IslandEscape Jamaica | Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styles.css">

        
        <!--SweetAlert2-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <meta name="author" content="Charma Whorms">
        <!--I CERTIFY THAT I HAVE NOT GIVEN OR RECEIVED ANY UNAUTHORIZED ASSISTANCE ON THIS 
        ASSIGNMENT -C.Whorms-->
        

    </head>
    <body>
        <?php
            include 'session.php';
            include 'config.php';

            redirectIfLoggedIn();

            $errorMessage = '';
        
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
                $email = trim($_POST["email"]);
                $password = $_POST["password"];

                // Prepare query to find user by email
                $query = mysqli_prepare($conn, "SELECT id, roleId, password_hash FROM users WHERE email = ?");
                mysqli_stmt_bind_param($query, "s", $email);
                mysqli_stmt_execute($query);

                $result = mysqli_stmt_get_result($query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $user = mysqli_fetch_assoc($result);

                    // Verify password
                    if (password_verify($password, $user["password_hash"])) {
                        session_regenerate_id(true); // Prevent session fixation
                        $_SESSION["id"] = $user["id"];
                        $_SESSION["roleId"] = $user["roleId"];

                        // Redirect based on role
                        if ($user["roleId"] == 2) {
                            // Farmer
                            header("Location: farmerDashboard.php");
                            exit();
                        } elseif ($user["roleId"] == 1) {
                            // Customer
                            header("Location: customerHome.php");
                            exit();
                        } else {
                            // Unknown role
                            $errorMessage = "Invalid account role. Please contact support.";
                        }
                    } else {
                        $errorMessage = "Invalid login credentials. Please try again.";
                    }
                } else {
                    $errorMessage = "Invalid email or password.";
                }
            }
        ?>

        
        <div class="login-section"  style="width: 60%;">
        <div class="form-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center fw-bold">Welcome Farmers!</h3>
                                <p class="text-center">Sign in to access your account</p>
                                <?php if ($errorMessage != ''): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $errorMessage; ?>
                                    </div>
                                <?php endif; ?>

                                <form action="login.php" method="POST">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" id="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password:</label>
                                        <input type="password" id="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" name="submit" class="btn btn-primary">Login</button>
                                    </div>
                                </form>

                                <p class="mt-3 text-center">Don't have an account? <a href="registerFarmer.php">Register as a Farmer</a> <a href="registerCustomer.php">Register as a Customer</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>