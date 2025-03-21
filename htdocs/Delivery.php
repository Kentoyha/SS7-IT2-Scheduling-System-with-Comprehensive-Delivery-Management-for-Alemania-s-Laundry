<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Delivery.php

include("db_connect.php");
include("Menu.php");
include("Logout.php");

session_start();

// ✅ Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php");
    exit();
}

$today = date('Y-m-d');

// ✅ Update status from "Assigned" to "Out for Delivery" if delivery date is today
$update_sql = "UPDATE Delivery 
               SET Status = 'Out for Delivery' 
               WHERE Delivery_date = ? 
               AND Status = 'Assigned'";

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

    // ✅ Update Delivery status
    $stmt = $conn->prepare("UPDATE Delivery SET Status = ? WHERE Delivery_ID = ?");
    $stmt->bind_param("si", $new_status, $delivery_id);

    if ($stmt->execute()) {
        // ✅ Also update the Orders status linked to this delivery
        $stmt2 = $conn->prepare("
            UPDATE Orders 
            SET Status = ? 
            WHERE Order_ID = (
                SELECT Order_ID FROM Delivery WHERE Delivery_ID = ?
            )
        ");
        $stmt2->bind_param("si", $new_status, $delivery_id);
        $stmt2->execute();

        echo "<script>alert('Status changed to $new_status. Order is now located at the Laundry Orders page'); window.location.href='Delivery.php';</script>";
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// ✅ Get the value of 'show_unassigned' from the GET request
$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

// ✅ Construct the SQL query based on the 'show_unassigned' value
if ($show_unassigned) {
    // ✅ Show unassigned deliveries (Orders with Status 'To be Delivered' or Deliveries with Status 'Assigned')
    $sql = "
        SELECT 
            Orders.Order_ID, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity, 
            Orders.Cleaning_type, Orders.Place, Orders.Status AS OrderStatus, 
            Delivery.Delivery_ID, Delivery.Delivery_date, Delivery.Delivery_staff_name, Delivery.Contact_info, Delivery.Status AS DeliveryStatus
        FROM Orders 
        LEFT JOIN Delivery ON Orders.Order_ID = Delivery.Order_ID 
        WHERE Orders.Status = 'To be Delivered' OR Delivery.Status = 'Assigned'
    ";
} else {
    // ✅ Show active deliveries (Deliveries with Status not 'Delivered' or 'Assigned')
    $sql = "
        SELECT Delivery.*, Delivery.Status AS DeliveryStatus, Orders.Order_ID, Orders.Order_date, Orders.Place, 
               Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type 
        FROM Delivery 
        INNER JOIN Orders ON Delivery.Order_ID = Orders.Order_ID 
        WHERE Delivery.Status != 'Delivered' AND Delivery.Status != 'Assigned'
    ";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Monitoring and Management</title>
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
        .status-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            transition: background-color 0.3s ease;
        }
        .status-btn :hover {
            transform: translateY(-2px); /* Slight lift on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* Increased shadow on hover */
        }
        .ready-for-pickup {
            background-color: #5cb85c;
        }
        .completed {
            background-color: #5bc0de;
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
        @media (max-width: 768px) {
            table {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<h1>Deliveries</h1>

<!-- ✅ Toggle Button for Showing/Hiding Unassigned Deliveries -->
<form method="GET" style="text-align: center;">
    <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
    <button type="submit" class="toggle-btn">
        <?php echo $show_unassigned ? 'Display Active Deliveries' : 'Display Assigned and To be Delivered Orders'; ?>
    </button>
</form>

<table>
    <thead>
        <tr>
            <th>Order Date</th>
            <th>Laundry Details</th>
            <th>Delivery Date</th>
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
                    <td><?php echo htmlspecialchars($row['Order_date']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['Laundry_quantity']) . 'x ' . htmlspecialchars($row['Laundry_type']); ?>
                        <br>
                        <?php echo htmlspecialchars($row['Cleaning_type']); ?>
                        <br>
                        <?php echo htmlspecialchars($row['Place']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['Delivery_date'] ?? 'Unassigned'); ?></td>
                    <td><?php echo htmlspecialchars($row['Delivery_staff_name'] ?? 'Unassigned'); ?></td>
                    <td><?php echo htmlspecialchars($row['Contact_info'] ?? 'Unassigned'); ?></td>
                    <td>
                        <?php
                        if ($show_unassigned) {
                            // ✅ Showing unassigned deliveries (show DeliveryStatus if exists or fallback to OrderStatus)
                            echo htmlspecialchars($row['DeliveryStatus'] ?? $row['OrderStatus']);
                        } else {
                            // ✅ Showing active deliveries (show DeliveryStatus)
                            echo htmlspecialchars($row['DeliveryStatus']);
                        }
                        ?>
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
                <td colspan="<?php echo $show_unassigned ? 6 : 7; ?>">No pending deliveries.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
