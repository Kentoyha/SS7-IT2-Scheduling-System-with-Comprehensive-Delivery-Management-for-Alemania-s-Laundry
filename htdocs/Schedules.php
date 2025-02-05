  <?php 
include 'Menu.php';

session_start(); // Start the session

// Check if the user is logged in and has the correct account level
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

// If the user is logged in and is an admin, display the dashboard

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduling System</title>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f1f8fc; /* Light blue for freshness */
            color: #333; /* Dark gray for text */
            padding: 20px;
        }

        /* Header styles */
        header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 36px;
            color: #3a7eab; /* Soft blue for a clean, fresh vibe */
            margin-bottom: 10px;
        }

        p {
            font-size: 18px;
            color: #5d8e8c; /* Soft teal green to resemble fresh laundry */
            margin-bottom: 20px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #3a7eab; /* Soft blue for headers */
            color: white;
            font-size: 18px;
        }

        td {
            font-size: 16px;
            color: #333;
        }

        .no-orders {
            color: #ff8c42; /* Warm orange for no orders text */
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            padding: 20px;
        }

        /* Dashboard section */
        .dashboard-actions {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Link styles */
        a {
            font-size: 16px;
            color: #ff8c42; /* Warm orange for a pop of color */
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            border: 2px solid #ff8c42; /* Border color matches link text */
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #ff8c42; /* Background color change on hover */
            color: white;
        }

    </style>
</head>

<body>
    <link rel="stylesheet" href="style2.css">
    <header>
        <h1>Schedules</h1>
    </header>
    <div class="order-form">
        <form action="Schedules.php" method="POST">
            <label for="schedule_date">Schedule Date</label>
            <input type="date" id="schedule_date" name="schedule_date" required>

            <label for="time_slot">Time Slot</label>
            <select id="time_slot" name="time_slot" required>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
                <option value="evening">Evening</option>
            </select>
            

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="In Progress">In Progress</option>
            </select>

            <button type="submit" name="submit_order">Schedule</button>
          
        </form>
    </div>
</body>
</html>
</html>
    <div class="container">
        <table>
            <tr>
                <th>Schedule Date</th>
                <th>Time Slot</th>
            </tr>
            <?php 
session_start(); // Start the session

// Check if the user is logged in and has the correct account level
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 1) {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

// Handle form submission
if (isset($_POST['submit_order'])) {
    $conn = new mysqli("localhost", "root", "", "laundry_db");
    $status = $_POST['status'];

    $sql = "INSERT INTO Deliveries (Delivery_staff_firstname, Delivery_Date, Status) VALUES ('$staff_name', '$delivery_date', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New order has been placed successfully!');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
            <?php
            $conn = new mysqli("localhost", "root", "", "laundry_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "SELECT * FROM schedule";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['Schedule date']}</td>
                            <td>{$row['Time Slot']}</td>
                            
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No schedules found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>