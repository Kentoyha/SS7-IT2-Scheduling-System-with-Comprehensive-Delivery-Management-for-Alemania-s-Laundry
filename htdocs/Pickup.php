<?php
include("db_connect.php");
include("Menu2.php");

session_start();

// Ensure only authorized users can access this page
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 2) {
    header("Location: login.php");
    exit();
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pickup_ID'])) {
    $Pickup_ID = intval($_POST['Pickup_ID']);
    $new_status = "Completed";
    
    // Update Pickups table
    $stmt = $conn->prepare("UPDATE Pickups SET Status = ? WHERE Pickup_ID = ?");
    $stmt->bind_param("si", $new_status, $Pickup_ID);
    
    if ($stmt->execute()) {
        // Update Orders table
        $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE Order_ID = (SELECT Order_ID FROM Pickups WHERE Pickup_ID = ?)");
        $stmt->bind_param("si", $new_status, $Pickup_ID);
        $stmt->execute();
        
        echo "<script>alert('Status updated successfully.'); window.location.href = 'Pickup.php';</script>";
        exit();
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickups</title>
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
        .complete-btn { background-color: green; color: white; border: none; padding: 6px 12px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Pickups</h1>

    <table>
        <tr>
            <th>Order Details</th>
            <th>Pickup Date</th>
            <th>Pickup Staff Name</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $sql = "SELECT Pickups.*, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place 
                FROM Pickups 
                INNER JOIN Orders ON Pickups.Order_ID = Orders.Order_ID
                WHERE Pickups.Status != 'Completed'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Laundry_quantity'] . " x " . $row['Laundry_type'] . " - " . $row['Cleaning_type'] . "<br>" . $row['Place'] . "</td>";
                echo "<td>" . $row['Date'] . "</td>";
                echo "<td>" . $row['Pickup_staff_name'] . "</td>";
                echo "<td>" . $row['Contact_info'] . "</td>";
                echo "<td>" . $row['Status'] . "</td>";
                
                if ($row['Status'] == 'Picked up') {
                    echo "<td>
                            <form method='POST'>
                                <input type='hidden' name='Pickup_ID' value='" . $row['Pickup_ID'] . "'>
                                <button type='submit' class='complete-btn'>Complete</button>
                            </form>
                          </td>";
                } else {
                    echo "<td>-</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>