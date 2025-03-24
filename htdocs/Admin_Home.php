<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/home.php


// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/home.php
include("db_connect.php");
include("Menu.php");
include("Logout.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// ✅ Check if the user is logged in and is an admin
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != '1') {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Handle form submission for status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Order_ID']) && isset($_POST['status'])) {
    $Order_ID = intval($_POST['Order_ID']); // Ensure it's an integer
    $new_status = htmlspecialchars($_POST['status']); // Prevent XSS

    // Update Order status
    $stmt = $conn->prepare("UPDATE Laundry_Orders SET Status = ? WHERE Order_ID = ?");
    $stmt->bind_param("si", $new_status, $Order_ID);
    $stmt->execute();

    if ($new_status == 'Completed') {
        // ✅ Check if order is from "The Hotel"
        $sql = "SELECT Place FROM Laundry_Orders WHERE Order_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $Order_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if ($order['Place'] == 'Hotel' || $order['Place'] == 'The Hotel') {
            // ✅ Get Delivery and Pickup IDs if they exist
            $Delivery_ID = NULL;
            $Pickup_ID = NULL;

            $sql = "SELECT Delivery_ID FROM Delivery WHERE Order_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $Order_ID);
            $stmt->execute();
            $result = $stmt->get_result();
            $delivery = $result->fetch_assoc();
            if ($delivery) {
                $Delivery_ID = $delivery['Delivery_ID'];
            }

            $sql = "SELECT Pick_up_ID FROM Pick_ups WHERE Order_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $Order_ID);
            $stmt->execute();
            $result = $stmt->get_result();
            $pick_up = $result->fetch_assoc();
            if ($pick_up) {
                $Pick_up_ID = $pick_up['Pick_up_ID'];
            }

            // ✅ Insert into Receipts table with NULL handling
            $sql = "INSERT INTO Receipts (Order_ID, Delivery_ID, Pick_up_ID, Date_completed, Time_completed, Status) 
                    VALUES (?, ?, ?, CURDATE(), CURTIME(), 'Unchecked')";
            $stmt = $conn->prepare($sql);

            // Bind parameters correctly
            if ($Delivery_ID === NULL && $Pickup_ID === NULL) {
                $stmt->bind_param("iss", $Order_ID, $Delivery_ID, $Pick_up_ID);
            } elseif ($Delivery_ID === NULL) {
                $stmt->bind_param("isi", $Order_ID, $Delivery_ID, $Pick_up_ID);
            } elseif ($Pick_up_ID === NULL) {
                $stmt->bind_param("iis", $Order_ID, $Delivery_ID, $Pick_up_ID);
            } else {
                $stmt->bind_param("iii", $Order_ID, $Delivery_ID, $Pick_up_ID);
            }

            $stmt->execute();

            // ✅ Show a different message and redirect to Receipts.php
            echo "<script>alert('Order is now completed. A receipt has been generated.'); window.location.href='Admin_Home.php';</script>";
            exit();
        }
    }

    // ✅ If status is not 'Completed', show a different message
    echo "<script>alert('Status has been changed.'); window.location.href='Admin_Home.php';</script>";
    exit();
}

// Pagination settings
$results_per_page = 6;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Retrieve total number of orders
$total_query = "SELECT COUNT(*) AS total FROM Laundry_Orders WHERE Status = 'In Progress'";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_results = $total_row['total'];

$total_pages = ceil($total_results / $results_per_page);

// Fetch paginated orders
$sql = "SELECT Order_ID, Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
        FROM Laundry_Orders
        WHERE Status = 'In Progress'
        ORDER BY Priority_number ASC
        LIMIT $start_from, $results_per_page";

$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Progress Orders</title>
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
            color: black; 
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
                <th>Laundry Type</th>
                <th>Laundry Quantity</th>
                <th>Cleaning Type</th>
                <th>Place</th>
                <th>Priority Number</th>
                <th>Status</th>
                <th>Set Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!$query) {
                echo "<tr><td colspan='8'>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
            } else {
                while ($result = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($result["Laundry_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Laundry_quantity"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Cleaning_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Place"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Priority_number"]) . "</td>";
                    echo "<td>" . htmlspecialchars($result["Status"]) . "</td>";
                    echo "<td>";
                    if ($result['Status'] == 'In Progress' && ($result['Place'] == 'Hotel' || $result['Place'] == 'The Hotel')) {
                        echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($result['Order_ID']) . "'><button class='status-btn completed' name='status' value='Completed'>Completed</button></form>";
                    } elseif ($result['Status'] == 'In Progress') {
                        echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($result['Order_ID']) . "'><button class='status-btn ready-for-pickup' name='status' value='Ready for Pick up'>Ready for Pick up</button></form>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                if (mysqli_num_rows($query) == 0) {
                    echo "<tr><td colspan='8'>No records found.</td></tr>";
                }
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php
        if ($current_page > 1) {
            echo "<a href='Admin_Home.php?page=" . ($current_page - 1) . "'>&laquo; Prev</a>";
        }

        for ($page = 1; $page <= $total_pages; $page++) {
            echo "<a href='Admin_Home.php?page=$page' class='" . ($page == $current_page ? "active" : "") . "'>$page</a>";
        }

        if ($current_page < $total_pages) {
            echo "<a href='Admin_Home.php?page=" . ($current_page + 1) . "'>Next &raquo;</a>";
        }
        ?>
    </div>
</body>
</html>