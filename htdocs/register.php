<?php
include 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $account_level = $_POST['account_level'];

    // Validate input
    if (empty($username) || empty($password) || empty($contact) || empty($email) || empty($account_level)) {
        echo "<script>alert('All fields are required!');</script>";
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Determine the correct table
    if ($account_level == 1) {
        $query = "INSERT INTO Admin (Username, Password, Contact_info, Email) VALUES (?, ?, ?, ?)";
    } elseif ($account_level == 2) {
        $query = "INSERT INTO User (Username, Password, Contact_info, Email) VALUES (?, ?, ?, ?)";
    } else {
        echo "<script>alert('Invalid account level.');</script>";
        exit();
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $contact, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('" . ($account_level == 1 ? "Admin" : "User") . " registered successfully.');
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

    <div class="logo-container">
        <img src="Images/bg6.jpg" alt="logo" class="logo">
    </div>

    <h3 class="login-title">Account Registration</h3>

    <form method="POST" class="login-form">
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" class="form-control" required>
            <input type="password" name="password" placeholder="Password" class="form-control" required>
        </div>    

        <div class="form-group">
            <input type="number" name="contact" placeholder="Contact number" required>
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <select name="account_level" required>
            <option value="" disabled selected>Select Role</option>
            <option value="1">Admin</option>
            <option value="2">User</option>
        </select>

        <input type="submit" value="Register">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</body>
</html>
