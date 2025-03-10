<?php
session_start();
session_destroy(); // Destroy session
setcookie("user_data", "", time() - 3600, "/"); // Clear user cookie
header("Location: ../index.php"); // Redirect to login/home page
exit();
?>