<?php
session_start();
include 'db_connect.php';
include 'Menu2.php';

// Ensure the user is logged in and has a valid session
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != '2') {
    header("Location: login.php");
    exit();
}

// Fetch Admin_ID if not already set
if (!isset($_SESSION['User_ID'])) {
    $username = $_SESSION['username'];
    $query = "SELECT User_ID FROM Users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['User_ID'] = $row['User_ID'];
    } else {
        die("Error: User_ID not found for the logged-in user.");
    }
    mysqli_stmt_close($stmt);
}

$User_ID = $_SESSION['User_ID'];
$today = date('Y-m-d'); 
$tomorrow = date('Y-m-d', strtotime('+1 day')); 
$next_day = date('Y-m-d', strtotime('+2 days'));

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['Order_ID'])){
    $Order_id = trim($_GET['Order_ID']);
    
    $sql = "SELECT Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
            FROM `Orders` WHERE Order_ID = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $Order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $Order = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Order not found.'); window.location.href='Orders2.php';</script>";
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Order ID not provided.'); window.location.href='Orders2.php';</script>";
    exit();
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
                <td><?php echo htmlspecialchars($Order['Laundry_type']); ?></td>
            </tr>
            <tr>
                <td>Laundry Quantity</td>
                <td><?php echo htmlspecialchars($Order['Laundry_quantity']); ?></td>
            </tr>
            <tr>
                <td>Cleaning Type</td>
                <td><?php echo htmlspecialchars($Order['Cleaning_type']); ?></td>
            </tr>
            <tr>
                <td>Place</td>
                <td><?php echo htmlspecialchars($Order['Place']); ?></td>
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
        $Status = "On the way";

        if (!empty($Contact) && !preg_match('/^\d{11}$/', $Contact)) {
            die("<script>alert('Invalid contact number. It must be exactly 11 digits.'); window.history.back();</script>");
        }

        $Contact = !empty($Contact) ? $Contact : NULL;

        mysqli_begin_transaction($conn);

        try {
            $sql = "INSERT INTO `Pickups` (Order_ID, User_ID, Date, Pickup_staff_name, Contact_info, Status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iissss", $Order_id, $User_ID, $Date, $Staff, $Contact, $Status);
            $result1 = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if(!$result1) {
                throw new Exception("Error inserting into Pickups: " . mysqli_error($conn));
            }

            $updateOrderSql = "UPDATE `Orders` SET `Status` = ? WHERE `Order_ID` = ?";
            $stmtUpdate = mysqli_prepare($conn, $updateOrderSql);
            mysqli_stmt_bind_param($stmtUpdate, "si", $Status, $Order_id);
            $result2 = mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);

            if(!$result2) {
                throw new Exception("Error updating order status: " . mysqli_error($conn));
            }

            mysqli_commit($conn);

            echo "<script>alert('Staff assigned successfully. Status is set to $Status.'); window.location.href='Orders2.php?Order_ID=$Order_id';</script>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Transaction failed: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    mysqli_close($conn);
    ?>
</body>
</html>
