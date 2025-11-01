<?php
session_start();
include 'config.php'; // Include your database connection file

// Check if a redirection parameter exists (e.g., from checkout)
$redirect_url = htmlspecialchars($_GET['redirect'] ?? 'login.php');

$registration_successful = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize and Collect Input
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName'] ?? '');
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber'] ?? '');
    $address1 = mysqli_real_escape_string($conn, $_POST['address1'] ?? '');
    $parish = mysqli_real_escape_string($conn, $_POST['parish'] ?? '');
    // address2 is optional
    $address2 = mysqli_real_escape_string($conn, $_POST['address2'] ?? '');

    // 2. Server-side Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($phoneNumber) || empty($address1) || empty($parish)) {
        $error_message = "All required fields must be filled out.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // 3. Check for existing email
        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error_message = "This email is already registered. Please log in.";
        } else {
            // 4. Secure Password Hashing
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $roleId = 3; // Assuming 3 is the roleId for 'Customer' (verify this in your roles table)

            // 5. Start Transaction for Atomic Inserts
            mysqli_begin_transaction($conn);
            
            try {
                // A. Insert into users table
                $sql_user = "INSERT INTO users (roleId, email, password_hash, created_at) VALUES (?, ?, ?, NOW())";
                $stmt_user = mysqli_prepare($conn, $sql_user);
                mysqli_stmt_bind_param($stmt_user, "iss", $roleId, $email, $password_hash);
                mysqli_stmt_execute($stmt_user);
                $new_user_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt_user);

                // B. Insert into customers table
                $sql_customer = "INSERT INTO customers (userId, firstName, lastName, phoneNumber, address1, address2, parish) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_customer = mysqli_prepare($conn, $sql_customer);
                mysqli_stmt_bind_param($stmt_customer, "issssss", 
                    $new_user_id, 
                    $firstName, 
                    $lastName, 
                    $phoneNumber, 
                    $address1, 
                    $address2, 
                    $parish
                );
                mysqli_stmt_execute($stmt_customer);
                mysqli_stmt_close($stmt_customer);
                
                // C. Commit the transaction
                mysqli_commit($conn);
                
                $registration_successful = true;
                
                // Automatically log the user in
                $_SESSION['id'] = $new_user_id;
                $_SESSION['roleId'] = $roleId;
                $_SESSION['email'] = $email;
                $_SESSION['firstName'] = $firstName;
                
                header("Location: " . $redirect_url);
                exit();

            } catch (Exception $e) {
                // D. Rollback on failure
                mysqli_rollback($conn);
                error_log("Customer registration failed: " . $e->getMessage());
                $error_message = "Registration failed due to a system error. Please try again.";
            }
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StockCrop | Register Customer</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> 
    <link rel="icon" type="image/png" href="assets/icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-6 mb-4 fw-bold text-center">Join StockCrop as a Customer</h1>
                <p class="text-center text-muted mb-4">Access fresh, local produce delivered right to your door.</p>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <span class="material-symbols-outlined align-middle me-2">error</span>
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <?php if ($registration_successful && !isset($_SESSION['id'])): // Fallback success message ?>
                    <div class="alert alert-success" role="alert">
                        Registration successful! You can now <a href="login.php" class="alert-link">log in</a>.
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <form method="POST" action="registerCustomer.php?redirect=<?= $redirect_url ?>">
                        
                        <h5 class="mb-3 text-success">Account Credentials</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required value="<?= htmlspecialchars($_POST['phoneNumber'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <small class="text-muted">Min 6 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-success">Personal Details & Primary Address</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address1" class="form-label">Address Line 1 (Street Address)</label>
                            <input type="text" class="form-control" id="address1" name="address1" required value="<?= htmlspecialchars($_POST['address1'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address2" class="form-label">Address Line 2 (Apartment, unit, etc. - Optional)</label>
                            <input type="text" class="form-control" id="address2" name="address2" value="<?= htmlspecialchars($_POST['address2'] ?? '') ?>">
                        </div>
                        <div class="mb-4">
                            <label for="parish" class="form-label">Parish</label>
                            <select class="form-select" id="parish" name="parish" required>
                                <option value="">Select your Parish</option>
                                <?php 
                                    $parishes = [
                                        "Kingston", "St. Andrew", "St. Thomas", "Portland", "St. Mary", "St. Ann", 
                                        "Trelawny", "St. James", "Hanover", "Westmoreland", "St. Elizabeth", 
                                        "Manchester", "Clarendon", "St. Catherine"
                                    ];
                                    $selected_parish = $_POST['parish'] ?? '';
                                    foreach ($parishes as $p) {
                                        $selected = ($p === $selected_parish) ? 'selected' : '';
                                        echo "<option value=\"$p\" $selected>$p</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <span class="material-symbols-outlined align-middle me-2">person_add</span>
                            Create Customer Account
                        </button>
                    </form>
                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php?redirect=<?= $redirect_url ?>">Log In here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>