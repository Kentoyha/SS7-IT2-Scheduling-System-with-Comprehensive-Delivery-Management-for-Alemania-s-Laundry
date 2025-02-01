<?php
include 'db_connect.php';

session_start(); // Start the session

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check in the Admin table
    $query = "SELECT * FROM Admin WHERE Username = '$username' AND Password = '$password'";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn)); // Debugging

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['Username'];
        $_SESSION['account_level'] = 1; // Admin account level
        header("Location: dashboard.php"); // Redirect to admin dashboard
        exit();
    } else {
        // Check in the User table
        $query = "SELECT * FROM User WHERE Username = '$username' AND Password = '$password'";
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn)); // Debugging

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['username'] = $row['Username'];
            $_SESSION['account_level'] = 2; // User account level
            header("Location: dashboard1.php"); // Redirect to user dashboard
            exit();
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>LOG IN </h1>
    </header>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="submit" value="Login">
    </form>
</body>
</html>