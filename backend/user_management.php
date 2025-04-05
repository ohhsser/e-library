<?php
include 'connection.php';

// Handle creating a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['_method'] !== 'PUT') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $src = isset($_POST['src']) ? $_POST['src'] : "";
    $date = date("y/m/d");

    // Check if email already exists
    $selectedTable = ($role === "user") ? "user" : "admin";

    $checkEmailSql = "SELECT * FROM `$selectedTable` WHERE email = '$email'";
    $result = $con->query($checkEmailSql);

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists."]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $sql = "INSERT INTO `$selectedTable` (name, email, phone, password, src, date) 
            VALUES ('$name', '$email', '$phone', '$hashedPassword', '$src', '$date')";

    if ($con->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "User created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $con->error]);
    }
    exit;
}


// Handle fetching all users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $users = [];

    // Fetch all users from the 'user' table
    $userResult = $con->query("SELECT * FROM user");
    while ($row = $userResult->fetch_assoc()) {
        $row['role'] = 'user'; // Add role to each user entry
        $users[] = $row;
    }

    // Fetch all users from the 'admin' table
    $adminResult = $con->query("SELECT * FROM admin");
    while ($row = $adminResult->fetch_assoc()) {
        $row['role'] = 'admin'; // Add role to each admin entry
        $users[] = $row;
    }

    echo json_encode($users);
    exit;
}


// Handle deleting a user
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $userId = $data['id'];
    $email = $data['email'];
    $role = $data['role'];

    $selectedTable = ($role === "user") ? "user" : "admin";
    $sql = "DELETE FROM `$selectedTable` WHERE id = '$userId' AND email = '$email'";

    if ($con->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $con->error]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $src = isset($_POST['avatarUrl']) ? $_POST['avatarUrl'] : null;
    $role = isset($_POST['role']) ? $_POST['role'] : null;

        $updateFields = [];
    $params = [];
    $types = ''; // To store parameter types for bind_param
    
    if ($email) {
        $updateFields[] = "email = ?";
        $params[] = $email;
        $types .= 's'; // 's' for string
    }
    
    if ($phone) {
        $updateFields[] = "phone = ?";
        $params[] = $phone;
        $types .= 's'; // 's' for string
    }
    
    if ($password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updateFields[] = "password = ?";
        $params[] = $hashedPassword;
        $types .= 's'; // 's' for string
    }
    
    if ($src) {
        $updateFields[] = "src = ?";
        $params[] = $src;
        $types .= 's'; // 's' for string
    }
    
    if (empty($updateFields)) {
        echo json_encode(["status" => "error", "message" => "No data to update"]);
        exit;
    }
    
    $selectedTable = ($role === "user") ? "user" : "admin";
    $sql = "UPDATE `$selectedTable` SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $params[] = $id;
    $types .= 'i'; // 'i' for integer (id)
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $con->error]);
        exit;
    }
    
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "User updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No rows updated"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    
    $stmt->close();
}