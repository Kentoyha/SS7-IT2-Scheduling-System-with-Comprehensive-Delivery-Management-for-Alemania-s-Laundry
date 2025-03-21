<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Home1.php

include 'db_connect.php'; // Include the database connection file
include 'Menu2.php'; // Include the menu
include 'Logout.php';
session_start(); // Start the session

// Check if the user is logged in and has the correct account level
if (!isset($_SESSION['username']) ||  $_SESSION['account_level'] != "2") {
    header("Location: index.php");
    exit();
}

// Pagination settings
$results_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Retrieve total number of orders
// ✅ Corrected SQL query to count orders with 'In Progress' or 'Ready for Pick up' status
$total_query = "SELECT COUNT(*) AS total FROM Orders WHERE STATUS IN ('In Progress', 'Ready for Pick up') AND PLACE != 'Hotel'";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_results = $total_row['total'];

$total_pages = ceil($total_results / $results_per_page);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Progress Monitoring</title>
    <link rel="stylesheet" href="">
</head>
<body>
    
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
            width: 98%; /* Increased width */
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Slightly stronger shadow */
            background-color: #fff;
            border-radius: 10px; /* More rounded corners */
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px; /* Adjusted padding */
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: #444; /* Slightly darker text */
        }

        th {
            background-color: #f0f0f0; /* Slightly darker header */
            color: #333; /* Darker header text */
            font-weight: 600; /* Slightly bolder header text */
            letter-spacing: 0.8px; /* Adjusted letter spacing */
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
       
        tr:hover {
            background-color: #ebf9ff; /* Lighter hover color */
            transition: background-color 0.3s ease;
        }

        .status-btn {
            padding: 9px 14px; /* Adjusted padding */
            border: none;
            border-radius: 5px; /* More rounded buttons */
            cursor: pointer;
            font-size: 14px;
            color: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease; /* Added transform and box-shadow transition */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Added subtle shadow */
        }

        .status-btn:hover {
            transform: translateY(-2px); /* Slight lift on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* Increased shadow on hover */
        }

        .to-be-delivered { background-color: #F4A460; } /* Sandy Brown */
        .in-progress { background-color: #5cb85c; } /* Emerald Green */
        .completed { background-color: #5bc0de; } /* Turquoise */
        .ready-for-pickup { background-color: #DAA520; } /* Darker Gold */

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
    </style>
</head>
<body>
    <h1>Processing Orders</h1>
    <table>
        <thead>
            <tr>
                <th>Order Date</th>
                <th>Laundry Type</th>
                <th>Laundry Quantity</th>
                <th>Cleaning Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // ✅ Corrected SQL query to fetch orders with 'In Progress' status and not from 'Hotel'
            $sql = "SELECT * FROM Orders WHERE STATUS IN ('In Progress','Ready for Pick up') AND PLACE != 'Hotel' ORDER BY Priority_number DESC LIMIT $start_from, $results_per_page";
            $query = mysqli_query($conn, $sql);
            if (!$query) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            } else {
                while ($result = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($result["Order_date"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Laundry_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Laundry_quantity"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Cleaning_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Status"]) . "</td>";
                    echo "</tr>";
                }
            }

           
            ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php
        if ($current_page > 1) {
            echo "<a href='Home1.php?page=" . ($current_page - 1) . "'>&laquo; Prev</a>";
        }

        for ($page = 1; $page <= $total_pages; $page++) {
            echo "<a href='Home1.php?page=$page' class='" . ($page == $current_page ? "active" : "") . "'>$page</a>";
        }

        if ($current_page < $total_pages) {
            echo "<a href='Home1.php?page=" . ($current_page + 1) . "'>Next &raquo;</a>";
        }
        ?>
    </div>
</body>
</html>