<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Retrieving form data
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST['role'];

    // Determine which table to use based on role
    $selected = ($role === "user") ? "user" : "admin";

    // Fetch user record from the database
    $query = "SELECT * FROM `$selected` WHERE email='$email'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            // Store user data in an array
            $user_data = [
                $user["email"],
                $user["name"],
                $user["src"],
                $role,
                $user["date"]
            ];

            // Save the session
            $_SESSION['user_data'] = $user_data;

            // Save the cookies
            setcookie("user_data", json_encode($user_data), time() + (86400 * 30), "/");

            // Redirect to the dashboard
            header("Location: ../dashboard.php");
            exit();
        } else {
            // Password mismatch
            header("Location: ../index.php?error=Invalid password");
            exit();
        }
    } else {
        // User not found
        header("Location: ../index.php?error=User not found");
        exit();
    }
}
?>