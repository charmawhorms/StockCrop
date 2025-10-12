<?php
    include 'config.php';
    $farmerRoleId = 2;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
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

        // Check if email already exists
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
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query2 = mysqli_prepare($conn, "INSERT INTO users (roleId, email, password_hash) VALUES(?, ?, ?)");
            mysqli_stmt_bind_param($query2, "iss", $farmerRoleId, $email, $hashedPassword);

            if (mysqli_stmt_execute($query2)) {
                $userId = mysqli_insert_id($conn);
                $insertFarmer = mysqli_prepare($conn, 
                    "INSERT INTO farmers (userId, firstName, lastName, email, phoneNumber, radaIdNumber, address1, address2, parish, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                mysqli_stmt_bind_param($insertFarmer, "issssssss", $userId, $firstName, $lastName, $email, $phoneNumber, $radaIdNumber, $address1, $address2, $parish);

                if (mysqli_stmt_execute($insertFarmer)) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: 'Your farmer account has been created.',
                            confirmButtonText: 'OK'
                        }).then(() => { window.location.href = 'login.php'; });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to create farmer details.' });
                    </script>";
                }
            } else {
                echo "<script>
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to create user account.' });
                </script>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StockCrop | Farmer Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #F5F7FA; }

        .register-hero {
            background: url('https://images.unsplash.com/photo-1601004890684-d8cbf643f5f2?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .register-overlay {
            position: absolute;
            inset: 0;
            background: rgba(2,128,55,0.65);
        }

        .register-card {
            position: relative;
            z-index: 2;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.15);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        .register-card h3 { color: #028037; font-weight: 700; margin-bottom: 15px; }
        .register-card p { color: #555; margin-bottom: 25px; }

        .register-card .form-control:focus {
            border-color: #028037;
            box-shadow: 0 0 0 0.2rem rgba(2,128,55,0.25);
        }

        .btn-register {
            background-color: #FFEB3B;
            color: #212529;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-register:hover {
            background-color: #FDD835;
        }

        .register-card a {
            color: #028037;
            text-decoration: none;
        }

        .register-card a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .register-hero { padding: 40px 15px; }
        }
    </style>
</head>
<body>
    <section class="register-hero">
        <div class="register-overlay"></div>
        <div class="register-card">
            <h3 class="text-center">Farmer Registration</h3>
            <p class="text-center">Fill out the form to create your farmer account</p>

            <form action="registerFarmer.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label fw-semibold">First Name</label>
                        <input type="text" id="firstName" name="firstName" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label fw-semibold">Last Name</label>
                        <input type="text" id="lastName" name="lastName" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="phoneNumber" class="form-label fw-semibold">Phone Number</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="radaIdNumber" class="form-label fw-semibold">RADA ID Number</label>
                    <input type="text" id="radaIdNumber" name="radaIdNumber" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="address1" class="form-label fw-semibold">Address Line 1</label>
                    <input type="text" id="address1" name="address1" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="address2" class="form-label fw-semibold">Address Line 2</label>
                    <input type="text" id="address2" name="address2" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="parish" class="form-label fw-semibold">Parish</label>
                    <select id="parish" name="parish" class="form-select" required>
                        <option value="">-- Select Your Parish --</option>
                        <option>Clarendon</option>
                        <option>Hanover</option>
                        <option>Kingston</option>
                        <option>Manchester</option>
                        <option>Portland</option>
                        <option>St. Andrew</option>
                        <option>St. Ann</option>
                        <option>St. Catherine</option>
                        <option>St. Elizabeth</option>
                        <option>St. James</option>
                        <option>St. Mary</option>
                        <option>St. Thomas</option>
                        <option>Trelawny</option>
                        <option>Westmoreland</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" name="submit" class="btn btn-register">Register</button>
                </div>

                <p class="text-center mb-0">
                    Already have an account? <a href="login.php">Login here</a>
                </p>
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
