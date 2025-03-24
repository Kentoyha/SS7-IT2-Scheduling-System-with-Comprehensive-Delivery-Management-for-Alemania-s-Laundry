<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Orders.php
include("db_connect.php");
include("Menu.php");
include("Logout.php");
session_start();

// Ensure only admin users can access this page
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != '1') {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Handle form submission for status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Order_ID']) && isset($_POST['status'])) {
    $Order_ID = intval($_POST['Order_ID']);
    $new_status = htmlspecialchars($_POST['status']);

    $stmt = $conn->prepare("UPDATE Laundry_Orders SET Status = ? WHERE Order_ID = ?");
    $stmt->bind_param("si", $new_status, $Order_ID);
    $stmt->execute();
    echo "<script>alert('Status has been changed'); window.location.href='Laundry_Orders.php';</script>";
    exit();
}

// Handle form submission for placing orders (from Laundry_Orders.php)
if (isset($_POST['Order'])) {
    $Order_date = date("Y-m-d");
    $Laundry_Type = $_POST['Laundry_Type'];
    $Laundry_Quantity = $_POST['Laundry_Quantity'];
    $Cleaning_Type = $_POST['Cleaning_Type'];
    $Place = "Hotel"; // Set default place to "Hotel"
    $Priority_number = $_POST['Priority'];
    $Status = $_POST['Status'];

    // Ensure User_ID is retrieved from session
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
        echo "<script>alert('Order is Placed  Successfully'); window.location.href='Laundry_Orders.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Pagination settings
$results_per_page = 4;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Retrieve total number of orders
$total_query = "SELECT COUNT(*) AS total FROM Laundry_Orders WHERE Status IN ('Pending', 'Delivered')";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_results = $total_row['total'];

$total_pages = ceil($total_results / $results_per_page);

// Fetch paginated orders
$sql = "SELECT Order_ID, Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status 
        FROM Laundry_Orders
        WHERE Status IN ('Pending', 'Delivered')
        ORDER BY Priority_number ASC
        LIMIT $start_from, $results_per_page";

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

/* Center title properly */
.order-title {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: black;
}

/* Main container layout */
.container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 40px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin: 30px auto;
    max-width: 90%;
    flex-wrap: wrap;
}

/* Left section (Form) */
.left-section {
    width: 45%;
    text-align: center;
}

/* Right section (Table) */
.right-section {
    width: 50%;
}

/* Form container styles */
.form-container {
    background-color: #f8f9fa;
    padding: 25px;
    margin-top: 10px;
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Inputs and Selects */
input[type="text"],
input[type="number"],
select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 16px;
    transition: 0.3s ease-in-out;
}

input:focus, select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

/* Buttons */
button {
    background-color: #007bff;
    color: white;
    padding: 12px 22px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
}

/* Table Styles */
.table-container {
    width: 100%;
}

table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
}

/* Table Headers & Cells */
th, td {
    padding: 16px 18px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    color: black;
    font-size: 16px;
}

th {
    background-color: #e0e0e0;
    color: #333;
    font-weight: bold;
    letter-spacing: 1px;
    font-size: 18px;
}

/* Row hover effect */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #e3f2fd;
    transition: background-color 0.3s ease;
}

/* Status Buttons */
.status-btn {
    padding: 10px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    color: white;
    transition: all 0.3s ease;
}

.status-btn:hover {
    transform: scale(1.05);
}

/* Status Colors */
.to-be-delivered {
    background-color: #DAA520; /* GoldenRod */
}

.in-progress {
    background-color: #5bc0de; /* Light Blue */
}

/* Pagination Styling */
.pagination {
    text-align: center;
    margin-top: 40px;
}

/* Pagination Links */
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

    .left-section,
    .right-section {
        width: 100%;
        margin-bottom: 20px;
    }

    th, td {
        font-size: 14px;
        padding: 12px;
    }

    button {
        font-size: 14px;
        padding: 10px 18px;
    }

    .pagination a {
        font-size: 14px;
        padding: 8px 14px;
    }
}

    </style>
</head>
<body>

<div class="container">
    <div class="left-section">
        <!-- Centered Title -->
        <h2 class="order-title">Place New Order</h2>

        <!-- Place Order Form -->
        <div class="form-container">
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
                <input type="text" name="Place" id="Place" value="Hotel" readonly>

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
    </div>

    <div class="right-section">
        <h2 class="order-title">Current Orders</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order Date</th>
                        <th>Laundry Details</th>
                        <th>Place</th>
                        <th>Priority Number</th>
                        <th>Status</th>
                        <th>Set Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$result) {
                        echo "<tr><td colspan='6'>Error: " . mysqli_error($conn) . "</td></tr>";
                    } else {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . date('m/d/Y', strtotime($row["Order_date"])) . "</td>";
                            echo "<td> " . htmlspecialchars($row["Laundry_quantity"]) . " " . htmlspecialchars($row["Laundry_type"]) . "<br> " . htmlspecialchars($row["Cleaning_type"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Place"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Priority_number"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["Status"]) . "</td>";
                            echo "<td>";
                            if ($row['Status'] == 'Pending' && ($row['Place'] != 'Hotel' && $row['Place'] != 'The Hotel')) {
                                echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($row['Order_ID']) . "'><button class='status-btn to-be-delivered' name='status' value='To be Delivered'>To be Delivered</button></form>";
                            } elseif ($row['Status'] == 'Pending' && ($row['Place'] == 'Hotel' || $row['Place'] == 'The Hotel')) {
                                echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($row['Order_ID']) . "'><button class='status-btn in-progress' name='status' value='In Progress'>In Progress</button></form>";
                            } elseif ($row['Status'] == 'Delivered') {
                                echo "<form method='POST'><input type='hidden' name='Order_ID' value='" . htmlspecialchars($row['Order_ID']) . "'><button class='status-btn in-progress' name='status' value='In Progress'>In Progress</button></form>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        if ($result->num_rows === 0) {
                            echo "<tr><td colspan='6'>No records found.</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Pagination Links -->
            <div class="pagination">
                <?php
                if ($current_page > 1) {
                    echo "<a href='Laundry_Orders.php?page=" . ($current_page - 1) . "'>&laquo; Prev</a>";
                }

                for ($page = 1; $page <= $total_pages; $page++) {
                    echo "<a href='Laundry_Orders.php?page=$page' class='" . ($page == $current_page ? "active" : "") . "'>$page</a>";
                }

                if ($current_page < $total_pages) {
                    echo "<a href='Laundry_Orders.php?page=" . ($current_page + 1) . "'>Next &raquo;</a>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
    
</body>
</html>