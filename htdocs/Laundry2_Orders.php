<?php 
session_start();
include 'Menu2.php';
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="Style3.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Laundry Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin: 0 auto; /* Centers the container horizontally */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .order-instructions {
            background-color: #f1f1f1;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .order-instructions h3 {
            color: #333;
        }

        .order-instructions p {
            color: #555;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 10px;
            width: 250px;
            margin-right: 10px;
        }

        .search-container button {
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #555;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: 100%;
        }

        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Laundry Orders</h2>
        
        <!-- Instructions Section -->
        <div class="order-instructions">
            <h3>How to Place an Order</h3>
            <p>To place a new order, simply provide your laundry details below. Choose the type of laundry, and specify the amount. After submitting the form, your order will appear in the list below, and we will contact you for pickup and delivery.</p>
        </div>

        <!-- Order Form -->
        <form action="submit_order.php" method="POST">
            <h3>Place a New Order</h3>
            <label for="laundry_type">Laundry Type:</label>
            <select name="laundry_type" id="laundry_type" required>
                <option value="Clothes">Clothes</option>
                <option value="Bedding">Bedding</option>
                <option value="Curtains">Curtains</option>
                <option value="Towels">Towels</option>
                <!-- Add more options as needed -->
            </select><br><br>

            <label for="laundry_amount">Amount:</label>
            <input type="number" name="laundry_amount" id="laundry_amount" placeholder="Enter amount" required><br><br>

            <label for="order_date">Preferred Order Date:</label>
            <input type="date" name="order_date" id="order_date" required><br><br>

               
            </select><br><br>

            <button type="submit" class="btn">Place Order</button>
        </form>


        
        </table>
    </div>
</body>
</html>