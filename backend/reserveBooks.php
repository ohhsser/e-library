<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST["book_id"];
    $bookname = $_POST["bookname"];
    $src = $_POST["src"];
    $user_email = $_POST["user_email"];
    $issue_date = date("Y/m/d");

    // Check if the book exists and get the quantity
    $stmt = $con->prepare("SELECT quantity FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Book not found"]);
        exit;
    }

    $stmt->bind_result($quantity);
    $stmt->fetch();
    $stmt->close();

    // Ensure the book is available
    if ($quantity <= 0) {
        echo json_encode(["status" => "error", "message" => "Book is out of stock"]);
        exit;
    }

    // Check if the user has already reserved the book
    $stmt = $con->prepare("SELECT * FROM reserved WHERE user_email = ? AND book_id = ?");
    $stmt->bind_param("si", $user_email, $book_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Book already reserved"]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Insert the reservation
    $stmt = $con->prepare("INSERT INTO reserved (user_email, book_id, bookname, src, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $user_email, $book_id, $bookname, $src, $issue_date);

    if ($stmt->execute()) {
        $stmt->close();

        // Decrease book quantity by 1
        $new_quantity = $quantity - 1;
        $stmt = $con->prepare("UPDATE books SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $book_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "success", "message" => "Book reserved successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to reserve book"]);
    }
}
