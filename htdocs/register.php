<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/register.php

// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/register.php

include 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if an admin user already exists
$admin_exists = false;
$check_admin_query = "SELECT COUNT(*) FROM Users WHERE Usertype = 'Admin'";
$admin_result = mysqli_query($conn, $check_admin_query);

if ($admin_result) {
    $admin_count = mysqli_fetch_array($admin_result)[0];
    $admin_exists = ($admin_count > 0);
} else {
    echo "<script>alert('Database error checking for existing admin.');</script>";
}

// Check the total number of accounts
$total_accounts = 0;
$check_total_query = "SELECT COUNT(*) FROM Users";
$total_result = mysqli_query($conn, $check_total_query);

if ($total_result) {
    $total_accounts = mysqli_fetch_array($total_result)[0];
} else {
    echo "<script>alert('Database error checking for total accounts.');</script>";
}

if ($total_accounts >= 2) {
    echo "<script>alert('Maximum number of accounts reached. Registration is closed.'); window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);

    // If admin exists, automatically set account_level to 2 (User)
    $account_level = $admin_exists ? 2 : $_POST['account_level'];

    // Validate input
    if (empty($username) || empty($password) || empty($contact) || empty($email)) {
        echo "<script>alert('All fields are required!');</script>";
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Determine user type
    $usertype = ($account_level == 1) ? 'Admin' : 'User';

    // Insert into Users table
    $query = "INSERT INTO Users (Username, Password, Contact_info, Email, Usertype) VALUES (?, ?, ?, ?, ?)";

    // Prepare SQL statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $contact, $email, $usertype);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('$usertype registered successfully.');
                    window.location.href='login.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_stmt_error($stmt) . "');</script>";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Database error. Please try again later.');</script>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <header>
        <h1>Welcome to Alemania's Laundry</h1>
    </header>

    
    <form method="POST" class="login-form">
    <div class="logo-container">
        <img src="Images/bg6.jpg" alt="logo" class="logo">
    </div>

    <h3 class="login-title">Account Registration</h3>

        <div class="form-group">
            <input type="text" name="username" placeholder="Username" class="form-control" required>
            <input type="password" name="password" placeholder="Password" class="form-control" required>
        </div>    

        <div class="form-group">
            <input type="number" name="contact" placeholder="Contact number" required>
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <?php if (!$admin_exists): ?>
        <select name="account_level" required>
            <option value="" disabled selected>Select Role</option>
            <option value="1">Admin</option>
            <option value="2">User</option>
        </select>
        <?php endif; ?>

        <input type="submit" value="Register">
        <p>Already have an account? <a href="index.php">Login</a></p>
    </form>
</body>
</html>