<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Pickups.php
session_start();

include("db_connect.php");
include("Menu.php");
include("Logout.php");

if (!isset($_SESSION['username']) && $_SESSION['account_level'] != 1) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pickup_ID']) && isset($_POST['status'])) {
    $Pickup_ID = intval($_POST['Pickup_ID']);
    $new_status = $_POST['status'];

    // Update Pickup status
    $stmt = $conn->prepare("UPDATE Pickups SET Status = ? WHERE Pickup_ID = ?");
    $stmt->bind_param("si", $new_status, $Pickup_ID);

    if ($stmt->execute()) {
        // Also update the Orders status
        $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE Order_ID = (SELECT Order_ID FROM Pickups WHERE Pickup_ID = ?)");
        $stmt->bind_param("si", $new_status, $Pickup_ID);
        $stmt->execute();

        echo "<script>alert('Status updated successfully.'); window.location.href = 'Pickups.php';</script>";
        exit();
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}

$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

$sql = $show_unassigned
    ? "SELECT Orders.Order_ID, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place, Orders.Status, 
            Pickups.Pickup_ID, Pickups.Date, Pickups.Pickup_staff_name, Pickups.Status AS PickupStatus, Pickups.Contact_info
        FROM Orders 
        INNER JOIN Pickups ON Orders.Order_ID = Pickups.Order_ID 
        WHERE Pickups.Status IN ('Ready for Pick up', 'Picked up')
        ORDER BY 
            CASE 
                WHEN Pickups.Status = 'Picked up' THEN 1
                WHEN Pickups.Status = 'Ready for Pick up' THEN 2
                ELSE 3
            END, 
            Pickups.Date ASC"
    : "SELECT Orders.Order_ID, Orders.Laundry_type, Orders.Laundry_quantity, Orders.Cleaning_type, Orders.Place, Orders.Status, 
        Pickups.Pickup_ID, Pickups.Date, Pickups.Pickup_staff_name, Pickups.Status AS PickupStatus, Pickups.Contact_info
        FROM Orders 
        INNER JOIN Pickups ON Orders.Order_ID = Pickups.Order_ID 
        WHERE Pickups.Status = 'On the way'
        ORDER BY Pickups.Date ASC";


$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickups Management</title>
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
            width: 98%; /* Increased width */
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Slightly stronger shadow */
            background-color: #fff;
            border-radius: 10px; /* More rounded corners */
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px; /* Adjusted padding */
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: #444; /* Slightly darker text */
        }

        th {
            background-color: #f0f0f0; /* Slightly darker header */
            color: #333; /* Darker header text */
            font-weight: bold; /* Slightly bolder header text */
            text-transform: uppercase;
            letter-spacing: 0.8px; /* Adjusted letter spacing */
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ebf9ff; /* Lighter hover color */
            transition: background-color 0.3s ease;
        }


        .status-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .ready-for-pickup {
            background-color: #5cb85c;
        }

        .toggle-btn {
            display: block;
            width: 250px;
            margin: 20px auto;
            padding: 10px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h1>Pickups Management</h1>

    <form method="GET" style="margin-bottom: 10px; text-align: center;">
        <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
        <button type="submit" class="toggle-btn">
            <?php echo $show_unassigned ? 'View Active Pickups' : ' View Picked up  & Ready for Pick Up Orders '; ?>
        </button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Order Details</th>
                <th>Pickup Date</th>
                <th>Pickup Staff Name</th>
                <th>Contact Info</th>
                <th>Status</th>
                <?php 
                $show_set_status = false;
                if ($query && mysqli_num_rows($query) > 0) {
                    mysqli_data_seek($query, 0);
                    while ($row = mysqli_fetch_assoc($query)) {
                        if ($row['PickupStatus'] !== 'Picked up') {
                            $show_set_status = true;
                            break;
                        }
                    }
                    mysqli_data_seek($query, 0); // Reset pointer to fetch data again
                }

                if (!$show_unassigned && $show_set_status) { 
                    echo '<th>Set Status</th>'; 
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($query) {
                while ($row = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Laundry_quantity']) . " " . htmlspecialchars($row['Laundry_type']) . "<br>" . htmlspecialchars($row['Cleaning_type']) . "<br>" . htmlspecialchars($row['Place']) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Date"] ?? 'Not Assigned') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Pickup_staff_name"] ?? 'Not Assigned') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Contact_info"] ?? 'Not Assigned') . "</td>";
                    echo "<td>" . htmlspecialchars($row["PickupStatus"]) . "</td>";
                    
                    if (!$show_unassigned && $show_set_status && $row['PickupStatus'] !== 'Picked up' && isset($row['Pickup_ID'])) {
                        echo "<td><form method='POST'>
                                <input type='hidden' name='Pickup_ID' value='" . htmlspecialchars($row['Pickup_ID']) . "'>
                                <button type='submit' name='status' value='Picked up' class='status-btn ready-for-pickup'>Picked up</button>
                              </form></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>