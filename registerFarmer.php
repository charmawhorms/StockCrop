<!DOCTYPE html>
<html lang="en">
    <head>
        <title>StockCrop | Register</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="assets/icon.png">
        <link rel="stylesheet" href="styles.css">
        
        <!--SweetAlert2-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <?php
            include 'config.php';
            // Define farmer role ID
            $farmerRoleId = 2;
        
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
                
                //Fetch form data
                $firstName = htmlspecialchars(trim($_POST["firstName"]));
                $lastName = htmlspecialchars(trim($_POST["lastName"]));
                $email = htmlspecialchars(trim($_POST["email"]));
                $phoneNumber = htmlspecialchars(trim($_POST["phoneNumber"]));
                $radaIdNumber = htmlspecialchars(trim($_POST["radaIdNumber"]));
                $address1 = htmlspecialchars(trim($_POST["address1"]));
                $address2 = htmlspecialchars(trim($_POST["address2"]));
                $parish = htmlspecialchars(trim($_POST["parish"]));
                $password = $_POST["password"];
                $confirmPassword = $_POST["confirmPassword"];

                // Check if password and confirm password match
                if ($password !== $confirmPassword) {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Passwords do not match',
                            text: 'Please re-enter your password.'
                        });
                    </script>";
                    exit;
                }
                
                //Check if email already exist in the database
                $query = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
                mysqli_stmt_bind_param($query, "s", $email);
                mysqli_stmt_execute($query);
                $result = mysqli_stmt_get_result($query);
                
                if (mysqli_num_rows($result) > 0) {
                    echo "<script> 
                            Swal.fire({
                                icon: 'error',
                                title: 'Email already exists',
                                text: 'Please use a different email address.'
                            });
                        </script>";
                        exit;
                }
                else {
                    //Check if password and confirm password matches
                    if ($password == $confirmPassword) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $query2 = mysqli_prepare($conn, "INSERT INTO users (roleId, email, password_hash) VALUES(?, ?, ?)");
                        //$query2 = mysqli_prepare($conn, "INSERT INTO farmers (firstName, lastName, email, phoneNumber, radaIdNumber, address1, address2, parish, userPassword) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($query2, "iss", $farmerRoleId, $email, $hashedPassword);
                        
                        if (mysqli_stmt_execute($query2)) {
                            $userId = mysqli_insert_id($conn); // Get the auto-generated user ID
                            
                            // Insert farmer details into farmers table
                            $insertFarmer = mysqli_prepare($conn, 
                                "INSERT INTO farmers (userId, firstName, lastName, email, phoneNumber, radaIdNumber, address1, address2, parish, created_at)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
                            );
                            
                            mysqli_stmt_bind_param($insertFarmer, "issssssss", $userId, $firstName, $lastName, $email, $phoneNumber, $radaIdNumber, $address1, $address2, $parish);
                            
                            if (mysqli_stmt_execute($insertFarmer)) {
                                echo "<script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Registration Successful!',
                                        text: 'Your farmer account has been created.',
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        window.location.href = 'login.php';
                                    });
                                </script>";
                            } else {
                            echo "<script> 
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error creating user account',
                                        text: 'Please try again later.'
                                    });
                                </script>";
                        }
                    }
                    else {
                        echo "<script> 
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Password mismatch',
                                    text: 'Passwords do not match. Please check and try again.'
                                });
                            </script>";
                    }
                }
            }
        }
        ?>
        
        <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title fw-bold mb-2">Farmer Register</h2>
                        <p class="mb-4">Fill out the form to get registered!</p>
                        <form id="registration-form" action="registerFarmer.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name:</label>
                                    <input type="text" id="firstName" name="firstName" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name:</label>
                                    <input type="text" id="lastName" name="lastName" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="phoneNumber" class="form-label">Phone Number:</label>
                                <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="radaIdNumber" class="form-label">RADA ID Number:</label>
                                <input type="text" id="radaIdNumber" name="radaIdNumber" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="address1" class="form-label">Address Line 1:</label>
                                <input type="text" id="address1" name="address1" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="address2" class="form-label">Address Line 2:</label>
                                <input type="text" id="address2" name="address2" class="form-control" required>
                            </div>

                            <label for="parish">Parish:</label>
                            <select id="parish" name="parish" required>
                                <option value="">-- Select Your Parish --</option>
                                <option value="Clarendon">Clarendon</option>
                                <option value="Hanover">Hanover</option>
                                <option value="Kingston">Kingston</option>
                                <option value="Manchester">Manchester</option>
                                <option value="Portland">Portland</option>
                                <option value="St. Andrew">St. Andrew</option>
                                <option value="St. Ann">St. Ann</option>
                                <option value="St. Catherine">St. Catherine</option>
                                <option value="St. Elizabeth">St. Elizabeth</option>
                                <option value="St. James">St. James</option>
                                <option value="St. Mary">St. Mary</option>
                                <option value="St. Thomas">St. Thomas</option>
                                <option value="Trelawny">Trelawny</option>
                                <option value="Westmoreland">Westmoreland</option>
                            </select>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="submit" class="btn btn-primary">Register</button>
                                <p class="text-center">Already have an account? <a href="login.php">Login Here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>