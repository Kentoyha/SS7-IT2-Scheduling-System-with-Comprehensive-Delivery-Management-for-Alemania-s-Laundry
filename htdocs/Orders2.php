<?php
include("db_connect.php");
include("Menu2.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams</title>
    <style>
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: black;        }

       
        .add-team-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

       
        .add-team-btn {
            background-color: #4CAF50; 
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-team-btn:hover {
            background-color: #45a049;
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

        
        .actbutton, .actdelete {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .actedit {
            background-color: #1cc6ff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
        }
        .actedit:hover {
            background-color: #32b6e3;
        }

        .actbutton:hover, .actdelete:hover {
            background-color: #45a049;
        }

        .actdelete {
            background-color: #dc3545;
        }

        .actdelete:hover {
            background-color: #c82333;
        }

      
        img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
        

    </style>
</head>
<body>
    <h1>Orders</h1>

    <div class="add-team-container">
        <a href="Laundry2_Orders.php">
            <button class="add-team-btn">Place Order</button align="center">
        </a>
    </div>

    <table>
        <tr>
            
            <th>Laundry Type</th>
            <th>Laundry Quantity</th>
            <th>Cleaning Type</th>
            <th>Place</th>
            <th>Status</th>
            <th>Assign Staff</th>
        </tr>


        <?php

$sql = "SELECT Order_ID, Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
        FROM Orders 
        WHERE Status IN ('To be Delivered', 'Ready for Pick up') 
        ORDER BY Priority_number ASC";


$query = mysqli_query($conn, $sql);


if (!$query) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
} else
 {

    while ($result = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>" . $result["Laundry_type"] . "</td>";
        echo "<td>" . $result["Laundry_quantity"] . "</td>";
        echo "<td>" . $result["Cleaning_type"] . "</td>";
        echo "<td>" . $result["Place"] . "</td>";
        echo "<td>" . $result["Status"] . "</td>";
        echo "<td>";
            echo "<a href='Assign_delivery_staff.php?Order_ID=" . $result["Order_ID"] . "' class='actedit'>Delivery</a>";
            echo "<a href='Assign_pickup_staff.php?Order_ID=" . $result["Order_ID"] . "' class='actbutton'>Pick up</a>";
            echo "</td>";
        
        echo "</tr>";
    }
    echo "</table>";
}
?>


    </table>
</body>
</html>
