<?php 
include 'Menu.php';

session_start();

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
    <div class="container">
        <h2>Delivery Schedule</h2>
        <table>
            <tr>
                <th>Schedule ID</th>
                <th>Schedule Date</th>
                <th>Time Slot</th>
            </tr>
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
            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>