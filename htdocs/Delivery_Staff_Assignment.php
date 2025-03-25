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
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        h2 {
            text-align: center;
            color:black;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: black;
            font-size: 16px;
        }

        .table th {
            background-color: #e9ecef;
            font-weight: 600;
            
        }

        .table tbody tr:hover {
            background-color: #ebf9ff;
            transition: background-color 0.3s ease;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
            color: #495057;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 4px;
            font-size: 14px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    
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
                        FROM `Laundry_Orders` WHERE Order_ID = ?";
                
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

                <div class="btn-container">
                    <button type="submit" name="Assign_staff" class="btn-success">Assign Staff</button>
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
                    $update_sql = "UPDATE `Laundry_Orders` SET Status = ? WHERE Order_ID = ?";
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
                            alert('Staff assigned successfully,Order status is now $Status');
                            window.location.href='User_Delivery.php';
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