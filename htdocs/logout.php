<?php
session_start();
unset($_SESSION['User_ID']);
unset($_SESSION['username']);
unset($_SESSION['account_level']);
session_regenerate_id(true); 
header("Location: index.php");
exit();
?>
