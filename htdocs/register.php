<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = htmlspecialchars($_POST['contact']);
    $email = htmlspecialchars($_POST['email']);
    $account_level = $_POST['account_level'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, contact, email, account_level) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $contact, $email, $account_level);
        $stmt->execute();
        $success = "Registration successful!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="number" name="contact" placeholder="Contact number" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <select name="account_level" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <input type="submit" value="Register">
    </form>
</body>
</html>