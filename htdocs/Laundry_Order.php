<?php
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
$results_per_page = 4;

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
      /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Page Title */
h1 {
    text-align: center;
    color: black;
    margin-bottom: 20px;
    font-family: Arial, sans-serif;
    font-size: 28px;
}

/* Main Container */
.container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 37px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin: 27px auto;
    max-width: 90%;
    flex-wrap: nowrap;
    gap: 40px;
}

/* Form Section */
.form-container {
    width: 45%;
    background-color: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 0;
}

/* Table Section */
.table-container {
    width: 50%;
    background-color: #fff;
    padding: 25px; /* Fixed padding */
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    min-height: 520px; /* Add a minimum height */
}

label {
    text-align: center;
    width: 100%;
    font-size: 16px;
}

/* Table Styling */
table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
}

th, td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    color: black;
    font-size: 18px;
}

th {
    background-color: #e9ecef;
    font-weight: 600;
    font-size: 20px;
}

tr:hover {
    background-color: #ebf9ff;
}

/* Form Inputs */
input[type="text"],
input[type="number"],
select {
    width: 100%;
    padding: 11px;
    margin-bottom: 13px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 15px;
}

/* Buttons */
button, .toggle-btn {
    background-color: #007bff;
    color: white;
    padding: 14px 22px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    transition: background-color 0.3s ease;
    font-weight: 600;
}

button:hover, .toggle-btn:hover {
    transform: translateY(-2px);
}

/* Action Buttons */
.actbutton, .actdelete, .actedit {
    padding: 10px 14px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    display: inline-block;
    margin: 6px;
    transition: 0.3s ease;
    font-size: 18px;
    font-weight: 600;
}

.actedit {
    background-color:#17A2B8;
    
}

.actedit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

.actbutton {
    background-color: #D97706;
}

.actbutton:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

.actdelete {
    background-color: #dc3545;
}

.actdelete:hover {
    background-color: #c82333;
}


.tubali {
    margin-bottom: 20px;
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: fit-content;
    margin-top: 1px;
    font-weight: 600;
}

/* Pagination Styling */
.pagination {
    text-align: center;
    margin: 20px 0;
    position: fixed; /* Add this line */
    bottom: 20px;    /* Adjust as needed */
    left: 73%; /* Adjust to align with the right section */
    transform: translateX(-50%);
    width: 25%; 
    min-height: 80px;

}

.pagination a {
    display: inline-block;
    padding: 10px 18px;
    text-decoration: none;
    border: 1px solid #ddd;
    color: #333;
    font-size: 16px;
    border-radius: 5px;
    transition: all 0.3s ease;
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

    .form-container, .table-container {
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
                    <option value="Towels">Towels</option>
                    <option value="Toppers">Toppers</option>
                    <option value="Tablecloths">Tablecloths</option>
                </select>

                <label for="Laundry_Quantity">Laundry Quantity:</label>
                <input type="number" name="Laundry_Quantity" id="Laundry_Quantity" required min="1">

                <label for="Cleaning_Type">Cleaning Type:</label>
                <select name="Cleaning_Type" id="Cleaning_Type" required>
                    <option value="Wet Cleaning">Wet Cleaning</option>
                    <option value="Dry Cleaning">Dry Cleaning</option>
                </select>

                <label for="Place">Place:</label>
                <input type="text" name="Beat Nawaan" id="Place" value="Beat Naawan" readonly>

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
            <button class="tubali" onclick="window.location.href='Laundry_Order.php?show_unassigned=<?php echo $show_unassigned ? 'false' : 'true'; ?>'">
                <?php echo $show_unassigned ? 'Display Approved Orders' : 'Display Pending Orders'; ?>
            </button>

            <table>
                <thead>
                    <tr>
                        <th>Laundry Details</th>
                        <th>Status</th>
                        <?php if (!$show_unassigned) { echo "<th>Assign Staff</th>"; } ?>
                        <?php if ($show_unassigned) { echo "<th>Edit</th>"; } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['Laundry_quantity']) . " " . htmlspecialchars($row['Laundry_type']) . "<br>" . htmlspecialchars($row['Cleaning_type']) . "</td>";
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

                            if ($show_unassigned) {
                                echo "<td>";
                                echo "<a href='edit_order.php?Order_ID=" . urlencode($row['Order_ID']) . "' class='actedit'>Edit</a>";
                                echo "</td>";
                            }

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='" . ($show_unassigned ? 4 : 3) . "'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php
                if ($current_page > 1) {
                    echo '<a href="Laundry_Order.php?page=' . ($current_page - 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">&laquo; Previous</a>';
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        echo '<a href="Laundry_Order.php?page=' . $i . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '" class="active">' . $i . '</a>';
                    } else {
                        echo '<a href="Laundry_Order.php?page=' . $i . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">' . $i . '</a>';
                    }
                }

                if ($current_page < $total_pages) {
                    echo '<a href="Laundry_Order.php?page=' . ($current_page + 1) . '&show_unassigned=' . ($show_unassigned ? 'true' : 'false') . '">Next &raquo;</a>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>