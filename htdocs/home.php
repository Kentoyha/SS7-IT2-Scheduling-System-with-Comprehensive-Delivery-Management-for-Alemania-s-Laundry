<?php
include("db_connect.php");
include("Menu.php");
include("Logout.php");
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
<link rel="stylesheet" href="home.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>This is the admin dashboard.</p>
    <a href="logout.php">Logout</a>

       <!-- <h1 align="center">Scheduling System with Comprehensive Delivery Management  </h1>
        <br>
        
    <div class="logo" style="text-align: center;">
        <img src="uploads/bg6.jpg" alt="logo" style="border-radius: 50%;">
      </div> 
        
<br>    
<br>
        <p style="text-align: center;">
            IYSL is a regional soccer association that offers teenagers the opportunity to participate in soccer games and practices. <br>
            The leagues typically focus on skill development, teamwork, and sportsmanship, providing a structured environment for players of various skill levels.<br>
            Each city in the region has one team that represents it. <br>
            Each team has a maximum of 15 players and a minimum of 11 players. Each team also has up to three coaches. <br>
            Each team plays two games (home and visitor) against all the other teams during the season.
        </p> -->

</body>
</html>
