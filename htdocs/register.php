<?php
include 'db_connect.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>ACCOUNT REGISTRATION</h1>
    </header>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="number" name="contact" placeholder="Contact number" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <select name="account_level">
            <option value="1">Admin</option>
            <option value="2">User</option>
        </select>
        <input type="submit" name="submit" value="Register">
    </form>

        <?php
        
        if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $account_level = $_POST['account_level'];

            if($account_level == 1){
                $query = "INSERT INTO Admin(Username, Password, Contact_info, Email) VALUES ('$username', '$password', '$contact', '$email')";
                if (mysqli_query($conn, $query)) {
                    echo "<script>
                            alert('Admin registered successfully.');
                            window.location.href='login.php';
                          </script>";
                    exit();
                } else {
                    echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
                }
            } else {
                $query = "INSERT INTO User(Username, Password, Contact_info, Email) VALUES ('$username', '$password', '$contact', '$email')";
                if (mysqli_query($conn, $query)) {
                    echo "<script>
                            alert('User registered successfully.');
                            window.location.href='login.php';
                          </script>";
                    exit();
                } else {
                    echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
                }
            }
            
        }
        ?>

    

</body>
</html>