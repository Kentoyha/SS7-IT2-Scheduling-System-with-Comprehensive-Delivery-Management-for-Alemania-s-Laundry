<?php
include 'Menu.php'; // Include the menu
session_start(); // Start the session

// Check if the user is logged in and has the correct account level
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

// If the user is logged in and is an admin, display the dashboard
?>
<!DOCTYPE html>
<html lang="en">
    <link rel="stylesheet" href="dashboard.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>This is the admin dashboard.</p>
    <a href="logout.php">Logout</a>
</body>
</html>