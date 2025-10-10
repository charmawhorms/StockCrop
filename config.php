<?php
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $database = "stockcrop";

    // Establishing the connection
    $conn = mysqli_connect($servername, $username, $password, $database);

    // Checking the connection
    if (!$conn) {
        // If the connection fails, die with the error message
        die("Connection failed: " . mysqli_connect_error());
    }
?>
