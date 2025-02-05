<?php 
session_start();
include 'Menu2.php';
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="Style3.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin: 0 auto; /* Centers the container horizontally */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .search-container, .status-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-container input, .status-container select {
            padding: 10px;
            margin-right: 10px;
            width: 250px;
        }

        .search-container button, .status-container button {
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        .search-container button:hover, .status-container button:hover {
            background-color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a:hover {
            background-color: #444;
        }

        .pagination .active {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delivery Schedule</h2>

        <!-- Search by Customer Name or Date -->
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by Customer Name or Date" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Filter by Status -->
        <div class="status-container">
            <form method="GET" action="">
                <select name="status_filter">
                    <option value="">All Status</option>
                    <option value="Pending" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Completed" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="In Progress" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <table>
            <tr>
               
                <th>Address</th>
                <th>Schedule Date</th>
                <th>Time Slot</th>
                <th>Status</th>
            </tr>

            <?php
            // Error handling for DB connection
            $conn = new mysqli("localhost", "root", "", "laundry_db");

            if ($conn->connect_error) {
                echo "<tr><td colspan='5'>Error connecting to the database. Please try again later.</td></tr>";
                exit();
            }

            // Pagination logic
            $limit = 10; // Number of records per page
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Search and filter functionality
            $search_query = '';
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search_term = $conn->real_escape_string($_GET['search']);
                $search_query = " AND (customer_name LIKE '%$search_term%' OR date LIKE '%$search_term%')";
            }

            $status_filter = '';
            if (isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
                $status = $conn->real_escape_string($_GET['status_filter']);
                $status_filter = " AND status = '$status'";
            }

            // Final query for fetching data
            $sql = "SELECT * FROM schedule WHERE 1 $search_query $status_filter LIMIT $limit OFFSET $offset";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['address']}</td>
                            <td>{$row['date']}</td>
                            <td>{$row['time']}</td>
                            <td>{$row['status']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No schedules found</td></tr>";
            }

            // Pagination controls
            $sql_count = "SELECT COUNT(*) as total FROM schedule WHERE 1 $search_query $status_filter";
            $result_count = $conn->query($sql_count);
            $total_rows = $result_count->fetch_assoc()['total'];
            $total_pages = ceil($total_rows / $limit);

            // Closing connection
            $conn->close();
            ?>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>&status_filter=<?php echo isset($_GET['status_filter']) ? $_GET['status_filter'] : ''; ?>">Prev</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>&status_filter=<?php echo isset($_GET['status_filter']) ? $_GET['status_filter'] : ''; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>&status_filter=<?php echo isset($_GET['status_filter']) ? $_GET['status_filter'] : ''; ?>">Next</a>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>