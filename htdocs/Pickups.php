<?php
session_start(); // ✅ Start session at the top

include("db_connect.php");
include("Menu.php");
include("Logout.php");

// ✅ Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php"); 
    exit();
}

// ✅ Handle status update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pickup_ID']) && isset($_POST['status'])) {
    $Pickup_ID = intval($_POST['Pickup_ID']);
    $new_status = trim(htmlspecialchars($_POST['status'])); 

   
    $valid_statuses = ['Ready for Pick up','On the way', 'Picked up'];
    if (!in_array($new_status, $valid_statuses)) {
        die("Error: Invalid status value.");
    }

    $stmt = $conn->prepare("UPDATE Pickups SET Status = ? WHERE Pickup_ID = ?");
    $stmt->bind_param("si", $new_status, $Pickup_ID);

    if ($stmt->execute()) {
        echo "<script>
                alert('Status updated successfully.');
                window.location.href = 'Pickups.php';
              </script>";
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
    <link rel="stylesheet" href="home.css">
    <title>Pickups Management</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1> <!-- ✅ Prevent XSS -->
    <h2>Pickups List</h2>

    <table>
        <tr>
            <th>Order Details</th>
            <th>Pickup Date</th>
            <th>Pickup Staff Name</th>
            <th>Status</th>
        </tr>

        <?php
        
        $sql = "SELECT Pickups.*, 
                       Orders.Laundry_type, 
                       Orders.Laundry_quantity, 
                       Orders.Cleaning_type, 
                       Orders.Place 
                FROM Pickups 
                INNER JOIN Orders ON Pickups.Order_ID = Orders.Order_ID 
                ORDER BY Pickups.Date ASC"; 

        $query = mysqli_query($conn, $sql);
        if (!$query) {
            echo "Error: " . mysqli_error($conn);
        } else {
            while ($row = mysqli_fetch_assoc($query)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Laundry_quantity']) . "<br>" . 
                             htmlspecialchars($row['Laundry_type']) . "<br>" . 
                             htmlspecialchars($row['Cleaning_type']) . "<br>" . 
                             htmlspecialchars($row['Place']) . "</td>";
                echo "<td>" . htmlspecialchars($row["Date"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["Pickup_staff_name"]) . "</td>";
                echo "<td>
                        <form method='POST'>
                            <input type='hidden' name='Pickup_ID' value='" . htmlspecialchars($row['Pickup_ID']) . "'>
                            <select name='status' onchange='this.form.submit()'>
                                <option value='Ready for Pick up' " . ($row['Status'] == 'Ready for Pick up' ? 'selected' : '') . ">Ready for Pick up</option>
                                <option value='In Progress' " . ($row['Status'] == 'On the way' ? 'selected' : '') . ">On the way</option>
                                <option value='Completed' " . ($row['Status'] == 'Picked up' ? 'selected' : '') . ">Picked up</option>
                            </select>
                        </form>
                      </td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</body>
</html>
