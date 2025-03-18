<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Pickup.php

include("db_connect.php");
include("Menu2.php");
include("Logout.php");
session_start();

// Ensure only authorized users can access this page
if (!isset($_SESSION['username']) && $_SESSION['account_level'] != 2) {
    header("Location: login.php");
    exit();
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pickup_ID'])) {
    $Pickup_ID = intval($_POST['Pickup_ID']);
    $new_status = "Completed";

    // Fetch Order_ID from Pickups table
    $stmt = $conn->prepare("SELECT Order_ID FROM Pickups WHERE Pickup_ID = ?");
    $stmt->bind_param("i", $Pickup_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $pickupData = $result->fetch_assoc();
    $Order_ID = $pickupData['Order_ID'];

    if ($Order_ID) {
        // Update Pickups table
        $stmt = $conn->prepare("UPDATE Pickups SET Status = ? WHERE Pickup_ID = ?");
        $stmt->bind_param("si", $new_status, $Pickup_ID);
        $stmt->execute();

        // Update Orders table
        $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE Order_ID = ?");
        $stmt->bind_param("si", $new_status, $Order_ID);
        $stmt->execute();

        // Fetch Delivery_ID from Delivery table
        $stmt = $conn->prepare("SELECT Delivery_ID FROM Delivery WHERE Order_ID = ?");
        $stmt->bind_param("i", $Order_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $deliveryData = $result->fetch_assoc();
        $Delivery_ID = $deliveryData ? $deliveryData['Delivery_ID'] : NULL;

        // Generate receipt
        $stmt = $conn->prepare("INSERT INTO Receipts (Order_ID, Delivery_ID, Pickup_ID, Date_completed, Time_completed) VALUES (?, ?, ?, CURDATE(), CURTIME())");
        $stmt->bind_param("iii", $Order_ID, $Delivery_ID, $Pickup_ID);
        $stmt->execute();

        echo "<script>alert('Order is now completed. A receipt has been generated.'); window.location.href = 'Receipt.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: Order not found.');</script>";
    }
}

// ✅ Get the value of show_unassigned from the GET request
$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickups</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: black;
            text-transform: uppercase;
        }

        table {
            width: 98%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: #444;
        }

        th {
            background-color: #f0f0f0;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ebf9ff;
            transition: background-color 0.3s ease;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }
        }
        .complete-btn {
             background-color: #007bff; /* Blue color */
             color: white;
             border: none;
             padding: 15px 15px;
             cursor: pointer;
             border-radius: 5px; /* Rounded corners */
             transition: background-color 0.3s ease; /* Smooth transition for hover effect */
        }

        .complete-btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .styled-button {
            background-color: #3498db; /* A different shade of blue */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .styled-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Pickups</h1>

    <!-- ✅ Add a form to toggle between showing assigned and unassigned pickups -->
    <form method="GET" style="margin-bottom: 10px; text-align: center;">
        <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
        <button type="submit" class="styled-button">
            <?php echo $show_unassigned ? 'Show Assigned Pickups' : 'Show Pickups On The Way'; ?>
        </button>
    </form>

    <?php
        // ✅ Construct the SQL query based on whether to show unassigned pickups or not
        $sql = "SELECT Pickups.*, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place 
                FROM Pickups 
                INNER JOIN Orders ON Pickups.Order_ID = Orders.Order_ID";

        if ($show_unassigned) {
            $sql .= " WHERE Pickups.Status = 'On the way'"; // Show only 'On the way' pickups
        } else {
            $sql .= " WHERE Pickups.Status = 'Picked up'"; // Show assigned pickups
        }

        $sql .= " ORDER BY Pickups.Date ASC";

        $result = mysqli_query($conn, $sql);
    
    echo "<table>";
    echo "<tr>";
    echo "<th>Order Details</th>";
    echo "<th>Pickup Date</th>";
    echo "<th>Pickup Staff Name</th>";
    echo "<th>Contact Info</th>";
    echo "<th>Status</th>";
    if (!$show_unassigned) {
        echo "<th>Action</th>";
    }
    echo "</tr>";

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Laundry_quantity']) . " x " . htmlspecialchars($row['Laundry_type']) . " - " . htmlspecialchars($row['Cleaning_type']) . "<br>" . htmlspecialchars($row['Place']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Pickup_staff_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Contact_info']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
            
            if (!$show_unassigned) {
                if ($row['Status'] == 'Picked up') {
                    echo "<td>
                            <form method='POST'>
                                <input type='hidden' name='Pickup_ID' value='" . htmlspecialchars($row['Pickup_ID']) . "'>
                                <button type='submit' class='complete-btn'>Completed</button>
                            </form>
                          </td>";
                } else {
                    echo "<td>-</td>";
                }
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found.</td></tr>";
    }
    echo "</table>";
    ?>
</body>
</html>