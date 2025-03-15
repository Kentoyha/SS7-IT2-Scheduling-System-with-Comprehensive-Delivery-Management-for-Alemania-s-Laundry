<?php
include("db_connect.php");
include("Menu.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $delivery_id = $_POST['Delivery_ID'];
    $new_status = $_POST['status'];

    // Update Delivery status
    $stmt = $conn->prepare("UPDATE Delivery SET Status = ? WHERE Delivery_ID = ?");
    $stmt->bind_param("si", $new_status, $delivery_id);

    if ($stmt->execute()) {
        // Also update the Orders status
        $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE Order_ID = (SELECT Order_ID FROM Delivery WHERE Delivery_ID = ?)");
        $stmt->bind_param("si", $new_status, $delivery_id);
        $stmt->execute();

        echo "<script>alert('Status changed to $new_status. Order is now located at Orders Page'); window.location.href='Delivery.php';</script>";
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deliveries</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        h1 { text-align: center; color: black; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background-color: #fff; }
        table th, table td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        table th { background-color: #f2f2f2; font-weight: bold; color: #333; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        table tr:hover { background-color: #f1f1f1; }
        .status-btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; color: white; }
        .ready-for-pickup { background-color: #FFD700; }
        .completed { background-color: #008CBA; }
        .toggle-btn {
            display: block;
            width: 250px;
            margin: 20px auto;
            padding: 10px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
        }
        .toggle-btn:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>Deliveries</h1>

    <!-- ✅ Toggle Button for Showing Unassigned Deliveries -->
    <form method="GET" style="text-align: center;">
        <input type="hidden" name="show_unassigned" value="<?php echo isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true' ? 'false' : 'true'; ?>">
        <button type="submit" class="toggle-btn">
            <?php echo isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true' ? 'Show Assigned Deliveries Only' : 'Display Unassigned Deliveries'; ?>
        </button>
    </form>

    <?php
    $show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

    // ✅ Modify SQL query
    if ($show_unassigned) {
        // Show all orders with "To be Delivered" status
        $sql = "SELECT Orders.Order_ID, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity, 
                       Orders.Cleaning_type, Orders.Place, Orders.Status, 
                       Delivery.Delivery_ID, Delivery.Delivery_date, Delivery.Delivery_staff_name, Delivery.Contact_info 
                FROM Orders 
                LEFT JOIN Delivery ON Orders.Order_ID = Delivery.Order_ID 
                WHERE Orders.Status = 'To be Delivered'";
    } else {
        // Show only active deliveries (excluding "Delivered")
        $sql = "SELECT Delivery.*, Orders.Order_ID, Orders.Order_date, Orders.Place, Orders.Laundry_type, 
                       Orders.Laundry_quantity, Orders.Cleaning_type 
                FROM Delivery 
                INNER JOIN Orders ON Delivery.Order_ID = Orders.Order_ID 
                WHERE Delivery.Status != 'Delivered' AND Orders.Place != 'The Hotel'";
    }

    $result = mysqli_query($conn, $sql);

    echo "<table>";
    echo "<tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Laundry Details</th>
            <th>Delivery Date</th>
            <th>Delivery Staff</th>
            <th>Contact Info</th>
            <th>Status</th>";

    // ✅ Hide "Set Status" column when show_unassigned=true
    if (!$show_unassigned) {
        echo "<th>Set Status</th>";
    }

    echo "</tr>";

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Order_ID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Order_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Laundry_quantity']) . "x " . 
                         htmlspecialchars($row['Laundry_type']) . "<br>" . 
                         htmlspecialchars($row['Cleaning_type']) . "<br>" . 
                         htmlspecialchars($row['Place']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Delivery_date'] ?? 'Unassigned') . "</td>";
            echo "<td>" . htmlspecialchars($row['Delivery_staff_name'] ?? 'Unassigned') . "</td>";
            echo "<td>" . htmlspecialchars($row['Contact_info'] ?? 'Unassigned') . "</td>";
            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";

            // ✅ Hide "Set Status" buttons when show_unassigned=true
            if (!$show_unassigned) {
                echo "<td>";
                if ($row['Status'] == 'Out for Delivery') {
                    echo "<form method='POST'>
                            <input type='hidden' name='Delivery_ID' value='" . $row['Delivery_ID'] . "'>
                            <button class='status-btn ready-for-pickup' name='status' value='Delivered'>Delivered</button>
                          </form>";
                }
                echo "</td>";
            }

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='" . ($show_unassigned ? 7 : 8) . "'>No pending deliveries.</td></tr>";
    }
    echo "</table>";
    ?>
</body>
</html>
