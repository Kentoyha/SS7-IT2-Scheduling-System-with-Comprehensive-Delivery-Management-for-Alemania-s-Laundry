<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Pickup.php

include("db_connect.php");
include("Menu2.php");
include("Logout.php");
session_start();

// Ensure only authorized users can access this page
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != 2) {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

$today = date('Y-m-d');

// ✅ Update status from "Assigned" to "On the way" if pickup date is today
$update_sql = "UPDATE Pick_ups SET Status = 'On the Way' WHERE Date = ? AND Status = 'Assigned'";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("s", $today);
$update_stmt->execute();

if ($update_stmt->affected_rows > 0) {
    echo "<script>console.log('Pick up statuses updated to On the way successfully.');</script>";
} else {
    echo "<script>console.log('No pick up statuses updated to On the way.');</script>";
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pick_up_ID'])) {
    $Pick_up_ID = intval($_POST['Pick_up_ID']);
    $new_status = "Completed";

    // Fetch Order_ID from Pickups table
    $stmt = $conn->prepare("SELECT Order_ID FROM Pick_ups WHERE Pick_up_ID = ?");
    $stmt->bind_param("i", $Pick_up_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $pick_upData = $result->fetch_assoc();

    if ($pick_upData) {
        $Order_ID = $pick_upData['Order_ID'];

        // Update Pickups table
        $stmt = $conn->prepare("UPDATE Pick_ups SET Status = ? WHERE Pick_up_ID = ?");
        $stmt->bind_param("si", $new_status, $Pick_up_ID);
        $stmt->execute();

        // Update Orders table
        $stmt = $conn->prepare("UPDATE Laundry_Orders SET Status = ? WHERE Order_ID = ?");
        $stmt->bind_param("si", $new_status, $Order_ID);
        $stmt->execute();

        // Fetch Delivery_ID from Delivery table
        $stmt = $conn->prepare("SELECT Delivery_ID FROM Delivery WHERE Order_ID = ?");
        $stmt->bind_param("i", $Order_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $deliveryData = $result->fetch_assoc();
        $Delivery_ID = $deliveryData ? $deliveryData['Delivery_ID'] : NULL;

        // Generate receipt
        $stmt = $conn->prepare("INSERT INTO Receipts (Order_ID, Delivery_ID, Pick_up_ID, Date_completed, Time_completed, Status) VALUES (?, ?, ?, CURDATE(), CURTIME(), 'Unchecked')");

        if ($Delivery_ID === NULL) {
            $stmt->bind_param("iii", $Order_ID, $Delivery_ID, $Pick_up_ID);
        } else {
            $stmt->bind_param("iii", $Order_ID, $Delivery_ID, $Pick_up_ID);
        }
        $stmt->execute();

        echo "<script>alert('Order is now completed. A receipt has been generated.'); window.location.href = 'User_Pick_ups.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: Order not found.');</script>";
    }
}

// ✅ Get the value of show_unassigned from the GET request
$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

// ✅ Pagination settings
$results_per_page = 7;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pick ups</title>
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

        th,
        td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: black;
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
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
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

        .complete-btn {
            background-color: #1cc6ff;
            color: white;
            border: none;
            padding: 15px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .complete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .styled-button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .styled-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Pick ups Management</h1>

    <!-- ✅ Add a form to toggle between showing assigned and unassigned pickups -->
    <form method="GET" style="margin-bottom: 10px; text-align: center;">
        <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
        <button type="submit" class="styled-button">
            <?php echo $show_unassigned ? 'Display Active Pick ups' : 'Display Assigned Pick ups '; ?>
        </button>
    </form>

    <?php
    // ✅ Construct the SQL query based on whether to show unassigned pickups or not
    $sql = "SELECT Pick_ups.*, Laundry_Orders.Laundry_type, Laundry_Orders.Laundry_quantity, Laundry_Orders.Cleaning_type, Laundry_Orders.Place, Pick_ups.Pick_up_staff_name
                FROM Pick_ups 
                INNER JOIN Laundry_Orders ON Pick_ups.Order_ID = Laundry_Orders.Order_ID";

    if ($show_unassigned) {
        $sql .= " WHERE Pick_ups.Status = 'Assigned'"; // Show only 'Ready for Pick up' and 'Assigned' pickups
    } else {
        $sql .= " WHERE Pick_ups.Status IN ('Picked up', 'On the way')"; // Show picked up pickups
    }

    $sql .= " ORDER BY Pick_ups.Date ASC LIMIT $start_from, $results_per_page";

    $result = mysqli_query($conn, $sql);

    // ✅ Get total records for pagination
    $total_query = "SELECT COUNT(*) AS total FROM Pick_ups INNER JOIN Laundry_Orders ON Pick_ups.Order_ID = Laundry_Orders.Order_ID";
    if ($show_unassigned) {
        $total_query .= " WHERE Pick_ups.Status IN ('Ready for Pick up', 'Assigned')";
    } else {
        $total_query .= " WHERE Pick_ups.Status IN ('Picked up', 'On the way')";
    }
    $total_result = mysqli_query($conn, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $results_per_page);

    echo "<table>";
    echo "<tr>";
    echo "<th>Order Details</th>";
    echo "<th>Pickup Date</th>";
    echo "<th>Pickup Staff Name</th>";
    echo "<th>Contact Info</th>";
    echo "<th>Status</th>";
    if (!$show_unassigned) {
        echo "<th>Set Status</th>";
    }
    echo "</tr>";

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Laundry_quantity']) . " " . htmlspecialchars($row['Laundry_type']) . " <br>" . htmlspecialchars($row['Cleaning_type']) .  "</td>";
            echo "<td>" . date('m/d/Y', strtotime($row['Date'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['Pick_up_staff_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Contact_info']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";

            if (!$show_unassigned) {
                if ($row['Status'] == 'Picked up') {
                    echo "<td>
                            <form method='POST'>
                                <input type='hidden' name='Pick_up_ID' value='" . htmlspecialchars($row['Pick_up_ID']) . "'>
                                <button type='submit' class='complete-btn'>Completed</button>
                            </form>
                          </td>";
                } else {
                    echo "<td>-</td>";
                }
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found.</td></tr>";
    }
    echo "</table>";
    ?>

    <!-- ✅ Pagination links -->
    <div class="pagination">
        <?php
        $page_url = "User_Pick_ups.php"; // Set the base page URL
        if ($current_page > 1) {
            echo "<a href='" . $page_url . "?page=" . ($current_page - 1) . "&show_unassigned=" . ($show_unassigned ? 'true' : 'false') . "'>&laquo; Previous</a>";
        }
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $current_page) {
                echo "<a class='active'>" . $i . "</a>";
            } else {
                echo "<a href='" . $page_url . "?page=" . $i . "&show_unassigned=" . ($show_unassigned ? 'true' : 'false') . "'>" . $i . "</a>";
            }
        }
        if ($current_page < $total_pages) {
            echo "<a href='" . $page_url . "?page=" . ($current_page + 1) . "&show_unassigned=" . ($show_unassigned ? 'true' : 'false') . "'>Next &raquo;</a>";
        }
        ?>
    </div>
</body>

</html>