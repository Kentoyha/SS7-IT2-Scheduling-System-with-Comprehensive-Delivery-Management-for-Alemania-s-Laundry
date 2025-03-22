<?php
include("db_connect.php");
include("Menu2.php");

session_start();

// Check if the user is logged in and has a valid session
if (!isset($_SESSION['User_ID']) || $_SESSION['account_level'] != 2) {
    echo "<script>alert('You are not authorized to access this page.'); window.location.href='index.php';</script>";
    exit();

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
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
                    <option value="" disabled selected> Click to Select</option>
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
                    <option value="Wet Cleaning">Wet Cleaning</option>
                    <option value="Dry Cleaning">Dry Cleaning</option>
                </select> 
            </td>
        </tr>
        <tr>
    <td> Place </td>
        <td>
         <?php
         $placeValue = isset($_POST['Place']) ? htmlspecialchars($_POST['Place']) : 'Beat Naawan';
         ?>
         <input type="text" name="Place" value="<?php echo $placeValue; ?>" required>
        </td>
    </tr>

        <tr>
            <td> Priority Number </td>
            <td>
                <select name="Priority" required>
                    <option value="" disabled selected>Select Priority Number</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                </select>
            </td>
        </tr>
        <input type="hidden" name="Status" value="Pending"> 
        <tr>
            <td colspan="2">
                <button type="submit" name="Order">Place Order</button>
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

    input[type="text"], input[type="number"], select {
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

if (isset($_POST['Order'])) {
    $Order_date = date("Y-m-d");
    $Laundry_Type = $_POST['Laundry_Type'];
    $Laundry_Quantity = $_POST['Laundry_Quantity'];
    $Cleaning_Type = $_POST['Cleaning_Type'];
    $Place = "Beat Naawan"; 
    $Priority_number = $_POST['Priority'];
    $Status = $_POST['Status'];

   
    $User_ID = isset($_SESSION['User_ID']) ? $_SESSION['User_ID'] : NULL;

    if ($User_ID === NULL) {
        echo "<script>alert('Error: User not found. Please log in again.');</script>";
        exit();
    }

    // Use prepared statements to prevent SQL Injection
    $sql = "INSERT INTO Orders (Order_date, Laundry_type, Laundry_quantity, Cleaning_type, Place, Priority_number, Status, User_ID)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssissisi", $Order_date, $Laundry_Type, $Laundry_Quantity, $Cleaning_Type, $Place, $Priority_number, $Status, $User_ID);
    $query = mysqli_stmt_execute($stmt);

    if ($query) {
        echo "<script>alert('Order Placed Successfully'); window.location.href='Orders2.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

?>

</body>
</html>
