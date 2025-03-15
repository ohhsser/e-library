<?php
session_start();
include './backend/connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get book ID
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch book details
$book = null;
if ($book_id > 0) {
    $result = $conn->query("SELECT * FROM tbl_books WHERE id = $book_id");
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        die("Book not found.");
    }
} else {
    die("Invalid book ID.");
}

// Handle form submission (update book details)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $genre = $_POST['genre'];
    $quantity = intval($_POST['quantity']);

    $stmt = $conn->prepare("UPDATE tbl_books SET title = ?, author = ?, isbn = ?, genre = ?, quantity = ? WHERE id = ?");
    $stmt->bind_param("ssssii", $title, $author, $isbn, $genre, $quantity, $book_id);

    if ($stmt->execute()) {
        echo "Book updated successfully! <a href='manage_books.php'>Back to Book Management</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<h2>Edit Book</h2>
<a href="manage_books.php">Back to Book Management</a>

<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required><br>

    <label>Author:</label><br>
    <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required><br>

    <label>ISBN:</label><br>
    <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" required><br>

    <label>Genre:</label><br>
    <input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>" required><br>

    <label>Quantity:</label><br>
    <input type="number" name="quantity" value="<?= intval($book['quantity']) ?>" required><br>

    <button type="submit">Update Book</button>
</form>