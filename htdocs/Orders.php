<?php
include("db_connect.php");
include("Menu.php");

session_start();

// Ensure only admin users can access this page
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != '1') {
    header("Location: login.php");
    exit();
}

// Handle form submission for status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Order_ID']) && isset($_POST['status'])) {
    $Order_ID = intval($_POST['Order_ID']); // Ensure it's an integer
    $new_status = htmlspecialchars($_POST['status']); // Prevent XSS

    // Update Order status
    $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE Order_ID = ?");
    $stmt->bind_param("si", $new_status, $Order_ID);
    $stmt->execute();
    echo "<script>alert('Status has been changed'); window.location.href='Orders.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: black;
        }

        .add-team-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .add-team-btn {
            background-color: #4CAF50; 
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-team-btn:hover {
            background-color: #45a049;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .status-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
        }

        .to-be-delivered { background-color: #FFA500; }
        .in-progress { background-color: #4CAF50; }
    </style>
</head>
<body>
    <h1>Orders</h1>

    <div class="add-team-container">
        <a href="Laundry_Orders.php">
            <button class="add-team-btn">Place Order</button>
        </a>
    </div>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Laundry Type</th>
            <th>Order Date</th>
            <th>Laundry Quantity</th>
            <th>Cleaning Type</th>
            <th>Place</th>
            <th>Priority Number</th>
            <th>Status</th>
            <th>Set Status</th>
        </tr>

        <?php
       $sql = "SELECT Order_ID, Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
       FROM Orders
       WHERE Status IN ('Pending', 'Delivered')
       ORDER BY Priority_number ASC";

        $query = mysqli_query($conn, $sql);
        if (!$query) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
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
                echo "<td>" . htmlspecialchars($result["Status"]) . "</td>";
                echo "<td>";
                if ($result['Status'] == 'Pending' && ($result['Place'] != 'Hotel' && $result['Place'] != 'The Hotel')) {
                    echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($result['Order_ID']) . "'><button class='status-btn to-be-delivered' name='status' value='To be Delivered'>To be Delivered</button></form>";
                } elseif ($result['Status'] == 'Pending' && ($result['Place'] == 'Hotel' || $result['Place'] == 'The Hotel')) {
                    echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($result['Order_ID']) . "'><button class='status-btn in-progress' name='status' value='In Progress'>In Progress</button></form>";
                } elseif ($result['Status'] == 'Delivered') {
                    echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($result['Order_ID']) . "'><button class='status-btn in-progress' name='status' value='In Progress'>In Progress</button></form>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</body>
</html>
