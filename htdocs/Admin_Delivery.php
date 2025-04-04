<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Delivery.php

include("db_connect.php");
include("Menu.php");
include("Logout.php");

session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != 1) {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

$today = date('Y-m-d');

// Update status from "Assigned" to "Out for Delivery" if delivery date is today
$update_sql = "UPDATE Delivery SET Status = 'Out for Delivery' WHERE Delivery_date = ? AND Status = 'Assigned'";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("s", $today);
$update_stmt->execute();

if ($update_stmt->affected_rows > 0) {
    echo "<script>console.log('Delivery statuses updated successfully.');</script>";
} else {
    echo "<script>console.log('No delivery statuses updated.');</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $delivery_id = $_POST['Delivery_ID'];
    $new_status = $_POST['status'];

    // Update Delivery status
    $stmt = $conn->prepare("UPDATE Delivery SET Status = ? WHERE Delivery_ID = ?");
    $stmt->bind_param("si", $new_status, $delivery_id);

    if ($stmt->execute()) {
        // Also update the Orders status linked to this delivery
        $stmt2 = $conn->prepare("UPDATE Laundry_Orders SET Status = ? WHERE Order_ID = (SELECT Order_ID FROM Delivery WHERE Delivery_ID = ?)");
        $stmt2->bind_param("si", $new_status, $delivery_id);
        $stmt2->execute();

        echo "<script>alert('Status changed to $new_status.'); window.location.href='Admin_Delivery.php';</script>";
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Pagination settings
$results_per_page = 5;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Get the value of 'show_unassigned' from the GET request
$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

// Construct the SQL query based on the 'show_unassigned' value
if ($show_unassigned) {
    // Show unassigned deliveries (Orders with Status 'To be Delivered' or Deliveries with Status 'Assigned')
    $sql = "SELECT 
                Laundry_Orders.Order_ID,  Laundry_Orders.Order_date,  Laundry_Orders.Laundry_type,  Laundry_Orders.Laundry_quantity, 
                 Laundry_Orders.Cleaning_type,  Laundry_Orders.Place,  Laundry_Orders.Status AS OrderStatus, 
                Delivery.Delivery_ID, Delivery.Delivery_date, Delivery.Delivery_staff_name, Delivery.Contact_info, Delivery.Status AS DeliveryStatus
            FROM  Laundry_Orders 
            LEFT JOIN Delivery ON  Laundry_Orders.Order_ID = Delivery.Order_ID 
            WHERE  Laundry_Orders.Status = 'To be Delivered' OR Delivery.Status = 'Assigned'
            LIMIT $start_from, $results_per_page";

    // Get total records for pagination
    $total_query = "SELECT COUNT(*) AS total 
                    FROM  Laundry_Orders 
                    LEFT JOIN Delivery ON  Laundry_Orders.Order_ID = Delivery.Order_ID 
                    WHERE  Laundry_Orders.Status = 'To be Delivered' OR Delivery.Status = 'Assigned'";
} else {
    // Show active deliveries (Deliveries with Status not 'Delivered' or 'Assigned')
    $sql = "SELECT 
                Delivery.*, Delivery.Status AS DeliveryStatus,  Laundry_Orders.Order_ID,  Laundry_Orders.Order_date,  Laundry_Orders.Place, 
                 Laundry_Orders.Laundry_type,  Laundry_Orders.Laundry_quantity,  Laundry_Orders.Cleaning_type
            FROM Delivery 
            INNER JOIN  Laundry_Orders ON Delivery.Order_ID =  Laundry_Orders.Order_ID 
            WHERE Delivery.Status != 'Delivered' AND Delivery.Status != 'Assigned'
            LIMIT $start_from, $results_per_page";

    // Get total records for pagination
    $total_query = "SELECT COUNT(*) AS total 
                    FROM Delivery 
                    INNER JOIN  Laundry_Orders ON Delivery.Order_ID =  Laundry_Orders.Order_ID 
                    WHERE Delivery.Status != 'Delivered' AND Delivery.Status != 'Assigned'";
}

$result = mysqli_query($conn, $sql);
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
    <title>Delivery Monitoring and Management</title>
    <style>
       /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Centered Page Title */
h1 {
    text-align: center;
    font-weight: bold;
    margin-bottom: -22px;
    color: black;
    font-size: 35px;
    margin-top: -5px;
}

/* Main Container */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin: 30px auto;
    max-width: 90%;
}

/* Table Styles */
.table-container {
    width: 100%;
}

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
    padding: 16px 18px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    color: black;
    font-size: 18px;
}

th {
    background-color: #e0e0e0;
    color: #333;
    font-weight: 700;
    letter-spacing: 1px;
    font-size: 20px;
}

/* Row hover effect */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #e3f2fd;
    transition: background-color 0.3s ease;
}

/* Status Buttons */
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
    
}

.status-btn:hover {
    transform: scale(1.05);
}

/* Status Colors */
.ready-for-pickup {
    background-color: #5cb85c; /* Green */
}

.completed {
    background-color: #B8860B   ; /* Light Blue */
}

/* Toggle Button */
.toggle-btn {
    display: block;
    width: 250px;
    margin: 20px auto;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    text-align: center;
    transition: all 0.3s ease;
}

.toggle-btn:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

/* Pagination Styling */
.pagination {
    text-align: center;
    margin-top: 20px;
    min-height: 60px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
}
/* Pagination Links */
.pagination a {
    display: inline-block;
    padding: 10px 18px;
    text-decoration: none;
    border: 1px solid #ddd;
    color: #333;
    font-size: 16px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.pagination a.active {
    background-color: #007bff;
    color: white;
    border: 1px solid #007bff;
}

.pagination a:hover:not(.active) {
    background-color: #ddd;
}

/* Responsive Design */
@media (max-width: 768px) {
    table {
        width: 100%;
    }

    th, td {
        font-size: 14px;
        padding: 12px;
    }

    .toggle-btn {
        width: 200px;
        font-size: 14px;
        padding: 10px;
    }

    .pagination a {
        font-size: 14px;
        padding: 8px 14px;
    }
}


    </style>
</head>
<body>

<h1>Deliveries</h1>

<!-- Toggle Button for Showing/Hiding Unassigned Deliveries -->
<form method="GET" style="text-align: center;">
    <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
    <button type="submit" class="toggle-btn">
        <?php echo $show_unassigned ? 'Display Active Deliveries' : 'Display Assigned and To be Delivered Orders'; ?>
    </button>
</form>

<table>
    <thead>
        <tr>
            <th>Laundry Details</th>
            <?php if ($show_unassigned): ?>
                <th>Delivery Date</th>
            <?php endif; ?>
            <th>Delivery Staff</th>
            <th>Contact Info</th>
            <th>Status</th>
            <?php if (!$show_unassigned): ?>
                <th>Set Status</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($row['Laundry_quantity']) . ' ' . htmlspecialchars($row['Laundry_type']); ?>
                        <br>
                        <?php echo htmlspecialchars($row['Cleaning_type']); ?>
                    </td>
                    <?php if ($show_unassigned): ?>
                        <td>
                            <?php echo isset($row['Delivery_date']) ? htmlspecialchars(date('m/d/Y', strtotime($row['Delivery_date']))) : 'Unassigned'; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php echo htmlspecialchars($row['Delivery_staff_name'] ?? 'Unassigned'); ?>
                    </td>
                    <td>
                        <?php 
                        if ($show_unassigned && isset($row['OrderStatus']) && $row['OrderStatus'] == 'To be Delivered') {
                            echo 'Unassigned';
                        } else {
                            echo htmlspecialchars($row['Contact_info'] ?? '');
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['DeliveryStatus'] ?? $row['OrderStatus'] ?? ''); ?>
                    </td>
                    <?php if (!$show_unassigned): ?>
                        <td>
                            <?php if ($row['DeliveryStatus'] === 'Out for Delivery'): ?>
                                <form method="POST">
                                    <input type="hidden" name="Delivery_ID" value="<?php echo htmlspecialchars($row['Delivery_ID']); ?>">
                                    <button class='status-btn completed' name='status' value='Delivered'>Delivered</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?php echo $show_unassigned ? 6 : 7; ?>">No records found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Pagination links -->
<div class="pagination">
    <?php
    if ($current_page > 1) {
        echo '<a href="Admin_Delivery.php?page=' . ($current_page - 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">&laquo; Previous</a>';
    }

    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = ($i == $current_page) ? 'active' : '';
        echo '<a href="Admin_Delivery.php?page=' . $i . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '" class="' . $active_class . '">' . $i . '</a>';
    }

    if ($current_page < $total_pages) {
        echo '<a href="Admin_Delivery.php?page=' . ($current_page + 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">Next &raquo;</a>';
    }
    ?>
</div>

</body>
</html>