<?php 
include 'Menu.php';
include 'db_connect.php';

session_start(); // Start the session

// Check if the user is logged in and has the correct account level
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Orders</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f1f8fc; /* Light blue for freshness */
            color: #333; /* Dark gray for text */
            padding: 20px;
        }

        /* Header styles */
        header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 36px;
            color: #3a7eab; /* Soft blue for a clean, fresh vibe */
            margin-bottom: 10px;
        }

        p {
            font-size: 18px;
            color: #5d8e8c; /* Soft teal green to resemble fresh laundry */
            margin-bottom: 20px;
        }

        /* Form styles */
        .order-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .order-form input, .order-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .order-form button {
            background-color: #3a7eab;
            color: white;
            padding: 12px 20px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .order-form button:hover {
            background-color: #2b5b77;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #3a7eab; /* Soft blue for headers */
            color: white;
            font-size: 18px;
        }

        td {
            font-size: 16px;
            color: #333;
        }

        .no-orders {
            color: #ff8c42; /* Warm orange for no orders text */
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            padding: 20px;
        }

        /* Dashboard section */
        .dashboard-actions {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Link styles */
        a {
            font-size: 16px;
            color: #ff8c42; /* Warm orange for a pop of color */
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            border: 2px solid #ff8c42; /* Border color matches link text */
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #ff8c42; /* Background color change on hover */
            color: white;
        }

    </style>
</head>
<body>
    <header>
        <h1>Laundry Orders</h1>
    </header>

   
    <div class="order-form">
        <h2>Place a New Order</h2>
        <form action="Laundry_Orders.php" method="POST">

            <label for="laundry_amount">Laundry Amount</label>
            <select name="laundry_amount">
                <option value="none">-----Laundry Kilograms------</option>
                <option value="30">30kg</option>
                <option value="40">40kg</option>
                <option value="50">50kg</option>
                <option value="60">60kg</option>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="In Progress">In Progress</option>
            </select>

            <button type="submit" name="submit_order" link>Submit Order</button>
       
        </form>
    </div>

    <div class="container">
        <table>
            <tr>
                <th>Laundry Amount</th>
                <th>Order Date</th>
                <th>Status</th>
            </tr>
            <?php
            

            if (isset($_POST['submit_order'])) {
                
                $laundry_amount = $_POST['laundry_amount'];
                $status = $_POST['status'];
                $order_date = date('Y-m-d'); // Set the current date

                
                $sql = "INSERT INTO Laundry_Orders (Laundry_amount, Order_date, status)
                        VALUES ( '$laundry_amount', '$order_date', '$status')";

                if ($conn->query($sql) === TRUE) {
                    echo "<p>New order has been placed successfully!</p>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            
            $sql = "SELECT * FROM Laundry_Orders";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['Order_id']}</td>
                            <td>{$row['Laundry_amount']}</td>
                            <td>{$row['Order_date']}</td>
                            <td>{$row['Status']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='no-orders'>No orders found</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>
