<?php
include 'db_connect.php';
session_start();

if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $account_level = $_POST['account_level'];
    if($account_level == 1){
        $stmt = $conn->prepare("SELECT * FROM Admin WHERE username = ?");
        $_SESSION['username'] = $user['username'];
        $_SESSION['account_level'] = $user['account_level'];
        echo "<script>
                        alert('Welcome Admin.');
                          window.location.href='dashboard.php';
                          </script>";
                          exit();
    } 
    else{
        $stmt = $conn->prepare("SELECT * FROM User WHERE username = ?");
        $_SESSION['username'] = $user['username'];
        $_SESSION['account_level'] = $user['account_level'];
        echo "<script>
                        alert('Welcome User.');
                          window.location.href='dashboard1.php';
                          </script>";
                          exit();
    }
         
    } else {
        $error = "Invalid username or password.";
    }
    
       
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in page</title>
</head>
<body>
    <h1>Log in</h1>
    <form  method="POST">
        
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="account_level" >
            <option value="1">Admin</option>
            <option value="2">User</option>
        </select>
        <input type="submit" value="Log in">



    </form>
</body>
</html>