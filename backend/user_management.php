<?php
include 'connection.php';

// Handle creating a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['_method'] !== 'PUT') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $src = "cat.webp_6277b6abbf35d";
    $date = date("y/m/d");

    // Check if email already exists
    $checkEmailSql = "SELECT * FROM user WHERE email = '$email'";
    $result = $con->query($checkEmailSql);

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists."]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $sql = "INSERT INTO user (name, email, phone, password, src, date) 
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

    $userResult = $con->query("SELECT * FROM user");
    while ($row = $userResult->fetch_assoc()) {
        $users[] = $row;
    }

    $resultAdmin = $con->query("SELECT * FROM admin");
    while ($row = $resultAdmin->fetch_assoc()) {
        $users[] = $row;
    }


    echo json_encode($users);
    exit;
}

// Handle deleting a user
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $userId = $data['id'];

    $sql = "DELETE FROM user WHERE id = '$userId'";

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
    $src = isset($_FILES['src']) ? $_FILES['src'] : null;

    $updateFields = [];
    $params = [];

    if ($email) {
        $updateFields[] = "email = ?";
        $params[] = $email;
    }

    if ($phone) {
        $updateFields[] = "phone = ?";
        $params[] = $phone;
    }

    if ($password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updateFields[] = "password = ?";
        $params[] = $hashedPassword;
    }

    if ($src && $src['error'] === 0) {
        $srcPath = 'uploads/' . basename($src['name']);
        move_uploaded_file($src['tmp_name'], $srcPath);
        $updateFields[] = "src = ?";
        $params[] = $srcPath;
    }

    if (empty($updateFields)) {
        echo json_encode(["status" => "error", "message" => "No data to update"]);
        exit;
    }

    $sql = "UPDATE user SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $params[] = $id;
    $stmt = $con->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    exit;
}

$con->close();
?>