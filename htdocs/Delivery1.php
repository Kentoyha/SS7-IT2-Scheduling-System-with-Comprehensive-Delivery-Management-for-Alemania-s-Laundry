<?php
include 'db_connect.php';
include 'Menu2.php';
include 'Logout.php';

session_start();
// ✅ Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 2) {
    header("Location: login.php"); // Redirect to login page if not an admin
    exit();
}

?>

<!DOCTYPE html>
<html>
<body>
 <style>
   
   body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .title {
        text-align: center;
        color: #333;
        margin-top: 20px;
    }

    .addplayer {
        text-align: center;
        margin: 20px 0;
    }

    .addplayer button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
    }

    .addplayer button:hover {
        background-color: #45a049;
    }

    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px;
        text-align: Center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .button {
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        border-radius: 5px;
    }

    .button.green {
        background-color: #4CAF50;
        color: white;
    }

    .button.red {
        background-color: #f44336;
        color: white;
    }

    .button.green:hover {
        background-color: #45a049;
    }

    .button.red:hover {
        background-color: #e53935;
    }

    select {
        padding: 10px;
        font-size: 16px;
        margin-right: 10px;
    }

    img {
        border-radius: 50%;
    }
</style>
       
        <h1 align="center"> Delivery </h1>
        <div class="container">
        <div class="buanga">
        <div class="addplayer">
          
        </div>
        </div>
        </div>
        
        <?php
        
        ?>
        <table align="center" cellspacing="0" cellpadding="10">
        <tr>
            
            <th>Order Date</th>
            <th>Delivery Date</th>
            <th>Delivery <br> Staff Name</th>
            <th>Contact <br> Info  </th>
            <th>Status</th>
           
            
        

        </tr>
   
        <?php
       
       $sql = "SELECT Delivery.*, Orders.Order_date 
       FROM Delivery 
       INNER JOIN Orders ON Delivery.Order_ID = Orders.Order_ID 
       WHERE Delivery.Status = 'Out for Delivery'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            
            echo "<td>" . $row['Order_date'] . "</td>";
            echo "<td>" . $row['Delivery_date'] . "</td>";
            echo "<td>" . $row['Delivery_staff_name'] . "</td>";
            echo "<td>" . $row['Contact_info'] . "</td>";
            echo "<td>" . $row['Status'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No records found.</td></tr>";
    }
    ?>
    </table>
 <style>
        .actdelete {
            color: white;
            background-color: red;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .actdelete:hover {
            background-color: darkred;
        }
   </style>
    
    <?php
    ?> 
    <?php
    if (isset($_GET['action']) && isset($_GET['Game_id'])) {
        $action = trim($_GET['action']);
        $Game_id = trim($_GET['Game_id']);

        if ($action == 'delete') {
            $sql = "DELETE FROM Game WHERE Game_id = $Game_id";
            if (mysqli_query($conn, $sql)) {
                echo "<script> alert('Game has been removed'); window.location='Games.php'; </script>";
            }
        }
    }
    ?>
    </body>
</html>