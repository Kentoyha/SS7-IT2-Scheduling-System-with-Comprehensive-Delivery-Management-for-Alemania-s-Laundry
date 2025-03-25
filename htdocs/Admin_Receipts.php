<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Admin_Receipts.php


error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
include 'Menu.php';
include 'Logout.php';
session_start();

if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != "1") {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Automatically update status to 'Checked' if Receipt_ID is clicked
if (isset($_GET['mark_checked_id'])) {
    $receipt_id = intval($_GET['mark_checked_id']);

    // Update status to "Checked"
    $update_sql = "UPDATE Receipts SET Status = 'Checked' WHERE Receipt_ID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $receipt_id);
    if (!$stmt->execute()) {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();

    header("Location: Admin_Receipts.php?selected_receipt_id=$receipt_id&filter=" . ($_GET['filter'] ?? 'Unchecked'));
    exit();
}

// Function to display receipt details
function displayReceiptDetails($conn, $receipt_id) {
    $sql = "SELECT Receipts.*, Laundry_Orders.Order_date, Laundry_Orders.Laundry_type, Laundry_Orders.Laundry_quantity,
                   Laundry_Orders.Cleaning_type, Laundry_Orders.Place, Laundry_Orders.Status,
                   Delivery.Delivery_date, Delivery.Delivery_staff_name,
                   Pick_ups.Date AS Pick_up_Date, Pick_ups.Pick_up_staff_name
            FROM Receipts
            INNER JOIN Laundry_Orders ON Receipts.Order_ID = Laundry_Orders.Order_ID
            LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
            LEFT JOIN Pick_ups ON Pick_ups.Pick_up_ID = Receipts.Pick_up_ID
            WHERE Receipts.Receipt_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $receipt_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        echo "<div class='receipt-details'>";
        echo "<h2>Order Receipt</h2>";
        echo "<p><strong>Order Number:</strong> " . htmlspecialchars($order["Order_ID"] ?? 'N/A') . "</p>";
        echo "<p><strong>Date Ordered:</strong> " . htmlspecialchars(date('m/d/Y', strtotime($order["Order_date"] ?? ''))) . "</p>";
        echo "<p><strong>Cleaning Type:</strong> " . htmlspecialchars($order["Cleaning_type"] ?? 'N/A') . "</p>";
        echo "<p><strong>Laundry Type:</strong> " . htmlspecialchars($order["Laundry_type"] ?? 'N/A') . "</p>";
        echo "<p><strong>Quantity:</strong> " . htmlspecialchars($order["Laundry_quantity"] ?? 'N/A') . "</p>";
        echo "<p><strong>Delivery Date:</strong> " . htmlspecialchars(($order["Delivery_date"] != null) ? date('m/d/Y', strtotime($order["Delivery_date"])) : "N/A") . "</p>";
        echo "<p><strong>Delivery Staff:</strong> " . htmlspecialchars($order["Delivery_staff_name"] ?? "N/A") . "</p>";
        echo "<p><strong>Pick up Date:</strong> " . htmlspecialchars(($order["Pick_up_Date"] != null) ? date('m/d/Y', strtotime($order["Pick_up_Date"])) : "N/A") . "</p>";
        echo "<p><strong>Pick up Staff:</strong> " . htmlspecialchars($order["Pick_up_staff_name"] ?? "N/A") . "</p>";
        echo "<p><strong>Date Completed:</strong> " . htmlspecialchars(isset($order["Date_completed"]) ? date('m/d/Y', strtotime($order["Date_completed"])) : 'N/A') . "</p>";
        echo "<p><strong>Time Completed:</strong> " . htmlspecialchars(isset($order["Time_completed"]) ? date('h:i A', strtotime($order["Time_completed"])) : 'N/A') . "</p>";
        echo "<p class='status'><strong>Status:</strong> " . htmlspecialchars($order["Status"] ?? 'N/A') . "</p>";
        echo "</div>";
    } else {
        echo "<div class='receipt-details'><p>Select an order to view details.</p></div>";
    }
    $stmt->close();
}

// Get filter (default to "Unchecked")
$filter = $_GET['filter'] ?? 'Unchecked';
$condition = $filter == 'Checked' ? "AND Receipts.Status = 'Checked'" : "AND Receipts.Status = 'Unchecked'";

// Get selected receipt
$selected_receipt_id = isset($_GET['selected_receipt_id']) ? intval($_GET['selected_receipt_id']) : null;

$results_per_page = 6;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Retrieve total number of receipts
$total_query = "SELECT COUNT(*) AS total FROM Receipts WHERE 1 $condition";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_results = $total_row['total'];

$total_pages = ceil($total_results / $results_per_page);
// Fetch receipts
$sql = "SELECT Receipts.*, 
               Laundry_Orders.Order_date, Laundry_Orders.Laundry_type, Laundry_Orders.Laundry_quantity,
               Laundry_Orders.Cleaning_type, Laundry_Orders.Place, Laundry_Orders.Status,
               Delivery.Delivery_date, Delivery.Delivery_staff_name,
               Pick_ups.Date AS Pick_up_Date, Pick_ups.Pick_up_staff_name
        FROM Receipts
        INNER JOIN Laundry_Orders ON Receipts.Order_ID = Laundry_Orders.Order_ID
        LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
        LEFT JOIN Pick_ups ON Pick_ups.Pick_up_ID = Receipts.Pick_up_ID
        WHERE 1 $condition
        ORDER BY Laundry_Orders.Order_ID DESC
        LIMIT $start_from, $results_per_page";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
   /* General Styles */
/* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    text-align: center;
    color: #333;
}

/* Page Title */
h1 {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    color: black;
    font-size: 35px;
    margin-bottom: 10px;
    margin-top: -1px;
}

/* Filter Buttons */
.filter-buttons {
    margin-top: -10px;
}

.filter-buttons button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
    margin: 5px;
    font-weight: bold;
}

.filter-buttons button:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

/* Pagination */
.pagination {
    text-align: center;
    margin-top: 20px;
    min-height: 130;
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    bottom: 0;
    left: 73%;
    transform: translateX(-50%);
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

/* Main Container */
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
    min-height: 475px;
    margin-top: 10px;
}

/* Receipt Details */
.receipt-container {
    background-color: #f8f9fa;
    padding: 20px;
    margin-top: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 45%;
    min-width: 320px;
}

.receipt-container h2 {
    text-align: center;
    color: black;
    font-size: 22px;
    font-weight: bold;
}

.receipt-details p {
    font-size: 16px;
    margin: 7px 0;
    color: #333;
}

/* Mark as Checked Button */
.check-button {
    display: block;
    width: 100%;
    background: #28a745;
    color: white;
    border: none;
    padding: 12px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: 0.3s ease-in-out;
    margin-top: 10px;
    font-weight: bold;
    text-transform: uppercase;
}

.check-button:hover {
    background: #218838;
    transform: scale(1.05);
}

/* Order Table */
.table-container {
    width: 50%;
    min-width: 350px;
}

table {
    width: 88%;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
}

/* Table Header & Cells */
th, td {
    padding: 16px 18px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    color: black;
    font-size: 18px;
}

th {
    background-color: #e0e0e0;
    color: #333;
    font-weight: bold;
    letter-spacing: 1px;
    font-size: 20px;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #ebf9ff;
    transition: background-color 0.3s ease-in-out;
}

/* Links */
a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

a:hover {
    color: #0056b3;
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        align-items: center;
        padding: 15px;
    }

    .receipt-container,
    .table-container {
        width: 100%;
        margin-bottom: 20px;
    }

    table {
        font-size: 14px;
    }

    .filter-buttons button,
    .check-button {
        font-size: 14px;
        padding: 10px;
    }

    .pagination a {
        font-size: 14px;
        padding: 8px 14px;
    }
}

</style>

<body>

    <h1>Completed Orders</h1>

    <div class="filter-buttons">
        <button onclick="window.location.href='?filter=Checked'">Display Checked</button>
        <button onclick="window.location.href='?filter=Unchecked'">Display Unchecked</button>
    </div>

    <div class="container">
        <div class="receipt-container">
            <?php
            if ($selected_receipt_id) {
                displayReceiptDetails($conn, $selected_receipt_id);
            } else {
                echo "<p>Select an order number to view details.</p>";
            }
            ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Date Ordered</th>
                        <th>Date Delivered</th>
                        <th>Date Picked up</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><a href='?mark_checked_id=" . htmlspecialchars($row['Receipt_ID']) . "&filter=" . htmlspecialchars($filter) . "&selected_receipt_id=" . htmlspecialchars($row['Receipt_ID']) . "'>" . htmlspecialchars($row['Order_ID']) . "</a></td>";
                        echo "<td>" . htmlspecialchars(date('m/d/Y', strtotime($row["Order_date"]))) . "</td>";
                        echo "<td>" . htmlspecialchars(($row["Delivery_date"] != null) ? date('m/d/Y', strtotime($row["Delivery_date"])) : "N/A") . "</td>";
                        echo "<td>" . htmlspecialchars(($row["Pick_up_Date"] != null) ? date('m/d/Y', strtotime($row["Pick_up_Date"])) : "N/A") . "</td>";
                        echo "</tr>";
                    }

                    if ($result->num_rows === 0) {
                        echo "<tr><td colspan='4'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=<?= $current_page - 1 ?>&filter=<?= urlencode($filter) ?>"> &laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?= $i ?>&filter=<?= urlencode($filter) ?>" class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages) : ?>
                    <a href="?page=<?= $current_page + 1 ?>&filter=<?= urlencode($filter) ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>

<?php $conn->close(); ?>