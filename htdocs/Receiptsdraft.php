<?php 
include 'db_connect.php'; // Database connection
include 'Menu.php'; // Include the menu
include 'Logout.php';
session_start(); // Start session

// Check if user is logged in and has the correct access level
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != "1") { 
    header("Location: login.php");
    exit();
}

// Mark receipt as checked
if (isset($_GET['Receipt_ID'])) {
    $receipt_id = intval($_GET['Receipt_ID']);
    $update_sql = "UPDATE Receipts SET Status = 'Checked' WHERE Receipt_ID = $receipt_id";
    mysqli_query($conn, $update_sql);
    header("Location: " . $_SERVER['PHP_SELF']); // Reload page to reflect changes
    exit();
}

// Get the filter parameter (default to "Unchecked")
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Unchecked';
$condition = $filter == 'Checked' ? "AND Receipts.Status = 'Checked'" : "AND Receipts.Status = 'Unchecked'";

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

        .filter-buttons {
            text-align: center;
            margin: 20px 0;
        }

        .filter-buttons button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px;
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

<!-- Filter Buttons -->
<div class="filter-buttons">
    <button onclick="window.location.href='?filter=Checked'">Display Checked</button>
    <button onclick="window.location.href='?filter=Unchecked'">Display Unchecked</button>
</div>

<?php
// Main SQL Query
$sql = "SELECT Receipts.*, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity, 
               Orders.Cleaning_type, Orders.Place, Orders.Status, 
               Delivery.Delivery_date, Delivery.Delivery_staff_name, 
               Pickups.Date AS Pickup_Date, Pickups.Pickup_staff_name
        FROM Receipts
        INNER JOIN Orders ON Receipts.Order_ID = Orders.Order_ID
        LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
        LEFT JOIN Pickups ON Receipts.Pickup_ID = Pickups.Pickup_ID
        WHERE 1 $condition"; // Applying filter condition

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
} else {
?>

<table>
    <tr>
        <th>Order Details</th>
        <th>Date Ordered</th>
        <th>Date Delivered</th>
        <th>Date Picked up</th>
        <th>Action</th>
    </tr>

    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td><a href='Generation2.php?Receipt_ID=" . htmlspecialchars($row['Receipt_ID']) . "'>" . htmlspecialchars($row['Order_ID']) . "</a></td>";
        echo "<td>" . htmlspecialchars($row["Order_date"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Delivery_date"] ?? "N/A") . "</td>";
        echo "<td>" . htmlspecialchars($row["Pickup_Date"] ?? "N/A") . "</td>";
        echo "<td>";
        if ($row['Status'] === 'Unchecked') {
            echo "<a href='?Receipt_ID=" . $row['Receipt_ID'] . "' style='color: red;'>Mark as Checked</a>";
        } else {
            echo "<span style='color: green;'>Checked</span>";
        }
        echo "</td>";
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
