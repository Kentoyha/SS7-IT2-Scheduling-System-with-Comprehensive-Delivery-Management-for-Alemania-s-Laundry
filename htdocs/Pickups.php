<?php
session_start();

include("db_connect.php");
include("Menu.php");
include("Logout.php");

if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php"); 
    exit();
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

$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

$sql = $show_unassigned
    ? "SELECT Orders.Order_ID, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place, Orders.Status 
        FROM Orders WHERE Orders.Status = 'Ready for Pick up'"
    : "SELECT Orders.Order_ID, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place, Orders.Status, 
        Pickups.Pickup_ID, Pickups.Date, Pickups.Pickup_staff_name 
        FROM Orders INNER JOIN Pickups ON Orders.Order_ID = Pickups.Order_ID 
        WHERE Pickups.Pickup_ID IS NOT NULL AND Pickups.Status != 'Completed'
        ORDER BY 
            CASE WHEN Pickups.Status = 'On the way' THEN 1 ELSE 2 END, 
            Pickups.Date ASC";

$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Pickups Management</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <h2>Pickups List</h2>

    <form method="GET" style="margin-bottom: 10px; text-align: center;">
        <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
        <button type="submit" class="toggle-btn">
            <?php echo $show_unassigned ? 'View Active Pickups' : 'View Ready for Pick Up Orders'; ?>
        </button>
    </form>

    <table>
        <tr>
            <th>Order Details</th>
            <th>Pickup Date</th>
            <th>Pickup Staff Name</th>
            <th>Status</th>
            <?php 
            $show_set_status = false;
            while ($row = mysqli_fetch_assoc($query)) {
                if ($row['Status'] !== 'Picked up') {
                    $show_set_status = true;
                    break;
                }
            }
            mysqli_data_seek($query, 0); // Reset pointer to fetch data again

            if (!$show_unassigned && $show_set_status) { 
                echo '<th>Set Status</th>'; 
            }
            ?>
        </tr>

        <?php
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                echo "<tr><td>" . htmlspecialchars($row['Laundry_quantity']) . "x " . htmlspecialchars($row['Laundry_type']) . "<br>" . htmlspecialchars($row['Cleaning_type']) . "<br>" . htmlspecialchars($row['Place']) . "</td>";
                echo "<td>" . htmlspecialchars($row["Date"] ?? 'Not Assigned') . "</td>";
                echo "<td>" . htmlspecialchars($row["Pickup_staff_name"] ?? 'Not Assigned') . "</td>";
                echo "<td>" . htmlspecialchars($row["Status"]) . "</td>";
                
                // Only show "Set Status" column if the status is NOT "Picked up"
                if (!$show_unassigned && $show_set_status && $row['Status'] !== 'Picked up' && isset($row['Pickup_ID'])) {
                    echo "<td><form method='POST'>
                            <input type='hidden' name='Pickup_ID' value='" . htmlspecialchars($row['Pickup_ID']) . "'>
                            <button type='submit' name='status' value='Picked up' class='status-btn ready-for-pickup'>Picked up</button>
                          </form></td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
