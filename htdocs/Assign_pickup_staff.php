<?php
include 'db_connect.php';
include 'Menu2.php';

// Get today's date
$today = date('Y-m-d'); 
$tomorrow = date('Y-m-d', strtotime('+1 day')); 
$next_day = date('Y-m-d', strtotime('+2 days'));

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['Order_ID'])){
    $Order_id = trim($_GET['Order_ID']);
    
    // Retrieve order details
    $sql = "SELECT Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
            FROM `Orders` WHERE Order_ID = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $Order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $Order = mysqli_fetch_assoc($result);
    } else {
        echo "Order not found or error in query: " . mysqli_error($conn);
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Order ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Staff</title>
</head>
<body>
    <h1>Staff Assignment</h1>

    <form action="" method="post">
        <table align="center" cellspacing="0" cellpadding="10" border="1">
            <tr>
                <td>Laundry Type</td>
                <td><?php echo htmlspecialchars($Order['Laundry_type'] ?? ''); ?></td>
            </tr>
            <tr>
                <td>Laundry Quantity</td>
                <td><?php echo htmlspecialchars($Order['Laundry_quantity'] ?? ''); ?></td>
            </tr>
            <tr>
                <td>Cleaning Type</td>
                <td><?php echo htmlspecialchars($Order['Cleaning_type'] ?? ''); ?></td>
            </tr>
            <tr>
                <td>Place</td>
                <td><?php echo htmlspecialchars($Order['Place'] ?? ''); ?></td>
            </tr>
            <tr>
                <td><label for="Staff">Pick up Staff Name</label></td>
                <td><input name="Staff" type="text" placeholder="Enter Staff Name" required></td>
            </tr>
            <tr>
                <td><label for="Delivery_Date">Pick up Date</label></td>
                <td>
                    <select name="Delivery_Date" required>
                        <option value="<?php echo $today; ?>">Now (<?php echo $today; ?>)</option>
                        <option value="<?php echo $tomorrow; ?>">Tomorrow (<?php echo $tomorrow; ?>)</option>
                        <option value="<?php echo $next_day; ?>">Next Day After Tomorrow (<?php echo $next_day; ?>)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="Contact">Contact Number (Optional)</label></td>
                <td>
                    <input name="Contact" type="text" placeholder="Contact number" maxlength="11" pattern="\d{11}" 
                           title="Contact number must be exactly 11 digits" oninput="this.value = this.value.replace(/\D/, '')">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="text-align: center;">
                        <button type="submit" name="Assign_staff" class="button green">Assign</button>
                    </div>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if(isset($_POST['Assign_staff'])){
        $Staff = trim($_POST['Staff']);
        $Contact = trim($_POST['Contact']); 
        $Date = trim($_POST['Delivery_Date']);  
        $Admin_ID = 1;  
        $Status = "On the way";  

        // Validate contact number (must be exactly 11 digits)
        if (!empty($Contact) && !preg_match('/^\d{11}$/', $Contact)) {
            die("<script>alert('Invalid contact number. It must be exactly 11 digits.'); window.history.back();</script>");
        }

        $Contact = !empty($Contact) ? $Contact : NULL;

        // Insert into Pickups table
        $sql = "INSERT INTO `Pickups` (Order_ID, Admin_ID, Date, Pickup_staff_name, Contact_info, Status) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iissss", $Order_id, $Admin_ID, $Date, $Staff, $Contact, $Status);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if($result){
            // ✅ Update Orders table to set Status = "On the way"
            $updateOrderSql = "UPDATE `Orders` SET `Status` = ? WHERE `Order_ID` = ?";
            $stmtUpdate = mysqli_prepare($conn, $updateOrderSql);
            mysqli_stmt_bind_param($stmtUpdate, "si", $Status, $Order_id);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);

            echo "<script>
                    alert('Staff assigned successfully. Status is set to $Status.');
                    window.location.href='Orders2.php?Order_ID=$Order_id';
                  </script>";
        } else {
            echo "<script>alert('Error in assigning staff: " . mysqli_error($conn) . "');</script>";
        }
    }

    // Close database connection
    mysqli_close($conn);
    ?>
</body>
</html>
