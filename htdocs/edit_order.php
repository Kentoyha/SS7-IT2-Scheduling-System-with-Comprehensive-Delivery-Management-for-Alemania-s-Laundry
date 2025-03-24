<?php
include("db_connect.php");
include("Menu2.php");

session_start();

// Check if user is logged in and has the correct role
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != 2) {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Validate and retrieve the Order_ID from the GET request
if (!isset($_GET['Order_ID']) || empty($_GET['Order_ID'])) {
    echo "Invalid Order ID.";
    exit();
}

$order_id = intval($_GET['Order_ID']);

// Fetch order details from the database
$sql = "SELECT * FROM Laundry_Orders WHERE Order_ID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Handle form submission (Update Order)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $laundry_type = $_POST['Laundry_Type'];
    $laundry_quantity = $_POST['Laundry_Quantity'];
    $cleaning_type = $_POST['Cleaning_Type'];
    $place = $_POST['Place'];
    $priority_number = $_POST['Priority'];
    
    // Force Status to "Pending" on edit
    $status = "Pending";
    
    $update_sql = "UPDATE Laundry_Orders SET Laundry_type=?, Laundry_quantity=?, Cleaning_type=?, Place=?, Priority_number=?, Status=? WHERE Order_ID=?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "sissisi", $laundry_type, $laundry_quantity, $cleaning_type, $place, $priority_number, $status, $order_id);

    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Order has been updated'); window.location.href='Laundry_Order.php';</script>";
        exit();
    } else {
        echo "Error updating order: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="addt.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 40%;
            background: #fff;
            padding: 30px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
        }
        button {
            width: 48%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }
        .cancel-btn {
            width: 48%;
            padding: 10px;
            background-color: #dc3545;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }
        @media (max-width: 600px) {
            .container {
                width: 90%;
            }
            .btn-group {
                flex-direction: column;
            }
            button, .cancel-btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Order</h2>
    <form method="POST">
        <label for="Laundry_Type">Laundry Type:</label>
        <select name="Laundry_Type" required>
            <option value="Beddings" <?php if ($order['Laundry_type'] == "Beddings") echo "selected"; ?>>Beddings</option>
            <option value="Curtains" <?php if ($order['Laundry_type'] == "Curtains") echo "selected"; ?>>Curtains</option>
            <option value="Towels" <?php if ($order['Laundry_type'] == "Towels") echo "selected"; ?>>Towels</option>
            <option value="Toppers" <?php if ($order['Laundry_type'] == "Toppers") echo "selected"; ?>>Toppers</option>
            <option value="Tablecloths" <?php if ($order['Laundry_type'] == "Tablecloths") echo "selected"; ?>>Tablecloths</option>

        <label for="Laundry_Quantity">Laundry Quantity:</label>
        <input type="number" name="Laundry_Quantity" value="<?php echo htmlspecialchars($order['Laundry_quantity']); ?>" required min="1">

        <label for="Cleaning_Type">Cleaning Type:</label>
        <select name="Cleaning_Type" required>
        <option value="Wet Cleaning" <?php if ($order['Cleaning_type'] == "Wet Cleaning") echo "selected"; ?>>Wet Cleaning</option>
            <option value="Dry Cleaning" <?php if ($order['Cleaning_type'] == "Dry Cleaning") echo "selected"; ?>>Dry Cleaning</option>
        </select>

        <label for="Place">Place:</label>
        <input type="text" name="Place" value="<?php echo htmlspecialchars($order['Place']); ?>" required>

        <label for="Priority">Priority Number:</label>
        <select name="Priority" required>
            <option value="3" <?php if ($order['Priority_number'] == "3") echo "selected"; ?>>3</option>
            <option value="2" <?php if ($order['Priority_number'] == "2") echo "selected"; ?>>2</option>
            <option value="1" <?php if ($order['Priority_number'] == "1") echo "selected"; ?>>1</option>
        </select>

        <!-- No status field is shown since we force it to 'Pending' on update -->

        <div class="btn-group">
            <button type="submit">Update Order</button>
            <a href="Orders2.php" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
