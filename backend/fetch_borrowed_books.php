<?php
session_start();
include 'connection.php';

$user_email = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"], true)[0] : null;
$user_role = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"])[3] : $_SESSION['user_data'][3];

if ($user_role === "admin") {
    $stmt = $con->prepare("SELECT id, user_email, book_id, bookname, src, date FROM reserved");
    $stmt->execute();
} else {
    $stmt = $con->prepare("SELECT id, user_email, book_id, bookname, src, date FROM reserved WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
}
$result = $stmt->get_result();

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $book_ids = explode(',', $row['book_id']); // Assuming book_id is a comma-separated list

        $bookDetails = []; // To store details of all books related to this row

        foreach ($book_ids as $book_id) {
            $book_id = intval(trim($book_id)); // Ensure it's a valid integer
            if ($book_id > 0) {
                // Fetch book details from the 'books' table
                $bookStmt = $con->prepare("SELECT id, name, author, category, description, published, quantity, rack_no FROM books WHERE id = ?");
                $bookStmt->bind_param("i", $book_id);
                $bookStmt->execute();
                $bookResult = $bookStmt->get_result();

                if ($bookResult->num_rows > 0) {
                    $bookDetails[] = $bookResult->fetch_assoc(); // Add each book's details to the array
                }
            }
        }

        $row['book_details'] = $bookDetails; // Attach all fetched book details
        $books[] = $row;
    }
}

echo json_encode($books);
?>