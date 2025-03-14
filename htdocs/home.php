<?php
session_start(); // ✅ Start session at the top

include("db_connect.php");
include("Menu.php");
include("Logout.php");

// ✅ Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php"); // Redirect to login page if not an admin
    exit();
}

// ✅ Handle status update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Order_ID']) && isset($_POST['status'])) {
    $Order_ID = intval($_POST['Order_ID']);
    $new_status = trim(htmlspecialchars($_POST['status'])); // Prevent XSS

    // ✅ Allowed statuses
    $valid_statuses = ['In Progress', 'Ready for Pick up'];
    if (!in_array($new_status, $valid_statuses)) {
        die("Error: Invalid status value.");
    }

    // ✅ Update Order Status using prepared statements
    $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE Order_ID = ?");
    $stmt->bind_param("si", $new_status, $Order_ID);

    if ($stmt->execute()) {
        echo "<script>
                alert('Status updated successfully.');
                window.location.href = 'home.php';
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
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1> <!-- ✅ Prevent XSS -->
    <h2>Current Orders</h2>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Laundry Type</th>
            <th>Order Date</th>
            <th>Quantity</th>
            <th>Cleaning Type</th>
            <th>Location</th>
            <th>Priority</th>
            <th>Status</th>
        </tr>

        <?php
        // ✅ Fetch orders with 'In Progress' status
        $sql = "SELECT Order_ID, Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
                FROM Orders 
                WHERE Status = 'In Progress' 
                ORDER BY Priority_number ASC, Order_date ASC"; // Prioritize by priority & date

        $query = mysqli_query($conn, $sql);
        if (!$query) {
            echo "Error: " . mysqli_error($conn);
        } else {
            while ($result = mysqli_fetch_assoc($query)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($result["Order_ID"]) . "</td>";
                echo "<td>" . htmlspecialchars($result["Laundry_type"]) . "</td>";
                echo "<td>" . htmlspecialchars($result["Order_date"]) . "</td>";
                echo "<td>" . htmlspecialchars($result["Laundry_quantity"]) . "</td>";
                echo "<td>" . htmlspecialchars($result["Cleaning_type"]) . "</td>";
                echo "<td>" . htmlspecialchars($result["Place"]) . "</td>";
                echo "<td>" . htmlspecialchars($result["Priority_number"]) . "</td>";
                echo "<td>
                        <form method='POST'>
                            <input type='hidden' name='Order_ID' value='" . htmlspecialchars($result['Order_ID']) . "'>
                            <select name='status' onchange='this.form.submit()'>
                                <option value='In Progress' " . ($result['Status'] == 'In Progress' ? 'selected' : '') . ">In Progress</option>
                                <option value='Ready for Pick up' " . ($result['Status'] == 'Ready for Pick up' ? 'selected' : '') . ">Ready for Pick up</option>
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
