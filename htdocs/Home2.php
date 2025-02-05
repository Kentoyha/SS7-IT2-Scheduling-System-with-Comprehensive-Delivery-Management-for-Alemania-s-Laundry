<?php
include 'Menu2.php';
include 'db_connect.php';

// Set the page title
$title = "Scheduling System with Comprehensive Delivery Management for Alemania's Laundry";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="Style3.css"> <!-- Link to external CSS file -->
    <style>
        /* Add custom styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            background-color: #007BFF;
            padding: 50px 20px;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        /* Feature Cards */
        .features {
            display: flex;
            justify-content: space-between;
            margin: 0 10%;
        }

        .feature-card {
            width: 30%;
            padding: 20px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: scale(1.05); /* Hover effect for feature card scaling */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Increase box-shadow on hover */
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: rotate(15deg); /* Rotate icon on hover */
        }

        .feature-card h2 {
            font-size: 20px;
            color: #333;
        }

        .feature-card p {
            font-size: 16px;
            color: #555;
        }

        /* Button Styling */
        .btn {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #4CAF50; /* Green hover effect for button */
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navigation Menu -->
    <?php ?>
    
    <header class="hero">
        <div class="hero-content">
            <h1>Welcome to Alemania's Laundry</h1>
            <p>Our platform ensures efficient scheduling and reliable delivery services tailored for your laundry needs.</p>
            <a href="dashboard.php" class="btn">Get Started</a>
        </div>
    </header>
    
    <main class="container">
        <section class="features">
            <div class="feature-card">
                <img src="https://cdn-icons-png.flaticon.com/128/15440/15440333.png" alt="Easy Scheduling Icon" class="feature-icon">
                <h2>Easy Scheduling</h2>
                <p>Schedule your laundry pickup with just a few clicks.</p>
            </div>
            <div class="feature-card">
                <img src="https://cdn-icons-png.flaticon.com/128/18303/18303485.png" alt="Real-time Tracking Icon" class="feature-icon">
                <h2>Real-time Tracking</h2>
                <p>Stay updated on the status of your laundry anytime, anywhere.</p>
            </div>
            <div class="feature-card">
                <img src="https://cdn-icons-png.flaticon.com/128/590/590501.png" alt="Secure Payments Icon" class="feature-icon">
                <h2>Secure Payments</h2>
                <p>Make hassle-free and secure transactions.</p>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Alemania's Laundry. All rights reserved.</p>
    </footer>
</body>
</html>
