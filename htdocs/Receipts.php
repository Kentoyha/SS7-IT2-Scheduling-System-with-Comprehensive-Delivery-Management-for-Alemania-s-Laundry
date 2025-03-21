<?php
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
    $stmt->execute();
    $stmt->close();
    
    // Redirect to show the updated receipt details
    header("Location: Receipts.php?selected_receipt_id=$receipt_id&filter=" . ($_GET['filter'] ?? 'Unchecked'));
    exit();
}

// Function to display receipt details
function displayReceiptDetails($conn, $receipt_id) {
    $sql = "SELECT Receipts.*, Orders.Order_ID, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity,
                   Orders.Cleaning_type, Orders.Place, Orders.Status,
                   Delivery.Delivery_date, Delivery.Delivery_staff_name,
                   Pickups.Date AS Pickup_Date, Pickups.Pickup_staff_name,
                   Receipts.Date_completed, Receipts.Time_completed
            FROM Receipts
            INNER JOIN Orders ON Receipts.Order_ID = Orders.Order_ID
            LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
            LEFT JOIN Pickups ON Receipts.Pickup_ID = Pickups.Pickup_ID
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
        echo "<p><strong>Delivery Date:</strong> " . htmlspecialchars(($order["Delivery_date"] != null) ? date('m/d/Y', strtotime($order["Delivery_date"])) : "N/A") . "</p>";
        echo "<p><strong>Delivery Staff:</strong> " . htmlspecialchars($order["Delivery_staff_name"] ?? "N/A") . "</p>";
        echo "<p><strong>Pick up Date:</strong> " . htmlspecialchars(($order["Pickup_Date"] != null) ? date('m/d/Y', strtotime($order["Pickup_Date"])) : "N/A") . "</p>";
        echo "<p><strong>Pick up Staff:</strong> " . htmlspecialchars($order["Pickup_staff_name"] ?? "N/A") . "</p>";
        echo "<p><strong>Date Completed:</strong> " . htmlspecialchars(date('m/d/Y', strtotime($order["Date_completed"]))) . "</p>";
        echo "<p><strong>Time Completed:</strong> " . htmlspecialchars(date('h:i A', strtotime($order["Time_completed"]))) . "</p>";
        echo "<p class='status'><strong>Status:</strong> " . htmlspecialchars($order["Status"]) . "</p>";
        echo "</div>";
    } else {
        echo "<div class='receipt-details'><p>Select an order to view details.</p></div>";
    }
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
$sql = "SELECT Receipts.*, Orders.Order_date, Orders.Laundry_type, Orders.Laundry_quantity,
               Orders.Cleaning_type, Orders.Place, Orders.Status,
               Delivery.Delivery_date, Delivery.Delivery_staff_name,
               Pickups.Date AS Pickup_Date, Pickups.Pickup_staff_name
        FROM Receipts
        INNER JOIN Orders ON Receipts.Order_ID = Orders.Order_ID
        LEFT JOIN Delivery ON Receipts.Delivery_ID = Delivery.Delivery_ID
        LEFT JOIN Pickups ON Receipts.Pickup_ID = Pickups.Pickup_ID
        WHERE 1 $condition
        ORDER BY Orders.Order_ID DESC
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
    body {
     font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    text-align: center;
}

/* Page Title */
h1 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: black;
        }


/* Filter Buttons */
.filter-buttons {
    margin-top:-10px;
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
}

.filter-buttons button:hover {
    background-color: #0056b3;
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
            font-weight:normal ;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

.container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 30px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    max-width: 95%;
    flex-wrap: wrap;
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
}

.check-button:hover {
    background: #218838;
}

/* Order Table */
.table-container {
    width: 50%;
    min-width: 350px;
}

table {
    width: 100%;
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
    color: black;
}

th {
    background-color: #f0f0f0;
    color: #333;
    font-weight: 600;
    letter-spacing: 0.8px;
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
    transition: color 0.3s ease; /* Smooth transition for color change */
}

a:hover {
    color: #0056b3; /* Darker color on hover */
    text-decoration: none; /* Remove underline on hover */
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        align-items: center;
        padding: 15px;
    }

    .receipt-container, .table-container {
        width: 100%;
        margin-bottom: 20px;
    }

    table {
        font-size: 14px;
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
                        echo "<td><a href='?mark_checked_id=" . htmlspecialchars($row['Receipt_ID']) . "&filter=" . htmlspecialchars($filter) . "'>" . htmlspecialchars($row['Order_ID']) . "</a></td>";
                        echo "<td>" . htmlspecialchars(date('m/d/Y', strtotime($row["Order_date"]))) . "</td>";
                        echo "<td>" . htmlspecialchars(($row["Delivery_date"] != null) ? date('m/d/Y', strtotime($row["Delivery_date"])) : "N/A") . "</td>";
                        echo "<td>" . htmlspecialchars(($row["Pickup_Date"] != null) ? date('m/d/Y', strtotime($row["Pickup_Date"])) : "N/A") . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="?page=<?= $current_page - 1 ?>&filter=<?= urlencode($filter) ?>"> &laquo; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>&filter=<?= urlencode($filter) ?>" 
           class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?= $current_page + 1 ?>&filter=<?= urlencode($filter) ?>">Next &raquo;</a>
    <?php endif; ?>
</div>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>
