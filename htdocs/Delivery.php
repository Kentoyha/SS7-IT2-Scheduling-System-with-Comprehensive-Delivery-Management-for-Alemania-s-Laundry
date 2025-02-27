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

        // Determine the order location for the alert message
        $stmt = $conn->prepare("SELECT Orders.Place FROM Orders INNER JOIN Delivery ON Orders.Order_ID = Delivery.Order_ID WHERE Delivery.Delivery_ID = ?");
        $stmt->bind_param("i", $delivery_id);
        $stmt->execute();
        $stmt->bind_result($order_location);
        $stmt->fetch();
        $stmt->close();

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
        table { width: 80%; margin: 0 auto; border-collapse: collapse; background-color: #fff; }
        table th, table td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        table th { background-color: #f2f2f2; font-weight: bold; color: #333; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        table tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
    <h1>Deliveries</h1>

    <table>
        <tr>
            <th>Order Date</th>
            <th>Laundry info</th>
            <th>Delivery Date</th>
            <th>Delivery <br> Staff Name</th>
            <th>Contact <br> Info</th>
            <th>Status</th>
        </tr>
        <?php
        $sql = "SELECT Delivery.*, Orders.Place, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type 
                FROM Delivery 
                INNER JOIN Orders ON Delivery.Order_ID = Orders.Order_ID 
                WHERE Delivery.Status != 'Delivered'"; // Exclude delivered orders
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Order_date'] . "</td>";
                echo "<td>" . $row['Laundry_quantity'] . "<br>" . $row['Laundry_type'] . "<br>" . $row['Cleaning_type'] . "<br>" . $row['Place'] . "</td>";
                echo "<td>" . $row['Delivery_date'] . "</td>";
                echo "<td>" . $row['Delivery_staff_name'] . "</td>";
                echo "<td>" . $row['Contact_info'] . "</td>";
                echo "<td>
                        <form method='POST'>
                            <input type='hidden' name='Delivery_ID' value='" . $row['Delivery_ID'] . "'>
                            <select name='status' onchange='this.form.submit()'>
                                <option value='Out for Delivery' " . ($row['Status'] == 'Out for Delivery' ? 'selected' : '') . ">Out for Delivery</option>
                                <option value='Delivered' " . ($row['Status'] == 'Delivered' ? 'selected' : '') . ">Delivered</option>
                            </select>
                        </form>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No pending deliveries.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
