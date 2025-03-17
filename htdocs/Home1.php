<?php
include 'db_connect.php'; // Include the database connection file
include 'Menu2.php'; // Include the men
include 'Logout.php';
session_start(); // Start the session

// Check if the user is logged in and has the correct account level
if (!isset($_SESSION['username']) && $_SESSION['account_level'] != "2") {
    header("Location: login.php"); 
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="home.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    

    <style>
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: black;        }

       
        .add-team-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

       
        .add-team-btn {
            background-color: #4CAF50; 
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-team-btn:hover {
            background-color: #45a049;
        }

        
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        
        .actbutton, .actdelete {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .actedit {
            background-color: #1cc6ff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
        }
        .actedit:hover {
            background-color: #32b6e3;
        }

        .actbutton:hover, .actdelete:hover {
            background-color: #45a049;
        }

        .actdelete {
            background-color: #dc3545;
        }

        .actdelete:hover {
            background-color: #c82333;
        }

      
        img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
        

    </style>
</head>
<body>
    <h1>On going Orders</h1>
    <table>
        <tr>
            <th>Order Date</th>
            <th>Laundry Type</th>
            <th>Laundry Quantity</th>
            <th>Cleaning Type</th>
            <th>Status</th>
        </tr>

        <?php
        $sql = "SELECT * FROM Orders WHERE STATUS = 'In Progress' ORDER BY Priority_number DESC";
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        } else {
            while ($result = mysqli_fetch_assoc($query)) {
                echo "<tr>";
                echo "<td>" . $result["Order_date"] . "</td>";
                echo "<td>" . $result["Laundry_type"] . "</td>";
                echo "<td>" . $result["Laundry_quantity"] . "</td>";
                echo "<td>" . $result["Cleaning_type"] ."</td>";
                echo "<td>" . $result["Status"] . "</td>";
                echo "</tr>";
            }
        }

        if (isset($_GET['action']) && isset($_GET['Team_id'])) {
            $action = trim($_GET['action']);
            $Team_id = trim($_GET['Team_id']);

            if ($action == 'delete') {
                $sql = "DELETE FROM Team WHERE Team_id = $Team_id";
                if (mysqli_query($conn, $sql)) {
                    echo "<script>alert('Team has been removed'); window.location='Teams.php';</script>";
                }
            }
        }
        ?>
    </table>
</body>
</html>


