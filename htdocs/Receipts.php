<?php
include 'db_connect.php';
include 'Menu.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Retrieve completed orders with necessary details
$sql = "SELECT 
            r.Receipt_ID, 
            o.Order_ID, 
            o.Laundry_type, 
            o.Laundry_quantity, 
            o.Cleaning_type, 
            o.Place, 
            d.Delivery_ID, 
            p.Pickup_ID, 
            o.Date_completed, 
            o.Time_completed
        FROM Receipts r
        INNER JOIN Orders o ON r.Order_ID = o.Order_ID
        LEFT JOIN Delivery d ON r.Delivery_ID = d.Delivery_ID
        LEFT JOIN Pickups p ON r.Pickup_ID = p.Pickup_ID
        WHERE o.Status = 'Completed'
        ORDER BY o.Date_completed DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 90%; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .print-btn { margin-top: 20px; padding: 10px 15px; background: green; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2>Receipts</h2>

    <table>
        <thead>
            <tr>
                <th>Receipt ID</th>
                <th>Order Details</th>
                <th>Delivery ID</th>
                <th>Pickup ID</th>
                <th>Completion Date</th>
                <th>Completion Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Receipt_ID']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['Laundry_quantity']) . "x " . 
                                    htmlspecialchars($row['Laundry_type']) . "<br>" . 
                                    "Cleaning: " . htmlspecialchars($row['Cleaning_type']) . "<br>" . 
                                    "Place: " . htmlspecialchars($row['Place']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['Delivery_ID'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['Pickup_ID'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['Date_completed']); ?></td>
                    <td><?php echo htmlspecialchars($row['Time_completed']); ?></td>
                    <td>
                        <button class="print-btn" onclick="printReceipt(<?php echo $row['Receipt_ID']; ?>)">Print</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function printReceipt(receiptID) {
    window.open("print_receipt.php?Receipt_ID=" + receiptID, "_blank");
}
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
