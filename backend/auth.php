<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    //retrieving form data
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST['role'];

    //db search if user exist or not
    $selected = ($role === "user") ? "user" : "admin";
    $query = "SELECT * FROM `$selected` WHERE email='$email'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        //get the user data
        $user_data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            //store data of user on array
            if ($row["email"] == $email) {
                $user_data[0] = $row["email"];
                $user_data[1] = $row["name"];
                $user_data[2] = $row["src"];
                $user_data[3] = $role;
                $user_data[4] = $row["date"];
            }
        }

        //save the session
        $_SESSION['user_data'] = $user_data;

        //save the cookies
        setcookie("user_data", json_encode($user_data), time() + (86400 * 30), "/");

        // successfully redirect to hompage
        header("Location: ../dashboard.php"); // Redirect to home page
    } else {
        // error . redirect to login page again
        header("Location: ../index.php"); // Redirect to login page
    }
}
?>