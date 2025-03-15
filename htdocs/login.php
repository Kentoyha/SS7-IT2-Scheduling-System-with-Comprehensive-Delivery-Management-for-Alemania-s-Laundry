<?php
include 'db_connect.php';
session_start(); // Start the session

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    
    $query = "SELECT * FROM Users WHERE Username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['Password'])) { 
            $_SESSION['username'] = $username;
            $_SESSION['account_level'] = ($row['Usertype'] === 'Admin') ? 1 : 2;

            if ($row['Usertype'] === 'Admin') {
                $_SESSION['User_ID'] = $row['Admin_ID'];
                header("Location: home.php");
            } else {
                $_SESSION['User_ID'] = $row['User_ID'];
                header("Location: Home1.php");
            }
            exit();
        }
    }

    // Show error message only once if login fails
    echo "<script>alert('Invalid username or password.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Styles1.css">
</head>
<body>
    <header>
        <h1>Welcome to Alemania's Laundry</h1>
    </header>

    <div class="logo-container">
        <img src="Images/bg6.jpg" alt="logo" class="logo">
    </div>

    <h3 class="login-title">Log in</h3>

    <form method="POST" class="login-form">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="submit" value="Login">
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
</body>
</html>
