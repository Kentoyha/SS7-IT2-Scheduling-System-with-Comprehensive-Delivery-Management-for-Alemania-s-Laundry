<?php
include 'db_connect.php';
include 'Menu2.php';

session_start();

// Check if the user is not logged in
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != '2') {
    header("Location: login.php"); 
    exit();
}

$User_ID = $_SESSION['User_ID'];
$today = date('Y-m-d'); 
$tomorrow = date('Y-m-d', strtotime('+1 day')); 
$next_day = date('Y-m-d', strtotime('+2 days'));

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Staff</title>
</head>
<body>
    <?php
    if(isset($_GET['Order_ID'])){
        $Order_id = trim($_GET['Order_ID']);
        
        // Retrieve order details
        $sql = "SELECT Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
                FROM `Orders` WHERE Order_ID = ?";
        
        // Use prepared statement
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

    <h1>Staff Assignment</h1>

    <form action="" method="post">
        <table align="center" cellspacing="0" cellpadding="10" border="1">
            <tr>
                <td>Laundry Type</td>
                <td><?php echo isset($Order['Laundry_type']) ? $Order['Laundry_type'] : ''; ?></td>
            </tr>
            <tr>
                <td>Laundry Quantity</td>
                <td><?php echo isset($Order['Laundry_quantity']) ? $Order['Laundry_quantity'] : ''; ?></td>
            </tr>
            <tr>
                <td>Cleaning Type</td>
                <td><?php echo isset($Order['Cleaning_type']) ? $Order['Cleaning_type'] : ''; ?></td>
            </tr>
            <tr>
                <td>Place</td>
                <td><?php echo isset($Order['Place']) ? $Order['Place'] : ''; ?></td>
            </tr>
            <tr>
                <td><label for="Staff">Delivery Staff Name</label></td>
                <td><input name="Staff" type="text" placeholder="Enter Staff Name" required></td>
            </tr>
            <tr>
                <td><label for="Delivery_Date">Delivery Date</label></td>
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
        $Delivery_Date = trim($_POST['Delivery_Date']); // Gets the selected date from dropdown
        $Status = "Out for Delivery"; 
        $User_ID = $_SESSION['User_ID'];
        // Start Transaction
        mysqli_begin_transaction($conn);

        try {
            // Insert into Delivery table
            $sql = "INSERT INTO `Delivery` (Order_ID, User_ID, Delivery_date, Delivery_staff_name, Contact_info, Status) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iissss", $Order_id, $User_ID, $Delivery_Date, $Staff, $Contact, $Status);
            $result1 = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if(!$result1) {
                throw new Exception("Error inserting into Delivery: " . mysqli_error($conn));
            }

            // Update Order status to "Out for Delivery"
            $update_sql = "UPDATE `Orders` SET Status = ? WHERE Order_ID = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt, "si", $Status, $Order_id);
            $result2 = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if(!$result2) {
                throw new Exception("Error updating order status: " . mysqli_error($conn));
            }

            // Commit transaction
            mysqli_commit($conn);

            echo "<script>alert('Staff assigned successfully, Order is now Out for Delivery'); window.location.href='Orders2.php';</script>";
        } catch (Exception $e) {
            // Rollback on failure
            mysqli_rollback($conn);
            echo "<script>alert('Transaction failed: " . $e->getMessage() . "');</script>";
        }
    }
    ?>

</body>
</html>
