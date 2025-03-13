<?php
include("db_connect.php");
include("Menu.php");

session_start();

// Ensure only authorized users can access this page
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 2) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickups</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        h1 { text-align: center; color: black; }
        table { width: 80%; margin: 0 auto; border-collapse: collapse; background-color: #fff; }
        table th, table td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        table th { background-color: #f2f2f2; font-weight: bold; color: #333; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        table tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
    <h1>Pickups</h1>

    <table>
        <tr>
            <th>Order Details</th>
            <th>Pickup Date</th>
            <th>Pickup Staff Name</th>
            <th>Contact Info</th>
        </tr>
        <?php
        $sql = "SELECT Pickups.*, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place 
                FROM Pickups 
                INNER JOIN Orders ON Pickups.Order_ID = Orders.Order_ID";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Laundry_quantity'] . " x " . $row['Laundry_type'] . " - " . $row['Cleaning_type'] . "<br>" . $row['Place'] . "</td>";
                echo "<td>" . $row['Pickup_date'] . "</td>";
                echo "<td>" . $row['Pickup_staff_name'] . "</td>";
                echo "<td>" . $row['Contact_info'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
