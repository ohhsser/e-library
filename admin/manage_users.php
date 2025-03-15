<?php
session_start();
include './backend/connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $conn->query("INSERT INTO user (name, email, username, password)
                  VALUES ('$name', '$email', '$username', '$password')");
}
?>

<h2>Manage Users</h2>
<a href="./index.php">Back</a>

<h3>Add New User</h3>
<form method="POST">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <select name="role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select><br>
    <button type="submit">Add User</button>
</form>

<h3>User List</h3>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php
    $users = $conn->query("SELECT * FROM tbl_users");
    while ($user = $users->fetch_assoc()) {
        echo "<tr>
                <td>{$user['name']}</td>
                <td>{$user['email']}</td>
                <td>{$user['role']}</td>
                <td><a href='../func/delete_user.php?id={$user['id']}'>Delete</a></td>
              </tr>";
    }
    ?>
</table>