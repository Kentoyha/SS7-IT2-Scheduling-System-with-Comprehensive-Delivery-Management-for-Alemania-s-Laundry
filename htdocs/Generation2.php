<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Generation2.php
include 'db_connect.php';
include 'Logout.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['account_level'] != "1") {
    header("Location: login.php");
    exit();
}

// Check if Receipt_ID is set and valid
if (!isset($_GET['Receipt_ID']) || !is_numeric($_GET['Receipt_ID'])) {
    echo "<script>alert('Invalid request.'); window.location.href='Receipt.php';</script>";
    exit();
}

$receipt_id = intval($_GET['Receipt_ID']);

// Update the status to "Checked"
$update_sql = "UPDATE Receipts SET Status = 'Checked' WHERE Receipt_ID = ?";
$stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($stmt, "i", $receipt_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error updating status: " . mysqli_error($conn));
}
mysqli_stmt_close($stmt);

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
        WHERE Receipts.Receipt_ID = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $receipt_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<p style='text-align:center; color:red;'>Order not found.</p>";
    exit();
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #<?php echo htmlspecialchars($order["Order_ID"]); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 20px; /* Add padding around the content */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 98vh; /* Use min-height to ensure full height */
        }

        .receipt-container {
            background: #ffffff;
            width: 98%; /* Occupy more width */
            max-width: 800px; /* Set a maximum width */
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 12px; /* More rounded corners */
            text-align: center;
            box-sizing: border-box; /* Include padding in width calculation */
        }

        .receipt-header {
            font-size: 28px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 20px;
        }

        .receipt-info {
            text-align: left;
            font-size: 16px;
            color: #444;
            margin-bottom: 20px;
        }

        .receipt-info div {
            margin-bottom: 12px;
        }

        .receipt-footer {
            margin-top: 25px;
            font-size: 14px;
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
        Order is <strong><?php echo htmlspecialchars($order["Status"]); ?></strong>
    </div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>