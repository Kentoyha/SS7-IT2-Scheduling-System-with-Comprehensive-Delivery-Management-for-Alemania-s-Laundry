<?php
include("db_connect.php");
include("menu.php");



session_start();



if (!isset($_SESSION['username']) || $_SESSION['account_level'] != 2) {
    header("Location: login.php"); // Redirect to login page if not logged in or not a user
  
   
}


    
  
?>


<head>
    <link rel="stylesheet" href="addt.css">
</head>

<body>
<h1>Place Order</h1>

<form method="POST" action="">
    <table border=1 align="center" cellspacing="0" cellpadding="10">
    
        <tr>
            <td> Laundry Type </td>
            <td> 
                <select name="Laundry_Type" required>
                    <option value="">Select</option>
                    <option value="Beddings">Beddings</option>
                    <option value="Curtains">Curtains</option>
                    <option value="Towel">Towel</option>
                    <option value="Topper">Topper</option>
                    <option value="Table Cloth">Table Cloth</option>
                    <option value="Mixed">Mixed</option>
                </select> 
            </td>
        </tr>
        <tr>
            <td> Laundry Quantity </td>
            <td> <input type="number" name="Laundry_Quantity" required min="1"> </td>
        </tr>
        <tr>
            <td> Cleaning Type </td>
            <td> 
                <select name="Cleaning_Type" required>
                    <option value="">Select</option>
                    <option value="Dry Cleaning">Dry Cleaning</option>
                    <option value="Wet Cleaning">Wet Cleaning</option>
                    <option value="Spot Cleaning">Spot Cleaning</option>
                    <option value="Mixed">Mixed</option>
                </select> 
            </td>
        </tr>
        <tr>    
            <td> Place </td>
            <td> <input type="text" name="Place" required> </td>
        </tr>
        <tr>
            <td> Priority Note (optional) </td>
            <td> <textarea name="Priority_note" rows="4" cols="50"></textarea> </td>
        </tr>
        <input type="hidden" name="Status" value="Pending"> 
        <tr>
            <td colspan="2">
                <button type="submit" name="Order"> Submit</button>
            </td>
        </tr>
    </table>
</form>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['Order'])) {
    $Order_date = date("Y-m-d");
    $Laundry_Type = $_POST['Laundry_Type'];
    $Laundry_Quantity = $_POST['Laundry_Quantity'];
    $Cleaning_Type = $_POST['Cleaning_Type'];
    $Place = $_POST['Place'];
    $Priority_note = $_POST['Priority_note'];
    $Status = $_POST['Status'];
    $User_id = $_SESSION['User_ID'];
   

    $sql = "INSERT INTO Orders (Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_note, Status, User_ID)
     VALUES ('$Order_date', '$Laundry_Type', '$Laundry_Quantity', '$Cleaning_Type', '$Place', '$Priority_note', '$Status' , '$User_id')";

    $query = mysqli_query($conn, $sql);

    if ($query) {
        echo "Order placed successfully!";
    } else {
        echo "<script> alert('Error: " . mysqli_error($conn) . "'); </script>";
    }
    
}
?>
</body>
</html>