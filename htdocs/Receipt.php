<?php
include 'db_connect.php';
include 'Menu2.php';
include 'Logout.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['username']) || $_SESSION['account_level'] != "2") {
    header("Location: login.php");
    exit();
}

// Check if an order is clicked and update its status
if (isset($_GET['Receipt_ID'])) {
    $order_id = intval($_GET['Receipt_ID']);
    $update_sql = "UPDATE Receipts SET Status = 'Checked' WHERE Receipt_ID = $order_id";
    mysqli_query($conn, $update_sql);
}

// Get the filter parameter (default to "Unchecked")
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Unchecked';

// Modify SQL query based on filter
$condition = $filter == 'Checked' ? "AND Receipts.Status = 'Checked'" : "AND Receipts.Status = 'Unchecked'";

$sql = "SELECT Receipts.*, Orders.Order_ID, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity, 
               Orders.Cleaning_type, Orders.Place, 
               Delivery.Delivery_date, Delivery.Delivery_staff_name, 
               Pickups.Date AS Pickup_Date, Pickups.Pickup_staff_name
        FROM Receipts 
        INNER JOIN Orders ON Receipts.Order_ID = Orders.Order_ID
        LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
        LEFT JOIN Pickups ON Receipts.Pickup_ID = Pickups.Pickup_ID
        WHERE Orders.Place != 'Hotel' $condition
        ORDER BY Orders.Order_ID DESC";

$result = mysqli_query($conn, $sql);
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
            text-align: center;
        }

        h1 {
            color: black;
        }

        .filter-buttons {
            margin: 20px;
        }

        .filter-buttons button {
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            margin: 5px;
        }

        .checked-btn {
            background-color: #28a745;
            color: white;
        }

        .unchecked-btn {
            background-color: #dc3545;
            color: white;
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

<div class="filter-buttons">
    <button class="checked-btn" onclick="filterOrders('Checked')">Display Checked</button>
    <button class="unchecked-btn" onclick="filterOrders('Unchecked')">Display Unchecked</button>
</div>

<table>
    <tr>
        <th>Order Number</th>
        <th>Date Ordered</th>
        <th>Date Delivered</th>
        <th>Date Picked up</th>
    </tr>

    <?php
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td><a href='Generation.php?Receipt_ID=" . htmlspecialchars($row['Receipt_ID']) . "&filter=" . urlencode($filter) . "'>" . htmlspecialchars($row['Order_ID']) . "</a></td>";
            echo "<td>" . htmlspecialchars($row["Order_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Delivery_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Pickup_Date"]) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No orders found.</td></tr>";
    }
    ?>
</table>

<script>
    function filterOrders(status) {
        window.location.href = "?filter=" + status;
    }
</script>

</body>
</html>

<?php
mysqli_close($conn);
?>
