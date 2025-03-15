<?php
session_start();
include 'connection.php';

$query = "SELECT id, name, author, category, description, published, src, quantity, rack_no FROM books";
$result = $con->query($query);

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

echo json_encode($books);
?>