<?php
include 'Menu2.php'; // Include the men
session_start(); // Start the session

// Check if the user is logged in and has the correct account level



?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="Style3.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>This is the user dashboard.</p>
    <a href="logout.php">Logout</a>
</body>
</html>

