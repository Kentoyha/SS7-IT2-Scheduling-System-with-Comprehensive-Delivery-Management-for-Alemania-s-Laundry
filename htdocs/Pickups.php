<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Pickups.php

session_start();

include("db_connect.php");
include("Menu.php");
include("Logout.php");

if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != 1) {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Update status from "Assigned" to "On the way" if pickup date is today
$today = date('Y-m-d');
$update_sql = "UPDATE Pickups SET Status = 'On the way' WHERE Date = '$today' AND Status = 'Assigned'";
$update_result = mysqli_query($conn, $update_sql);

if ($update_result) {
    echo "<script>console.log('Pickup statuses updated successfully.');</script>";
} else {
    echo "<script>console.error('Error updating pickup statuses: " . mysqli_error($conn) . "');</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pickup_ID']) && isset($_POST['status'])) {
    $Pickup_ID = intval($_POST['Pickup_ID']);
    $new_status = $_POST['status'];

    // Update Pickup status
    $stmt = $conn->prepare("UPDATE Pickups SET Status = ? WHERE Pickup_ID = ?");
    $stmt->bind_param("si", $new_status, $Pickup_ID);

    if ($stmt->execute()) {
        // Also update the Orders status
        $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE Order_ID = (SELECT Order_ID FROM Pickups WHERE Pickup_ID = ?)");
        $stmt->bind_param("si", $new_status, $Pickup_ID);
        $stmt->execute();

        echo "<script>alert('Status updated successfully.'); window.location.href = 'Pickups.php';</script>";
        exit();
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}

// Pagination settings
$results_per_page = 5;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

// Construct the SQL query based on whether to show unassigned pickups or not
$show_unassigned_condition = $show_unassigned
    ? "(Orders.Status = 'Ready for Pick up' OR Pickups.Status = 'Assigned')"
    : "(Pickups.Status IN ('On the way', 'Picked up') AND Orders.Status != 'Ready for Pick up' AND Orders.Status != 'Assigned')";

$sql = "SELECT Orders.Order_ID, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place, Orders.Status AS OrderStatus, 
            Pickups.Pickup_ID, Pickups.Date, Pickups.Pickup_staff_name, Pickups.Status AS PickupStatus, Pickups.Contact_info
        FROM Orders 
       LEFT JOIN Pickups ON Orders.Order_ID = Pickups.Order_ID
        WHERE $show_unassigned_condition
        ORDER BY 
    CASE 
        WHEN Pickups.Status = 'On the way' THEN 1
        WHEN Pickups.Status = 'Picked up' THEN 2
        WHEN Pickups.Status = 'Assigned' THEN 3
        ELSE 4
    END,
    Pickups.Date ASC
        LIMIT $start_from, $results_per_page;";

$query = mysqli_query($conn, $sql);

// Get total records for pagination
$total_query = "SELECT COUNT(*) AS total
                FROM Orders 
                LEFT JOIN Pickups ON Orders.Order_ID = Pickups.Order_ID 
                WHERE $show_unassigned_condition";

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
    <title>Pick ups Management</title>
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
            font-weight: bold; /* Slightly bolder header text */
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
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .ready-for-pickup {
            background-color: #5bc0de;
        }

        .ready-for-pickup:hover {
            transform: translateY(-2px); /* Slight lift on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* Increased shadow on hover */
        }

        .toggle-btn {
            display: block;
            width: 250px;
            margin: 20px auto;
            padding: 10px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: #0056b3;
        }

        /* Pagination styles */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h1>Pick ups Management</h1>

    <form method="GET" style="margin-bottom: 10px; text-align: center;">
        <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
        <button type="submit" class="toggle-btn">
            <?php echo $show_unassigned ? 'Display Active Pick ups' : ' Display Assigned and Ready for Pick Up Orders '; ?>
        </button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Order Details</th>
                <?php if ($show_unassigned): ?>
                <th>Pickup Date</th>
                <?php endif; ?>
                <th>Pickup Staff Name</th>
                <th>Contact Info</th>
                <th>Order Status</th>
                <?php
                $show_set_status = false;
                if ($query && mysqli_num_rows($query) > 0) {
                    mysqli_data_seek($query, 0);
                    while ($row = mysqli_fetch_assoc($query)) {
                        if ($row['OrderStatus'] !== 'Picked up') {
                            $show_set_status = true;
                            break;
                        }
                    }
                    mysqli_data_seek($query, 0); // Reset pointer to fetch data again
                }

                if (!$show_unassigned && $show_set_status) {
                    echo '<th>Set Status</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($query) {
                while ($row = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Laundry_quantity']) . " " . htmlspecialchars($row['Laundry_type']) . "<br>" . htmlspecialchars($row['Cleaning_type']) .  "</td>";
                    if ($show_unassigned){
                        echo "<td>" . htmlspecialchars(date('m/d/Y', strtotime($row["Date"]))) . "</td>";
                    }
                    echo "<td>" . htmlspecialchars($row["Pickup_staff_name"] ?? 'Not Assigned') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Contact_info"] ?? 'Not Assigned') . "</td>";
                    echo "<td>" . htmlspecialchars($row["OrderStatus"]) . "</td>";

                    if (!$show_unassigned && $show_set_status && $row['OrderStatus'] !== 'Picked up' && isset($row['Pickup_ID'])) {
                        echo "<td><form method='POST'>
                                <input type='hidden' name='Pickup_ID' value='" . htmlspecialchars($row['Pickup_ID']) . "'>
                                <button type='submit' name='status' value='Picked up' class='status-btn ready-for-pickup'>Picked up</button>
                              </form></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination links -->
    <div class="pagination">
        <?php
        if ($current_page > 1) {
            echo '<a href="Pickups.php?page=' . ($current_page - 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">&laquo; Previous</a>';
        }

        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = ($i == $current_page) ? 'active' : '';
            echo '<a href="Pickups.php?page=' . $i . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '" class="' . $active_class . '">' . $i . '</a>';
        }

        if ($current_page < $total_pages) {
            echo '<a href="Pickups.php?page=' . ($current_page + 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">Next &raquo;</a>';
        }
        ?>
    </div>
</body>
</html>