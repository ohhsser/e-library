<?php
// DB Connection
include './connection.php';

header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST["id"] ?? null;
    $username = $_POST["username"] ?? null;
    $user_email = $_POST["email"] ?? null;
    $bookname = $_POST["book_name"] ?? null;
    $date = date("Y/m/d");
    $src = $_POST["src"] ?? null;

    if (!$book_id || !$username || !$user_email || !$bookname) {
        echo json_encode(["status" => "error", "message" => "Missing required fields."]);
        exit;
    }

    // Check if the book exists in the reserved table
    $query = "SELECT * FROM reserved WHERE book_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the reservation
        $delete_query = "DELETE FROM reserved WHERE book_id = ?";
        $delete_stmt = $con->prepare($delete_query);
        $delete_stmt->bind_param("i", $book_id);

        if ($delete_stmt->execute()) {
            // Increment book quantity in `books` table
            $update_query = "UPDATE books SET quantity = quantity + 1 WHERE id = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("i", $book_id);
            $update_stmt->execute();

            // Insert into `returned` table
            $insert_query = "INSERT INTO returned ( username, user_email, book_id, bookname, date, src) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $con->prepare($insert_query);
            $insert_stmt->bind_param("ssisss",  $username, $user_email, $book_id, $bookname, $date, $src);

            if ($insert_stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Book successfully returned and recorded."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to record returned book."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete reservation."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No reservation found for this book."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
