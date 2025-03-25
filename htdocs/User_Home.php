<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Home1.php

include 'db_connect.php'; // Include the database connection file
include 'Menu2.php'; // Include the menu
include 'Logout.php';
session_start(); // Start the session

// Check if the user is logged in and has the correct account level
if (!isset($_SESSION['User_ID']) ||  $_SESSION['account_level'] != "2") {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Pagination settings
$results_per_page = 12;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Retrieve total number of orders
// ✅ Corrected SQL query to count orders with 'In Progress' or 'Ready for Pick up' status
$total_query = "SELECT COUNT(*) AS total FROM Laundry_Orders WHERE STATUS IN ('In Progress', 'Ready for Pick up') AND PLACE != 'Hotel'";
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

     /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Page Title */
h1 {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    color: black;
    font-size: 28px; /* Enhanced for readability */
}

/* Table Styling */
table {
    width: 88%;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
}

/* Table Headers & Cells */
th, td {
    padding: 16px 20px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    color: black;
    font-size: 18px;
}

th {
    background-color: #eaeaea;
    color: #333;
    font-weight: 700;
    letter-spacing: 1px;
    font-size: 20px;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #e3f5ff;
    transition: background-color 0.3s ease-in-out;
}

/* Button Styling */
.status-btn {
    padding: 12px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    font-weight: 600;
    text-transform: uppercase;
}

.status-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

/* Status Colors */
.to-be-delivered { background-color: #F4A460; } /* SandyBrown */
.in-progress { background-color: #5cb85c; } /* Green */
.completed { background-color: #5bc0de; } /* Light Blue */
.ready-for-pickup { background-color: #DAA520; } /* GoldenRod */

/* Pagination Styling */
.pagination {
    text-align: center;
    margin-top: 20px;
    position: fixed;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
}

/* Pagination Links */
.pagination a {
    display: inline-block;
    padding: 10px 18px;
    text-decoration: none;
    color: #333;
    font-size: 16px; /* Consistent font size */
    border: 1px solid #ddd; /* Added border */
    margin: 0 4px; /* Added spacing */
    border-radius: 5px; /* Added rounded corners */
    transition: background-color 0.3s ease, color 0.3s ease;
}

.pagination a.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.pagination a:hover:not(.active) {
    background-color: #ddd;
}

/* Responsive Design */
@media (max-width: 768px) {
    table {
        width: 100%;
    }

    .status-btn {
        font-size: 14px;
        padding: 10px 14px;
    }

    .pagination a {
        padding: 8px 14px;
        font-size: 14px;
    }
}

    </style>
</head>
<body>
    <h1>Processing Orders</h1>
    <table>
        <thead>
            <tr>
                <th>Laundry Type</th>
                <th>Laundry Quantity</th>
                <th>Cleaning Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // ✅ Corrected SQL query to fetch orders with 'In Progress' status and not from 'Hotel'
            $sql = "SELECT * FROM Laundry_Orders WHERE STATUS IN ('In Progress','Ready for Pick up') AND PLACE != 'Hotel' ORDER BY Priority_number DESC LIMIT $start_from, $results_per_page";
            $query = mysqli_query($conn, $sql);
            if (!$query) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            } else {
                while ($result = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($result["Laundry_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Laundry_quantity"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Cleaning_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Status"]) . "</td>";
                    echo "</tr>";
                }
            }
                if (mysqli_num_rows($query) == 0) {
                    echo "<tr><td colspan='4'>No records found.</td></tr>";
                }
           
            ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php
        if ($current_page > 1) {
            echo "<a href='User_Home.php?page=" . ($current_page - 1) . "'>&laquo; Prev</a>";
        }

        for ($page = 1; $page <= $total_pages; $page++) {
            echo "<a href='User_Home.php?page=$page' class='" . ($page == $current_page ? "active" : "") . "'>$page</a>";
        }

        if ($current_page < $total_pages) {
            echo "<a href='User_Home.php?page=" . ($current_page + 1) . "'>Next &raquo;</a>";
        }
        ?>
    </div>
</body>
</html>