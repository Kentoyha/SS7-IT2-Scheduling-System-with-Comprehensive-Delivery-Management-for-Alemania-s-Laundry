<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Delivery1.php

// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Delivery1.php

include 'db_connect.php';
include 'Menu2.php';
include 'Logout.php';

session_start();
// ✅ Check if the user is logged in and has the correct account level
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 2) {
    header("Location: login.php"); // Redirect to login page if not an admin
    exit();
}

$today = date('Y-m-d');

// Update status from "Assigned" to "Out for Delivery" if delivery date is today
$update_sql = "UPDATE Delivery SET Status = 'Out for Delivery' WHERE Delivery_date = '$today' AND Status = 'Assigned'";
$update_result = mysqli_query($conn, $update_sql);

if ($update_result) {
    echo "<script>console.log('Delivery statuses updated successfully.');</script>";
} else {
    echo "<script>console.error('Error updating delivery statuses: " . mysqli_error($conn) . "');</script>";
}

// Pagination settings
$results_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Retrieve total number of delivery records
$total_query = "SELECT COUNT(*) AS total FROM Delivery WHERE Status IN ('Out for Delivery', 'Assigned')";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_results = $total_row['total'];

$total_pages = ($total_results > 0) ? ceil($total_results / $results_per_page) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delivery Monitoring</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: black;
        }

        table {
            width: 98%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: #444;
        }

        th {
            background-color: #f0f0f0;
            color: #333;
            font-weight: bold;
            letter-spacing: 0.8px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ebf9ff;
            transition: background-color 0.3s ease;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h1 align="center">Deliveries</h1>

    <table align="center" cellspacing="0" cellpadding="10">
        <tr>
            <th>Order Date</th>
            <th>Delivery Date</th>
            <th>Delivery Staff Name</th>
            <th>Contact Info</th>
            <th>Status</th>
        </tr>
        <?php
        $sql = "SELECT Delivery.*, Orders.Order_date 
                FROM Delivery 
                INNER JOIN Orders ON Delivery.Order_ID = Orders.Order_ID 
                WHERE Delivery.Status IN ('Out for Delivery' , 'Assigned')
                ORDER BY Delivery.Delivery_date ASC
                LIMIT $start_from, $results_per_page";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Order_date'] . "</td>";
                echo "<td>" . $row['Delivery_date'] . "</td>";
                echo "<td>" . $row['Delivery_staff_name'] . "</td>";
                echo "<td>" . $row['Contact_info'] . "</td>";
                echo "<td>" . $row['Status'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found.</td></tr>";
        }
        ?>
    </table>

    <?php if ($total_results > 0) { ?>
    <div class="pagination">
        <?php
        if ($current_page > 1) {
            echo "<a href='Delivery1.php?page=" . ($current_page - 1) . "'>&laquo; Prev</a>";
        }

        for ($page = 1; $page <= $total_pages; $page++) {
            echo "<a href='Delivery1.php?page=$page' class='" . ($page == $current_page ? "active" : "") . "'>$page</a>";
        }

        if ($current_page < $total_pages) {
            echo "<a href='Delivery1.php?page=" . ($current_page + 1) . "'>Next &raquo;</a>";
        }
        ?>
    </div>
    <?php } ?>
</body>
</html>