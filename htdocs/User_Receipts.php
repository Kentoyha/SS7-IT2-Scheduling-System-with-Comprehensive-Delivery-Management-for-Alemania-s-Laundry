<?php
// filepath: /workspaces/SS7-IT2-Scheduling-System-with-Comprehensive-Delivery-Management-for-Alemania-s-Laundry/htdocs/Receipt.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
include 'Menu2.php'; // For account_level 2 users (adjust if needed)
include 'Logout.php';

session_start();

// Check if user is logged in and has the right account level
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != "2") {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Function to display the details of a selected receipt
function displayReceiptDetails($conn, $receipt_id) {
    $sql = "SELECT Receipts.*, Laundry_Orders.Order_ID, Laundry_Orders.Order_date, Laundry_Orders.Laundry_type, Laundry_Orders.Laundry_quantity,
                   Laundry_Orders.Cleaning_type, Laundry_Orders.Place, Laundry_Orders.Status,
                   Delivery.Delivery_date, Delivery.Delivery_staff_name,
                   Pick_ups.Date AS Pick_up_Date, Pick_ups.Pick_up_staff_name,
                   Receipts.Date_completed, Receipts.Time_completed
            FROM Receipts
            INNER JOIN Laundry_Orders ON Receipts.Order_ID = Laundry_Orders.Order_ID
            LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
            LEFT JOIN Pick_ups ON Receipts.Pick_up_ID = Pick_ups.Pick_up_ID
            WHERE Receipts.Receipt_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $receipt_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order) {
        echo "<div class='receipt-details'>";
        echo "<h2>Order Receipt</h2>";
        echo "<p><strong>Order Number:</strong> " . htmlspecialchars($order["Order_ID"]) . "</p>";
        echo "<p><strong>Date Ordered:</strong> " . htmlspecialchars(date('m/d/Y', strtotime($order["Order_date"]))) . "</p>";
        echo "<p><strong>Cleaning Type:</strong> " . htmlspecialchars($order["Cleaning_type"]) . "</p>";
        echo "<p><strong>Laundry Type:</strong> " . htmlspecialchars($order["Laundry_type"]) . "</p>";
        echo "<p><strong>Quantity:</strong> " . htmlspecialchars($order["Laundry_quantity"]) . "</p>";
        echo "<p><strong>Delivery Date:</strong> " . htmlspecialchars(date('m/d/Y', strtotime($order["Delivery_date"]))) . "</p>";
        echo "<p><strong>Delivery Staff:</strong> " . htmlspecialchars($order["Delivery_staff_name"] ?? "N/A") . "</p>";
        echo "<p><strong>Pickup Date:</strong> " . htmlspecialchars(date ('m/d/Y', strtotime($order["Pick_up_Date"] ))) . "</p>";
        echo "<p><strong>Pickup Staff:</strong> " . htmlspecialchars($order["Pick_up_staff_name"] ?? "N/A") . "</p>";
        echo "<p><strong>Date Completed:</strong> " . htmlspecialchars(date('m/d/Y', strtotime($order["Date_completed"]))) . "</p>";
        echo "<p><strong>Time Completed:</strong> " . htmlspecialchars(date('h:i A', strtotime($order["Time_completed"]))) . "</p>";
        echo "<p class='status'><strong>Status:</strong> " . htmlspecialchars($order["Status"]) . "</p>";
        echo "</div>";
    } else {
        echo "<div class='receipt-details'><p>Select an order to view details.</p></div>";
    }
}

// --- FILTERING ---
$filter = $_GET['filter'] ?? 'Unchecked';
$condition = ($filter === 'Checked') ? "AND Receipts.Status = 'Checked'" : "AND Receipts.Status = 'Unchecked'";

// --- SELECTED RECEIPT ---
$selected_receipt_id = isset($_GET['selected_receipt_id']) ? intval($_GET['selected_receipt_id']) : null;

// If an order is clicked, automatically update status to 'Checked'
if (isset($_GET['selected_receipt_id'])) {
    $receipt_id = intval($_GET['selected_receipt_id']);
    $update_sql = "UPDATE Receipts SET Status = 'Checked' WHERE Receipt_ID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $receipt_id);
    $stmt->execute();
    $stmt->close();
}

// --- PAGINATION SETTINGS ---
$results_per_page = 6;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Get total number of filtered receipts
$total_query = "SELECT COUNT(*) AS total 
                FROM Receipts 
                INNER JOIN Laundry_Orders ON Receipts.Order_ID = Laundry_Orders.Order_ID 
                WHERE Laundry_Orders.Place != 'Hotel' 
                AND Receipts.Delivery_ID IS NOT NULL 
                AND Receipts.Pick_up_ID IS NOT NULL 
                $condition";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_results = $total_row['total'];
$total_pages = ceil($total_results / $results_per_page);

// --- FETCH RECEIPTS FOR CURRENT PAGE ---
$sql = "SELECT Receipts.*, Laundry_Orders.Order_ID, Laundry_Orders.Order_date, Laundry_Orders.Laundry_type, Laundry_Orders.Laundry_quantity,
               Laundry_Orders.Cleaning_type, Laundry_Orders.Place, Laundry_Orders.Status,
               Delivery.Delivery_date, Delivery.Delivery_staff_name,
               Pick_ups.Date AS Pick_up_Date, Pick_ups.Pick_up_staff_name
        FROM Receipts
        INNER JOIN Laundry_Orders ON Receipts.Order_ID = Laundry_Orders.Order_ID
        LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
        LEFT JOIN Pick_ups ON Receipts.Pick_up_ID = Pick_ups.Pick_up_ID
        WHERE Laundry_Orders.Place != 'Hotel' 
        AND Receipts.Delivery_ID IS NOT NULL 
        AND Receipts.Pick_up_ID IS NOT NULL 
        $condition
        ORDER BY Laundry_Orders.Order_ID DESC
        LIMIT $start_from, $results_per_page";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipts</title>
    <link rel="stylesheet" href="styles.css">
    <style>
       /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    text-align: center;
    color: #333;
}

h1 {
    text-align: center;
    font-weight: bold;
    margin-bottom: 15px;
    color: black;
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
    border-radius: 6px;
    cursor: pointer;
    margin: 5px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.filter-buttons button:hover {
    background-color: #0056b3;
}

/* Main Container */
.container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 30px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    max-width: 95%;
    flex-wrap: wrap;
}

/* Receipt Container */
.receipt-container {
    background-color: #f8f9fa;
    padding: 20px;
    margin-top: 20px;
    border-radius: 8px;
    width: 45%;
    min-width: 320px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.receipt-container h2 {
    color: black;
}

.receipt-details p {
    font-size: 16px;
    margin: 7px 0;
    color: #333;
}

/* Table Container */
.table-container {
    width: 50%;
    min-width: 350px;
}

/* Table Styling */
table {
    width: 100%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 14px 16px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    color: black;
}

th {
    background-color: #f0f0f0;
    color: #333;
    font-weight: 600;
    letter-spacing: 0.6px;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #ebf9ff;
    transition: background-color 0.3s ease;
}

/* Links */
a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    color: #0056b3;
}

/* Pagination */
.pagination {
    text-align: center;
    margin: 20px 0;
}

.pagination a {
    display: inline-block;
    padding: 10px 16px;
    text-decoration: none;
    border-radius: 6px;
    border: 1px solid #ddd;
    margin: 0 2px;
    color: #333;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.pagination a.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.pagination a:hover:not(.active) {
    background-color: #ddd;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        padding: 15px;
    }

    .receipt-container,
    .table-container {
        width: 100%;
        margin-bottom: 20px;
    }
}

    </style>
</head>

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
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><a href="?selected_receipt_id=<?= htmlspecialchars($row['Receipt_ID']) ?>&filter=<?= urlencode($filter) ?>&page=<?= $current_page ?>">
                                        <?= htmlspecialchars($row['Order_ID']) ?>
                                    </a></td>
                                <td><?= date('m/d/Y', strtotime($row['Order_date'])) ?></td>
                                <td><?= date('m/d/Y', strtotime($row['Delivery_date'])) ?></td>
                                <td><?= date('m/d/Y', strtotime($row['Pick_up_Date'])) ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=<?= $current_page - 1 ?>&filter=<?= urlencode($filter) ?>">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?= $i ?>&filter=<?= urlencode($filter) ?>"
                       class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
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