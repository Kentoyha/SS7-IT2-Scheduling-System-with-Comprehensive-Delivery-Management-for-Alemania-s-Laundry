<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Assign_delivery_staff.php

include 'db_connect.php';
include 'Menu2.php';


session_start();

// Check if the user is not logged in
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != '2') {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
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
    <title>Staff Assignment Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-center mb-4">Staff Assignment</h2>

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
                    echo "<div class='alert alert-danger'>Order not found or error in query.</div>";
                    exit;
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "<div class='alert alert-warning'>Order ID not provided.</div>";
                exit;
            }
            ?>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>Laundry Type</th>
                        <td><?php echo htmlspecialchars($Order['Laundry_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Laundry Quantity</th>
                        <td><?php echo htmlspecialchars($Order['Laundry_quantity']); ?></td>
                    </tr>
                    <tr>
                        <th>Cleaning Type</th>
                        <td><?php echo htmlspecialchars($Order['Cleaning_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Place</th>
                        <td><?php echo htmlspecialchars($Order['Place']); ?></td>
                    </tr>
                </tbody>
            </table>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="Staff" class="form-label">Delivery Staff Name</label>
                    <input name="Staff" type="text" class="form-control" placeholder="Enter Staff Name" required>
                </div>

                <div class="mb-3">
                    <label for="Delivery_Date" class="form-label">Delivery Date</label>
                    <select name="Delivery_Date" class="form-select" required>
                        <option value="<?php echo $today; ?>">Now (<?php echo $today; ?>)</option>
                        <option value="<?php echo $tomorrow; ?>">Tomorrow (<?php echo $tomorrow; ?>)</option>
                        <option value="<?php echo $next_day; ?>">Next Day After Tomorrow (<?php echo $next_day; ?>)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="Contact" class="form-label">Contact Number (Optional)</label>
                    <input name="Contact" type="text" class="form-control" placeholder="Contact number" maxlength="11" pattern="\d{11}"
                           title="Contact number must be exactly 11 digits"
                           oninput="this.value = this.value.replace(/\D/, '')">
                </div>

                <div class="d-grid">
                    <button type="submit" name="Assign_staff" class="btn btn-success">Assign Staff</button>
                </div>
            </form>

            <?php
            if(isset($_POST['Assign_staff'])){
                $Staff = trim($_POST['Staff']);
                $Contact = trim($_POST['Contact']);
                $Delivery_Date = trim($_POST['Delivery_Date']);

                // Determine the status based on the delivery date
                if ($Delivery_Date == $today) {
                    $Status = "Out for Delivery";
                } else {
                    $Status = "Assigned";
                }

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

                    // Update Order status
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

                    echo "<script>
                            alert('Staff assigned successfully,The delivery for the Order is now $Status');
                            window.location.href='Delivery1.php';
                          </script>";
                } catch (Exception $e) {
                    // Rollback on failure
                    mysqli_rollback($conn);
                    echo "<div class='alert alert-danger'>Transaction failed: " . $e->getMessage() . "</div>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>