<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Receipt.php
include 'db_connect.php'; // Database connection
include 'Menu2.php'; // Include the menu
include 'Logout.php';
session_start(); // Start session

// Check if user is logged in and has the correct access level
if (!isset($_SESSION['username']) && $_SESSION['account_level'] != "2") {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts</title>
    <link rel="stylesheet" href="home.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: black;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<h1>Completed Orders</h1>


<?php

$sql = "SELECT Receipts.*, Orders.Order_ID, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity, 
               Orders.Cleaning_type, Orders.Place, Orders.Status, 
               Delivery.Delivery_date, Delivery.Delivery_staff_name, 
               Pickups.Date AS Pickup_Date, Pickups.Pickup_staff_name
        FROM Receipts 
        INNER JOIN Orders ON Receipts.Order_ID = Orders.Order_ID
        LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
        LEFT JOIN Pickups ON Receipts.Pickup_ID = Pickups.Pickup_ID
        WHERE Orders.Place != 'Hotel'
        ORDER BY Orders.Order_ID DESC";
       

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
} else {
?>

<table>
    <tr>
        <th>Order Number</th>
        <th>Date Ordered</th>
        <th>Date Delivered</th>
        <th>Date Picked up</th>
        <th>Date Completed</th>
        <th>Time Completed</th>
    </tr>

    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td><a href='Generation.php?order_id=" . htmlspecialchars($row['Order_ID']) . "'>" . htmlspecialchars($row['Order_ID']) . "</a></td>";
        echo "<td>" . htmlspecialchars($row["Order_date"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Delivery_date"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Pickup_Date"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Date_completed"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Time_completed"]) . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<?php
}
mysqli_close($conn);
?>

</body>
</html>