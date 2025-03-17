<?php
include 'db_connect.php'; // Database connection
include 'Logout.php';
session_start(); // Start session

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "<script>alert('Wag kang kupal tanginamo.'); window.location.href='Receipt.php';</script>";
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

// Fetch order details
$sql = "SELECT Receipts.*, Orders.Order_ID, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity, 
               Orders.Cleaning_type, Orders.Place, Orders.Status, 
               Delivery.Delivery_date, Delivery.Delivery_staff_name, 
               Pickups.Date AS Pickup_Date, Pickups.Pickup_staff_name,
               Receipts.Date_completed, Receipts.Time_completed
        FROM Receipts 
        INNER JOIN Orders ON Receipts.Order_ID = Orders.Order_ID
        LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
        LEFT JOIN Pickups ON Receipts.Pickup_ID = Pickups.Pickup_ID
        WHERE Orders.Order_ID = '$order_id'";

$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<p style='text-align:center; color:red;'>Order not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #<?php echo htmlspecialchars($order_id); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .receipt-container {
            background: #ffffff;
            width: 400px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: center;
        }

        .receipt-header {
            font-size: 22px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 10px;
        }

        .receipt-info {
            text-align: left;
            font-size: 14px;
            color: #444;
            margin-bottom: 10px;
        }

        .receipt-info div {
            margin-bottom: 8px;
        }

        .receipt-footer {
            margin-top: 15px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="receipt-header">Order Receipt</div>
    
    <div class="receipt-info">
        <div><strong>Order Number:</strong> <?php echo htmlspecialchars($order["Order_ID"]); ?></div>
        <div><strong>Date Ordered:</strong> <?php echo htmlspecialchars($order["Order_date"]); ?></div>
        <div><strong>Cleaning Type:</strong> <?php echo htmlspecialchars($order["Cleaning_type"]); ?></div>
        <div><strong>Laundry Type:</strong> <?php echo htmlspecialchars($order["Laundry_type"]); ?></div>
        <div><strong>Quantity:</strong> <?php echo htmlspecialchars($order["Laundry_quantity"]); ?></div>
        <div><strong>Pickup Date:</strong> <?php echo htmlspecialchars($order["Pickup_Date"]) ?: "N/A"; ?></div>
        <div><strong>Pickup Staff:</strong> <?php echo htmlspecialchars($order["Pickup_staff_name"]) ?: "N/A"; ?></div>
        <div><strong>Delivery Date:</strong> <?php echo htmlspecialchars($order["Delivery_date"]) ?: "N/A"; ?></div>
        <div><strong>Delivery Staff:</strong> <?php echo htmlspecialchars($order["Delivery_staff_name"]) ?: "N/A"; ?></div>
        <div><strong>Date Completed:</strong> <?php echo htmlspecialchars($order["Date_completed"]) ?: "N/A"; ?></div>
        <div><strong>Time Completed:</strong> <?php echo htmlspecialchars($order["Time_completed"]) ?: "N/A"; ?></div>
    </div>

    <div class="receipt-footer">
        Order is <?php echo htmlspecialchars($order["Status"]); ?>
    </div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
