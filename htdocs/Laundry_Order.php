<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Orders2.php
include("db_connect.php");
include("Menu2.php");
include("Logout.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != 2) {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Handle form submission for placing orders
if (isset($_POST['Order'])) {
    $Order_date = date("Y-m-d");
    $Laundry_Type = $_POST['Laundry_Type'];
    $Laundry_Quantity = $_POST['Laundry_Quantity'];
    $Cleaning_Type = $_POST['Cleaning_Type'];
    $Place = "Beat Naawan";
    $Priority_number = $_POST['Priority'];
    $Status = $_POST['Status'];

    $User_ID = isset($_SESSION['User_ID']) ? $_SESSION['User_ID'] : NULL;

    if ($User_ID === NULL) {
        echo "<script>alert('Error: User not found. Please log in again.');</script>";
        exit();
    }

    // Use prepared statements to prevent SQL Injection
    $sql = "INSERT INTO Laundry_Orders (Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status, User_ID)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssissisi", $Order_date, $Laundry_Type, $Laundry_Quantity, $Cleaning_Type, $Place, $Priority_number, $Status, $User_ID);
    $query = mysqli_stmt_execute($stmt);

    if ($query) {
        echo "<script>alert('Order is Placed Successfully'); window.location.href='Laundry_Order.php';</script>";
        exit(); // Ensure script stops execution after redirect
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Pagination settings
$results_per_page = 5;

// Get current page number
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = intval($_GET['page']);
} else {
    $current_page = 1;
}

// Determine filter mode
$show_unassigned = isset($_GET['show_unassigned']) && $_GET['show_unassigned'] === 'true';

// ✅ Adjust SQL query based on filter mode
if ($show_unassigned) {
    // Show only "Pending" orders excluding "Hotel"
    $sql = "SELECT Order_ID, Laundry_type, Laundry_quantity, Cleaning_type, Place, Status
            FROM Laundry_Orders
            WHERE Status = 'Pending'
            AND Place != 'Hotel'";
} else {
    // Show "Approved Orders" (To be Delivered, Ready for Pickup) excluding "Hotel"
    $sql = "SELECT Order_ID, Laundry_type, Laundry_quantity, Cleaning_type, Place, Status
            FROM Laundry_Orders
            WHERE Status IN ('To be Delivered', 'Ready for Pick up')
            AND Place != 'Hotel'";
}

// Count total number of results
$total_results = mysqli_num_rows(mysqli_query($conn, $sql));

// Calculate total number of pages
$total_pages = ceil($total_results / $results_per_page);

// Calculate the starting result for the query
$start_from = ($current_page - 1) * $results_per_page;

// Modify SQL query to include LIMIT clause for pagination
$sql .= " ORDER BY Order_ID ASC LIMIT " . $start_from . ',' . $results_per_page;

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Orders Management</title>
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
            color: black;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 45px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 30px auto;
            max-width: 93%;
        }

        .form-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 45%;
            order: -1;
            display: flex;
            flex-direction: column; /* Add this line */
            align-items: center; /* Center items horizontally */
        }

        label {
            text-align: center;
            width: 100%;
        }

        .table-container {
            width: 50%;
        }

       

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: -20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: black;
        }

        th {
            background-color: #e9ecef;
            font-weight: 600;
        }

        tr:hover {
            background-color:  #ebf9ff;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
           
        }

        button, .toggle-btn {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover, .toggle-btn:hover {
            background-color: #0056b3;
        }

        .actbutton,
        .actdelete,
        .actedit {
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            display: inline-block;
            margin: 4px;
            transition: background-color 0.3s ease;
        }

        .actedit {
            background-color: #1cc6ff;
        }

        .actedit:hover {
            transform: translateY(-2px); /* Slight lift on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* Increased shadow on hover */
        }

        .actbutton {
            background-color: #4CAF50;
        }

        .actbutton:hover {
            transform: translateY(-2px); /* Slight lift on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* Increased shadow on hover */
        }

        .actdelete {
            background-color: #dc3545;
        }

        .actdelete:hover {
            background-color: #c82333;
        }

        .toggle-btn {
            margin-bottom: 20px;
            display: block;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: stretch;
            }

            .form-container,
            .table-container {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Place Order Form -->
        <div class="form-container">
            <h2><strong>Place New Order</strong></h2>
            <form method="POST" action="">
                <label for="Laundry_Type">Laundry Type:</label>
                <select name="Laundry_Type" id="Laundry_Type" required>
                    <option value="" disabled selected>Click to Select</option>
                    <option value="Beddings">Beddings</option>
                    <option value="Curtains">Curtains</option>
                    <option value="Towel">Towels</option>
                    <option value="Topper">Toppers</option>
                    <option value="Table Cloth">Tablecloths</option>
                </select>

                <label for="Laundry_Quantity">Laundry Quantity:</label>
                <input type="number" name="Laundry_Quantity" id="Laundry_Quantity" required min="1">

                <label for="Cleaning_Type">Cleaning Type:</label>
                <select name="Cleaning_Type" id="Cleaning_Type" required>
                    <option value="Wet Cleaning">Wet Cleaning</option>
                    <option value="Dry Cleaning">Dry Cleaning</option>
                </select>

                <label for="Place">Place:</label>
                <?php
                $placeValue = isset($_POST['Place']) ? htmlspecialchars($_POST['Place']) : 'Beat Naawan';
                ?>
                <input type="text" name="Place" id="Place" value="<?php echo $placeValue; ?>" required>

                <label for="Priority">Priority Number:</label>
                <select name="Priority" id="Priority" required>
                    <option value="" disabled selected>Select Priority Number</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                </select>

                <input type="hidden" name="Status" value="Pending">

                <div style="text-align: center;">
                    <button type="submit" name="Order">Place Order</button>
                </div>
            </form>
        </div>

        <!-- Display Orders Table -->
        <div class="table-container">
            <!-- Toggle Button -->
            <div style="display: flex; justify-content: center;">
                <form method="GET">
                    <input type="hidden" name="show_unassigned" value="<?php echo $show_unassigned ? 'false' : 'true'; ?>">
                    <button type="submit" class="toggle-btn">
                        <?php echo $show_unassigned ? 'Display Approved Orders' : 'Display Pending Orders'; ?>
                    </button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Laundry Details</th>
                        <th>Status</th>
                        <!-- ✅ Show "Assign Staff" only for Approved Orders -->
                        <?php if (!$show_unassigned) {
                            echo "<th>Assign Staff</th>";
                        } ?>
                        <!-- ✅ Show "Edit" only for Pending Orders -->
                        <?php if ($show_unassigned) {
                            echo "<th>Edit</th>";
                        } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td> "  . htmlspecialchars($row['Laundry_quantity']) ." " . htmlspecialchars($row['Laundry_type']) . "<br>"  . htmlspecialchars($row['Cleaning_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";

                            
                            if (!$show_unassigned) {
                                echo "<td>";
                                if ($row["Status"] == "To be Delivered") {
                                    echo "<a href='Delivery_Staff_Assignment.php?Order_ID=" . urlencode($row["Order_ID"]) . "' class='actedit'>Delivery</a>";
                                } elseif ($row["Status"] == "Ready for Pick up") {
                                    echo "<a href='Pick_up_Staff_Assignment.php?Order_ID=" . urlencode($row["Order_ID"]) . "' class='actbutton'>Pick up</a>";
                                }
                                echo "</td>";
                            }

                            // ✅ Show "Edit" only when viewing pending orders
                            if ($show_unassigned) {
                                echo "<td>";
                                echo "<a href='edit_order.php?Order_ID=" . urlencode($row['Order_ID']) . "' class='actedit'>Edit</a>";
                                echo "</td>";
                            }

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='" . ($show_unassigned ? 6 : 5) . "'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="pagination" style="text-align: center;">
                <?php
                // Display previous page link
                if ($current_page > 1) {
                    echo '<a href="Laundry_Order.php?page=' . ($current_page - 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">&laquo; Previous</a>';
                }

                // Display page links
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        echo '<a href="#" class="active">' . $i . '</a>';
                    } else {
                        echo '<a href="Laundry_Order.php?page=' . $i . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">' . $i . '</a>';
                    }
                }

                // Display next page link
                if ($current_page < $total_pages) {
                    echo '<a href="Laundry_Order.php?page=' . ($current_page + 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">Next &raquo;</a>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>