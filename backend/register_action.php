<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
include 'connection.php';

// Password validation function
function isValidPassword($password)
{
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $src = "cat.webp_6277b6abbf35d";
    $date = date("y/m/d");

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../createaccount.php");
        exit();
    }

    // Check password strength
    if (!isValidPassword($password)) {
        $_SESSION['error'] = "Password must be at least 6 characters long and contain at least one uppercase letter, one number, and one special character.";
        header("Location: ../createaccount.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../createaccount.php");
        exit();
    }

    // Select correct table
    $selectedTable = ($role === "user") ? "user" : "admin";

    // Check if email already exists
    $check_sql = "SELECT * FROM `$selectedTable` WHERE email = ?";
    $stmt = $con->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered.";
        header("Location: ../createaccount.php");
        exit();
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $insert_sql = "INSERT INTO `$selectedTable` (name, email, password, phone, src, date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($insert_sql);
    $stmt->bind_param("ssssss", $name, $email, $hashedPassword, $phone, $src, $date);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Account created successfully! You can now log in.";
        header("Location: ../index.php");
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: ../createaccount.php");
    }
    exit();
}
